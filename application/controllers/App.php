<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App extends CI_Controller
{

  protected $payload;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == "OPTIONS") {
      die();
    }
    parent::__construct();
    $this->load->model('web');
    $this->load->database();

    log_message('info', 'Api.php loaded');
  }

  public function index(){
    echo "API"; //this is test
  }

  private function getDecodedPayload()
  {
    $body = $this->security->xss_clean($this->input->raw_input_stream);
    $data = json_decode($body, true);
    log_message('error', $this->input->raw_input_stream);

    if (json_last_error() !== JSON_ERROR_NONE) {

      throw new Exception('Invalid JSON payload');
    }

    foreach ($data as $key => $value) {
      $this->payload[$key] = $value;
    }

    return $this->payload;
  }

  private function validateSignInType()
  {
    $defaultException = new Exception('Invalid sign-in type');
    if (!isset($this->payload['signon-type'])) {
      throw $defaultException;
    }
    $type = $this->payload['signon-type'];
    $validType = false;
    switch ($type) {
      case 'overseer':
        $validType = true;
        break;
      case 'worker':
        $validType = true;
        break;
      default:
        $validType = false;
        break;
    }

    if (!$validType) {
      throw $defaultException;
    }

    return $validType;
  }

  private function getRelatedBuildingSitesInformation($user = null)
  {

    try {
      $user = $this->db
        ->from('user')
        ->where('id', $user->id)
        ->get()->row();

      if (!$user) {
        throw new Exception('User not found');
      }

      $overseerIn = $this->db
        ->select('fk_speciality')
        ->from('supervisor')
        ->where('fk_user', $user->id)
        ->get()->result();

      if (!$overseerIn) {
        throw new Exception('User not found as overseer');
      }

      $specialityIds = array_map(function ($item) {
        return $item->fk_speciality;
      }, $overseerIn);

      if (!$specialityIds) {
        throw new Exception('Specialities not found');
      }

      $specialities = $this->db
        ->select('id, name, fk_building_site')
        ->from('speciality')
        ->where_in('id', $specialityIds)
        ->get()->result();

      if (!$specialities) {
        throw new Exception('Specialities not found');
      }

      $buildingSiteIds = array_map(function ($item) {
        return $item->fk_building_site;
      }, $specialities);

      if (!$buildingSiteIds) {
        throw new Exception('Building sites not found');
      }

      $buildingSites = $this->db
        ->select('id, name')
        ->from('building_site')
        ->where_in('id', $buildingSiteIds)
        ->get()->result();

      if (!$buildingSites) {
        throw new Exception('Building sites not found');
      }

      //get area from each building site

      foreach ($buildingSites as $k => $buildingSite) {
        $buildingSites[$k]->areas = $this->db
          ->select('id, name')
          ->from('area')
          ->where('fk_building_site', $buildingSite->id)
          ->get()->result();

        if (!$buildingSites[$k]->areas) {
          unset($buildingSites[$k]);
          //throw new Exception('Area not found at building site ' . $buildingSite->name);
        }

        //get zone from each area

        foreach ($buildingSites[$k]->areas as $kk => $area) {

          $buildingSites[$k]->areas[$kk]->zones = $this->db
            ->select('id, name, fk_area')
            ->from('zone')
            ->where('fk_area', $area->id)
            ->get()->result();

          if (!$buildingSites[$k]->areas[$kk]->zones) {
            unset($buildingSites[$k]->areas[$kk]);
            //throw new Exception('Zone not found at area ' . $area->name . ' of building site ' . $buildingSite->name);
          }
        }
      }

      foreach ($buildingSites as $k => $buildingSite) {
        $buildingSites[$k]->specialities = array_filter($specialities, function ($speciality) use ($buildingSite) {
          return $speciality->fk_building_site == $buildingSite->id;
        });
        //remove fk_building_site from specialities
        foreach ($buildingSites[$k]->specialities as $k2 => $speciality) {
          unset($buildingSites[$k]->specialities[$k2]->fk_building_site);
        }
        //remove keys from specialities
        $buildingSites[$k]->specialities = array_values($buildingSites[$k]->specialities);
      }

      return $buildingSites;
    } catch (Exception $e) {
      return new Exception($e->getMessage());
    }
  }

  private function getRelatedBuildingSitesActivitiesInformation($buildingSites = null)
  {
    try {

      if (!$buildingSites) {
        throw new Exception('Building site data not found');
      }

      $activities = [];

      foreach ($buildingSites as $buildingSite) {
        $t = new stdClass;
        $t->id = $buildingSite->id;
        $t->areas = [];
        foreach ($buildingSite->areas as $area) {
          $t2 = new stdClass;
          $t2->id = $area->id;
          $t2->zones = [];
          foreach ($area->zones as $zone) {
            $t3 = new stdClass;
            $t3->id = $zone->id;
            $t3->activities = $this->db
              ->select('id, name, fk_zone, unt, qty, eff, activity_code')
              ->from('activity')
              ->where('fk_zone', $zone->id)
              ->get()->result();
            $t2->zones[] = $t3;
          }
          $t->areas[] = $t2;
        }
        $activities[] = $t;
      }

      return $activities;
    } catch (Exception $e) {
      return new Exception($e->getMessage());
    }
  }

  public function sign_in()
  {
    header('Content-Type: application/json');
    $response = [];
    try {
      $this->getDecodedPayload();
      $this->validateSignInType();
      $this->load->model('user');
      if ($this->payload['signon-type'] == 'overseer') {
        $role = $this->db
          ->select('id, value_p')
          ->from('role')
          ->where('value_p', 'Supervisores')
          ->get()->row();
        if (!$role) {
          throw new Exception('Role not found');
        }
        $user = $this->db
          ->select('id, email, first_name, last_name')
          ->from('user')
          ->where('email', $this->payload['userEmail'])
          ->where('password', pack('H*', hash('sha512', $this->payload['userPassword'])))
          ->where('fk_role', $role->id)
          ->get()->row();
        if (!$user) {
          throw new Exception('User not found');
        }
        $user->role = $role->value_p;
        $user->signonType = $this->payload['signon-type'];
        $response = [];

        //compress string data

        $response['user_data'] = $user;
      }

      echo json_encode($response);
    } catch (Exception $e) {
      $this->output->set_status_header(400);
      echo json_encode(
        [
          'message' => $e->getMessage()
        ]
      );
    }
  }

  public function set_building_site_data()
  {
    header('Content-Type: application/json');
    $response = [];
    try {
      $this->getDecodedPayload();
      $user = (object) $this->payload['user_data'];
      $buildingSites = $this->getRelatedBuildingSitesInformation($user);
      $response['buildingSites'] = $buildingSites;
      $response['activities'] = $this->getRelatedBuildingSitesActivitiesInformation($buildingSites);

      echo json_encode($response);
    } catch (Exception $e) {
      $this->output->set_status_header(400);
      echo json_encode(
        [
          'message' => $e->getMessage()
        ]
      );
    }
  }

  public function test()
  {
    header('Content-Type: application/json');
    $this->output->set_status_header(200);
    echo json_encode(
      [
        'message' => "Test"
      ]
    );
  }

  public function set_app_data()
  {
    header('Content-Type: application/json');
    $response = [];
    try {
      $this->getDecodedPayload();
      $this->payload['user_data'] = (object) $this->payload['user_data'];

      $response['user_data'] = $this->db
        ->select('id, email, first_name, last_name')
        ->from('user')
        ->where('id', $this->payload['user_data']->id)
        ->get()->row();

      $activity = $this->db->select('id, fk_building_site, fk_speciality, fk_speciality_role, activity_code, qty')->from('activity')->where('id', $this->payload['activityId'])->get()->row();

      if (!$activity || is_null($activity)) {
        throw new Exception('Activity not found');
      }


      $i = 0;
      if (trim($this->payload['imageBase64'] != "")) {
        $imageType = $this->db->select('id, code_name')->from('image_type')->where('code_name', 'activity_report')->get()->row();
        $this->db->set('fk_image_type', $imageType->id);
        $this->db->set('name', '');
        $this->db->set('ext', '');
        $this->db->insert('image');
        $i = $this->db->insert_id();

        $config = $this->web->get_upload_config('activity_report');
        $config['upload_path'] = $config['upload_path'] . $i . '/';
        if (!is_dir($config['upload_path'])) {
          mkdir($config['upload_path'], 0777, true);
        }

        $encodedImageFile = base64_decode($this->payload['imageBase64']);
        $encodedImageFile = str_replace('data:image/jpeg;base64,', '', $encodedImageFile);
        $decodedImageFile = base64_decode($encodedImageFile);

        $dt = new DateTime('NOW', new DateTimeZone('America/Santiago'));
        $name = md5($dt->format('dmYHis') . $i);
        $ext = ".jpg";

        file_put_contents(FCPATH . $config['upload_path'] . $name . $ext, $decodedImageFile);

        $this->db->set('name', $name);
        $this->db->set('ext', $ext);
        $this->db->where('id', $i);
        $this->db->update('image');
      }

      $this->db->set('fk_building_site', $activity->fk_building_site);
      $this->db->set('fk_speciality', $activity->fk_speciality);
      $this->db->set('fk_speciality_role', $activity->fk_speciality_role);
      $this->db->set('activity_code', $activity->activity_code);
      $this->db->set('fk_activity', $activity->id);
      $this->db->set('hh', 0);
      $this->db->set('activity_date', $this->payload['date']);
      $this->db->set('activity_date_f', strtotime($this->payload['date']) / 86400);
      $this->payload['qty'] = str_replace(',', '.', $this->payload['qty']);
      $this->payload['qty'] = round($this->payload['qty'], 4);
      $this->db->set('avance', $this->payload['qty']);
      $this->db->set('p_avance', $activity->qty == 0 ? 0 : $this->payload['qty'] / $activity->qty * 100);
      $this->db->set('comment', $this->payload['comments']);
      $this->db->set('machinery', $this->payload['machinery']);
      $this->db->set('fk_image', $i);
      $this->db->insert('activity_registry');
      $activityRegistryId = $this->db->insert_id();

      $response['activities'] = [];
      $response['removedId'] = $activityRegistryId;
      $response['success'] = true;

      echo json_encode($response);
    } catch (\Exception $e) {
      $this->output->set_status_header(400);
      echo json_encode(
        [
          'message' => $e->getMessage()
        ]
      );
    }
  }

  public function full_building_site_list(
    array $buildingSiteIds = []
  ) {
    header('Content-Type: application/json');
    try {
      $data = $this->db
        ->select('id, name')
        ->from('building_site')
        ->where_in('id', $buildingSiteIds)
        ->get()->result();

      if (!$data) {
        throw new Exception('Building sites not found');
      }

      //add specialities and roles to building sites

      foreach ($data as $k => $buildingSite) {
        $data[$k]->specialities = $this->db
          ->select('id, name')
          ->from('speciality')
          ->where('fk_building_site', $buildingSite->id)
          ->get()->result();

        if (!$data[$k]->specialities) {
          unset($data[$k]);
          //throw new Exception('Speciality not found at building site ' . $buildingSite->name);
        }

        //get roles from each speciality

        foreach ($data[$k]->specialities as $kk => $speciality) {

          $data[$k]->specialities[$kk]->specialityRoles = $this->db
            ->select('id, name')
            ->from('speciality_role')
            ->where('fk_speciality', $speciality->id)
            ->get()->result();

          if (!$data[$k]->specialities[$kk]->specialityRoles) {
            unset($data[$k]->specialities[$kk]);
            //throw new Exception('Role not found at speciality ' . $speciality->name . ' of building site ' . $buildingSite->name);
          }
        }
      }

      //add areas and zones to building sites

      foreach ($data as $k => $buildingSite) {
        $data[$k]->areas = $this->db
          ->select('id, name')
          ->from('area')
          ->where('fk_building_site', $buildingSite->id)
          ->get()->result();

        if (!$data[$k]->areas) {
          unset($data[$k]);
          //throw new Exception('Area not found at building site ' . $buildingSite->name);
        }

        //get zone from each area

        foreach ($data[$k]->areas as $kk => $area) {

          $data[$k]->areas[$kk]->zones = $this->db
            ->select('id, name, fk_area')
            ->from('zone')
            ->where('fk_area', $area->id)
            ->get()->result();

          if (!$data[$k]->areas[$kk]->zones) {
            unset($data[$k]->areas[$kk]);
            //throw new Exception('Zone not found at area ' . $area->name . ' of building site ' . $buildingSite->name);
          }
        }
      }

      //add activities and activity_data to building sites

      foreach ($data as $k => $buildingSite) {
        $data[$k]->activities = $this->db
          ->select('id, name, fk_zone, fk_speciality_role, unt, qty, eff, activity_code')
          ->from('activity')
          ->where('fk_building_site', $buildingSite->id)
          ->get()->result();

        if (!$data[$k]->activities) {
          unset($data[$k]);
          //throw new Exception('Activity not found at building site ' . $buildingSite->name);
        } else {
          //append activity->speciality_role and activity->zone to each activity

          foreach ($data[$k]->activities as $kk => $activity) {
            $data[$k]->activities[$kk]->speciality_role = $this->db
              ->select('id, name')
              ->from('speciality_role')
              ->where('id', $activity->fk_speciality_role)
              ->get()->row();

            $data[$k]->activities[$kk]->zone = $this->db
              ->select('id, name')
              ->from('zone')
              ->where('id', $activity->fk_zone)
              ->get()->row();

            if (!$data[$k]->activities[$kk]->zone) {
              unset($data[$k]->activities[$kk]);
              //throw new Exception('Zone not found at activity ' . $activity->name . ' of building site ' . $buildingSite->name);
            }
          }
        }

      }

      $this->output->set_status_header(200);
      return (
        (object) [
          'status' => 'success',
          'message' => 'Building sites fetched successfully',
          'data' => $data
        ]
      );

    } catch (Exception $e) {
      $this->output->set_status_header(400);
      return (object) (
        [
          'status' => 'error',
          'message' => $e->getMessage()
        ]
      );
    }
  }

  public function new_sign_in($signinType = 'overseer')
  {
    header('Content-Type: application/json');

    $response = [];
    try {


      //method must be POST

      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
      }

      //recieve payload as JSON
      $payload = json_decode(file_get_contents('php://input'), true);

      if ($signinType == 'overseer') {
        $user = $this->db
          ->select('user.id, email, first_name, last_name')
          ->from('user')
          ->join('role', 'role.id = user.fk_role')
          ->where('email', $payload['username'])
          ->where('password', pack('H*', hash('sha512', $payload['password'])))
          ->where('role.value_p', 'Supervisores')
          ->where('deleted_at', null)
          ->get()->row();

        if (!$user) {
          throw new Exception('Credenciales incorrectas');
        }

        $overseeIn = $this->db
          ->select('fk_speciality, fk_building_site')
          ->from('supervisor')
          ->join('speciality', 'speciality.id = supervisor.fk_speciality')
          ->where('fk_user', $user->id)
          ->get()->result();

        if (!$overseeIn) {
          throw new Exception('Supervisor no ha sido asignado a ninguna especialidad ni obra');
        }

        $buildingSiteIds = array_map(function ($item) {
          return $item->fk_building_site;
        }, $overseeIn);

        $specialityIds = [];

        if (!$buildingSiteIds) {
          throw new Exception('Obras no encontradas');
        }

        foreach ($overseeIn as $item) {
          $specialityIds[] = $item->fk_speciality;
        }

        if (!$specialityIds) {
          throw new Exception('Especialidades no encontradas');
        }

        $user->extra = [
          'signon_type' => 'overseer',
          'building_site_ids' => $buildingSiteIds,
          'speciality_ids' => $specialityIds,
          'message' => 'Supervisor autenticado correctamente'
        ];

      } elseif ($signinType == 'worker') {
        $user = $this->db
          ->select('id, dni, name, email, fk_building_site, fk_speciality, fk_speciality_role')
          ->from('worker')
          ->where('email', $payload['username'])
          ->where('password', pack('H*', hash('sha512', $payload['password'])))
          ->get()->row();

        if (!$user) {
          throw new Exception('Credenciales incorrectas');
        }

        $buildingSiteIds = [
          $user->fk_building_site
        ];

        $specialityIds = [
          $user->fk_speciality
        ];

        $specialityRoleIds = [
          $user->fk_speciality_role
        ];

        $user->extra = [
          'signon_type' => 'worker',
          'building_site_ids' => $buildingSiteIds,
          'speciality_ids' => $specialityIds,
          'speciality_role_ids' => $specialityRoleIds,
          'message' => 'Trabajador autenticado correctamente'
        ];

      }

      $response = $this->full_building_site_list(
        $buildingSiteIds
      );

      $response = (object) [
        'status' => $response->status,
        'message' => $user->extra['message'],
        'data' => $response->data,
        'user' => $user
      ];

      echo json_encode($response);
    } catch (Exception $e) {
      $this->output->set_status_header(400);
      echo json_encode(
        [
          'status' => 'error',
          'message' => $e->getMessage()
        ]
      );
    }
  }

  public function new_app_sync($signinType = 'overseer')
  {
    header('Content-Type: application/json');

    $response = [];
    try {

      //method must be POST

      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
      }

      //recieve payload as JSON

      $payload = json_decode(file_get_contents('php://input'), true);

      //$user = (object) $payload['user'];

      if ($signinType == 'overseer') {

        $date = new DateTime('NOW', new DateTimeZone('America/Santiago'));

        log_message('error', 
          json_encode(
            [
              'date' => $date->format('Y-m-d H:i:s'),
              'payload' => $payload
            ]
          )
        );

        $registryList = json_decode($payload['payload']);

        foreach($registryList as $registry){
          $hh = $registry->hh;
          $activityDate = $registry->activity_date; //comes in format Y-m-d
          $comment = $registry->comment;
          $machinery = $registry->machinery;
          $imageBase64 = $registry->image;
          $avance = $registry->avance;
          $activityId = $registry->fk_activity;
          $buildingSiteId = $registry->fk_building_site;
          //workerActivities TODO

          $activity = $this->db->select('activity_code, qty, fk_speciality, fk_speciality_role')->from('activity')->where('id', $activityId)->get()->row();

          $this->db->set('activity_code', $activity->activity_code);
          $this->db->set('activity_date', $activityDate);
          $this->db->set('activity_date_f', strtotime($activityDate) / 86400);
          $this->db->set('hh', $hh);
          $this->db->set('comment', $comment);
          $this->db->set('machinery', $machinery);
          $this->db->set('avance', $avance);
          $this->db->set('p_avance', round($avance / $activity->qty * 100, 2));
          $this->db->set('fk_speciality', $activity->fk_speciality);
          $this->db->set('fk_speciality_role', $activity->fk_speciality_role);
          $this->db->set('fk_image', 0);
          $this->db->set('workers', 0);
          $this->db->set('fk_activity', $activityId);
          $this->db->set('fk_building_site', $buildingSiteId);
          $this->db->insert('activity_registry');
        }

        echo json_encode(
          [
            'status' => 'success',
            'message' => 'Sincronización exitosa',
          ]
        );

      } elseif ($signinType == 'worker') {

        $this->db->set('fk_building_site', $payload['fk_building_site']);
        $this->db->set('fk_worker', $payload['fk_worker']);
        $this->db->set('code', $payload['code']);
        $this->db->set('hh', $payload['hh']);
        $this->db->set('date', $payload['date']);
        $this->db->insert('worker_activity');

        echo json_encode(
          [
            'data' => $payload,
            'status' => 'success',
            'message' => 'Sincronización exitosa',
          ]
        );

      }

    } catch (Exception $e) {
      $this->output->set_status_header(400);
      echo json_encode(
        [
          'status' => 'error',
          'message' => $e->getMessage()
        ]
      );
    }

  }

}