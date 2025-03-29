<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Users extends CI_Controller
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
		$this->load->model('user');
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
				)
			)
		);
		if ($user[0]->fk_role == 99) {
			redirect('login');
		}
	}

	public function index()
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
				)
			)
		);
		$data = $this->user->obtener(
			array(
				array(
					'fk_role >'	=> $user[0]->fk_role
				)
			)
		);
		$add_lib = array(
			'js_lib'	=>  array(
				//asset_js( '../node_modules/datatables/datatables.min.js' ),
				asset_js('front.js'),
			),
			'css_lib'	=>	array(
				//asset_css( '../node_modules/datatables/datatables.min.css' ),
			)
		);
		$this->load->view(CPATH . 'head', $this->web->get_header('Usuarios', $add_lib));
		$this->load->view(SPATH . 'user_list_structure', array('user' => $user[0], 'data' => $data));
		$this->load->view(CPATH . 'foot');
	}

	public function view($user_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $user_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
				)
			)
		);
		$data = $this->user->obtener(
			array(
				array(
					'id'	=>	$user_id
				)
			)
		);
		if ($user[0]->fk_role > $data[0]->fk_role) {
			redirect('login');
		}
		$add_lib = array(
			'js_lib'	=>  array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib'	=>	array(
				//asset_css( '...')
			)
		);
		$this->load->view(CPATH . 'head', $this->web->get_header('Ver usuario', $add_lib));
		$this->load->view(SPATH . 'user_view_structure', array('user' => $user[0], 'data' => $data[0]));
		$this->load->view(CPATH . 'foot');
	}

	public function add()
	{
		$logged_in = $this->session->userdata('logged_in');
		//$user_id = intval( $this->uri->segment(3) );
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('role');
		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
				)
			)
		);
		$error = array(
			'code' =>	0,
			'message'	=>	""
		);
		if ($user[0]->fk_role > 3) {
			redirect('login');
		}
		$data = new stdClass;
		$data->avatar_file = 'userdata';
		$roles = $this->role->obtener_ordenado(
			array(
				array(
					'id >' =>	$user[0]->fk_role
				),
				array(
					'id !=' =>	99
				)
			)
		);
		$roles_array = array();
		foreach ($roles as $k => $v) {
			$roles_array[$k] = $v->value_p;
		}
		$roles_array = array_reverse($roles_array, true);
		$add_lib = array(
			'js_lib'	=>  array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib'	=>	array(
				//asset_css( '...')
			)
		);
		$this->load->helper('form');
		if ($this->input->post('add')) {
			$this->load->library('form_validation');

			$this->form_validation->set_rules('first_name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('last_name', 'Apellido', 'trim|required');
			$this->form_validation->set_rules('email', 'Email', 'trim|required');
			$this->form_validation->set_rules('dni', 'RUT/DNI', 'trim|required');
			$this->form_validation->set_rules('address_1', 'Dirección 1', 'trim|required');
			$this->form_validation->set_rules('address_2', 'Dirección 2', 'trim');
			$this->form_validation->set_rules('phone_1', 'Fono 1', 'trim|required');
			$this->form_validation->set_rules('phone_2', 'Fono 2', 'trim');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Usuario', $add_lib));
				$this->load->view(SPATH . 'user_add_structure', array('user' => $user[0], 'data' => $data, 'roles' => $roles_array, 'error' => $error));
				$this->load->view(CPATH . 'foot');
			} else {
				$new = $this->user->insertar();
				$this->user->actualizar_rol($new);
				$data = $this->user->obtener(
					array(
						array(
							'id'	=>	$new
						)
					)
				);
				$data[0]->avatar_file = 'userdata';
				$it = $this->image_type->obtener(
					array(
						array(
							'code_name'	=>	'custom_avatar'
						)
					)
				);
				$config = $this->web->get_upload_config('custom_avatar');
				$config['upload_path'] = utf8_decode($config['upload_path'] . $data[0]->id);
				if (!is_dir($config['upload_path'])) {
					mkdir($config['upload_path'], 0777, true);
				}
				$this->load->library('upload', $config);
				$this->upload->do_upload($data[0]->avatar_file);
				$this->user->actualizar($data[0]->id);
				$name = "";
				if (is_string($this->upload->data('raw_name'))) {
					$name = utf8_encode($this->upload->data('raw_name'));
				}
				if ($this->upload->display_errors() == "<p>No seleccionaste un archivo para subir.</p>") {
					//Sin imagen
					redirect('users/edit/' . $new);
				} elseif ($this->upload->data('raw_name') == false || $this->upload->display_errors() != "") {
					//$this->user->actualizar_imagen( $data[0]->id, $name, 0, $this->upload->display_errors() );
					$error['code'] = 1;
					$error['message'] = str_replace('<p>', '', $this->upload->display_errors());
					$error['message'] = str_replace('</p>', '', $error['message']);
					$logged_in->error = 1;
					$logged_in->error_code = $error['code'];
					$logged_in->error_message = $error['message'];
					$this->session->set_userdata('logged_in', $logged_in);
					redirect('users/edit/' . $new);
				} else {
					$i = $this->image->insertar($name, $this->upload->data('file_ext'), $it[0]->id);
					$this->user->actualizar_imagen($data[0]->id, $name, $i, $this->upload->display_errors());
					redirect('users/edit/' . $new);
				}
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Usuario', $add_lib));
			$this->load->view(SPATH . 'user_add_structure', array('user' => $user[0], 'data' => $data, 'roles' => $roles_array, 'error' => $error));
			$this->load->view(CPATH . 'foot');
		}
	}

	public function edit($user_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		//$user_id = intval( $this->uri->segment(3) );
		if ($logged_in == FALSE || $user_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		if (isset($logged_in->error) && $logged_in->error == 1) {
			$error = array(
				'code' =>	$logged_in->error_code,
				'message'	=>	$logged_in->error_message
			);
			unset($logged_in->error);
			unset($logged_in->error_code);
			unset($logged_in->error_message);
			$this->session->set_userdata('logged_in', $logged_in);
		} else {
			$error = array(
				'code' =>	0,
				'message'	=>	""
			);
		}
		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
				)
			)
		);
		$data = $this->user->obtener(
			array(
				array(
					'id'	=>	$user_id
				)
			)
		);
		if ($user[0]->fk_role > $data[0]->fk_role) {
			redirect('login');
		}
		$data[0]->avatar_file = 'userdata';
		$roles = $this->role->obtener_ordenado();
		$roles_array = array();
		foreach ($roles as $k => $v) {
			$roles_array[$k] = $v->value_p;
		}
		$roles_array = array_reverse($roles_array, true);
		$add_lib = array(
			'js_lib'	=>  array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib'	=>	array(
				//asset_css( '...')
			)
		);
		$this->load->helper('form');
		if ($this->input->post('update_1')) {
			$this->load->library('form_validation');

			$this->form_validation->set_rules('first_name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('last_name', 'Apellido', 'trim|required');
			$this->form_validation->set_rules('email', 'Email', 'trim|required');
			$this->form_validation->set_rules('dni', 'RUT/DNI', 'trim|required');
			$this->form_validation->set_rules('address_1', 'Dirección 1', 'trim|required');
			$this->form_validation->set_rules('address_2', 'Dirección 2', 'trim');
			$this->form_validation->set_rules('phone_1', 'Fono 1', 'trim|required');
			$this->form_validation->set_rules('phone_2', 'Fono 2', 'trim');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Editar Usuario', $add_lib));
				$this->load->view(SPATH . 'user_edit_structure', array('user' => $user[0], 'data' => $data[0], 'roles' => $roles_array, 'error' => $error));
				$this->load->view(CPATH . 'foot');
			} else {
				$it = $this->image_type->obtener(
					array(
						array(
							'code_name'	=>	'custom_avatar'
						)
					)
				);
				$config = $this->web->get_upload_config('custom_avatar');
				$config['upload_path'] = utf8_decode($config['upload_path'] . $data[0]->id);
				if (!is_dir($config['upload_path'])) {
					mkdir($config['upload_path'], 0777, true);
				}
				$this->load->library('upload', $config);
				$this->upload->do_upload($data[0]->avatar_file);
				$this->user->actualizar($data[0]->id);
				$this->user->actualizar_rol($data[0]->id);
				$name = "";
				if (is_string($this->upload->data('raw_name'))) {
					$name = utf8_encode($this->upload->data('raw_name'));
				}
				if ($this->upload->display_errors() == "<p>No seleccionaste un archivo para subir.</p>") {
					//Sin cambios
					redirect('users/view/' . $user_id);
				} elseif ($this->upload->data('raw_name') == false || $this->upload->display_errors() != "") {
					//$this->user->actualizar_imagen( $data[0]->id, $name, 0, $this->upload->display_errors() );
					$error['code'] = 1;
					$error['message'] = str_replace('<p>', '', $this->upload->display_errors());
					$error['message'] = str_replace('</p>', '', $error['message']);
					$this->load->view(CPATH . 'head', $this->web->get_header('Editar Usuario', $add_lib));
					$this->load->view(SPATH . 'user_edit_structure', array('user' => $user[0], 'data' => $data[0], 'roles' => $roles_array, 'error' => $error));
					$this->load->view(CPATH . 'foot');
				} else {
					$i = $this->image->insertar($name, $this->upload->data('file_ext'), $it[0]->id);
					$this->user->actualizar_imagen($data[0]->id, $name, $i, $this->upload->display_errors());
					redirect('users/view/' . $user_id);
				}
			}
		} else if ($this->input->post('update_2')) {
			$this->load->library('form_validation');

			$this->form_validation->set_rules('password', 'Contraseña', 'trim|required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Editar Usuario', $add_lib));
				$this->load->view(SPATH . 'user_edit_structure', array('user' => $user[0], 'data' => $data[0], 'roles' => $roles_array, 'error' => $error));
				$this->load->view(CPATH . 'foot');
			} else {
				$this->user->actualizar_llave($data[0]->id);
				redirect('users/view/' . $user_id);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Editar Usuario', $add_lib));
			$this->load->view(SPATH . 'user_edit_structure', array('user' => $user[0], 'data' => $data[0], 'roles' => $roles_array, 'error' => $error));
			$this->load->view(CPATH . 'foot');
		}
	}

	public function remove($user_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $user_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->user->borrar($user_id);
		redirect('users');
	}

	public function checkUser()
	{


		$this->load->model('user');
		$user = $this->user->obtener(
			array(
				array(
					'id'	=>	$this->input->post('bid')
				)
			)
		);

		try {

			if (sizeof($user) == 0) {
				throw new Exception('No existe el usuario');
			}

			if ($user[0]->role->value_p == 'Arquitectos') {
				throw new Exception('No puedes eliminar a un arquitecto');
			}

			if ($user[0]->role->value_p == 'Administradores Generales') {
				throw new Exception('No puedes eliminar a un administrador general');
			}

			if ($user[0]->role->value_p == 'Clientes') {

				$this->load->model('building_site');

				$building_site = $this->building_site->obtener(
					array(
						array(
							'fk_client'	=>	$this->input->post('bid')
						)
					)
				);

				if (sizeof($building_site) > 0) {
					throw new Exception('El cliente tiene sitios de construcción asociados');
				}
			}

			if ($user[0]->role->value_p == 'Mandantes') {

				$this->load->model('building_site');

				$building_site = $this->building_site->obtener(
					array(
						array(
							'fk_contractor'	=>	$this->input->post('bid')
						)
					)
				);

				if (sizeof($building_site) > 0) {
					throw new Exception('El mandante tiene sitios de construcción asociados');
				}
			}

			if ($user[0]->role->value_p == 'Supervisores') {


				$this->load->model('supervisor');

				$supervisor = $this->supervisor->obtener(
					array(
						array(
							'fk_user'	=>	$this->input->post('bid')
						)
					)
				);

				if (sizeof($supervisor) > 0) {
					throw new Exception('El supervisor tiene sitios de construcción asociados');
				}
			}

			echo json_encode(array('status' => 'success'));
		} catch (Exception $e) {
			echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
		}
	}
}
