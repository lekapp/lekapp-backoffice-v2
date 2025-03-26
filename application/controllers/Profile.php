<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

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
	}

	public function index(){

		$logged_in = $this->session->userdata('logged_in');

		if( $logged_in == FALSE ){
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

		$add_lib = array(
			'js_lib'	=>  array(
				//asset_js( '...' ),
				asset_js( 'front.js' ),
				),
			'css_lib'	=>	array(
				//asset_css( '...')
				)
			);

		$this->load->view( CPATH . 'head', $this->web->get_header( 'Profile', $add_lib ) );
		$this->load->view( SPATH . 'profile_view_structure', array( 'user' => $user[0] ) );
		$this->load->view( CPATH . 'foot' );
	}

	public function edit(){

		$logged_in = $this->session->userdata('logged_in');

		if( $logged_in == FALSE ){
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

		$user[0]->avatar_file = 'userdata';

		$genders = $this->gender->obtener_ordenado();

		$genders_array = array();
		$error = array(
			'code' =>	0,
			'message'	=>	""
		);

		foreach($genders as $k => $v){
			$genders_array[$k] = $v->value;
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

		if( $this->input->post('update_1') ){

			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('first_name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('last_name', 'Apellido', 'trim|required');
			$this->form_validation->set_rules('email', 'Email', 'trim|required');
			$this->form_validation->set_rules('dni', 'RUT/DNI', 'trim|required');
			$this->form_validation->set_rules('address_1', 'Dirección 1', 'trim|required');
			$this->form_validation->set_rules('address_2', 'Dirección 2', 'trim');
			$this->form_validation->set_rules('phone_1', 'Fono 1', 'trim|required');
			$this->form_validation->set_rules('phone_2', 'Fono 2', 'trim');

			if ($this->form_validation->run() == FALSE){

				$this->load->view( CPATH . 'head', $this->web->get_header( 'Profile', $add_lib ) );
				$this->load->view( SPATH . 'profile_edit_structure', array( 'user' => $user[0], 'genders' => $genders_array, 'error' => $error ) );
				$this->load->view( CPATH . 'foot' );

			}
			else{

				$it = $this->image_type->obtener(
					array(
						array(
							'code_name'	=>	'custom_avatar'
							)
						)
					);

				$config = $this->web->get_upload_config( 'custom_avatar' );
				$config['upload_path'] = $config['upload_path'] . $user[0]->id;

				if( !is_dir( $config['upload_path'] ) ){
					$config['upload_path'] = ( $config['upload_path'] . $user[0]->id );
				}

				$this->load->library('upload', $config);
				$this->upload->do_upload( $user[0]->avatar_file );

				$this->user->actualizar( $user[0]->id );
				$this->user->actualizar_genero( $user[0]->id );

				$name = "";

				if(is_string( $this->upload->data('raw_name') ) && $this->upload->data('raw_name') != "" ){
					$name = utf8_encode( $this->upload->data('raw_name') );
				}

				if( $this->upload->display_errors() == "<p>No seleccionaste un archivo para subir.</p>" ){
					//Sin cambios
					
					redirect('profile');
				}
				elseif( $this->upload->data('raw_name') == false || $this->upload->display_errors() != "" ){
					
					//$this->user->actualizar_imagen( $user[0]->id, $name, 0, $this->upload->display_errors() );

					$error['code'] = 1;
					$error['message'] = str_replace( '<p>', '', $this->upload->display_errors() );
					$error['message'] = str_replace( '</p>', '', $error['message'] );

					$this->load->view( CPATH . 'head', $this->web->get_header( 'Profile', $add_lib ) );
					$this->load->view( SPATH . 'profile_edit_structure', array( 'user' => $user[0], 'genders' => $genders_array, 'error' => $error ) );
					$this->load->view( CPATH . 'foot' );

				} else {
					$i = $this->image->insertar( $name, $this->upload->data('file_ext'), $it[0]->id );
					$this->user->actualizar_imagen( $user[0]->id, $name, $i, $this->upload->display_errors() );

					redirect('profile');
				}

			}

		} else if( $this->input->post('update_2') ){

			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('password', 'Contraseña', 'trim|required');

			if ($this->form_validation->run() == FALSE){
				$this->load->view( CPATH . 'head', $this->web->get_header( 'Profile', $add_lib ) );
				$this->load->view( SPATH . 'profile_edit_structure', array( 'user' => $user[0], 'genders' => $genders_array, 'error' => $error ) );
				$this->load->view( CPATH . 'foot' );
			}
			else{

				$this->user->actualizar_llave( $user[0]->id );
				redirect('profile');

			}

		} else {
			$this->load->view( CPATH . 'head', $this->web->get_header( 'Profile', $add_lib ) );
			$this->load->view( SPATH . 'profile_edit_structure', array( 'user' => $user[0], 'genders' => $genders_array, 'error' => $error ) );
			$this->load->view( CPATH . 'foot' );
		}
	}

	public function add(){

		$logged_in = $this->session->userdata('logged_in');

		//$user_id = intval( $this->uri->segment(3) );

		if( $logged_in != FALSE ){
			$this->session->unset_userdata('logged_in');
		}

		$this->load->model('user');
		$this->load->model('role');

		$error = array(
			'code' =>	0,
			'message'	=>	""
		);

		$role = $this->role->obtener(
			array(
				array(
					'id'	=>	99
					)
				)
			);

		$user = array();

		$it = $this->image_type->obtener( 
			array(
				array(
					'code_name'	=>	'default_avatar'
					)
				) 
			);

		$itda = $it[0];

		$ida = $this->image->obtener( 
			array(
				array(
					'fk_image_type'	=>	$itda->id
					),
				array(
					'name'	=>	'avatar_u'
					)
				) 
			);
		$user[0] = new stdClass;
		$user[0]->first_name = "Nuevo";
		$user[0]->last_name = "Usuario";
		$user[0]->image = $ida[0];
		$user[0]->avatar_url = $user[0]->image->url . $user[0]->image->name . $user[0]->image->ext;
		$user[0]->role = $role[0];
		$user[0]->fk_role = $role[0]->id;

		$data = new stdClass;
		$data->avatar_file = 'userdata';

		$roles = $this->role->obtener_ordenado(
			array(
				array(
					'id >' =>	3
				),
				array(
					'value_p !=' =>	'Nuevos usuarios'
				)
			)
		);

		$roles_array = array();

		foreach($roles as $k => $v){
			$roles_array[$k] = $v->value_p;
		}

		$roles_array = array_reverse( $roles_array, true );

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
			
			$this->form_validation->set_rules('first_name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('last_name', 'Apellido', 'trim|required');
			$this->form_validation->set_rules('email', 'Email', 'trim|required');
			$this->form_validation->set_rules('dni', 'RUT/DNI', 'trim|required');
			$this->form_validation->set_rules('address_1', 'Dirección 1', 'trim|required');
			$this->form_validation->set_rules('address_2', 'Dirección 2', 'trim');
			$this->form_validation->set_rules('phone_1', 'Fono 1', 'trim|required');
			$this->form_validation->set_rules('phone_2', 'Fono 2', 'trim');

			if ($this->form_validation->run() == FALSE){

				$this->load->view( CPATH . 'head', $this->web->get_header( 'Añadir Usuario', $add_lib ) );
				$this->load->view( SPATH . 'profile_add_structure', array( 'user' => $user[0], 'data' => $data, 'roles' => $roles_array, 'error' => $error ) );
				$this->load->view( CPATH . 'foot' );

			}
			else{

				$new = $this->user->insertar();

				$this->user->actualizar_rol( $new );

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

				$config = $this->web->get_upload_config( 'custom_avatar' );
				$config['upload_path'] = utf8_decode( $config['upload_path'] . $data[0]->id );

				if( !is_dir( $config['upload_path'] ) ){
					mkdir( $config['upload_path'], 0777, true );
				}

				$this->load->library('upload', $config);
				$this->upload->do_upload( $data[0]->avatar_file );

				$this->user->actualizar( $data[0]->id );

				$name = "";

				if(is_string( $this->upload->data('raw_name') )){
					$name = utf8_encode( $this->upload->data('raw_name') );
				}

				$logged_in = new stdClass;
				$logged_in->email = $data[0]->email;
				$this->session->set_userdata('logged_in', $data[0]);

				if( $this->upload->display_errors() == "<p>No seleccionaste un archivo para subir.</p>" ){
					//Sin imagen
					redirect('profile');
				}
				elseif( $this->upload->data('raw_name') == false || $this->upload->display_errors() != "" ){
					//$this->user->actualizar_imagen( $data[0]->id, $name, 0, $this->upload->display_errors() );

					$error['code'] = 1;
					$error['message'] = str_replace( '<p>', '', $this->upload->display_errors() );
					$error['message'] = str_replace( '</p>', '', $error['message'] );

					$logged_in->error = 1;
					$logged_in->error_code = $error['code'];
					$logged_in->error_message = $error['message'];

					$this->session->set_userdata( 'logged_in', $logged_in );

					redirect('profile/edit' );

				} else {
					$i = $this->image->insertar( $name, $this->upload->data('file_ext'), $it[0]->id );
					$this->user->actualizar_imagen( $data[0]->id, $name, $i, $this->upload->display_errors() );

					redirect('dashboard');
				}

			}

		} else {

			$this->load->view( CPATH . 'head', $this->web->get_header( 'Añadir Usuario', $add_lib ) );
			$this->load->view( SPATH . 'profile_add_structure', array( 'user' => $user[0], 'data' => $data, 'roles' => $roles_array, 'error' => $error ) );
			$this->load->view( CPATH . 'foot' );

		}

	}

}
