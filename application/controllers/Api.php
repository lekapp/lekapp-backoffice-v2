<?php
defined('BASEPATH') or exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

class Api extends CI_Controller
{
    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('web');
        $this->load->library('session');
        $this->load->model('user');
    }

    //worker

    public function login()
    {

        $body = $this->security->xss_clean($this->input->raw_input_stream);
        $input = json_decode($body);

        if ($input != null) {

            $this->load->model('worker');
            $data = new stdClass;

            if (!isset($input->email)) {
                header('Content-Type: application/json');
                header('Status: 400');
                echo json_encode(
                    [
                        'error' => true,
                        'message' => "email no ingresado"
                    ]
                );
                exit;
            }

            if (!isset($input->password)) {
                header('Content-Type: application/json');
                header('Status: 400');
                echo json_encode(
                    [
                        'error' => true,
                        'message' => "password no ingresado"
                    ]
                );
                exit;
            }

            $worker = $this->worker->obtener(
                [
                    [
                        'email' => $input->email,
                        'password' => pack('H*', hash('sha512', $input->password))
                    ]
                ]
            );

            if (sizeof($worker) == 0) {
                header('Content-Type: application/json');
                header('Status: 400');
                echo json_encode(
                    [
                        'error' => true,
                        'message' => "Nombre de usuario o contraseña incorrecta"
                    ]
                );
                exit;
            }

            $data->name = $worker[0]->name;
            $data->email = $worker[0]->email;
            $data->fk_building_site = $worker[0]->fk_building_site;
            $data->fk_speciality = $worker[0]->fk_speciality;
            $data->fk_speciality_role = $worker[0]->fk_speciality_role;
            $data->building_site = $worker[0]->building_site->name;

            $data->speciality = $this->db->select('*')->from('speciality')->limit(1)->where('id', $worker[0]->fk_speciality)->get()->row()->name;
            $data->speciality_role = $this->db->select('*')->from('speciality_role')->limit(1)->where('id', $worker[0]->fk_speciality_role)->get()->row()->name;

            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            header('Status: 400');
            echo json_encode(
                [
                    'error' => true,
                    'message' => "No hay información de entrada"
                ]
            );
        }
    }

    public function uploadData()
    {

        $body = $this->security->xss_clean($this->input->raw_input_stream);
        $input = json_decode($body);

        if ($input != null) {

            if (!isset($input->stampI)) {
                header('Content-Type: application/json');
                header('Status: 400');
                echo json_encode(
                    [
                        'error' => true,
                        'message' => "stampI no ingresado"
                    ]
                );
                exit;
            }

            if (!isset($input->stampO)) {
                header('Content-Type: application/json');
                header('Status: 400');
                echo json_encode(
                    [
                        'error' => true,
                        'message' => "stampO no ingresado"
                    ]
                );
                exit;
            }

            if (!isset($input->SR_ID)) {
                header('Content-Type: application/json');
                header('Status: 400');
                echo json_encode(
                    [
                        'error' => true,
                        'message' => "SR_ID no ingresado"
                    ]
                );
                exit;
            }

            if (!isset($input->BS_ID)) {
                header('Content-Type: application/json');
                header('Status: 400');
                echo json_encode(
                    [
                        'error' => true,
                        'message' => "BS_ID no ingresado"
                    ]
                );
                exit;
            }

            if (!isset($input->code)) {
                header('Content-Type: application/json');
                header('Status: 400');
                echo json_encode(
                    [
                        'error' => true,
                        'message' => "code no ingresado"
                    ]
                );
                exit;
            }

            $this->load->model('activity_registry');
            $this->load->model('worker');
            $this->load->model('worker_activity');

            /*
            $t = $this->activity_registry->obtener([[
                'activity_code'	=>	$input->code,
                'fk_building_site'	=> $input->BS_ID
            ]]);
            */

            //$data->timeLeap = $worker[0]->name;

            $stampI = new DateTime($input->stampI);
            $stampO = new DateTime($input->stampO);

            $data = new stdClass;

            $seconds = $stampO->getTimeStamp() - $stampI->getTimeStamp();
            $hours = $seconds / 3600;

            $t = $this->activity_registry->obtener([
                [
                    'activity_code' => $input->code,
                    'fk_building_site' => $input->BS_ID,
                    'fk_speciality_role' => $input->SR_ID,
                    'activity_date' => $stampO->format('Y-m-d')
                ]
            ]);

            log_message('error', json_encode($input));
            log_message('error', json_encode($t));

            if (sizeof($t) > 0) {

                $w = $this->worker->obtener(
                    [
                        [
                            'fk_building_site' => $input->BS_ID,
                            'email' => $input->email
                        ]
                    ]
                );

                if (sizeof($w) > 0) {
                    $wa = $this->worker_activity->obtener([
                        [
                            'fk_building_site' => $input->BS_ID,
                            'fk_worker' => $w[0]->id,
                            'date' => $t[0]->activity_date,
                            'code' => $input->code
                        ]
                    ]);
                    if (sizeof($wa) > 0) {
                        $this->worker_activity->actualizar($wa[0]->id, $input->BS_ID, $w[0]->id, floatval($wa[0]->hh) + number_format($hours, 2), $t[0]->activity_date, $input->code);
                        $this->activity_registry->actualizar_hh($t[0]->id, $t[0]->hh + number_format($hours, 2), $t[0]->workers);
                    } else {
                        $this->worker_activity->generar($input->BS_ID, $w[0]->id, number_format($hours, 2), $t[0]->activity_date, $input->code);
                        $this->activity_registry->actualizar_hh($t[0]->id, $t[0]->hh + number_format($hours, 2), $t[0]->workers + 1);
                    }
                }

                log_message('error', json_encode($t[0]->id) . ' ' . $t[0]->hh . ' ' . $t[0]->workers . ' ' . number_format($hours, 2) . ' ' . ($t[0]->hh + number_format($hours, 2)) . ' ' . ($t[0]->workers + 1) . ' ' . $t[0]->activity_date);

                //$this->activity_registry->actualizar_hh($t[0]->id, $t[0]->hh + number_format($hours, 2), $t[0]->workers + 1);
                $data->old_hh = $t[0]->hh;
                $data->hh = number_format($hours, 2);
                $data->date = $t[0]->activity_date;
                header('Content-Type: application/json');
                echo json_encode($data);
            } else {
                header('Status: 400');
                log_message('error', 'No existe registro de la actividad actual');
                echo json_encode(
                    [
                        'error' => true,
                        'message' => "No existe registro de la actividad actual"
                    ]
                );
            }
        } else {
            header('Status: 400');
            log_message('error', 'No hay información de entrada');
            echo json_encode(
                [
                    'error' => true,
                    'message' => "No hay información de entrada"
                ]
            );
        }
    }

    //overseer

    public function login_supervisor2()
    {

        $body = json_encode($_POST);
        $input = json_decode($body);

        if ($input != null) {

            $data = new stdClass;

            if (!isset($input->email)) {
                header('Content-Type: application/json');
                header('Status: 400');
                echo json_encode(
                    [
                        'error' => true,
                        'message' => "email no ingresado"
                    ]
                );
                exit;
            }

            if (!isset($input->password)) {
                header('Content-Type: application/json');
                header('Status: 400');
                echo json_encode(
                    [
                        'error' => true,
                        'message' => "password no ingresado"
                    ]
                );
                exit;
            }

            $this->load->model('supervisor');
            $this->load->model('user');
            $this->load->model('role');
            $this->load->model('activity');
            $this->load->model('speciality_role');
            $this->load->model('zone');
            $this->load->model('area');

            $role_overseer = $this->role->obtener(
                [
                    [
                        'value_p' => 'Supervisores'
                    ]
                ]
            );

            $overseer = $this->user->obtener(
                [
                    [
                        'email' => $input->email,
                        'password' => pack('H*', hash('sha512', $input->password)),
                        'fk_role' => $role_overseer[0]->id
                    ]
                ]
            );

            if (sizeof($overseer) == 0) {
                header('Content-Type: application/json');
                header('Status: 400');
                echo json_encode(
                    [
                        'error' => true,
                        'message' => "Nombre de usuario o contraseña incorrecta"
                    ]
                );
                exit;
            }

            $data->name = $overseer[0]->first_name . ' ' . $overseer[0]->last_name;
            $data->email = $overseer[0]->email;

            $s = $this->supervisor->obtener(
                [
                    [
                        'fk_user' => $overseer[0]->id
                    ]
                ]
            );

            $data->specialities = [];
            $data->building_sites = [];
            $data->activities = [];
            $data->areas = [];
            $data->zones = [];
            $data->speciality_roles = [];

            foreach ($s as $lekapp) {

                $speciality = new stdClass;
                $building_site = new stdClass;

                $speciality->id = $lekapp->speciality->id;
                $speciality->name = $lekapp->speciality->name;
                $speciality->fk_building_site = $lekapp->speciality->building_site->id;

                $building_site->id = $lekapp->speciality->building_site->id;
                $building_site->name = $lekapp->speciality->building_site->name;

                $data->specialities[$speciality->id] = $speciality;
                $data->building_sites[$building_site->id] = $building_site;

                $activities = $this->activity->obtener([['fk_speciality' => $speciality->id]]);

                $zone_array = [];
                $area_array = [];

                $activity_codes = [];
                foreach ($activities as $activity_data) {

                    if (array_search($activity_data->activity_code, $activity_codes) !== false) {
                        continue;
                    }

                    $activity_codes[] = $activity_data->activity_code;

                    $activity = new stdClass;

                    $activity->id = $activity_data->id;
                    $activity->name = $activity_data->name;
                    $activity->f_data = $activity_data->f_data;
                    $activity->unt = $activity_data->unt;
                    $activity->qty = $activity_data->qty;
                    $activity->eff = $activity_data->eff;
                    $activity->activity_code = $activity_data->activity_code;

                    $activity->fk_speciality = $activity_data->fk_speciality;
                    $activity->fk_building_site = $activity_data->fk_building_site;
                    $activity->fk_speciality_role = $activity_data->fk_speciality_role;
                    $activity->fk_zone = $activity_data->fk_zone;

                    if (array_search($activity->fk_zone, $zone_array) === false) {
                        $zone_array[] = $activity->fk_zone;
                    }

                    $zone = $this->zone->obtener(
                        [
                            [
                                'id' => $activity_data->fk_zone
                            ]
                        ]
                    );

                    $area = $this->area->obtener(
                        [
                            [
                                'id' => $zone[0]->fk_area
                            ]
                        ]
                    );

                    if (array_search($area[0]->id, $area_array) === false) {
                        $area_array[] = $area[0]->id;
                    }

                    $data->activities[$activity->id] = $activity;
                }

                foreach ($zone_array as $zone_id) {
                    $current_zone = $this->zone->obtener(
                        [
                            [
                                'id' => $zone_id
                            ]
                        ]
                    );
                    if (sizeof($current_zone) > 0) {
                        $zone = new stdClass;
                        $zone->id = $current_zone[0]->id;
                        $zone->fk_building_site = $current_zone[0]->fk_building_site;
                        $zone->fk_area = $current_zone[0]->fk_area;
                        $zone->name = $current_zone[0]->name;
                    }
                    $data->zones[$zone->id] = $zone;
                }

                foreach ($area_array as $area_id) {
                    $current_area = $this->area->obtener(
                        [
                            [
                                'id' => $area_id
                            ]
                        ]
                    );
                    if (sizeof($current_area) > 0) {
                        $area = new stdClass;
                        $area->id = $current_area[0]->id;
                        $area->fk_building_site = $current_area[0]->fk_building_site;
                        $area->name = $current_area[0]->name;
                    }
                    $data->areas[$area->id] = $area;
                }

                $speciality_roles = $this->speciality_role->obtener([['fk_speciality' => $speciality->id]]);

                foreach ($speciality_roles as $speciality_role_data) {
                    $speciality_role = new stdClass;

                    $speciality_role->id = $speciality_role_data->id;
                    $speciality_role->name = $speciality_role_data->name;
                    $speciality_role->hh = $speciality_role_data->hh;
                    $speciality_role->fk_building_site = $speciality_role_data->fk_building_site;
                    $speciality_role->fk_speciality = $speciality_role_data->fk_speciality;

                    $data->speciality_roles[$speciality_role->id] = $speciality_role;
                }
            }

            $data->specialities = array_values($data->specialities);
            $data->building_sites = array_values($data->building_sites);
            $data->activities = array_values($data->activities);
            $data->zones = array_values($data->zones);
            $data->areas = array_values($data->areas);
            $data->speciality_roles = array_values($data->speciality_roles);

            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            header('Status: 400');
            echo json_encode(
                [
                    'error' => true,
                    'message' => "No hay información de entrada"
                ]
            );
        }
    }

    public function login_supervisor()
    {

        $body = $this->security->xss_clean($this->input->raw_input_stream);
        $input = json_decode($body);

        if ($input != null) {

            $data = new stdClass;

            if (!isset($input->email)) {
                header('Content-Type: application/json');
                header('Status: 400');
                echo json_encode(
                    [
                        'error' => true,
                        'message' => "email no ingresado"
                    ]
                );
                exit;
            }

            if (!isset($input->password)) {
                header('Content-Type: application/json');
                header('Status: 400');
                echo json_encode(
                    [
                        'error' => true,
                        'message' => "password no ingresado"
                    ]
                );
                exit;
            }

            $this->load->model('supervisor');
            $this->load->model('user');
            $this->load->model('role');
            $this->load->model('activity');
            $this->load->model('speciality_role');
            $this->load->model('zone');
            $this->load->model('area');

            $role_overseer = $this->role->obtener(
                [
                    [
                        'value_p' => 'Supervisores'
                    ]
                ]
            );

            $overseer = $this->user->obtener(
                [
                    [
                        'email' => $input->email,
                        'password' => pack('H*', hash('sha512', $input->password)),
                        'fk_role' => $role_overseer[0]->id
                    ]
                ]
            );

            if (sizeof($overseer) == 0) {
                header('Content-Type: application/json');
                header('Status: 400');
                echo json_encode(
                    [
                        'error' => true,
                        'message' => "Nombre de usuario o contraseña incorrecta"
                    ]
                );
                exit;
            }

            $data->name = $overseer[0]->first_name . ' ' . $overseer[0]->last_name;
            $data->email = $overseer[0]->email;

            $s = $this->supervisor->obtener(
                [
                    [
                        'fk_user' => $overseer[0]->id
                    ]
                ]
            );

            $data->specialities = [];
            $data->building_sites = [];
            $data->activities = [];
            $data->areas = [];
            $data->zones = [];
            $data->speciality_roles = [];

            foreach ($s as $lekapp) {

                $speciality = new stdClass;
                $building_site = new stdClass;

                $speciality->id = $lekapp->speciality->id;
                $speciality->name = $lekapp->speciality->name;
                $speciality->fk_building_site = $lekapp->speciality->building_site->id;

                $building_site->id = $lekapp->speciality->building_site->id;
                $building_site->name = $lekapp->speciality->building_site->name;

                $data->specialities[$speciality->id] = $speciality;
                $data->building_sites[$building_site->id] = $building_site;

                $activities = $this->activity->obtener([['fk_speciality' => $speciality->id]]);

                $zone_array = [];
                $area_array = [];

                $activity_codes = [];
                foreach ($activities as $activity_data) {

                    if (array_search($activity_data->activity_code, $activity_codes) !== false) {
                        continue;
                    }

                    $activity_codes[] = $activity_data->activity_code;

                    $activity = new stdClass;

                    $activity->id = $activity_data->id;
                    $activity->name = $activity_data->name;
                    $activity->f_data = $activity_data->f_data;
                    $activity->unt = $activity_data->unt;
                    $activity->qty = $activity_data->qty;
                    $activity->eff = $activity_data->eff;
                    $activity->activity_code = $activity_data->activity_code;

                    $activity->fk_speciality = $activity_data->fk_speciality;
                    $activity->fk_building_site = $activity_data->fk_building_site;
                    $activity->fk_speciality_role = $activity_data->fk_speciality_role;
                    $activity->fk_zone = $activity_data->fk_zone;

                    if (array_search($activity->fk_zone, $zone_array) === false) {
                        $zone_array[] = $activity->fk_zone;
                    }

                    $zone = $this->zone->obtener(
                        [
                            [
                                'id' => $activity_data->fk_zone
                            ]
                        ]
                    );

                    $area = $this->area->obtener(
                        [
                            [
                                'id' => $zone[0]->fk_area
                            ]
                        ]
                    );

                    if (array_search($area[0]->id, $area_array) === false) {
                        $area_array[] = $area[0]->id;
                    }

                    $data->activities[$activity->id] = $activity;
                }

                foreach ($zone_array as $zone_id) {
                    $current_zone = $this->zone->obtener(
                        [
                            [
                                'id' => $zone_id
                            ]
                        ]
                    );
                    if (sizeof($current_zone) > 0) {
                        $zone = new stdClass;
                        $zone->id = $current_zone[0]->id;
                        $zone->fk_building_site = $current_zone[0]->fk_building_site;
                        $zone->fk_area = $current_zone[0]->fk_area;
                        $zone->name = $current_zone[0]->name;
                    }
                    $data->zones[$zone->id] = $zone;
                }

                foreach ($area_array as $area_id) {
                    $current_area = $this->area->obtener(
                        [
                            [
                                'id' => $area_id
                            ]
                        ]
                    );
                    if (sizeof($current_area) > 0) {
                        $area = new stdClass;
                        $area->id = $current_area[0]->id;
                        $area->fk_building_site = $current_area[0]->fk_building_site;
                        $area->name = $current_area[0]->name;
                    }
                    $data->areas[$area->id] = $area;
                }

                $speciality_roles = $this->speciality_role->obtener([['fk_speciality' => $speciality->id]]);

                foreach ($speciality_roles as $speciality_role_data) {
                    $speciality_role = new stdClass;

                    $speciality_role->id = $speciality_role_data->id;
                    $speciality_role->name = $speciality_role_data->name;
                    $speciality_role->hh = $speciality_role_data->hh;
                    $speciality_role->fk_building_site = $speciality_role_data->fk_building_site;
                    $speciality_role->fk_speciality = $speciality_role_data->fk_speciality;

                    $data->speciality_roles[$speciality_role->id] = $speciality_role;
                }
            }

            $data->specialities = array_values($data->specialities);
            $data->building_sites = array_values($data->building_sites);
            $data->activities = array_values($data->activities);
            $data->zones = array_values($data->zones);
            $data->areas = array_values($data->areas);
            $data->speciality_roles = array_values($data->speciality_roles);

            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            header('Status: 400');
            echo json_encode(
                [
                    'error' => true,
                    'message' => "No hay información de entrada"
                ]
            );
        }
    }

    public function sync_supervisor()
    {

        $body = $this->security->xss_clean($this->input->raw_input_stream);
        $input = json_decode($body);

        try {
            if ($input != null) {

                $this->load->model('supervisor');
                $this->load->model('user');
                $this->load->model('role');
                $this->load->model('activity');
                $this->load->model('speciality_role');
                $this->load->model('zone');
                $this->load->model('activity_registry');
                $this->load->model('image_type');
                $this->load->model('image');

                $data = new stdClass;

                if (!isset($input->syncData)) {
                    header('Content-Type: application/json');
                    header('Status: 400');
                    echo json_encode(
                        [
                            'error' => true,
                            'message' => "email no ingresado"
                        ]
                    );
                    exit;
                }

                $nData = json_decode($input->syncData);
                $supervisores = (array) $nData;

                $data->message = "";

                $it = $this->image_type->obtener(
                    array(
                        array(
                            'code_name' => 'activity_report'
                        )
                    )
                );

                $dt = new DateTime('NOW', new DateTimeZone('America/Santiago'));

                foreach ($supervisores as $k => $entries) {
                    foreach ($entries as $entry) {
                        $activity = $this->activity->obtener([['id' => $entry->fk_activity]]);
                        $ar = $this->activity_registry->start($activity[0], $entry->activity_date);
                        $data->message .= $activity[0]->fk_building_site . '<br>';

                        //if p_avance has comma, replace by dot and round to 4 decimals

                        $entry->p_avance = str_replace(',', '.', $entry->p_avance);
                        $entry->p_avance = round($entry->p_avance, 4);

                        $this->activity_registry->actualizar_por_app($ar, $entry->comment, $entry->machinery, $entry->p_avance);
                        if (trim($entry->base64_image) != "") {
                            $it = $this->image_type->obtener(
                                [
                                    [
                                        'code_name' => 'activity_report'
                                    ]
                                ]
                            );

                            $i = $this->image->insertar('', '', $it[0]->id);
                            $config = $this->web->get_upload_config('activity_report');
                            $config['upload_path'] = $config['upload_path'] . $i . '/';

                            if (!is_dir($config['upload_path'])) {
                                mkdir($config['upload_path'], 0777, true);
                            }

                            $imageFile = base64_decode($entry->base64_image);

                            //crop image to a square of max 1024x1024

                            $image = imagecreatefromstring($imageFile);
                            $width = imagesx($image);
                            $height = imagesy($image);

                            $new_width = 1024;
                            $new_height = 1024;

                            $crop_width = $width;
                            $crop_height = $height;

                            if ($width > $height) {
                                $crop_width = $height;
                                $crop_height = $height;
                            } else {
                                $crop_width = $width;
                                $crop_height = $width;
                            }

                            $new_image = imagecreatetruecolor($new_width, $new_height);

                            imagecopyresampled($new_image, $image, 0, 0, ($width - $crop_width) / 2, ($height - $crop_height) / 2, $new_width, $new_height, $crop_width, $crop_height);

                            ob_start();

                            imagepng($new_image);

                            $imageFile = ob_get_contents();

                            ob_end_clean();

                            $name = md5($dt->format('dmYHis') . $i);
                            $ext = ".png";

                            $r = file_put_contents(FCPATH . $config['upload_path'] . $name . $ext, $imageFile);

                            $this->image->actualizar($i, $name, $ext);
                            $this->activity_registry->actualizar_imagen($ar, $i);
                        }
                    }
                }
                $data->error = null;
                $data->message = "Información sincronizada correctamente";
                header('Content-Type: application/json');
                echo json_encode($data);
            } else {
                header('Status: 400');
                throw new Exception("No hay información de entrada");
            }
        } catch (Exception $e) {
            echo json_encode(
                [
                    'error' => true,
                    'message' => $e->getMessage()
                ]
            );
        }
    }
}