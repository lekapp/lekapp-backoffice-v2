<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Speciality_roles extends CI_Controller
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
		if ($user[0]->fk_role > 1) {
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
		$this->load->model('speciality_role');
		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
				)
			)
		);
		$data = $this->speciality_role->obtener_ordenado();
		$add_lib = array(
			'js_lib'	=>  array(
				//asset_js( '../node_modules/datatables/datatables.min.js' ),
				asset_js('front.js'),
			),
			'css_lib'	=>	array(
				//asset_css( '../node_modules/datatables/datatables.min.css' ),
			)
		);
		$this->load->view(CPATH . 'head', $this->web->get_header('Rol de Especialidad', $add_lib));
		$this->load->view(SPATH . 'speciality_role_list_structure', array('user' => $user[0], 'data' => $data));
		$this->load->view(CPATH . 'foot');
	}
	public function edit($speciality_role_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $speciality_role_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('speciality_role');
		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
				)
			)
		);
		$data = $this->speciality_role->obtener(
			array(
				array(
					'id'	=>	$speciality_role_id
				)
			)
		);
		$specialities = $this->speciality->obtener_ordenado();
		$specialities_array = array();
		foreach ($specialities as $k => $v) {
			$specialities_array[$k] = '[' . $v->building_site->name . '] ' . $v->name;
		}
		$add_lib = array(
			'js_lib'	=>  array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib'	=>	array(
				//asset_js('...')
			),
		);
		$this->load->helper('form');
		if ($this->input->post('update')) {
			$this->load->library('form_validation');

			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('fk_speciality', 'Especialidad', 'required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Editar Rol Especialidad', $add_lib));
				$this->load->view(SPATH . 'speciality_role_edit_structure', array('user' => $user[0], 'data' => $data[0], 'specialities' => $specialities_array));
				$this->load->view(CPATH . 'foot');
			} else {
				$this->speciality_role->actualizar($data[0]->id);
				redirect('speciality_roles');
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Editar Rol Especialidad', $add_lib));
			$this->load->view(SPATH . 'speciality_role_edit_structure', array('user' => $user[0], 'data' => $data[0], 'specialities' => $specialities_array));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function add()
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('speciality_role');
		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
				)
			)
		);
		$specialities = $this->speciality->obtener_ordenado();
		$specialities_array = array();
		foreach ($specialities as $k => $v) {
			$specialities_array[$k] = '[' . $v->building_site->name . '] ' . $v->name;
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
		$this->load->helper('form');
		if ($this->input->post('add')) {
			$this->load->library('form_validation');

			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('fk_speciality', 'Especialidad', 'required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Rol Especialidad', $add_lib));
				$this->load->view(SPATH . 'speciality_role_add_structure', array('user' => $user[0], 'specialities' => $specialities_array));
				$this->load->view(CPATH . 'foot');
			} else {
				$new = $this->speciality_role->insertar();
				redirect('speciality_roles');
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Rol Especialidad', $add_lib));
			$this->load->view(SPATH . 'speciality_role_add_structure', array('user' => $user[0], 'specialities' => $specialities_array));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function remove($speciality_role_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $speciality_role_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('speciality_role');
		$this->speciality_role->borrar($speciality_role_id);
		redirect('speciality_roles');
	}
}
