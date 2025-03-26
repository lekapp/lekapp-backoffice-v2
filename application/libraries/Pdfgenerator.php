<?php

defined('BASEPATH') or exit('No direct script access allowed');
// Al requerir el autoload, cargamos todo lo necesario para trabajar
define('DOMPDF_DIR', dirname(__FILE__) . '/dompdf/');

require_once DOMPDF_DIR . "/autoload.inc.php";

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdfgenerator
{
	// por defecto, usaremos papel A4 en vertical, salvo que digamos otra cosa al momento de generar un PDF
	public function generate($html, $filename = '', $stream = TRUE, $paper = 'A4', $orientation = "portrait")
	{
		$options = new Options();
		$options->set('isRemoteEnabled', true);
		$dompdf = new DOMPDF($options);
		$dompdf->set_option('enable_css_float', true);
		$dompdf->set_option('isHtml5ParserEnabled', true);
		$dompdf->loadHtml($html);
		$dompdf->setPaper($paper, $orientation);
		$dompdf->render();
		if ($stream) {
			// "Attachment" => 1 hará que por defecto los PDF se descarguen en lugar de presentarse en pantalla.
			$dompdf->stream($filename . ".pdf", array("Attachment" => 1));
		} else {
			return $dompdf->output();
		}
	}
	
}
