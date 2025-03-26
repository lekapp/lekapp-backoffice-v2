<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Login extends CI_Controller
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
		$this->load->model('user');
		$this->load->library('session');
		$this->session->unset_userdata('logged_in');
	}

	public function index()
	{
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				//asset_js( 'charts-home.js' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '...')
			)
		);
		$this->load->helper('form');
		if ($this->input->post('acceder')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('email', 'Email usuario', 'trim|required');
			$this->form_validation->set_rules('password', 'Contraseña', 'trim|required|callback_check_login');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Login', $add_lib));
				$this->load->view(SPATH . 'login_structure');
				$this->load->view(CPATH . 'foot');
			} else {
				$user = $this->user->obtener(
					array(
						array(
							'email' => $this->input->post('email')
						)
					)
				);

				$logged_in = new stdClass;
				$logged_in->email = $user[0]->email;
				$this->session->set_userdata('logged_in', $user[0]);
				redirect('dashboard');

			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Login', $add_lib));
			$this->load->view(SPATH . 'login_structure');
			$this->load->view(CPATH . 'foot');
		}
	}

	public function check_login()
	{
		$user = $this->user->obtener(
			array(
				array(
					'email' => $this->input->post('email')
				),
				array(
					'password' => pack('H*', hash('sha512', $this->input->post('password')))
				)
			)
		);
		if (!is_array($user) || sizeof($user) == 0) {
			$this->form_validation->set_message('check_login', 'Email de usuario o contraseña incorrecta');
			return FALSE;
		} else {
			return TRUE;
		}
	}

}