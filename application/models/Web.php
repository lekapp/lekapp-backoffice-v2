<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Web extends CI_Model {

	var $nav_elements = array();
	var $header_config = array();
	var $paginate_limit = 0;
	var $controller = '';

	function __construct() {
		parent::__construct();

		$this->load->helper('assets');

		$this->nav_elements = array(
			'index' => 'Inicio'
			);
		
		$this->header_config = array(
			'title'	 	=>  SYSNAME,
			'css_lib'   =>  array(
				//asset_css( '.css' ),
				asset_css( '../vendor/bootstrap/css/bootstrap.min.css' ),
				asset_css( '../vendor/bootstrap/css/bootstrap-datepicker.min.css' ),
				asset_css( '../vendor/font-awesome/css/font-awesome.min.css' ),
				asset_css( '../vendor/datatables/datatables.min.css' ),
				asset_css( 'fontastic.css' ),
				'https://fonts.googleapis.com/css?family=Poppins:300,400,700',
				asset_css( 'style.default.css' ),
				asset_css( 'custom.css' ),
				),
			'meta_tag'  =>  array(
				'<meta charset="utf-8">',
				'<meta http-equiv="X-UA-Compatible" content="IE=edge">',
				'<meta name="viewport" content="width=device-width, initial-scale=1.0">',
				'<meta name="robots" content="all,follow">',
				'<link rel="shortcut icon" href="' . asset_img( 'favicon.ico' ) . '">'
				),
			'header_js_lib'	=>  array(
				//asset_js ( '.js' ),
				asset_js( '../vendor/jquery/jquery.min.js' ),
				asset_js( '../vendor/popper.js/umd/popper.min.js' ),
				asset_js( '../vendor/bootstrap/js/bootstrap.min.js' ),
				asset_js( '../vendor/jquery.cookie/jquery.cookie.js' ),
				asset_js( '../vendor/chart.js/Chart.min.js' ),
				asset_js( '../vendor/jquery-validation/jquery.validate.min.js' ),
				asset_js( '../vendor/datatables/datatables.min.js' ),
				asset_js( '../vendor/bootstrap/js/bootstrap-datepicker.min.js' ),
				asset_js( '../vendor/sweetalert/sweetalert2.all.js' ),
				)
			);
	}

	function get_header($subtitle = '', $additional_header = array()) {

		$custom_header = array_merge_recursive($this->header_config, $additional_header);
		if($subtitle == '') $subtitle = 'Inicio';

		$custom_header['title'] = $custom_header['title'] . ' - ' . $subtitle;

		return $custom_header;

	}

	function get_upload_config( $type = 'default_avatar' ){

		$array['allowed_types'] = 'gif|jpg|jpeg|png';

		switch( $type ){
			case 'default_avatar':
				$array['max_size']     = '1048576';
				$array['upload_path'] = ASSETS . UPLOAD . IMG . USER;
				$array['max_width'] = '512';
				$array['max_height'] = '512';
				break;
			case 'custom_avatar':
				$array['upload_path'] = ASSETS . UPLOAD . IMG . USER;
				$array['max_size']     = '2097152';
				$array['max_width'] = '1024';
				$array['max_height'] = '1024';
				break;
			case 'activity_report':
				$array['upload_path'] = ASSETS . UPLOAD . IMG . REPORT;
				$array['max_size']     = 0;
				$array['max_width'] = 0;
				$array['max_height'] = 0;
				break;
			case 'spreadsheet':
				$array['upload_path'] = ASSETS . UPLOAD . TEMPORAL;
				$array['allowed_types'] = 'xls|xlsx';
				$array['max_size']     = '2097152';
				$array['overwrite'] = TRUE;
				$array['encrypt_name'] = TRUE;
				break;
			default:
				$array['upload_path'] = ASSETS . UPLOAD . IMG ;
				$array['max_size']     = '7372800';
				$array['max_width'] = '1920';
				$array['max_height'] = '1920';
		}

		return $array;
	}

}
