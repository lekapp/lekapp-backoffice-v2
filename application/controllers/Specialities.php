<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Specialities extends CI_Controller {

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

	function __construct(){
		parent::__construct();
		$this->load->model('web');
		$this->load->library('session');
		$this->load->model('user');

		$logged_in = $this->session->userdata('logged_in');

		if( $logged_in == FALSE ){
			redirect('login');
		}

		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
					)
				)
			);

		if( $user[0]->fk_role > 1 ){
			redirect('login');
		}


	}

	public function index(){

		$logged_in = $this->session->userdata('logged_in');

		if( $logged_in == FALSE ){
			redirect('login');
		}

		$this->load->model('user');
		$this->load->model('speciality');

		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
					)
				)
			);

		$data = $this->speciality->obtener_ordenado();

		$add_lib = array(
			'js_lib'	=>  array(
				//asset_js( '../node_modules/datatables/datatables.min.js' ),
				asset_js( 'front.js' ),
				),
			'css_lib'	=>	array(
				//asset_css( '../node_modules/datatables/datatables.min.css' ),
				)
			);

		$this->load->view( CPATH . 'head', $this->web->get_header( 'Especialidad', $add_lib ) );
		$this->load->view( SPATH . 'speciality_list_structure', array( 'user' => $user[0], 'data' => $data ) );
		$this->load->view( CPATH . 'foot' );

	}

	public function edit( $speciality_id = 0 ){

		$logged_in = $this->session->userdata('logged_in');

		if( $logged_in == FALSE || $speciality_id == 0 ){
			redirect('login');
		}

		$this->load->model('user');
		$this->load->model('speciality');

		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
					)
				)
			);

		$data = $this->speciality->obtener(
			array(
				array(
					'id'	=>	$speciality_id
					)
				)
			);

		$building_sites = $this->building_site->obtener_ordenado();
		$building_sites_array = array();

		foreach($building_sites as $k => $v){
			$building_sites_array[$k] = $v->name;
		}

		//$levels_array = array_reverse( $levels_array, true );

		$add_lib = array(
			'js_lib'	=>  array(
				//asset_js( '...' ),
				asset_js( 'front.js' ),
				),
			'css_lib'	=>	array(
				//asset_js('...')
				),
			);

		$this->load->helper('form');

		if( $this->input->post('update') ){

			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');

			if ($this->form_validation->run() == FALSE){
				$this->load->view( CPATH . 'head', $this->web->get_header( 'Editar Especialidad', $add_lib ) );
				$this->load->view( SPATH . 'speciality_edit_structure', array( 'user' => $user[0], 'data' => $data[0], 'building_sites' => $building_sites_array ) );
				$this->load->view( CPATH . 'foot' );
			}
			else{

				$this->speciality->actualizar( $data[0]->id );

				redirect('specialities');

			}

		} else {
			$this->load->view( CPATH . 'head', $this->web->get_header( 'Editar Especialidad', $add_lib ) );
			$this->load->view( SPATH . 'speciality_edit_structure', array( 'user' => $user[0], 'data' => $data[0], 'building_sites' => $building_sites_array ) );
			$this->load->view( CPATH . 'foot' );
		}

	}

	public function add(){

		$logged_in = $this->session->userdata('logged_in');

		if( $logged_in == FALSE ){
			redirect('login');
		}

		$this->load->model('user');
		$this->load->model('speciality');

		$user = $this->user->obtener(
			array(
				array(
					'email'	=>	$logged_in->email
					)
				)
			);

		$building_sites = $this->building_site->obtener_ordenado();

		$building_sites_array = array();

		foreach($building_sites as $k => $v){
			$building_sites_array[$k] = $v->name;
		}

		$add_lib = array(
			'js_lib'	=>  array(
				//asset_js( '...' ),
				asset_js( 'front.js' ),
				),
			'css_lib'	=>	array(
				//asset_css( '...')
				)
			);

		$this->load->helper('form');

		if( $this->input->post('add') ){

			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');

			if ($this->form_validation->run() == FALSE){
				$this->load->view( CPATH . 'head', $this->web->get_header( 'Añadir Especialidad', $add_lib ) );
				$this->load->view( SPATH . 'speciality_add_structure', array( 'user' => $user[0], 'building_sites' => $building_sites_array ) );
				$this->load->view( CPATH . 'foot' );
			}
			else{

				$new = $this->speciality->insertar();

				redirect('specialities');

			}

		} else {

			$this->load->view( CPATH . 'head', $this->web->get_header( 'Añadir Especialidad', $add_lib ) );
			$this->load->view( SPATH . 'speciality_add_structure', array( 'user' => $user[0], 'building_sites' => $building_sites_array ) );
			$this->load->view( CPATH . 'foot' );

		}
	}

	public function remove( $speciality_id = 0 ){

		$logged_in = $this->session->userdata('logged_in');

		if( $logged_in == FALSE || $speciality_id == 0 ){
			redirect('login');
		}

		$this->load->model('user');
		$this->load->model('speciality');

		$this->speciality->borrar( $speciality_id );

		redirect('specialities');

	}


}
