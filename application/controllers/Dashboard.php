<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'third_party' . "/phpqrcode/qrlib.php";

class Dashboard extends CI_Controller
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

	function __construct()
	{
		parent::__construct();
		$this->load->model('web');
		$this->load->library('session');
	}

	public function index()
	{

		$logged_in = $this->session->userdata('logged_in');

		if ($logged_in == FALSE) {
			redirect('login');
		}

		$this->load->model('user');
		$this->load->model('supervisor');
		//$this->load->model('activity');
		$this->load->model('activity_data');

		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
				)
			)
		);

		if ($user[0]->role->value_p == "Supervisores") {

			$s = $this->supervisor->obtener(
				array(
					array(
						'fk_user'	=>	$user[0]->id
					)
				)
			);

			$user[0]->supervisor = $s;

			redirect('dashboard/search_speciality');
		} else {

			$user[0]->supervisor = null;
		}

		$add_lib = array(
			'js_lib'	=>  array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib'	=>	array(
				//asset_css( '...'),
			)
		);

		$this->load->view(CPATH . 'head', $this->web->get_header('Dashboard', $add_lib));
		$this->load->view(SPATH . 'dashboard_structure', array('user' => $user[0]));
		$this->load->view(CPATH . 'foot');
	}

	public function search_speciality()
	{

		$logged_in = $this->session->userdata('logged_in');

		if ($logged_in == FALSE) {
			redirect('login');
		}

		$this->load->model('user');
		$this->load->model('supervisor');
		//$this->load->model('activity');
		$this->load->model('activity_data');

		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
				)
			)
		);

		$add_lib = array(
			'js_lib'	=>  array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib'	=>	array(
				//asset_css( '...'),
			)
		);

		if ($user[0]->role->value_p == "Supervisores") {

			$s = $this->supervisor->obtener(
				array(
					array(
						'fk_user'	=>	$user[0]->id
					)
				)
			);

			$user[0]->supervisor = $s;
			$user[0]->extra['action'] = 'specialities';

			$this->load->view(CPATH . 'head', $this->web->get_header('Lekapp', $add_lib));
			$this->load->view(SPATH . 'app_structure', array('user' => $user[0]));
			$this->load->view(CPATH . 'foot');
		} else {
			redirect('login');
		}
	}

	public function search_activities($speciality_id)
	{

		$logged_in = $this->session->userdata('logged_in');

		if ($logged_in == FALSE) {
			redirect('login');
		}

		$this->load->model('user');
		$this->load->model('supervisor');
		$this->load->model('activity');
		$this->load->model('activity_data');

		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
				)
			)
		);

		$add_lib = array(
			'js_lib'	=>  array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib'	=>	array(
				//asset_css( '...'),
			)
		);

		$activities = $this->activity->obtener([['fk_speciality' => $speciality_id]]);

		$act_arr = [];

		foreach ($activities as $v) {
			$act_arr[$v->activity_code] = new stdClass;
			$act_arr[$v->activity_code]->code = $v->activity_code;
			$act_arr[$v->activity_code]->name = $v->name;
			$act_arr[$v->activity_code]->zone = $v->zone->name;
			$act_arr[$v->activity_code]->speciality_role = $v->speciality_role;
			$user[0]->speciality = $v->speciality->name;
		}

		sort($act_arr);

		$user[0]->data = $act_arr;

		$user[0]->extra['action'] = 'activities';

		$this->load->view(CPATH . 'head', $this->web->get_header('Lekapp', $add_lib));
		$this->load->view(SPATH . 'app_structure', array('user' => $user[0]));
		$this->load->view(CPATH . 'foot');
	}

	public function set($activity_encoded_code)
	{

		$raw_code = $activity_encoded_code;
		$s_code = base64_decode($activity_encoded_code);
		$s_code = explode("-", $s_code);
		$code = $s_code[1];
		$logged_in = $this->session->userdata('logged_in');

				
		if ($logged_in == FALSE) {
			redirect('login');
		}

		$this->load->model('user');
		$this->load->model('supervisor');
		$this->load->model('activity');
		$this->load->model('activity_registry');
		$this->load->model('speciality');

		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
				)
			)
		);

		$activities = $this->activity->obtener(
			[[
				'activity_code' => $code,
				'fk_building_site' => $s_code[0]
			]]
		);

		$zones = [];
		$specialities = [];
		$speciality_roles = [];
		$zid = 0;

		foreach ($activities as $v) {
			$zones[$v->zone->id] = $v->zone->name;
			$zid = $v->zone->id;
			$specialities[$v->speciality->id] = $v->speciality->name;
			$speciality_roles[$v->speciality_role->id] = $v->speciality_role->name;
		}

		$add_lib = array(
			'js_lib'	=>  array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib'	=>	array(
				//asset_css( '...'),
			)
		);

		$t = $this->activity_registry->obtener([[
			'activity_code'	=>	$code,
			'fk_building_site'	=> $s_code[0]
		]]);

		$codesDir = APPPATH . '/../assets/cache/qr/';
		$dtx = new DateTime('NOW', new DateTimeZone('America/Santiago'));
		$qr_code = base64_encode($dtx->format('Y-m-d') . '|' . $s_code[0] . '|' . $code . '|' . $zid);
		$codeFile = date('Ymd') . $qr_code . '.png';

		QRcode::png($qr_code, $codesDir . $codeFile, 'M', 10);

		$user[0]->file = $codeFile;
		$user[0]->code = $code;
		$user[0]->s_code = $s_code;
		$user[0]->activity_name = $activities[0]->name;
		$user[0]->zones = $zones;
		$user[0]->specialities = $specialities;
		$user[0]->speciality_roles = $speciality_roles;
		$user[0]->data = $t;
		$user[0]->extra['action'] = 'set';
		$user[0]->extra['code'] = $raw_code;

		$dt = new DateTime('NOW', new DateTimeZone('America/Santiago'));
		$sr = array_keys($user[0]->speciality_roles)[0];
		$z = array_keys($user[0]->zones)[0];
		$activity_exists = $this->activity_registry->obtener(
			[[
				'fk_building_site'		=>	$s_code[0],
				'activity_code'			=>	$code,
				'fk_speciality_role'	=>	$sr,
				'activity_date'			=>	$dt->format('Y-m-d')
			]]
		);

		$this->load->helper('form');

		if ($this->input->post('add')) {

			$this->load->library('form_validation');

			$this->form_validation->set_rules('avance', 'Avance', 'trim|required');
			$this->form_validation->set_rules('comment', 'Notas', 'trim');
			$this->form_validation->set_rules('machinery', 'HH', 'trim');

			if ($this->form_validation->run() == FALSE) {

				$this->load->view(CPATH . 'head', $this->web->get_header('Lekapp', $add_lib));
				$this->load->view(SPATH . 'app_structure', array('user' => $user[0]));
				$this->load->view(CPATH . 'foot');

			} else {

				$activity_exists = $this->activity_registry->obtener(
					[[
						'fk_building_site'		=>	$s_code[0],
						'activity_code'			=>	$code,
						'fk_speciality_role'	=>	$this->input->post('fk_speciality_role'),
						'activity_date'			=>	$dt->format('Y-m-d')
					]]
				);

				if (sizeof($activity_exists) > 0) {
					$new = $activity_exists[0]->id;
					$this->activity_registry->actualizar($new);
				} else {
					$activity = $this->activity->obtener(
						[[
							'fk_building_site'		=>	$s_code[0],
							'activity_code'			=>	$code,
							'fk_speciality_role'	=>	$this->input->post('fk_speciality_role'),
							'fk_zone'				=>	$this->input->post('fk_zone')
						]]
					);
					if (sizeof($activity) > 0) {
						$new = $this->activity_registry->insertar($activity[0], $dt->format('Y-m-d'));
					}
				}

				$it = $this->image_type->obtener(
					array(
						array(
							'code_name'	=>	'activity_report'
						)
					)
				);

				$i = $this->image->insertar('', '', $it[0]->id);

				$config = $this->web->get_upload_config('activity_report');
				$config['upload_path'] = $config['upload_path'] . $i;

				if (!is_dir($config['upload_path'])) {
					mkdir($config['upload_path'], 0777, true);
				}

				$this->load->library('upload', $config);

				$this->upload->do_upload('userfile');

				$config_img['image_library'] = 'gd2';
				$config_img['source_image'] = $this->upload->data('full_path');
				$config_img['new_image'] = $this->upload->data('full_path');
				$config_img['maintain_ratio'] = TRUE;
				$config_img['width']     = 1280;
				$config_img['height']   = 720;
				$this->load->library('image_lib', $config_img);
				$this->image_lib->resize();

				$name = "";

				if (is_string($this->upload->data('raw_name')) && $this->upload->data('raw_name') != "") {
					$name = utf8_encode($this->upload->data('raw_name'));
				}

				$this->image->actualizar($i, $name, $this->upload->data('file_ext'));
				$this->activity_registry->actualizar_imagen($new, $i);
				//$new = $this->speciality->insertar();

				redirect('dashboard/set/' . $activity_encoded_code);
			}
		} elseif ($this->input->post('start')) {
			
			$activity = $this->activity->obtener(
				[[
					'fk_building_site'		=>	$s_code[0],
					'activity_code'			=>	$code,
					'fk_speciality_role'	=>	$sr,
					'fk_zone'				=>	$z
				]]
			);

			if (sizeof($activity) > 0) {
				$new = $this->activity_registry->start($activity[0], $dt->format('Y-m-d'));
			}

			redirect('dashboard/set/' . $activity_encoded_code);

		} else {
			if (sizeof($activity_exists) > 0) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Lekapp', $add_lib));
				$this->load->view(SPATH . 'app_structure', array('user' => $user[0], 'exists' => true));
				$this->load->view(CPATH . 'foot');
			} else {

				$activity = $this->activity->obtener(
					[[
						'fk_building_site'		=>	$s_code[0],
						'activity_code'			=>	$code,
						'fk_speciality_role'	=>	$sr,
						'fk_zone'				=>	$z
					]]
				);

				

				if (sizeof($activity) > 0) {

					$this->load->view(CPATH . 'head', $this->web->get_header('Lekapp', $add_lib));
					$this->load->view(SPATH . 'app_structure', array('user' => $user[0], 'exists' => false, 'valid' => true));
					$this->load->view(CPATH . 'foot');

				} else {

					$this->load->view(CPATH . 'head', $this->web->get_header('Lekapp', $add_lib));
					$this->load->view(SPATH . 'app_structure', array('user' => $user[0], 'exists' => false, 'valid' => false, 'speciality_id' => $activity[0]->speciality->id));
					$this->load->view(CPATH . 'foot');

				}

			}
		}
		
	}

}
