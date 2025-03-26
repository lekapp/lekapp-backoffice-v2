<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Spanish_date
 * 
 * Parse timestamp from English to Spanish
 *
 * @access		public
 * @param   string
 * @return		string
 */
if (!function_exists('spanish_date')) {
	function spanish_date($timestamp) {
		date_default_timezone_set('America/Santiago');
		$f_timestamp = strtotime($timestamp);
		$dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$result = $dias[date('w', $f_timestamp)]." ".date('d', $f_timestamp)." de ".$meses[date('n', $f_timestamp)-1]. " del ".date('Y', $f_timestamp) ;
		return $result;
	}
}

if (!function_exists('spanish_hour')) {
	function spanish_hour($timestamp) {
		date_default_timezone_set('America/Santiago');
		$f_timestamp = strtotime($timestamp);
		$result = date('H:i:s', $f_timestamp) ;
		return $result;
	}
}

/* End of file url_helper.php */
/* Location: ./application/helpers/url_helper.php */