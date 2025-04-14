<?php
defined('BASEPATH') or exit('No direct script access allowed');
//include(APPPATH . 'third_party' . '/phpspreadsheet/load.php');
require APPPATH . 'third_party' . "/phpspreadsheet/vendor/autoload.php";
require "ClassStatic.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Building_sites extends CI_Controller
{
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
					'email' => $logged_in->email
				)
			)
		);
	}
	public function index()
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('building_site');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		if ($user[0]->fk_role < 3) {
			$data = $this->building_site->obtener_ordenado();
		} else if ($user[0]->role->value_p == "Clientes") {
			$data = $this->building_site->obtener_ordenado(
				[
					[
						'fk_client' => $user[0]->id
					]
				]
			);
		} else if ($user[0]->role->value_p == "Mandantes") {
			$data = $this->building_site->obtener_ordenado(
				[
					[
						'fk_contractor' => $user[0]->id
					]
				]
			);
		}
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '../node_modules/datatables/datatables.min.js' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '../node_modules/datatables/datatables.min.css' ),
			)
		);
		$this->load->view(CPATH . 'head', $this->web->get_header('Obras', $add_lib));
		$this->load->view(SPATH . 'building_site_list_structure', array('user' => $user[0], 'data' => $data));
		$this->load->view(CPATH . 'foot');
	}
	public function reverse_report_activity($building_site_id = 0)
	{
		$this->load->model('user');
		$this->load->model('role');
		$this->load->model('zone');
		$this->load->model('building_site');
		$this->load->model('speciality');
		$this->load->model('speciality_role');
		$this->load->model('activity');
		$this->load->model('activity_registry');
		$this->load->model('activity_data');
		$this->load->model('daily_report');
		$tpa = 0;
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$row = 1;
		$activity_items = $this->activity->obtener(
			[
				[
					'fk_building_site' => $building_site_id
				]
			]
		);
		$registry = [];
		$registryDateCells = [];
		$min_rec = 0;
		$max_rec = 0;
		foreach ($activity_items as $activity) {
			$t = $this->activity_registry->obtener([
				[
					'fk_building_site' => $building_site_id,
					'fk_activity' => $activity->id
				]
			]);
			if (sizeof($t) > 0) {
				$registry[$activity->activity_code][$t[0]->speciality->id][$t[0]->speciality_role->id] = $t;
				foreach ($t as $record) {
					if ($min_rec == 0 || $min_rec > strtotime($record->activity_date)) {
						$min_rec = strtotime($record->activity_date);
					}
					if ($max_rec == $min_rec || $max_rec < strtotime($record->activity_date)) {
						$max_rec = strtotime($record->activity_date);
					}
				}
			}
		}
		$activity_data = $this->activity_data->obtener(
			[
				[
					'fk_building_site' => $building_site_id
				]
			]
		);
		$dataDateCells = [];
		foreach ($activity_data as $ad) {
			if (!array_search(gmdate("Ymd", $ad->activity_date * 86400), $dataDateCells) !== false) {
				$dataDateCells[] = gmdate("Ymd", $ad->activity_date * 86400);
				if ($min_rec == 0 || $min_rec > $ad->activity_date * 86400) {
					$min_rec = $ad->activity_date * 86400;
				}
				if ($max_rec == $min_rec || $max_rec < $ad->activity_date * 86400) {
					$max_rec = $ad->activity_date * 86400;
				}
			}
		}
		for ($i = $min_rec; $i <= $max_rec; $i += 86400) {
			$registryDateCells[] = gmdate("Ymd", $i);
		}
		$dataDateCells = array_merge($dataDateCells, $registryDateCells);
		sort($dataDateCells, SORT_NUMERIC);
		$dataDateCells = array_unique($dataDateCells);
		$dataDateCells = array_values($dataDateCells);
		$i = 0;
		foreach ($dataDateCells as $k => $date) {
			$sheet->setCellValueByColumnAndRow($i + 17, 1, substr($date, 6, 2) . '-' . substr($date, 4, 2) . '-' . substr($date, 0, 4));
			$i++;
			if ($i % 7 == 0) {
				$sheet->setCellValueByColumnAndRow($i + 17, 1, "% Avance semana");
				$i++;
			}
		}
		if ($i % 7 != 0) {
			$sheet->setCellValueByColumnAndRow($i + 17, 1, "% Avance semana");
		}
		$cat_ad = [];
		foreach ($activity_data as $ad) {
			if (!isset($cat_ad[$ad->activity->activity_code])) {
				$cat_ad[$ad->activity->activity_code] = [];
			}
			if (!isset($cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality])) {
				$cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality] = [];
			}
			if (!isset($cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality][$ad->activity->fk_speciality_role])) {
				$cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality][$ad->activity->fk_speciality_role] = [];
			}
			$cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality][$ad->activity->fk_speciality_role][] = $ad;
		}
		$C = "Q"; // define la columna donde empieza la fecha
		foreach ($cat_ad as $a) {
			$row++;
			$n = $a;
			sort($n);
			sort($n[0]);
			sort($n[0][0]);
			sort($n[0][0][0]);
			$n = $n[0][0][0]->activity;
			$titles = [
				$n->zone->name,
				"",
				$n->speciality->name,
				"",
				$n->activity_code,
				"",
				$n->name,
				"",
				$n->unt,
				$n->qty,
				$n->eff,
			];
			$spreadsheet->getActiveSheet()
				->fromArray(
					$titles,
					NULL,
					"A{$row}"
				)
				->setCellValueExplicit(
					"E{$row}",
					$n->activity_code,
					\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
				);
			$row++;
			foreach ($a as $s) {
				$p_avance = [];
				$p_machinery = [];
				$p_comment = [];
				$roles = 0;
				$t_row = $row;
				$x = 0;
				foreach ($s as $k => $r) {
					$first = false;
					$first_column = 18;
					$sheet->getCell("H{$t_row}")->setValue($r[0]->speciality_role->name);
					$p_avance[$x] = [];
					foreach ($r as $d) {
						$p_avance[$x][gmdate("Ymd", $d->activity_date * 86400)] = floatval($d->p_avance);
						$tpa += $d->p_avance;
						if ($first == false) {
							if ($d->p_avance != 0)
								$first = true;
							else {
								$first_column++;
							}
						}
					}
					foreach ($dataDateCells as $date) {
						if (!isset($p_avance[$x][$date])) {
							$p_avance[$x][$date] = 0;
						}
					}
					ksort($p_avance[$x], SORT_NUMERIC);
					$t_row++;
					$x++;
					$sheet->getCell("G{$t_row}")->setValue("AVANCE DESCRITO EN TERRENO");
					$t_row++;
					foreach ($dataDateCells as $date) {
						if (!isset($p_avance[$x][$date])) {
							$p_avance[$x][$date] = 0;
						}
						foreach ($r as $data) {
							if (isset($registry[$data->activity->activity_code][$data->fk_speciality][$data->fk_speciality_role])) {
								foreach ($registry[$data->activity->activity_code][$data->fk_speciality][$data->fk_speciality_role] as $round) {
									if ($date == str_replace("-", "", $round->activity_date)) {
										$p_avance[$x][$date] = $round->p_avance;
										$p_machinery[$x][$date] = $round->machinery;
										$p_comment[$x][$date] = $round->comment;
									}
								}
							}
						}
					}
					$x++;
					$roles++;
					$roles++;
					$roles++;
				}
				$sh = [];
				/**
				 * ricardo.munoz
				 * arregla problema con arreglos de 2 dimensiones
				 * se selecciona el arreglo que contenga "numeros" 
				 */
				$key_cero = 0;
				foreach ($p_avance as $key_value => $value) {
					$i = 0;
					$sum = 0;
					$nr = [];
					foreach ($value as $v) {
						if (is_string($v) && 0 <> (float) $v)
							$key_cero = $key_value;
						$nr[] = $v;
						$i++;
						if ($i % 7 == 0) {
							$nr[] = NULL;
							$i++;
						}
					}
					$sh[] = $nr;
				}
				$sh = $sh[$key_cero];
				$spreadsheet->getActiveSheet()
					->fromArray(
						$sh,
						0,
						$C . $row,
						TRUE,
						TRUE
					);
				$sh_machinery = [];
				$row++;
				foreach ($p_machinery as $value) {
					$i = 0;
					$sum = 0;
					$nr_machinery = [];
					foreach ($value as $v) {
						$nr_machinery[] = $v;
						$i++;
						if ($i % 7 == 0) {
							$nr_machinery[] = NULL;
							$i++;
						}
					}
					$sh_machinery[] = $nr_machinery;
				}
				$spreadsheet->getActiveSheet()
					->fromArray(
						$sh_machinery,
						0,
						$C . $row,
						TRUE
					);
				$sh_comment = [];
				$row++;
				foreach ($p_comment as $value) {
					$i = 0;
					$sum = 0;
					$nr_comment = [];
					foreach ($value as $v) {
						$nr_comment[] = $v;
						$i++;
						if ($i % 7 == 0) {
							$nr_comment[] = NULL;
							$i++;
						}
					}
					$sh_comment[] = $nr_comment;
				}
				$spreadsheet->getActiveSheet()
					->fromArray(
						$sh_comment,
						0,
						$C . $row,
						TRUE
					);
				$row += $roles;
			}
		}
		$row = 1;
		$titles = [
			"Área",
			"Sub-area",
			"Especialidad",
			"Supervisor",
			"Item",
			"Descripción",
			"Descripción2",
			"Rol de especialidad",
			"Unid",
			"Cant",
			"Rend",
			"Nº Trabajadores",
			"Trabajo",
			"Duracion",
			"Comienzo Programado",
			"Fin Programado",
		];
		$spreadsheet->getActiveSheet()
			->fromArray(
				$titles,
				NULL,
				"A1"
			);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Planilla Avances.xlsx"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
		exit;
	}
	public function reverse_report_workers($building_site_id = 0)
	{
		$this->load->model('user');
		$this->load->model('building_site');
		$this->load->model('worker_activity');
		$this->load->model('worker');
		$this->load->model('activity_registry');
		$this->load->model('activity_data');
		$this->load->model('activity');
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$row = 1;
		$activity_items = $this->activity->obtener(
			[
				[
					'fk_building_site' => $building_site_id
				]
			]
		);
		$registry = [];
		$registryDateCells = [];
		$min_rec = 0;
		$max_rec = 0;
		foreach ($activity_items as $activity) {
			$t = $this->activity_registry->obtener([
				[
					'fk_building_site' => $building_site_id,
					'fk_activity' => $activity->id
				]
			]);
			if (sizeof($t) > 0) {
				$registry[$activity->activity_code][$t[0]->speciality->id][$t[0]->speciality_role->id] = $t;
				foreach ($t as $record) {
					if ($min_rec == 0 || $min_rec > strtotime($record->activity_date)) {
						$min_rec = strtotime($record->activity_date);
					}
					if ($max_rec == $min_rec || $max_rec < strtotime($record->activity_date)) {
						$max_rec = strtotime($record->activity_date);
					}
				}
			}
		}
		$activity_data = $this->activity_data->obtener(
			[
				[
					'fk_building_site' => $building_site_id
				]
			]
		);
		$dataDateCells = [];
		foreach ($activity_data as $ad) {
			if (!array_search(gmdate("Ymd", $ad->activity_date * 86400), $dataDateCells) !== false) {
				$dataDateCells[] = gmdate("Ymd", $ad->activity_date * 86400);
				if ($min_rec == 0 || $min_rec > $ad->activity_date * 86400) {
					$min_rec = $ad->activity_date * 86400;
				}
				if ($max_rec == $min_rec || $max_rec < $ad->activity_date * 86400) {
					$max_rec = $ad->activity_date * 86400;
				}
			}
		}
		for ($i = $min_rec; $i <= $max_rec; $i += 86400) {
			$registryDateCells[] = gmdate("Ymd", $i);
		}
		$dataDateCells = array_merge($dataDateCells, $registryDateCells);
		sort($dataDateCells, SORT_NUMERIC);
		$dataDateCells = array_unique($dataDateCells);
		$dataDateCells = array_values($dataDateCells);
		$titles = [
			"Tipo",
			"Especialidad",
			"Rol",
			"RUT",
			"Email",
			"Nombre Completo",
			'Código Actividad'
		];
		$spreadsheet->getActiveSheet()
			->fromArray(
				$titles,
				NULL,
				"A1"
			);
		$i = 0;
		foreach ($dataDateCells as $k => $date) {
			$sheet->setCellValueByColumnAndRow($i + 8, 1, substr($date, 6, 2) . '-' . substr($date, 4, 2) . '-' . substr($date, 0, 4));
			$i++;
		}
		$workers = $this->worker->obtener(
			[
				[
					'fk_building_site' => $building_site_id
				]
			]
		);
		$i = 2;
		/*
																																																																				$worker_all_movements = $this->worker_activity->obtener_ordenado(
																																																																					[[
																																																																						'fk_building_site'	=>	$building_site_id,
																																																																					]]
																																																																				);
																																																																				*/
		foreach ($workers as $worker) {
			$movement = [];
			$processed = [];
			$activities = [];
			$worker_movements = $this->worker_activity->obtener_ordenado(
				[
					[
						'fk_building_site' => $building_site_id,
						'fk_worker' => $worker->id
					]
				]
			);
			if (sizeof($worker_movements) > 0) {
				foreach ($worker_movements as $move) {
					if (!isset($activities[$move->code])) {
						$activities[$move->code] = [];
					}
					$activities[$move->code][str_replace("-", "", $move->date)] = $move->hh;
				}
				foreach ($activities as $code => $movement) {
					foreach ($dataDateCells as $date) {
						if (isset($movement[$date])) {
							$processed[$date] = $movement[$date];
						} else {
							$processed[$date] = "";
						}
					}
					$row = ["DIRECTO", $worker->speciality->name, $worker->speciality_role->name, $worker->dni, $worker->email, $worker->name, $code];
					$row = array_merge($row, $processed);
					$spreadsheet->getActiveSheet()
						->fromArray(
							$row,
							NULL,
							"A" . $i
						);
					$i++;
				}
			} else {
				$row = ["DIRECTO", $worker->speciality->name, $worker->speciality_role->name, $worker->dni, $worker->email, $worker->name, '-'];
				$row = array_merge($row, $processed);
				$spreadsheet->getActiveSheet()
					->fromArray(
						$row,
						NULL,
						"A" . $i
					);
				$i++;
			}
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Planilla Trabajadores.xlsx"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
		exit;
	}
	public function isEmptyRow($row)
	{
		foreach ($row as $cell) {
			if (null !== $cell)
				return false;
		}
		return true;
	}
	public function edit($building_site_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $building_site_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('role');
		$this->load->model('area');
		$this->load->model('zone');
		$this->load->model('building_site');
		$this->load->model('speciality');
		$this->load->model('speciality_role');
		$this->load->model('activity');
		$this->load->model('activity_data');
		$this->load->model('milestone');
		$this->load->model('worker');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$data = $this->building_site->obtener(
			array(
				array(
					'id' => $building_site_id
				)
			)
		);
		$data[0]->data_file = 'userdata';
		$data[0]->data_file_workers = 'workerdata';
		$client_role = $this->role->obtener(
			array(
				array(
					'value_p' => 'Clientes'
				)
			)
		);
		$clients = $this->user->obtener_ordenado(
			array(
				array(
					'fk_role' => $client_role[0]->id
				)
			)
		);
		$clients_array = array();
		foreach ($clients as $k => $v) {
			$clients_array[$k] = $v->first_name . ' ' . $v->last_name;
		}
		$contractor_role = $this->role->obtener(
			array(
				array(
					'value_p' => 'Mandantes'
				)
			)
		);
		$contractors = $this->user->obtener_ordenado(
			array(
				array(
					'fk_role' => $contractor_role[0]->id
				)
			)
		);
		$contractors_array = array();
		foreach ($contractors as $k => $v) {
			$contractors_array[$k] = $v->first_name . ' ' . $v->last_name;
		}
		$specialities = $this->speciality->obtener_ordenado(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		$roles_total = 0;
		foreach ($specialities as $k => $v) {
			$v->hh = 0;
			$t = $this->speciality_role->obtener(
				array(
					array(
						'fk_building_site' => $building_site_id,
						'fk_speciality' => $v->id
					)
				)
			);
			foreach ($t as $vv) {
				$v->hh += $vv->hh;
			}
			$v->speciality_roles_quantity = sizeof($t);
		}
		$areas = $this->area->obtener_ordenado(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		$activities_data = $this->activity_data->obtener_ordenado_esp(
			array(
				array(
					'fk_building_site' => $building_site_id,
					'version' => $data[0]->current_version
				)
			),
			null,
			null,
			'activity_date',
			'asc'
		);
		$milestones = $this->milestone->obtener_ordenado(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		$workers = $this->worker->obtener(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		$hh_total = 0;
		$activity_dates = array();
		$c_activity_dates = array();
		$lv = 0;
		$first_activity = null;
		foreach ($activities_data as $k => $activity) {
			if ($first_activity == null) {
				$first_activity = $activity;
			}
			$hh_total += floatval($activity->hh);
			//$activity->formatted_date = gmdate( "d-m-Y", $activity->activity_date * 86400 );
			if (!isset($activity_dates[$activity->activity_date])) {
				$activity_dates[$activity->activity_date] = floatval($activity->hh);
			} else {
				$activity_dates[$activity->activity_date] += floatval($activity->hh);
			}
			$c_activity_dates[$activity->activity_date] = floatval($activity->hh) + $lv;
			$lv = $c_activity_dates[$activity->activity_date];
		}
		foreach ($c_activity_dates as $k => $v) {
			$t = 100 * $v;
			$c_activity_dates[$k] = $t / $hh_total;
		}
		if (sizeof($activities_data) > 0) {
			$data[0]->first_activity = $first_activity;
			$data[0]->last_activity = $activity;
		}
		$data[0]->statistics = $c_activity_dates;
		$data[0]->hh_total = $hh_total;
		//($activity_data);
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_js('...')
			),
		);
		$this->load->helper('form');
		if ($this->input->post('update')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('code', 'Código', 'trim|required');
			$this->form_validation->set_rules('address_street', 'Calle', 'trim|required');
			$this->form_validation->set_rules('address_number', 'Número', 'trim|required');
			$this->form_validation->set_rules('address_city', 'Ciudad/Comuna', 'trim|required');
			$this->form_validation->set_rules('fk_client', 'Cliente', 'required');
			$this->form_validation->set_rules('fk_contractor', 'Mandante', 'required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Editar Rol Especialidad', $add_lib));
				$this->load->view(SPATH . 'building_site_edit_structure', array('user' => $user[0], 'data' => $data[0], 'clients' => $clients_array, 'contractors' => $contractors_array, 'specialities' => $specialities, 'areas' => $areas, 'milestones' => $milestones, 'workers' => $workers, 'activities_data' => $activities_data, 'activity_dates' => $activity_dates, 'c_activity_dates' => $c_activity_dates));
				$this->load->view(CPATH . 'foot');
			} else {
				$config = $this->web->get_upload_config('spreadsheet');
				$config['upload_path'] = utf8_decode($config['upload_path']);
				if (!is_dir($config['upload_path'])) {
					mkdir($config['upload_path'], 0777, true);
				}
				$this->load->library('upload', $config);
				$this->upload->do_upload($data[0]->data_file);
				if ($this->upload->data('file_size') != null) {
					if ($data[0]->current_version == 0) {
						$v_offset = 2;
						$filepath = $this->upload->data('file_path');
						$rawname = $this->upload->data('raw_name');
						$fileext = $this->upload->data('file_ext');
						$inputFileName = ($filepath . $rawname . $fileext);
						switch ($fileext) {
							case '.xls':
								$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
								break;
							case '.xlsx':
								$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
								break;
						}
						$reader->setReadDataOnly(true);
						$worksheetData = $reader->listWorksheetInfo($inputFileName);
						$totalRows = $worksheetData[0]['totalRows'];
						$totalCols = $worksheetData[0]['totalColumns'];
						$spreadsheet = $reader->load($inputFileName);
						$spreadsheet->setActiveSheetIndex(0);
						$worksheet = $spreadsheet->getActiveSheet();
						$cellArray_b = $worksheet
							->rangeToArray(
								'A1:' . 'L1',
								NULL,
								TRUE,
								TRUE,
								TRUE
							);
						$cellArray_b = $cellArray_b['1'];
						$this->building_site->configurar_diario(
							$building_site_id,
							$cellArray_b['A'],
							$cellArray_b['B'],
							$cellArray_b['C'],
							$cellArray_b['D'],
							$cellArray_b['E'],
							$cellArray_b['F'],
							$cellArray_b['G'],
							$cellArray_b['H'],
							$cellArray_b['I'],
							$cellArray_b['J'],
							$cellArray_b['K'],
							$cellArray_b['L']
						);
						$cellArray = $worksheet
							->rangeToArray(
								'A4:' . 'L' . ($totalRows),
								NULL,
								TRUE,
								TRUE,
								TRUE
							);
						$indexes = array();
						$ind_area = array();
						$ind_zone = array();
						$ind_esp = array();
						$ind_act = array();
						$ind_unt = array();
						$ind_qty = array();
						$ind_eff = array();
						$ind_code = array();
						$ind_calendario = array();
						foreach ($cellArray as $k => $v) {
							if ($v['A'] != '') {
								$indexes[] = intval($k) - 1;
								$ind_area[intval($k) - 1] = trim($v['A']);
								$ind_zone[intval($k) - 1] = trim($v['B']);
								$ind_esp[intval($k) - 1] = trim($v['C']);
								$ind_act[intval($k) - 1] = trim($v['F']);
								$ind_unt[intval($k) - 1] = trim(strtolower($v['I']));
								$ind_qty[intval($k) - 1] = trim($v['J']);
								$ind_eff[intval($k) - 1] = trim($v['K']);
								$ind_sup[intval($k) - 1] = trim($v['D']);
								$ind_code[intval($k) - 1] = trim($v['E']);
							}
						}
						$i = 0;
						$currentArea = '';
						$currentZone = '';
						$currentSpeciality = '';
						foreach ($cellArray as $k => $v) {
							$registro = new stdClass;

							$currentArea = $ind_area[$indexes[$i]] == '' ? $currentArea : $ind_area[$indexes[$i]];
							$currentZone = $ind_zone[$indexes[$i]] == '' ? $currentZone : $ind_zone[$indexes[$i]];
							$currentSpeciality = $ind_esp[$indexes[$i]] == '' ? $currentSpeciality : $ind_esp[$indexes[$i]];

							$registro->area = $currentArea;
							$registro->zona = $currentZone;
							$registro->especialidad = $currentSpeciality;
							$registro->actividad = $ind_act[$indexes[$i]];
							$registro->rol = trim($v['H']);
							$registro->act_ind = $indexes[$i];
							$registro->rol_ind = intval($k) - 1;
							$registro->unid = $ind_unt[$indexes[$i]];
							$registro->cant = $ind_qty[$indexes[$i]];
							$registro->rend = $ind_eff[$indexes[$i]];
							$registro->sup = $ind_sup[$indexes[$i]];
							$registro->code = str_replace(',', '.', $ind_code[$indexes[$i]]);
							$area = $this->area->obtener(
								array(
									array(
										'fk_building_site' => $building_site_id,
										'name' => $registro->area
									)
								)
							);
							if (sizeof($area) == 0) {
								$aid = $this->area->generar($building_site_id, $registro->area);
							} else {
								$aid = $area[0]->id;
							}
							$zone = $this->zone->obtener(
								array(
									array(
										'fk_building_site' => $building_site_id,
										'fk_area' => $aid,
										'name' => trim($registro->zona)
									)
								)
							);
							if (sizeof($zone) == 0) {
								$zid = $this->zone->generar($building_site_id, $aid, $registro->zona);
							} else {
								$zid = $zone[0]->id;
							}
							$speciality = $this->speciality->obtener(
								array(
									array(
										'fk_building_site' => $building_site_id,
										'name' => $registro->especialidad
									)
								)
							);
							if (sizeof($speciality) == 0) {
								$sid = $this->speciality->generar($building_site_id, $registro->especialidad);
							} else {
								$sid = $speciality[0]->id;
							}
							if (trim($registro->rol) != '') {
								$speciality_role = $this->speciality_role->obtener(
									array(
										array(
											'fk_speciality' => $sid,
											'name' => $registro->rol,
											'fk_building_site' => $building_site_id
										)
									)
								);
								if (sizeof($speciality_role) == 0) {
									$srid = $this->speciality_role->generar($building_site_id, $sid, $registro->rol);
								} else {
									$srid = $speciality_role[0]->id;
								}
								$ind_calendario[] = $registro;
								$this->activity->generar($building_site_id, $sid, $srid, $zid, $registro->actividad, $registro->rol_ind, $registro->unid, $registro->cant, $registro->rend, $registro->code);
								$supervisor_user = $this->user->obtener(
									array(
										array(
											'email' => trim(strtolower($registro->sup))
										)
									)
								);
								$this->load->model('supervisor');
								if (sizeof($supervisor_user) == 0) {
									$rol_supervisores = $this->role->obtener(
										array(
											array(
												'value_p' => 'Supervisores'
											)
										)
									);
									$supervisor_id = $this->user->generar(trim(strtolower($registro->sup)));
									$supervisor_user = $this->user->obtener(
										array(
											array(
												'id'
												=> $supervisor_id
											)
										)
									);
									$this->user->establecer_rol($supervisor_id, $rol_supervisores[0]->id);
								}
								$supervisor = $this->supervisor->obtener(
									array(
										array(
											'fk_user' => $supervisor_user[0]->id,
											'fk_speciality' => $sid
										)
									)
								);
								if (sizeof($supervisor) == 0) {
									$this->supervisor->insertar($sid, $supervisor_user[0]->id);
								}
							}
							if ($i < sizeof($indexes) - 1 && intval($k) - 2 > $indexes[$i]) {
								$i++;
							}
						}
						$activities = $this->activity->obtener_ordenado(
							array(
								array(
									'fk_building_site' => $building_site_id
								)
							)
						);
						foreach ($activities as $v) {
							$activity_index = intval($v->f_data) + 1;
							$cellArray_hh = $worksheet
								->rangeToArray(
									'Q' . $activity_index . ':' . $worksheetData[0]['lastColumnLetter'] . $activity_index,
									NULL,
									TRUE,
									TRUE,
									TRUE
								);
							$cell = $cellArray_hh[$activity_index];

							foreach ($cell as $k => $vv) {
								if ($vv != NULL) {
									$speciality_role = $this->speciality_role->obtener_ordenado(
										array(
											array(
												'id' => $v->fk_speciality_role
											)
										)
									);
									$date = $worksheet->getCell($k . '3')->getFormattedValue();
									$date = ($date - 25569);
									$this->activity_data->generar($v->id, $v->fk_building_site, $v->fk_speciality, $v->fk_speciality_role, $v->fk_zone, $date, $vv);
									$this->speciality_role->agregar_hh($v->fk_speciality_role, floatval($speciality_role[$v->fk_speciality_role]->hh) + floatval($vv));
								}
							}
						}

						$this->db->set('current_version', 1);
						$this->db->where('id', $building_site_id);
						$this->db->update('building_site');
					} else {

						$activity_data = $this->db->select('*')->from('activity_data')->where('fk_building_site', $building_site_id)->get()->result();

						foreach ($activity_data as $ad) {
							$this->db->set('fk_building_site', $building_site_id);
							$this->db->set('version', $data[0]->current_version + 1);
							$this->db->set('fk_speciality', $ad->fk_speciality);
							$this->db->set('fk_speciality_role', $ad->fk_speciality_role);
							$this->db->set('fk_zone', $ad->fk_zone);
							$this->db->set('fk_activity', $ad->fk_activity);
							$this->db->set('fk_image', $ad->fk_image);
							$this->db->set('has_image', $ad->has_image);
							$this->db->set('activity_date', $ad->activity_date);
							$this->db->set('activity_date_dt', $ad->activity_date_dt);
							$this->db->set('hh', $ad->hh);
							$this->db->set('comment', $ad->comment);
							$this->db->where('id', $ad->id);
							$this->db->insert('activity_data');
						}

						if (true) {
							$v_offset = 2;
							$filepath = $this->upload->data('file_path');
							$rawname = $this->upload->data('raw_name');
							$fileext = $this->upload->data('file_ext');
							$inputFileName = ($filepath . $rawname . $fileext);
							switch ($fileext) {
								case '.xls':
									$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
									break;
								case '.xlsx':
									$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
									break;
							}
							$reader->setReadDataOnly(true);
							$worksheetData = $reader->listWorksheetInfo($inputFileName);
							$totalRows = $worksheetData[0]['totalRows'];
							$totalCols = $worksheetData[0]['totalColumns'];
							$spreadsheet = $reader->load($inputFileName);
							$spreadsheet->setActiveSheetIndex(0);
							$worksheet = $spreadsheet->getActiveSheet();
							$cellArray_b = $worksheet
								->rangeToArray(
									'A1:' . 'L1',
									NULL,
									TRUE,
									TRUE,
									TRUE
								);
							$cellArray_b = $cellArray_b['1'];
							$this->building_site->configurar_diario(
								$building_site_id,
								$cellArray_b['A'],
								$cellArray_b['B'],
								$cellArray_b['C'],
								$cellArray_b['D'],
								$cellArray_b['E'],
								$cellArray_b['F'],
								$cellArray_b['G'],
								$cellArray_b['H'],
								$cellArray_b['I'],
								$cellArray_b['J'],
								$cellArray_b['K'],
								$cellArray_b['L']
							);
							$cellArray = $worksheet
								->rangeToArray(
									'A4:' . 'L' . ($totalRows),
									NULL,
									TRUE,
									TRUE,
									TRUE
								);
							$indexes = array();
							$ind_area = array();
							$ind_zone = array();
							$ind_esp = array();
							$ind_act = array();
							$ind_unt = array();
							$ind_qty = array();
							$ind_eff = array();
							$ind_code = array();
							$ind_calendario = array();
							foreach ($cellArray as $k => $v) {
								if ($v['A'] != '') {
									$indexes[] = intval($k) - 1;
									$ind_area[intval($k) - 1] = trim($v['A']);
									$ind_zone[intval($k) - 1] = trim($v['B']);
									$ind_esp[intval($k) - 1] = trim($v['C']);
									$ind_act[intval($k) - 1] = trim($v['F']);
									$ind_unt[intval($k) - 1] = trim(strtolower($v['I']));
									$ind_qty[intval($k) - 1] = trim($v['J']);
									$ind_eff[intval($k) - 1] = trim($v['K']);
									$ind_sup[intval($k) - 1] = trim($v['D']);
									$ind_code[intval($k) - 1] = trim($v['E']);
								}
							}
							$i = 0;
							foreach ($cellArray as $k => $v) {
								$registro = new stdClass;
								$registro->area = $ind_area[$indexes[$i]];
								$registro->zona = $ind_zone[$indexes[$i]];
								$registro->especialidad = $ind_esp[$indexes[$i]];
								$registro->actividad = $ind_act[$indexes[$i]];
								$registro->rol = trim($v['H']);
								$registro->act_ind = $indexes[$i];
								$registro->rol_ind = intval($k) - 1;
								$registro->unid = $ind_unt[$indexes[$i]];
								$registro->cant = $ind_qty[$indexes[$i]];
								$registro->rend = $ind_eff[$indexes[$i]];
								$registro->sup = $ind_sup[$indexes[$i]];
								$registro->code = str_replace(',', '.', $ind_code[$indexes[$i]]);
								$area = $this->area->obtener(
									array(
										array(
											'fk_building_site' => $building_site_id,
											'name' => $registro->area
										)
									)
								);
								if (sizeof($area) == 0) {
									$aid = $this->area->generar($building_site_id, $registro->area);
								} else {
									$aid = $area[0]->id;
								}
								$zone = $this->zone->obtener(
									array(
										array(
											'fk_building_site' => $building_site_id,
											'fk_area' => $aid,
											'name' => $registro->zona
										)
									)
								);
								if (sizeof($zone) == 0) {
									$zid = $this->zone->generar($building_site_id, $aid, $registro->zona);
								} else {
									$zid = $zone[0]->id;
								}
								$speciality = $this->speciality->obtener(
									array(
										array(
											'fk_building_site' => $building_site_id,
											'name' => $registro->especialidad
										)
									)
								);
								if (sizeof($speciality) == 0) {
									$sid = $this->speciality->generar($building_site_id, $registro->especialidad);
								} else {
									$sid = $speciality[0]->id;
								}
								if ($registro->rol != '') {
									$speciality_role = $this->speciality_role->obtener(
										array(
											array(
												'fk_speciality' => $sid,
												'name' => $registro->rol,
												'fk_building_site' => $building_site_id
											)
										)
									);
									if (sizeof($speciality_role) == 0) {
										$srid = $this->speciality_role->generar($building_site_id, $sid, $registro->rol);
									} else {
										$srid = $speciality_role[0]->id;
									}
									$ind_calendario[] = $registro;
									//$this->activity->generar($building_site_id, $sid, $srid, $zid, $registro->actividad, $registro->rol_ind, $registro->unid, $registro->cant, $registro->rend, $registro->code);
									$activity_to_replace = $this->db->select('*')->from('activity')
										->where('fk_building_site', $building_site_id)
										->where('activity_code', $registro->code)
										->get()->row();
									$activity_data_to_delete = $this->db->select('*')->from('activity_data')
										->where('fk_building_site', $building_site_id)
										->where('fk_activity', $activity_to_replace->id)
										->where('version', $data[0]->current_version)
										->get()->result();
									foreach ($activity_data_to_delete as $adtd) {
										$this->db->where('id', $adtd->id);
										$this->db->delete('activity_data');
									}
									$supervisor_user = $this->user->obtener(
										array(
											array(
												'email' => trim(strtolower($registro->sup))
											)
										)
									);
									$this->load->model('supervisor');
									if (sizeof($supervisor_user) == 0) {
										$rol_supervisores = $this->role->obtener(
											array(
												array(
													'value_p' => 'Supervisores'
												)
											)
										);
										$supervisor_id = $this->user->generar(trim(strtolower($registro->sup)));
										$supervisor_user = $this->user->obtener(
											array(
												array(
													'id'
													=> $supervisor_id
												)
											)
										);
										$this->user->establecer_rol($supervisor_id, $rol_supervisores[0]->id);
									}
									$supervisor = $this->supervisor->obtener(
										array(
											array(
												'fk_user' => $supervisor_user[0]->id,
												'fk_speciality' => $sid
											)
										)
									);
									if (sizeof($supervisor) == 0) {
										$this->supervisor->insertar($sid, $supervisor_user[0]->id);
									}
								}
								if ($i < sizeof($indexes) - 1 && intval($k) - 2 > $indexes[$i]) {
									$i++;
								}
							}
							$activities = $this->activity->obtener_ordenado(
								array(
									array(
										'fk_building_site' => $building_site_id
									)
								)
							);
							foreach ($activities as $v) {
								$activity_index = intval($v->f_data) + 1;
								$cellArray_hh = $worksheet
									->rangeToArray(
										'Q' . $activity_index . ':' . $worksheetData[0]['lastColumnLetter'] . $activity_index,
										NULL,
										TRUE,
										TRUE,
										TRUE
									);
								$cell = $cellArray_hh[$activity_index];

								foreach ($cell as $k => $vv) {
									if ($vv != NULL) {
										$speciality_role = $this->speciality_role->obtener_ordenado(
											array(
												array(
													'id' => $v->fk_speciality_role
												)
											)
										);
										$date = $worksheet->getCell($k . '3')->getFormattedValue();
										$date = ($date - 25569);
										$this->activity_data->generar($v->id, $v->fk_building_site, $v->fk_speciality, $v->fk_speciality_role, $v->fk_zone, $date, $vv);
										$this->speciality_role->agregar_hh($v->fk_speciality_role, floatval($speciality_role[$v->fk_speciality_role]->hh) + floatval($vv));
									}
								}
							}
						}

						$this->db->set(
							'current_version',
							$data[0]->current_version + 1
						);
						$this->db->where('id', $building_site_id);
						$this->db->update('building_site');
					}
				}
				$this->upload->do_upload($data[0]->data_file_workers);
				if ($this->upload->data('file_size') != null) {
					$v_offset = 2;
					$filepath = $this->upload->data('file_path');
					$rawname = $this->upload->data('raw_name');
					$fileext = $this->upload->data('file_ext');
					$inputFileName = ($filepath . $rawname . $fileext);
					switch ($fileext) {
						case '.xls':
							$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
							break;
						case '.xlsx':
							$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
							break;
					}
					$reader->setReadDataOnly(true);
					$worksheetData = $reader->listWorksheetInfo($inputFileName);
					$totalRows = $worksheetData[0]['totalRows'];
					$totalCols = $worksheetData[0]['totalColumns'];
					$spreadsheet = $reader->load($inputFileName);
					$spreadsheet->setActiveSheetIndex(0);
					$worksheet = $spreadsheet->getActiveSheet();
					$cellArray = $worksheet
						->rangeToArray(
							'A1:' . 'F' . $totalRows,
							NULL,
							TRUE,
							TRUE,
							TRUE
						);
					$this->load->model('worker');
					$not_title = false;
					foreach ($cellArray as $v) {
						if ($not_title) {
							if (strtolower(trim($v['A'])) == "directos") {
								$speciality = $v['B'];
								$speciality_role = $v['C'];
								$dni = $v['D'];
								$dni = str_replace('.', '', $dni);
								$dni = str_replace('-', '', $dni);
								$password = substr($dni, 0, 4);
								$email = $v['E'];
								$name = $v['F'];
								$sp = $this->speciality->obtener(
									[
										[
											'fk_building_site' => $building_site_id,
											'name' => $speciality
										]
									]
								);
								if (sizeof($sp) == 0)
									continue;
								$spr = $this->speciality_role->obtener(
									[
										[
											'fk_building_site' => $building_site_id,
											'fk_speciality' => $sp[0]->id,
											'name' => $speciality_role
										]
									]
								);
								if (sizeof($spr) == 0)
									continue;
								$this->worker->generar($building_site_id, $sp[0]->id, $spr[0]->id, $name, $email, $dni, $building_site_id . $password);
							}
						}
						$not_title = true;
					}
				}
				$this->building_site->actualizar($building_site_id);
				redirect('building_sites/edit/' . $building_site_id);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Editar Obra', $add_lib));
			$this->load->view(SPATH . 'building_site_edit_structure', array('user' => $user[0], 'data' => $data[0], 'clients' => $clients_array, 'contractors' => $contractors_array, 'specialities' => $specialities, 'areas' => $areas, 'milestones' => $milestones, 'workers' => $workers));
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
		$this->load->model('role');
		$this->load->model('building_site');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$client_role = $this->role->obtener(
			array(
				array(
					'value_p' => 'Clientes'
				)
			)
		);
		$clients = $this->user->obtener_ordenado(
			array(
				array(
					'fk_role' => $client_role[0]->id
				)
			)
		);
		$clients_array = array();
		foreach ($clients as $k => $v) {
			$clients_array[$k] = $v->first_name . ' ' . $v->last_name;
		}
		$contractor_role = $this->role->obtener(
			array(
				array(
					'value_p' => 'Mandantes'
				)
			)
		);
		$contractors = $this->user->obtener_ordenado(
			array(
				array(
					'fk_role' => $contractor_role[0]->id
				)
			)
		);
		$contractors_array = array();
		foreach ($contractors as $k => $v) {
			$contractors_array[$k] = $v->first_name . ' ' . $v->last_name;
		}
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '...')
			)
		);
		$this->load->helper('form');
		if ($this->input->post('add')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('code', 'Código', 'trim|required');
			$this->form_validation->set_rules('address_street', 'Calle', 'trim|required');
			$this->form_validation->set_rules('address_number', 'Número', 'trim|required');
			$this->form_validation->set_rules('address_city', 'Ciudad/Comuna', 'trim|required');
			$this->form_validation->set_rules('fk_client', 'Cliente', 'required');
			$this->form_validation->set_rules('fk_contractor', 'Mandante', 'required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Obra', $add_lib));
				$this->load->view(SPATH . 'building_site_add_structure', array('user' => $user[0], 'clients' => $clients_array, 'contractors' => $contractors_array));
				$this->load->view(CPATH . 'foot');
			} else {
				$new = $this->building_site->insertar();
				redirect('building_sites/edit/' . $new);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Obra', $add_lib));
			$this->load->view(SPATH . 'building_site_add_structure', array('user' => $user[0], 'clients' => $clients_array, 'contractors' => $contractors_array));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function add_area($building_site_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $building_site_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('area');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$building_sites = $this->building_site->obtener_ordenado();
		$building_sites_array = array();
		foreach ($building_sites as $k => $v) {
			$building_sites_array[$k] = $v->name;
		}
		$user[0]->building_site_id = $building_site_id;
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '...')
			)
		);
		$this->load->helper('form');
		if ($this->input->post('add')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('fk_building_site', 'Obra', 'required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Area', $add_lib));
				$this->load->view(SPATH . 'bs_area_add_structure', array('user' => $user[0], 'building_sites' => $building_sites_array));
				$this->load->view(CPATH . 'foot');
			} else {
				$new = $this->area->insertar();
				redirect('building_sites/edit_area/' . $new);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Area', $add_lib));
			$this->load->view(SPATH . 'bs_area_add_structure', array('user' => $user[0], 'building_sites' => $building_sites_array));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function add_milestone($building_site_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $building_site_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('milestone');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$building_sites = $this->building_site->obtener_ordenado();
		$building_sites_array = array();
		foreach ($building_sites as $k => $v) {
			$building_sites_array[$k] = $v->name;
		}
		$user[0]->building_site_id = $building_site_id;
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '...')
			)
		);
		$this->load->helper('form');
		if ($this->input->post('add')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('type', 'Tipo', 'trim|required');
			$this->form_validation->set_rules('date', 'Fecha', 'trim|required');
			$this->form_validation->set_rules('fk_building_site', 'Obra', 'required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Hito', $add_lib));
				$this->load->view(SPATH . 'bs_milestone_add_structure', array('user' => $user[0], 'building_sites' => $building_sites_array));
				$this->load->view(CPATH . 'foot');
			} else {
				$new = $this->milestone->insertar();
				redirect('building_sites/edit_milestone/' . $new);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Hito', $add_lib));
			$this->load->view(SPATH . 'bs_milestone_add_structure', array('user' => $user[0], 'building_sites' => $building_sites_array));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function add_zone($area_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $area_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('zone');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$user[0]->area_id = $area_id;

		$area = $this->area->obtener(
			array(
				array(
					'id' => $area_id
				)
			)
		);

		$user[0]->building_site_id = $area[0]->fk_building_site;

		$user[0]->building_site = $area[0]->building_site;
		$user[0]->area = $area[0];

		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '...')
			)
		);
		$this->load->helper('form');
		if ($this->input->post('add')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('fk_building_site', 'Obra', 'required');
			$this->form_validation->set_rules('fk_area', 'Area', 'required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Zona', $add_lib));
				$this->load->view(SPATH . 'bs_zone_add_structure', array('user' => $user[0]));
				$this->load->view(CPATH . 'foot');
			} else {
				$new = $this->zone->insertar();
				redirect('building_sites/edit_zone/' . $new);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Zona', $add_lib));
			$this->load->view(SPATH . 'bs_zone_add_structure', array('user' => $user[0]));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function add_speciality($building_site_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $building_site_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('speciality');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$building_sites = $this->building_site->obtener_ordenado();
		$building_sites_array = array();
		foreach ($building_sites as $k => $v) {
			$building_sites_array[$k] = $v->name;
		}
		$user[0]->building_site_id = $building_site_id;
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '...')
			)
		);
		$this->load->helper('form');
		if ($this->input->post('add')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('fk_building_site', 'Obra', 'required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Especialidad', $add_lib));
				$this->load->view(SPATH . 'bs_speciality_add_structure', array('user' => $user[0], 'building_sites' => $building_sites_array));
				$this->load->view(CPATH . 'foot');
			} else {
				$new = $this->speciality->insertar();
				redirect('building_sites/edit_speciality/' . $new);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Especialidad', $add_lib));
			$this->load->view(SPATH . 'bs_speciality_add_structure', array('user' => $user[0], 'building_sites' => $building_sites_array));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function add_speciality_role($speciality_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('role');
		$this->load->model('speciality_role_user');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$specialities = $this->speciality->obtener_ordenado();
		$specialities_array = array();
		foreach ($specialities as $k => $v) {
			$specialities_array[$k] = '[' . $v->building_site->name . '] ' . $v->name;
		}

		$thisSpeciality = $this->speciality->obtener(
			array(
				array(
					'id' => $speciality_id
				)
			)
		)[0];

		$user[0]->speciality_id = $speciality_id;
		$user[0]->building_site_id = $thisSpeciality->fk_building_site;
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '...')
			)
		);
		$this->load->helper('form');
		if ($this->input->post('add')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('hh', 'HH', 'trim|required');
			$this->form_validation->set_rules('fk_speciality', 'Especialidad', 'required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Rol Especialidad', $add_lib));
				$this->load->view(SPATH . 'bs_speciality_role_add_structure', array('user' => $user[0], 'specialities' => $specialities_array));
				$this->load->view(CPATH . 'foot');
			} else {
				$new = $this->speciality_role->insertar();
				redirect('building_sites/edit_speciality_role/' . $new);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Rol Especialidad', $add_lib));
			$this->load->view(SPATH . 'bs_speciality_role_add_structure', array('user' => $user[0], 'specialities' => $specialities_array));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function add_speciality_role_user($speciality_role_id, $user_id)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $user_id == 0 || $speciality_role_id == 0) {
			redirect('login');
		}
		$this->load->model('speciality_role_user');
		$user = $this->speciality_role_user->insertar($speciality_role_id, $user_id);
		redirect('building_sites/edit_speciality_role/' . $speciality_role_id);
	}
	public function edit_area($area_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $area_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('area');
		$this->load->model('zone');
		$this->load->model('activity_data');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$data = $this->area->obtener(
			array(
				array(
					'id' => $area_id
				)
			)
		);
		$zones = $this->zone->obtener(
			[
				[
					'fk_area' => $area_id
				]
			]
		);
		$building_sites = $this->building_site->obtener_ordenado();
		$building_sites_array = array();
		foreach ($building_sites as $k => $v) {
			$building_sites_array[$k] = $v->name;
		}
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_js('...')
			),
		);
		$this->load->helper('form');
		if ($this->input->post('update')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('fk_building_site', 'Obra', 'required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Editar Area', $add_lib));
				$this->load->view(SPATH . 'bs_area_edit_structure', array('user' => $user[0], 'data' => $data[0], 'building_sites' => $building_sites_array, 'zones' => $zones));
				$this->load->view(CPATH . 'foot');
			} else {
				$this->area->actualizar($data[0]->id);
				redirect('building_sites/edit/' . $data[0]->building_site->id);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Editar Area', $add_lib));
			$this->load->view(SPATH . 'bs_area_edit_structure', array('user' => $user[0], 'data' => $data[0], 'building_sites' => $building_sites_array, 'zones' => $zones));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function edit_zone($zone_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $zone_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('zone');
		$this->load->model('activity_data');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$data = $this->zone->obtener(
			array(
				array(
					'id' => $zone_id
				)
			)
		);
		$building_sites = $this->building_site->obtener_ordenado();
		$building_sites_array = array();
		foreach ($building_sites as $k => $v) {
			$building_sites_array[$k] = $v->name;
		}
		//$levels_array = array_reverse( $levels_array, true );
		$activities_data = $this->activity_data->obtener_ordenado_esp(
			array(
				array(
					'fk_zone' => $zone_id
				)
			),
			null,
			null,
			'activity_date',
			'asc'
		);
		$hh_total = 0;
		$activity_dates = array();
		$c_activity_dates = array();
		$lv = 0;
		foreach ($activities_data as $activity) {
			$hh_total += floatval($activity->hh);
			//$activity->formatted_date = gmdate( "d-m-Y", $activity->activity_date * 86400 );
			if (!isset($activity_dates[$activity->activity_date])) {
				$activity_dates[$activity->activity_date] = floatval($activity->hh);
			} else {
				$activity_dates[$activity->activity_date] += floatval($activity->hh);
			}
			$c_activity_dates[$activity->activity_date] = floatval($activity->hh) + $lv;
			$lv = $c_activity_dates[$activity->activity_date];
			//$activity_dates[ $activity->activity_date ] = $activity;
		}
		foreach ($c_activity_dates as $k => $v) {
			$t = 100 * $v;
			$c_activity_dates[$k] = $t / $hh_total;
		}
		$data[0]->statistics = $c_activity_dates;
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_js('...')
			),
		);
		$this->load->helper('form');
		if ($this->input->post('update')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('fk_building_site', 'Obra', 'required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Editar Area', $add_lib));
				$this->load->view(SPATH . 'bs_zone_edit_structure', array('user' => $user[0], 'data' => $data[0], 'building_sites' => $building_sites_array));
				$this->load->view(CPATH . 'foot');
			} else {
				$this->zone->actualizar($data[0]->id);
				redirect('building_sites/edit/' . $data[0]->building_site->id);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Editar Area', $add_lib));
			$this->load->view(SPATH . 'bs_zone_edit_structure', array('user' => $user[0], 'data' => $data[0], 'building_sites' => $building_sites_array));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function edit_milestone($milestone_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $milestone_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('milestone');
		$this->load->model('activity_data');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$data = $this->milestone->obtener(
			array(
				array(
					'id' => $milestone_id
				)
			)
		);
		$building_sites = $this->building_site->obtener_ordenado();
		$building_sites_array = array();
		foreach ($building_sites as $k => $v) {
			$building_sites_array[$k] = $v->name;
		}
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_js('...')
			),
		);
		$this->load->helper('form');
		if ($this->input->post('update')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('type', 'Tipo', 'trim|required');
			$this->form_validation->set_rules('date', 'Fecha', 'trim|required');
			$this->form_validation->set_rules('fk_building_site', 'Obra', 'required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Editar Hito', $add_lib));
				$this->load->view(SPATH . 'bs_milestone_edit_structure', array('user' => $user[0], 'data' => $data[0], 'building_sites' => $building_sites_array));
				$this->load->view(CPATH . 'foot');
			} else {
				$this->milestone->actualizar($data[0]->id);
				redirect('building_sites/edit/' . $data[0]->building_site->id);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Editar Hito', $add_lib));
			$this->load->view(SPATH . 'bs_milestone_edit_structure', array('user' => $user[0], 'data' => $data[0], 'building_sites' => $building_sites_array));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function edit_speciality($speciality_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $speciality_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('speciality');
		$this->load->model('speciality_role');
		$this->load->model('zone');
		$this->load->model('activity_data');
		$this->load->model('supervisor');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$data = $this->speciality->obtener(
			array(
				array(
					'id' => $speciality_id
				)
			)
		);
		$speciality_roles = $this->speciality_role->obtener(
			array(
				array(
					'fk_speciality' => $speciality_id
				)
			)
		);
		$supervisors = $this->supervisor->obtener(
			array(
				array(
					'fk_speciality' => $speciality_id
				)
			)
		);
		$building_sites = $this->building_site->obtener_ordenado();
		$building_sites_array = array();
		foreach ($building_sites as $k => $v) {
			$building_sites_array[$k] = $v->name;
		}
		$activities_data_s = $this->activity_data->obtener_ordenado_esp(
			array(
				array(
					'fk_speciality' => $speciality_id
				)
			),
			null,
			null,
			'activity_date',
			'asc'
		);
		$activity_dates = array();
		$t_activity_dates = array();
		$lv = 0;
		$hh_total = 0;
		foreach ($activities_data_s as $activity) {
			$hh_total += floatval($activity->hh);
			if (!isset($activity_dates[$activity->activity_date])) {
				$activity_dates[$activity->activity_date] = floatval($activity->hh);
			} else {
				$activity_dates[$activity->activity_date] += floatval($activity->hh);
			}
			$t_activity_dates[$activity->activity_date] = floatval($activity->hh) + $lv;
			$lv = $t_activity_dates[$activity->activity_date];
		}
		foreach ($t_activity_dates as $k => $v) {
			$t = 100 * $v;
			$t_activity_dates[$k] = $t / $hh_total;
		}
		$data[0]->statistics = $t_activity_dates;
		$data[0]->role_statistics = array();
		foreach ($speciality_roles as $sr) {
			$activities_data = $this->activity_data->obtener_ordenado_esp(
				array(
					array(
						'fk_speciality_role'
						=> $sr->id
					)
				),
				null,
				null,
				'activity_date',
				'asc'
			);
			$activity_dates = array();
			$c_activity_dates = array();
			$d_activity_dates = array();
			$lv = 0;
			$hh_total = 0;
			foreach ($activities_data as $activity) {
				$hh_total += floatval($activity->hh);
				if (!isset($activity_dates[$activity->activity_date])) {
					$activity_dates[$activity->activity_date] = floatval($activity->hh);
				} else {
					$activity_dates[$activity->activity_date] += floatval($activity->hh);
				}
				$c_activity_dates[$activity->activity_date] = floatval($activity->hh) + $lv;
				$lv = $c_activity_dates[$activity->activity_date];
				$d_activity_dates[$activity->activity_date] = $activity->activity_date;
			}
			foreach ($c_activity_dates as $k => $v) {
				$t = 100 * $v;
				$c_activity_dates[$k] = $t / $hh_total;
			}
			$data[0]->role_statistics[$sr->id] = new stdClass;
			$data[0]->role_statistics[$sr->id]->name = $sr->name;
			foreach ($activities_data_s as $k => $parent_activity) {
				if (!isset($c_activity_dates[$parent_activity->activity_date]) && $parent_activity->activity_date > 0) {
					//$activity_dates[ $parent_activity->activity_date ] = 0;
					$c_activity_dates[$parent_activity->activity_date] = 100;
				}
			}
			$data[0]->role_statistics[$sr->id]->dates = $c_activity_dates;
		}
		//d( $data[0]->role_statistics );
		//$levels_array = array_reverse( $levels_array, true );
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_js('...')
			),
		);
		$this->load->helper('form');
		if ($this->input->post('update')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('fk_building_site', 'Obra', 'required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Editar Especialidad', $add_lib));
				$this->load->view(SPATH . 'bs_speciality_edit_structure', array('user' => $user[0], 'data' => $data[0], 'building_sites' => $building_sites_array, 'speciality_roles' => $speciality_roles, 'supervisors' => $supervisors));
				$this->load->view(CPATH . 'foot');
			} else {
				$this->speciality->actualizar($data[0]->id);
				redirect('building_sites/edit/' . $data[0]->building_site->id);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Editar Especialidad', $add_lib));
			$this->load->view(
				SPATH . 'bs_speciality_edit_structure',
				array(
					'user' => $user[0],
					'data' => $data[0],
					'building_sites' => $building_sites_array,
					'speciality_roles'
					=> $speciality_roles,
					'supervisors' => $supervisors
				)
			);
			$this->load->view(CPATH . 'foot');
		}
	}
	public function edit_speciality_role($speciality_role_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $speciality_role_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('role');
		$this->load->model('speciality_role');
		$this->load->model('speciality_role_user');
		$this->load->model('worker');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$data = $this->speciality_role->obtener(
			array(
				array(
					'id' => $speciality_role_id
				)
			)
		);
		$specialities = $this->speciality->obtener_ordenado();
		$specialities_array = array();
		foreach ($specialities as $k => $v) {
			$specialities_array[$k] = '[' . $v->building_site->name . '] ' . $v->name;
		}
		// $role = $this->role->obtener(
		// 	array(
		// 		array(
		// 			'value_p' => 'Trabajadores'
		// 		)
		// 	)
		// );
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_js('...')
			),
		);
		$this->load->helper('form');
		if ($this->input->post('update')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('hh', 'HH', 'trim|required');
			$this->form_validation->set_rules('fk_speciality', 'Especialidad', 'required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Editar Rol Especialidad', $add_lib));
				$this->load->view(SPATH . 'bs_speciality_role_edit_structure', array('user' => $user[0], 'data' => $data[0], 'specialities' => $specialities_array));
				$this->load->view(CPATH . 'foot');
			} else {
				$this->speciality_role->actualizar($data[0]->id);
				redirect('building_sites/edit_speciality/' . $data[0]->speciality->id);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Editar Rol Especialidad', $add_lib));
			$this->load->view(SPATH . 'bs_speciality_role_edit_structure', array('user' => $user[0], 'data' => $data[0], 'specialities' => $specialities_array));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function p_remove($building_site_id = 0)
	{

		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $building_site_id == 0) {
			redirect('login');
		}

		$this->load->model('user');
		$this->load->model('building_site');
		$this->load->model('speciality');
		$this->load->model('speciality_role');
		$this->load->model('speciality_role_user');
		$this->load->model('zone');
		$this->load->model('area');
		$this->load->model('activity');
		$this->load->model('activity_data');
		$this->load->model('activity_registry');
		$this->load->model('daily_report');

		$a = $this->activity->obtener(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		$ar = $this->area->obtener(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		$z = $this->zone->obtener(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		$s = $this->speciality->obtener(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		$ad = $this->activity_data->obtener(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);

		$ac_r = $this->activity_registry->obtener(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		$dr = $this->daily_report->obtener(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		foreach ($a as $v) {
			$ad = $this->activity_data->obtener(
				array(
					array(
						'fk_activity'
						=> $v->id
					)
				)
			);
			$this->activity->borrar($v->id);
			foreach ($ad as $vv) {
				$this->activity_data->borrar($vv->id);
			}
		}
		foreach ($z as $v) {
			$this->zone->borrar($v->id);
		}
		foreach ($ar as $v) {
			$this->area->borrar($v->id);
		}
		foreach ($s as $v) {
			$sr = $this->speciality_role->obtener(
				array(
					array(
						'fk_speciality'
						=> $v->id
					)
				)
			);
			foreach ($sr as $vv) {
				$sru = $this->speciality_role_user->obtener(
					array(
						array(
							'fk_speciality_role'
							=> $vv->id
						)
					)
				);
				foreach ($sru as $vvv) {
					$this->speciality_role_user->borrar($vvv->id);
				}
				$this->speciality_role->borrar($vv->id);
			}
			$this->speciality->borrar($v->id);
		}
		foreach ($ad as $v) {
			$this->activity_data->borrar($v->id);
		}
		foreach ($ac_r as $v) {
			$this->activity_registry->borrar($v->id);
		}
		foreach ($dr as $v) {
			$this->daily_report->borrar($v->id);
		}

		$this->db->set('current_version', 0);
		$this->db->where('id', $building_site_id);
		$this->db->update('building_site');

		redirect('building_sites/edit/' . $building_site_id);
	}
	public function remove($building_site_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $building_site_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('building_site');
		$this->load->model('speciality');
		$this->load->model('speciality_role');
		$this->load->model('speciality_role_user');
		$this->load->model('zone');
		$this->load->model('activity');
		$this->load->model('activity_data');
		$z = $this->zone->obtener(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		$s = $this->speciality->obtener(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		$a = $this->activity->obtener(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		$ad = $this->activity_data->obtener(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		foreach ($z as $v) {
			$this->zone->borrar($v->id);
		}
		foreach ($a as $v) {
			$this->activity->borrar($v->id);
		}
		foreach ($ad as $v) {
			$this->activity_data->borrar($v->id);
		}
		foreach ($s as $v) {
			$sr = $this->speciality_role->obtener(
				[
					[
						'fk_speciality' => $v->id
					]
				]
			);
			foreach ($sr as $vv) {
				$sru = $this->speciality_role_user->obtener(
					[
						[
							'fk_speciality_role' => $vv->id
						]
					]
				);
				foreach ($sru as $vvv) {
					$this->speciality_role_user->borrar($vvv->id);
				}
				$this->speciality_role->borrar($vv->id);
			}
			$this->speciality->borrar($v->id);
		}
		$this->building_site->borrar($building_site_id);
		redirect('building_sites');
	}
	public function remove_area($area_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $area_id == 0) {
			redirect('login');
		}
		$this->load->model('area');
		$this->load->model('zone');
		$a = $this->area->obtener(
			array(
				array(
					'id' => $area_id
				)
			)
		);
		$this->area->borrar($area_id);
		$z = $this->zone->obtener(
			[
				[
					'fk_area' => $area_id
				]
			]
		);
		foreach ($z as $v) {
			$this->zone->borrar($v->id);
		}
		redirect('building_sites/edit/' . $a[0]->fk_building_site);
	}
	public function remove_zone($zone_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $zone_id == 0) {
			redirect('login');
		}
		$this->load->model('zone');
		$z = $this->zone->obtener(
			array(
				array(
					'id' => $zone_id
				)
			)
		);
		$this->zone->borrar($zone_id);
		redirect('building_sites/edit_area/' . $z[0]->fk_area);
	}
	public function remove_speciality($speciality_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $speciality_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('speciality');
		$this->load->model('speciality_role');
		$this->load->model('speciality_role_user');
		$data = $this->speciality->obtener(
			array(
				array(
					'id' => $speciality_id
				)
			)
		);
		$sr = $this->speciality_role->obtener(
			array(
				array(
					'fk_speciality' => $speciality_id
				)
			)
		);
		foreach ($sr as $v) {
			$sru = $this->speciality_role_user->obtener(
				array(
					array(
						'fk_speciality_role'
						=> $v->id
					)
				)
			);
			foreach ($sru as $vv) {
				$this->speciality_role_user->borrar($vv->id);
			}
			$this->speciality_role->borrar($v->id);
		}
		$this->speciality->borrar($speciality_id);
		redirect('building_sites/edit/' . $data[0]->building_site->id);
	}
	public function remove_speciality_role($speciality_role_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $speciality_role_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('speciality_role');
		$this->load->model('speciality_role_user');
		$data = $this->speciality_role->obtener(
			array(
				array(
					'id' => $speciality_role_id
				)
			)
		);
		$sru = $this->speciality_role_user->obtener(
			array(
				array(
					'fk_speciality_role' => $speciality_role_id
				)
			)
		);
		foreach ($sru as $v) {
			$this->speciality_role_user->borrar($v->id);
		}
		$this->speciality_role->borrar($speciality_role_id);
		redirect('building_sites/edit_speciality/' . $data[0]->speciality->id);
	}
	public function weekly($building_site_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('building_site');
		$this->load->model('weekly_report');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$user[0]->building_site_id = $building_site_id;
		$data = $this->weekly_report->obtener_ordenado(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '../node_modules/datatables/datatables.min.js' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '../node_modules/datatables/datatables.min.css' ),
			)
		);
		$this->load->view(CPATH . 'head', $this->web->get_header('Reportes semanales', $add_lib));
		$this->load->view(SPATH . 'building_site_weekly_list_structure', array('user' => $user[0], 'data' => $data));
		$this->load->view(CPATH . 'foot');
	}
	public function weekly_add($building_site_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('building_site');
		$this->load->model('activity_registry');
		$this->load->model('activity_data');
		$this->load->model('weekly_report');
		$this->load->model('milestone');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$user[0]->building_site_id = $building_site_id;
		$bs = $this->building_site->obtener(
			[
				[
					'id' => $building_site_id
				]
			]
		);
		$data = array();
		$data[0] = new stdClass;
		$data[0]->building_site = $bs[0];
		$data[0]->milestones = $this->milestone->obtener_ordenado(
			[
				[
					'fk_building_site' => $building_site_id
				]
			]
		);
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '...')
			)
		);
		$this->load->helper('form');
		if ($this->input->post('add')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('date', 'Fecha', 'trim|required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Añadir reporte semanal', $add_lib));
				$this->load->view(SPATH . 'building_site_weekly_add_structure', array('user' => $user[0], 'data' => $data[0]));
				$this->load->view(CPATH . 'foot');
			} else {
				$d = new stdClass;
				$d->fd = null;
				$d->ld = null;
				$activities_data = $this->activity_data->obtener_ordenado_esp(
					[
						[
							'fk_building_site' => $building_site_id
						]
					],
					null,
					null,
					'activity_date',
					'asc'
				);

				$total_hh = 0;

				foreach ($activities_data as $v) {
					$total_hh += $v->hh;
				}

				$activities_data_real = $this->activity_registry->obtener_ordenado_esp(
					[
						[
							'fk_building_site' => $building_site_id
						]
					],
					null,
					null,
					'activity_date_f',
					'asc'
				);
				$activities_cat = [];
				$activities_cat_d = [];
				$activities_cat_s = [];
				$activities_cat_st = [];
				$activities_cat_so = [];
				$activities_cat_ro = [];
				foreach ($activities_data as $v) {
					if ($d->fd == null) {
						$d->fd = $v->activity_date;
						$d->ld = $v->activity_date;
					}
					if ($v->activity_date < $d->fd) {
						$d->fd = $v->activity_date;
					}
					if ($d->ld < $v->activity_date) {
						$d->ld = $v->activity_date;
					}
					$n = explode('.', $v->activity->activity_code);
					if (!isset($activities_cat[$n[0]])) {
						$activities_cat[$n[0]] = new stdClass;
						$activities_cat[$n[0]]->id = $n[0];
						$activities_cat[$n[0]]->descripcion = "Tramo " . $n[0];
						$activities_cat[$n[0]]->actividad = [];
					}
					if (!isset($activities_cat[$n[0]]->actividad[$v->activity->activity_code])) {
						$activities_cat[$n[0]]->actividad[$v->activity->activity_code]['p'] = [];
						$activities_cat_d[$n[0]]->actividad[$v->activity->activity_code][$v->activity_date] = [];
						$activities_cat[$n[0]]->actividad[$v->activity->activity_code]['r'] = [];
						$activities_cat[$n[0]]->actividad[$v->activity->activity_code]['rt'] = [];
					}
					// Seccion PO
					$activities_cat[$n[0]]->actividad[$v->activity->activity_code]['p'][] = [
						'nDate' => $v->activity_date,
						'unid' => $v->activity->unt,
						'cant' => number_format(round($v->activity->qty, 2), 2, ',', '.'),
						'rend' => number_format(round($v->activity->eff, 2), 2, ',', '.'),
						'hh' => number_format(round($v->hh, 2), 2, ',', '.'),
						'thh' => 0,
						'name' => $v->activity->name,
						'zone' => $v->activity->zone->name
					];
					$activities_cat_d[$n[0]]->actividad[$v->activity->activity_code][$v->activity_date] = 0;
					$activities_cat_s[$v->activity->speciality->id]->id = $v->activity->speciality->id;
					$activities_cat_s[$v->activity->speciality->id]->name = $v->activity->speciality->name;
					$activities_cat_s[$v->activity->speciality->id]->fk_building_site = $v->activity->speciality->building_site->id;
					if (!isset($activities_cat_st[$v->fk_speciality]))
						$activities_cat_st[$v->fk_speciality] = 0;
					$activities_cat_st[$v->fk_speciality] += $v->hh;
					if (!isset($activities_cat_so[$v->fk_speciality]))
						$activities_cat_so[$v->fk_speciality] = [];
					$activities_cat_so[$v->fk_speciality][] = $v;
				}
				foreach ($activities_data as $v) {
					$n = explode('.', $v->activity->activity_code);
					foreach ($activities_cat[$n[0]]->actividad[$v->activity->activity_code]['p'] as $kd => $vd) {
						$activities_cat[$n[0]]->actividad[$v->activity->activity_code]['p'][$kd]['thh'] += $v->hh;
					}
					$activities_cat[$n[0]]->actividad[$v->activity->activity_code]['p'][$kd]['thh'] = array_map('ClassStatic::dec2', $activities_cat[$n[0]]->actividad[$v->activity->activity_code]['p'][$kd]['thh']);
					$activities_cat_d[$n[0]]->actividad[$v->activity->activity_code][$v->activity_date] += $v->hh;
				}
				foreach ($activities_data as $v) {
					if ($d->fd > $v->activity_date) {
						$d->fd = $v->activity_date;
					}
					if ($d->ld < $v->activity_date) {
						$d->ld = $v->activity_date;
					}
				}
				foreach ($activities_data_real as $v) {
					if ($d->fd > $v->activity_date_f) {
						$d->fd = $v->activity_date_f;
					}
					if ($d->ld < $v->activity_date_f) {
						$d->ld = $v->activity_date_f;
					}
				}
				$d->days = $d->ld - $d->fd;
				$activity_date = $this->input->post('date');
				$cd = strtotime($activity_date) / 86400 - $d->fd;
				$current_week = intval($cd / 7);
				$day_of_week = $cd % 7;
				$report_activities = [];
				$report_activities_code = [];
				$report_activities_s = [];
				$report_activities_r = [];
				if ($cd < 7) {
					foreach ($activities_data as $v) {
						if ($v->activity_date <= strtotime($activity_date) / 86400) {
							$n = explode('.', $v->activity->activity_code);
							if (!isset($report_activities[$n[0]]['prev_week'])) {
								$report_activities[$n[0]]['prev_week'] = [];
							}
							if (!isset($report_activities[$n[0]]['this_week'])) {
								$report_activities[$n[0]]['this_week'] = [];
							}
							if (isset($report_activities_s[$v->fk_speciality]['prev_week'])) {
								$report_activities_s[$v->fk_speciality]['prev_week'] = 0;
								$report_activities_r[$v->fk_speciality]['prev_week'] = 0;
							}
							if (isset($report_activities_s[$v->fk_speciality]['this_week'])) {
								$report_activities_s[$v->fk_speciality]['this_week'] = 0;
								$report_activities_r[$v->fk_speciality]['this_week'] = 0;
							}
							if (!isset($report_activities[$n[0]]['this_week'][$v->activity->activity_code])) {
								$report_activities[$n[0]]['prev_week'][$v->activity->activity_code]['cAvance'] = 0;
								$report_activities[$n[0]]['prev_week'][$v->activity->activity_code]['pAvance'] = 0;
								$report_activities[$n[0]]['prev_week'][$v->activity->activity_code]['hh'] = 0;
								$report_activities[$n[0]]['prev_week'][$v->activity->activity_code]['phh'] = 0;
								$report_activities[$n[0]]['this_week'][$v->activity->activity_code]['cAvance'] = 0;
								$report_activities[$n[0]]['this_week'][$v->activity->activity_code]['pAvance'] = 0;
								$report_activities[$n[0]]['this_week'][$v->activity->activity_code]['hh'] = 0;
								$report_activities[$n[0]]['this_week'][$v->activity->activity_code]['phh'] = 0;
								$report_activities_code[] = $v->activity->activity_code;
							}
						}
					}
					foreach ($activities_data_real as $v) {
						$nr = explode('.', $v->activity_code);
						$report_activities[$nr[0]]['this_week'][$v->activity_code]['cAvance'] += 1;
						$report_activities[$nr[0]]['this_week'][$v->activity_code]['pAvance'] += $v->p_avance;
						$report_activities[$nr[0]]['this_week'][$v->activity_code]['hh'] += $v->hh;
						$report_activities[$nr[0]]['this_week'][$v->activity_code]['phh'] += $activities_cat_d[$nr[0]]->actividad[$v->activity_code][$v->activity_date_f];
						$dd = strtotime($activity_date) / 86400 - strtotime($v->activity_date);
						if ($v->activity_date_f <= strtotime($activity_date) / 86400) {
							if (intval($dd / 7) < $current_week + 1) {
								$report_activities_r[$v->fk_speciality]['prev_week'] += $v->p_avance;
							} elseif (intval($dd / 7) >= $current_week) {
								$report_activities_r[$v->fk_speciality]['this_week'] += $v->p_avance;
							}
						}
						$activities_cat_s[$v->fk_speciality]->info_r = $report_activities_r[$v->fk_speciality];
					}
					foreach ($v->activity->activity_code as $code) {
						$n = explode('.', $code);
						$report_activities[$n[0]]['this_week'][$v->activity->activity_code]['pAvance'] = $report_activities[$n[0]]['this_week'][$v->activity->activity_code]['pAvance'] / $report_activities[$n[0]]['this_week'][$v->activity->activity_code]['cAvance'];
					}
					foreach ($activities_cat_so as $k => $speciality_data) {
						foreach ($speciality_data as $data) {
							$nr = explode('.', $data->activity_code);
							$dd = strtotime($activity_date) / 86400 - strtotime($data->activity_date);
							if ($data->activity_date <= strtotime($activity_date) / 86400) {
								if (intval($dd / 7) < $current_week + 1) {
									$report_activities_s[$k]['prev_week'] += $data->hh;
								} elseif (intval($dd / 7) >= $current_week) {
									$report_activities_s[$k]['this_week'] += $data->hh;
								}
							}
						}
						$activities_cat_s[$k]->info = $report_activities_s[$k];
					}
				} else {
					foreach ($activities_data as $v) {
						if ($v->activity_date <= strtotime($activity_date) / 86400) {
							$n = explode('.', $v->activity->activity_code);
							if (!isset($report_activities[$n[0]]['prev_week'])) {
								$report_activities[$n[0]]['prev_week'] = [];
							}
							if (!isset($report_activities[$n[0]]['this_week'])) {
								$report_activities[$n[0]]['this_week'] = [];
							}
							if (isset($report_activities_s[$v->fk_speciality]['prev_week'])) {
								$report_activities_s[$v->fk_speciality]['prev_week'] = 0;
								$report_activities_r[$v->fk_speciality]['prev_week'] = 0;
							}
							if (isset($report_activities_s[$v->fk_speciality]['this_week'])) {
								$report_activities_s[$v->fk_speciality]['this_week'] = 0;
								$report_activities_r[$v->fk_speciality]['this_week'] = 0;
							}
							if (!isset($report_activities[$n[0]]['this_week'][$v->activity->activity_code])) {
								$report_activities[$n[0]]['prev_week'][$v->activity->activity_code]['cAvance'] = 0;
								$report_activities[$n[0]]['prev_week'][$v->activity->activity_code]['pAvance'] = 0;
								$report_activities[$n[0]]['prev_week'][$v->activity->activity_code]['hh'] = 0;
								$report_activities[$n[0]]['prev_week'][$v->activity->activity_code]['phh'] = 0;
								$report_activities[$n[0]]['this_week'][$v->activity->activity_code]['cAvance'] = 0;
								$report_activities[$n[0]]['this_week'][$v->activity->activity_code]['pAvance'] = 0;
								$report_activities[$n[0]]['this_week'][$v->activity->activity_code]['hh'] = 0;
								$report_activities[$n[0]]['this_week'][$v->activity->activity_code]['phh'] = 0;
								$report_activities_code[] = $v->activity->activity_code;
							}
						}
					}
					foreach ($activities_data_real as $v) {
						$nr = explode('.', $v->activity_code);
						$dd = strtotime($activity_date) / 86400 - strtotime($v->activity_date);
						if (intval($dd / 7) < $current_week + 1) {
							$report_activities[$nr[0]]['prev_week'][$v->activity_code]['cAvance'] += 1;
							$report_activities[$nr[0]]['prev_week'][$v->activity_code]['pAvance'] += $v->p_avance;
							$report_activities[$nr[0]]['prev_week'][$v->activity_code]['hh'] += $v->hh;
							$report_activities[$nr[0]]['prev_week'][$v->activity_code]['phh'] += $activities_cat_d[$nr[0]]->actividad[$v->activity_code][$v->activity_date_f];
							$report_activities_r[$v->fk_speciality]['prev_week'] += $v->p_avance;
						} elseif (intval($dd / 7) >= $current_week) {
							$report_activities[$nr[0]]['this_week'][$v->activity_code]['cAvance'] += 1;
							$report_activities[$nr[0]]['this_week'][$v->activity_code]['pAvance'] += $v->p_avance;
							$report_activities[$nr[0]]['this_week'][$v->activity_code]['hh'] += $v->hh;
							$report_activities[$nr[0]]['this_week'][$v->activity_code]['phh'] += $activities_cat_d[$nr[0]]->actividad[$v->activity_code][$v->activity_date_f];
							$report_activities_r[$v->fk_speciality]['this_week'] += $v->p_avance;
						}
					}
					foreach ($v->activity->activity_code as $code) {
						$n = explode('.', $code);
						$report_activities[$n[0]]['prev_week'][$v->activity->activity_code]['pAvance'] = $report_activities[$n[0]]['prev_week'][$v->activity->activity_code]['pAvance'] / $report_activities[$n[0]]['prev_week'][$v->activity->activity_code]['cAvance'];
						$report_activities[$n[0]]['this_week'][$v->activity->activity_code]['pAvance'] = $report_activities[$n[0]]['this_week'][$v->activity->activity_code]['pAvance'] / $report_activities[$n[0]]['this_week'][$v->activity->activity_code]['cAvance'];
					}
					foreach ($activities_cat_so as $k => $speciality_data) {
						foreach ($speciality_data as $data) {
							$nr = explode('.', $data->activity_code);
							$dd = strtotime($activity_date) / 86400 - strtotime($data->activity_date);
							if ($data->activity_date <= strtotime($activity_date) / 86400) {
								if (intval($dd / 7) < $current_week + 1) {
									$report_activities_s[$k]['prev_week'] += $data->hh;
								} elseif (intval($dd / 7) >= $current_week) {
									$report_activities_s[$k]['this_week'] += $data->hh;
								}
							}
						}
						$activities_cat_s[$k]->info = $report_activities_s[$k];
						$activities_cat_s[$k]->activity_description = $data->activity->name;
					}
				}
				$t_qty = 0;
				$t_hh = 0;
				$activities_data_graph_prog = [];
				$activities_data_graph_real = [];
				$act = [];
				foreach ($activities_data as $v) {
					if (!isset($activities_data_graph_prog[$v->activity_date]))
						$activities_data_graph_prog[$v->activity_date] = 0;
					$activities_data_graph_prog[$v->activity_date] += $v->hh;
					$t_hh += $v->hh;
					if (!isset($act[$v->activity->id])) {
						$act[$v->activity->id] = true;
						$t_qty += $v->activity->qty;
					}
				}
				foreach ($activities_data_real as $v) {
					if (!isset($activities_data_graph_real[$v->activity_date_f]))
						$activities_data_graph_real[$v->activity_date_f] = 0;
					if ($v->activity_date_f <= $activity_date) {
						$activities_data_graph_real[$v->activity_date_f] += $v->p_avance;
					}
				}
				$dt_fd = new DateTime(gmdate("Y-m-d", intval($d->fd) * 86400), new DateTimeZone("America/Santiago"));
				$dt_ld = new DateTime(gmdate("Y-m-d", intval($d->ld) * 86400), new DateTimeZone("America/Santiago"));
				$activities_data_graph_prog_w = [];
				$activities_data_graph_real_w = [];
				$d->weeks = floor($d->days / 7);
				for ($i = $d->fd; $i < $d->ld + 7; $i += 7) {
					for ($ip = 0; $ip < 7; $ip++) {
						if (!isset($activities_data_graph_prog_w[$i]))
							$activities_data_graph_prog_w[$i] = 0;
						$activities_data_graph_prog_w[$i] += $activities_data_graph_prog[$i + $ip] * 100 / $t_hh;
						if ($i > $d->fd && $ip == 0) {
							$activities_data_graph_prog_w[$i] += $activities_data_graph_prog_w[$i - 7];
						}
					}
					for ($ir = 0; $ir < 7; $ir++) {
						if (!isset($activities_data_graph_real_w[$i]))
							$activities_data_graph_real_w[$i] = 0;
						$activities_data_graph_real_w[$i] += $activities_data_graph_real[$i + $ir] * 100 / $t_qty;
						if ($i > $d->fd && $ir == 0) {
							$activities_data_graph_real_w[$i] += $activities_data_graph_real_w[$i - 7];
						}
					}
				}
				$d->fd = $dt_fd->format("d-m-Y");
				$d->ld = $dt_ld->format("d-m-Y");
				$d->cw = $current_week + 1;
				$d->ac = $activities_cat;
				$d->as = $activities_cat_s;
				// trae conflictos al mostrar el informe semanal
				// $d->ra = array_map('ClassStatic::dec2', $report_activities);
				$d->ra = $report_activities;
				$d->rs = $report_activities_s;
				$d->rr = $report_activities_r;
				$d->rst = $activities_cat_st;
				$d->t_qty = $t_qty;
				$d->t_hh = $t_hh;
				$d->g_prog = array_map('ClassStatic::dec2', $activities_data_graph_prog_w);
				$d->g_real = array_map('ClassStatic::dec2', $activities_data_graph_real_w);
				$d->total_hh = $total_hh;
				$d->milestones = $data[0]->milestones;
				$n = 0;
				foreach ($d->g_real as $index => $real) {
					$n++;
					if ($n > $d->cw) {
						$d->g_real[$index] = 0;
					}
				}
				$dr = $this->weekly_report->obtener(
					array(
						array(
							'fk_building_site' => $building_site_id,
							'activity_date' => $activity_date
						)
					)
				);
				$report_no = sizeof($dr) + 1;
				$control_date = $activity_date;
				$new = $this->weekly_report->insertar($building_site_id, $control_date, $report_no, json_encode($d));
				redirect('building_sites/weekly/' . $building_site_id);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Añadir reporte semanal', $add_lib));
			$this->load->view(SPATH . 'building_site_weekly_add_structure', array('user' => $user[0], 'data' => $data[0]));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function weekly_add_new($building_site_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		if (true == true) {
			$this->load->model('user');
			$this->load->model('building_site');
			$this->load->model('activity_registry');
			$this->load->model('activity_data');
			$this->load->model('weekly_report');
			$this->load->model('zone');
			$this->load->model('milestone');
			$user = $this->user->obtener(
				array(
					array(
						'email' => $logged_in->email
					)
				)
			);
			$user[0]->building_site_id = $building_site_id;
			$bs = $this->building_site->obtener(
				array(
					array(
						'id' => $building_site_id
					)
				)
			);
			$data = array();
			$data[0] = new stdClass;
			$data[0]->building_site = $bs[0];
			$add_lib = array(
				'js_lib' => array(
					//asset_js( '...' ),
					asset_js('front.js'),
				),
				'css_lib' => array(
					//asset_css( '...')
				)
			);
			$this->load->helper('form');
		}
		if ($this->input->post('add')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('date', 'Fecha', 'trim|required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Añadir reporte semanal', $add_lib));
				$this->load->view(SPATH . 'building_site_weekly_add_new_structure', array('user' => $user[0], 'data' => $data[0]));
				$this->load->view(CPATH . 'foot');
			} else {
				$date = $this->input->post('date');
				$weeklyData = new stdClass;
				$weeklyData->selectedDate = new DateTime($date, new DateTimeZone('America/Santiago'));

				//Get this buildingSite activity, activity_data and activity_registry entries.

				$buildingSite = $this->building_site->obtener(
					array(
						array(
							'id' => $building_site_id
						)
					)
				);

				$activityData = $this->activity_data->obtener(
					array(
						array(
							'fk_building_site' => $building_site_id
						)
					)
				);

				$total_hh = 0;

				foreach ($activityData as $key => $value) {
					$total_hh += $value->hh;
				}

				$activityRegistry = $this->activity_registry->obtener(
					array(
						array(
							'fk_building_site' => $building_site_id,
							'checked !=' => null,
						)
					)
				);

				$weeklyReport = $this->weekly_report->obtener(
					array(
						array(
							'fk_building_site' => $building_site_id,
							'activity_date' => $weeklyData->selectedDate->format('Y-m-d')
						)
					)
				);

				$specialities = $this->speciality->obtener(
					array(
						array(
							'fk_building_site' => $buildingSite[0]->id
						)
					)
				);

				$lowestWeek = 0;
				$lowestDay = 0;
				$highestWeek = 0;
				$highestWeekR = 0;
				$highestDay = 0;
				$highestDayR = 0;

				//activity_data has a DATETIME parameter named activity_date_dt
				//we need to get the lowest week

				foreach ($activityData as $key => $value) {
					$week = date('YW', strtotime($value->activity_date_dt));
					$date = strtotime($value->activity_date_dt);
					if ($lowestWeek == 0) {
						$lowestWeek = $week;
					} else {
						if ($week < $lowestWeek) {
							$lowestWeek = $week;
						}
					}
					if ($lowestDay == 0) {
						$lowestDay = $date;
					} else {
						if ($date < $lowestDay) {
							$lowestDay = $date;
						}
					}
				}

				//activity_data has a DATETIME parameter named activity_date_dt
				//we need to get the highest week

				foreach ($activityData as $key => $value) {
					$week = date('YW', strtotime($value->activity_date_dt));
					$date = strtotime($value->activity_date_dt);
					if ($highestWeek == 0) {
						$highestWeek = $week;
					} else {
						if ($week > $highestWeek) {
							$highestWeek = $week;
						}
					}
					if ($highestDay == 0) {
						$highestDay = $date;
					} else {
						if ($date > $highestDay) {
							$highestDay = $date;
						}
					}
				}

				//activity_registry has a DATETIME parameter named activity_date
				//we need to get the highest week

				foreach ($activityRegistry as $key => $value) {
					$week = date('YW', strtotime($value->activity_date));
					$date = strtotime($value->activity_date);
					if ($highestWeekR == 0) {
						$highestWeekR = $week;
					} else {
						if ($week > $highestWeek) {
							$highestWeekR = $week;
						}
					}
					if ($highestDayR == 0) {
						$highestDayR = $date;
					} else {
						if ($date > $highestDay) {
							$highestDayR = $date;
						}
					}
				}

				$weeklyData->buildingSite = $buildingSite[0];
				$weeklyData->contractName = $weeklyData->buildingSite->name;
				$weeklyData->lowestWeek = $lowestWeek;
				$weeklyData->highestWeek = max($highestWeek, $highestWeekR);
				$weeklyData->lowestDay = $lowestDay;

				$dayOfWeek1 = date('w', $lowestDay);
				$weeklyData->lowestDayOfWeek = $dayOfWeek1;

				$dayOfWeek2 = date('w', strtotime($weeklyData->selectedDate->format('Y-m-d')));
				$weeklyData->selectedDayOfWeek = $dayOfWeek2;

				$difference = $weeklyData->selectedDayOfWeek - $weeklyData->lowestDayOfWeek;
				$weeklyData->selectedDateMaxDayPreviousWeek = clone $weeklyData->selectedDate;
				//$weeklyData->selectedDateMaxDayPreviousWeek->modify('-' . $difference . ' day');
				$weeklyData->selectedDateMaxDayCurrentWeek = clone $weeklyData->selectedDateMaxDayPreviousWeek;
				$weeklyData->selectedDateMaxDayCurrentWeek->modify('+' . 7 . ' day');
				$weeklyData->selectedDateMaxDayPreviousWeek = $weeklyData->selectedDateMaxDayPreviousWeek->format('Y-m-d');
				$weeklyData->selectedDateMaxDayCurrentWeek = $weeklyData->selectedDateMaxDayCurrentWeek->format('Y-m-d');

				$weeklyData->highestProgrammedDay = $highestDay;
				$weeklyData->highestDay = max($highestDay, $highestDayR);
				$dt1 = new DateTime();
				$dt1->setTimestamp($weeklyData->lowestDay);
				$dt2 = new DateTime();
				$dt2->setTimestamp($weeklyData->highestDay);
				$weeklyData->projectDurationInDays = $dt1->diff($dt2)->format('%a') + 1;

				$weeklyData->projectLowestWeek = $weeklyData->lowestWeek - $weeklyData->lowestWeek + 1;
				$weeklyData->projectHighestWeek = $weeklyData->highestWeek - $weeklyData->lowestWeek + 1;

				//Programmed Working Hours (Project Level)

				$weeklyData->projectTotalProgrammedWorkHoursBeforeCurrentWeek = round(
					$this->db->select('SUM(hh) as total')->from('activity_data')
						->where('fk_building_site', $building_site_id)
						->where('activity_date_dt <', $weeklyData->selectedDateMaxDayPreviousWeek)
						->get()->row()->total,
					2
				);
				$weeklyData->projectTotalProgrammedWorkHoursInCurrentWeek = round(
					$this->db->select('SUM(hh) as total')->from('activity_data')
						->where('fk_building_site', $building_site_id)
						->where('activity_date_dt >=', $weeklyData->selectedDateMaxDayPreviousWeek)
						->where('activity_date_dt <=', $weeklyData->selectedDateMaxDayCurrentWeek)
						->get()->row()->total,
					2
				);
				$weeklyData->projectTotalProgrammedWorkHours = round($weeklyData->projectTotalProgrammedWorkHoursBeforeCurrentWeek + $weeklyData->projectTotalProgrammedWorkHoursInCurrentWeek, 2);
				$weeklyData->projectTotalProgrammedWorkHoursMax = round($total_hh, 2);

				//Real Working Hours (Project Level)

				$weeklyData->projectTotalRealWorkHoursBeforeCurrentWeek = 0;

				$weeklyData->projectTotalRealWorkHoursInCurrentWeek = 0;

				$weeklyData->projectTotalRealWorkHours = round($weeklyData->projectTotalRealWorkHoursBeforeCurrentWeek + $weeklyData->projectTotalRealWorkHoursInCurrentWeek, 2);

				foreach ($specialities as $k => $speciality) {

					unset($speciality->building_site);

					//Programmed Working Hours (Speciality Level)

					$speciality->specialityTotalProgrammedWorkHoursBeforeCurrentWeek = round(
						$this->db->select('SUM(hh) as total')->from('activity_data')
							->where('fk_building_site', $building_site_id)
							->where('fk_speciality', $speciality->id)
							->where('activity_date_dt <', $weeklyData->selectedDateMaxDayPreviousWeek)
							->get()->row()->total,
						2
					);

					$speciality->specialityTotalProgrammedWorkHoursInCurrentWeek = round(
						$this->db->select('SUM(hh) as total')->from('activity_data')
							->where('fk_building_site', $building_site_id)
							->where('fk_speciality', $speciality->id)
							->where('activity_date_dt >=', $weeklyData->selectedDateMaxDayPreviousWeek)
							->where('activity_date_dt <=', $weeklyData->selectedDateMaxDayCurrentWeek)
							->get()->row()->total,
						2
					);

					$speciality->specialityTotalProgrammedWorkHoursMax = round(
						$this->db->select('SUM(hh) as total')->from('activity_data')
							->where('fk_building_site', $building_site_id)
							->where('fk_speciality', $speciality->id)
							->get()->row()->total,
						2
					);

					$speciality->specialityTotalProgrammedWorkHours = round($speciality->specialityTotalProgrammedWorkHoursBeforeCurrentWeek + $speciality->specialityTotalProgrammedWorkHoursInCurrentWeek, 2);

					//Real Working Hours (Speciality Level)

					$speciality->specialityTotalRealWorkHoursBeforeCurrentWeek = 0;

					$speciality->specialityTotalRealWorkHoursInCurrentWeek = 0;

					$speciality->specialityTotalRealWorkHours = round($speciality->specialityTotalRealWorkHoursBeforeCurrentWeek + $speciality->specialityTotalRealWorkHoursInCurrentWeek, 2);

					$specialities[$k] = $speciality;
				}

				$activities = $this->db->select('activity.id as aId, activity.activity_code as code, activity.unt as unt, activity.qty as qty, activity.eff as eff, activity.name as aName, zone.name as zName, area.name as arName, fk_speciality')
					->from('activity')
					->join('zone', 'zone.id = activity.fk_zone')
					->join('area', 'area.id = zone.fk_area')
					->where('activity.fk_building_site', $building_site_id)
					->order_by('zone.id', 'ASC')
					->get()->result();

				$weeklyData->activitiesResume = new stdClass;
				$weeklyData->activitiesResume->activityProjectProgrammedWorkHours = 0;

				$weeklyData->activitiesResume->activityProjectQuantityBeforeCurrentWeek = 0;
				$weeklyData->activitiesResume->activityProjectQuantityInCurrentWeek = 0;
				$weeklyData->activitiesResume->activityProjectQuantity = 0;

				$weeklyData->activitiesResume->activityProjectSavedHHBeforeCurrentWeek = 0;
				$weeklyData->activitiesResume->activityProjectSavedHHInCurrentWeek = 0;
				$weeklyData->activitiesResume->activityProjectSavedHH = 0;

				$weeklyData->activitiesResume->activityProjectTotalQuantity = 0;

				$weeklyData->activitiesResume->activityProjectWorkHoursBeforeCurrentWeek = 0;
				$weeklyData->activitiesResume->activityProjectWorkHoursInCurrentWeek = 0;
				$weeklyData->activitiesResume->activityProjectWorkHours = 0;

				foreach ($activities as $k => $activity) {

					$weeklyData->activitiesResume->activityProjectTotalQuantity += $activity->qty;

					//Programmed Working Hours (Activity Level)

					$activity->activityTotalProgrammedWorkHoursBeforeCurrentWeek = round(
						$this->db->select('SUM(hh) as total')->from('activity_data')
							->where('fk_building_site', $building_site_id)
							->where('fk_activity', $activity->aId)
							->where('activity_date_dt <', $weeklyData->selectedDateMaxDayPreviousWeek)
							->get()->row()->total,
						2
					);

					$activity->activityTotalProgrammedWorkHoursInCurrentWeek = round(
						$this->db->select('SUM(hh) as total')->from('activity_data')
							->where('fk_building_site', $building_site_id)
							->where('fk_activity', $activity->aId)
							->where('activity_date_dt >=', $weeklyData->selectedDateMaxDayPreviousWeek)
							->where('activity_date_dt <=', $weeklyData->selectedDateMaxDayCurrentWeek)
							->get()->row()->total,
						2
					);

					$activity->activityTotalProgrammedWorkHours = round($activity->activityTotalProgrammedWorkHoursBeforeCurrentWeek + $activity->activityTotalProgrammedWorkHoursInCurrentWeek, 2);

					//Real Working Hours (Activity Level)

					$activity->activityTotalRealWorkHoursBeforeCurrentWeek = round(
						$this->db->select('SUM(hh) as total')->from('activity_registry')
							->where('fk_building_site', $building_site_id)
							->where('fk_activity', $activity->aId)
							->where('activity_date <', $weeklyData->selectedDateMaxDayPreviousWeek)
							->where('checked !=', null)
							->get()->row()->total,
						2
					);

					$weeklyData->activitiesResume->activityProjectWorkHoursBeforeCurrentWeek += $activity->activityTotalRealWorkHoursBeforeCurrentWeek;

					$activity->activityTotalRealWorkHoursInCurrentWeek = round(
						$this->db->select('SUM(hh) as total')->from('activity_registry')
							->where('fk_building_site', $building_site_id)
							->where('fk_activity', $activity->aId)
							->where('activity_date >=', $weeklyData->selectedDateMaxDayPreviousWeek)
							->where('activity_date <=', $weeklyData->selectedDateMaxDayCurrentWeek)
							->where('checked !=', null)
							->get()->row()->total,
						2
					);

					$weeklyData->activitiesResume->activityProjectWorkHoursInCurrentWeek += $activity->activityTotalRealWorkHoursInCurrentWeek;

					$activity->activityTotalRealWorkHours = round($activity->activityTotalRealWorkHoursBeforeCurrentWeek + $activity->activityTotalRealWorkHoursInCurrentWeek, 2);

					$weeklyData->activitiesResume->activityProjectWorkHours += $activity->activityTotalRealWorkHours;

					//Quantity (Activity Level)

					$q = $this->db->select('avance as total')->from('activity_registry')
						->where('fk_building_site', $building_site_id)
						->where('fk_activity', $activity->aId)
						->where('activity_date <', $weeklyData->selectedDateMaxDayPreviousWeek)
						->where('checked !=', null)
						->order_by('activity_date', 'DESC')
						->limit(1)
						->get()->row();
					if (!isset($q->total) || $q->total == null) {
						$q = 0;
					} else {
						$q = $q->total;
					}
					$activity->activityTotalQuantityBeforeCurrentWeek = round(
						$q,
						2
					);

					$weeklyData->activitiesResume->activityProjectQuantityBeforeCurrentWeek += $activity->activityTotalQuantityBeforeCurrentWeek;

					$q = $this->db->select('avance as total')->from('activity_registry')
						->where('fk_building_site', $building_site_id)
						->where('fk_activity', $activity->aId)
						->where('activity_date >=', $weeklyData->selectedDateMaxDayPreviousWeek)
						->where('activity_date <=', $weeklyData->selectedDateMaxDayCurrentWeek)
						->where('checked !=', null)
						->order_by('activity_date', 'DESC')
						->limit(1)
						->get()->row();
					if (!isset($q->total) || $q->total == null) {
						$q = 0;
					} else {
						$q = $q->total;
					}

					$activity->activityTotalQuantityInCurrentWeek = round(
						$q,
						2
					);

					$deltaQuantity = $activity->activityTotalQuantityInCurrentWeek - $activity->activityTotalQuantityBeforeCurrentWeek;
					if ($deltaQuantity < 0) {
						$deltaQuantity = 0;
					}
					$activity->activityTotalQuantityInCurrentWeek = round($deltaQuantity, 2);

					$weeklyData->activitiesResume->activityProjectQuantityInCurrentWeek += $activity->activityTotalQuantityInCurrentWeek;

					$activity->activityTotalQuantity = round($activity->activityTotalQuantityBeforeCurrentWeek + $activity->activityTotalQuantityInCurrentWeek, 2);

					$weeklyData->activitiesResume->activityProjectQuantity += $activity->activityTotalQuantity;

					//Real Advance (Activity Level)

					if ($activity->qty > 0) {
						$activity->activityTotalRealAdvanceBeforeCurrentWeek = round($activity->activityTotalQuantityBeforeCurrentWeek / $activity->qty * 100, 2);
						$activity->activityTotalRealAdvanceInCurrentWeek = round($activity->activityTotalQuantityInCurrentWeek / $activity->qty * 100, 2);
						$activity->activityTotalRealAdvance = round(($activity->activityTotalRealAdvanceBeforeCurrentWeek + $activity->activityTotalRealAdvanceInCurrentWeek), 2);
					} else {
						$activity->activityTotalRealAdvanceBeforeCurrentWeek = 0;
						$activity->activityTotalRealAdvanceInCurrentWeek = 0;
						$activity->activityTotalRealAdvance = 0;
					}

					//Saved HH (Activity Level)

					$activity->activityTotalSavedHHBeforeCurrentWeek = round($activity->activityTotalQuantityBeforeCurrentWeek * $activity->activityTotalProgrammedWorkHoursBeforeCurrentWeek, 2);

					$weeklyData->activitiesResume->activityProjectSavedHHBeforeCurrentWeek += $activity->activityTotalSavedHHBeforeCurrentWeek;

					$activity->activityTotalSavedHHInCurrentWeek = round($activity->activityTotalQuantityInCurrentWeek * $activity->activityTotalProgrammedWorkHoursInCurrentWeek, 2);

					$weeklyData->activitiesResume->activityProjectSavedHHInCurrentWeek += $activity->activityTotalSavedHHInCurrentWeek;

					$activity->activityTotalSavedHH = round($activity->activityTotalQuantity * $activity->activityTotalProgrammedWorkHours, 2);

					$weeklyData->activitiesResume->activityProjectSavedHH += $activity->activityTotalSavedHH;

					//PF (Activity Level)

					if ($activity->activityTotalSavedHHBeforeCurrentWeek == 0) {
						$activity->activityTotalPFBeforeCurrentWeek = 0;
					} else {
						$activity->activityTotalPFBeforeCurrentWeek = round($activity->activityTotalRealWorkHoursBeforeCurrentWeek / $activity->activityTotalSavedHHBeforeCurrentWeek, 2);
					}
					if ($activity->activityTotalSavedHHInCurrentWeek == 0) {
						$activity->activityTotalPFInCurrentWeek = 0;
					} else {
						$activity->activityTotalPFInCurrentWeek = round($activity->activityTotalRealWorkHoursInCurrentWeek / $activity->activityTotalSavedHHInCurrentWeek, 2);
					}
					if ($activity->activityTotalSavedHH == 0) {
						$activity->activityTotalPF = 0;
					} else {
						$activity->activityTotalPF = round($activity->activityTotalRealWorkHours / $activity->activityTotalSavedHH, 2);
					}

					//Total Programmed HH

					$q = $this->db->select('SUM(hh) as total')->from('activity_data')
						->where('fk_building_site', $building_site_id)
						->where('fk_activity', $activity->aId)
						->limit(1)
						->get()->row();

					if (!isset($q->total) || $q->total == null) {
						$q = 0;
					} else {
						$q = $q->total;
					}

					$activity->activityProjectProgrammedWorkHours = round($q, 2);
					$weeklyData->activitiesResume->activityProjectProgrammedWorkHours += $activity->activityProjectProgrammedWorkHours;

					$activities[$k] = $activity;
				}

				if ($weeklyData->activitiesResume->activityProjectSavedHHBeforeCurrentWeek > 0) {
					$weeklyData->activitiesResume->activityPFBeforeCurrentWeek = round($weeklyData->activitiesResume->activityProjectWorkHoursBeforeCurrentWeek / $weeklyData->activitiesResume->activityProjectSavedHHBeforeCurrentWeek, 2);
				} else {
					$weeklyData->activitiesResume->activityPFBeforeCurrentWeek = 0;
				}

				if ($weeklyData->activitiesResume->activityProjectSavedHHInCurrentWeek > 0) {
					$weeklyData->activitiesResume->activityPFInCurrentWeek = round($weeklyData->activitiesResume->activityProjectWorkHoursInCurrentWeek / $weeklyData->activitiesResume->activityProjectSavedHHInCurrentWeek, 2);
				} else {
					$weeklyData->activitiesResume->activityPFInCurrentWeek = 0;
				}

				if ($weeklyData->activitiesResume->activityProjectSavedHH > 0) {
					$weeklyData->activitiesResume->activityPF = round($weeklyData->activitiesResume->activityProjectWorkHours / $weeklyData->activitiesResume->activityProjectSavedHH, 2);
				} else {
					$weeklyData->activitiesResume->activityPF = 0;
				}

				$aAn2 = 0;
				$a2 = 0;
				foreach ($activities as $activity) {
					$aAn2 += round($activity->activityProjectProgrammedWorkHours * $activity->activityTotalRealAdvanceBeforeCurrentWeek / 100, 2);
					$a2 += round($activity->activityProjectProgrammedWorkHours * $activity->activityTotalRealAdvanceInCurrentWeek / 100, 2);
				}

				$weeklyData->projectTotalRealWorkHoursBeforeCurrentWeek = $aAn2 / $weeklyData->activitiesResume->activityProjectProgrammedWorkHours;

				$weeklyData->projectTotalRealWorkHoursInCurrentWeek = $a2 / $weeklyData->activitiesResume->activityProjectProgrammedWorkHours;

				foreach($specialities as $key => $speciality) {
					$aAn2 = 0;
					$a2 = 0;
					foreach($activities as $activity) {
						if($activity->fk_speciality == $speciality->id) {
							$aAn2 += round($activity->activityProjectProgrammedWorkHours * $activity->activityTotalRealAdvanceBeforeCurrentWeek / 100, 2);
							$a2 += round($activity->activityProjectProgrammedWorkHours * $activity->activityTotalRealAdvanceInCurrentWeek / 100, 2);
						}
					}
					$speciality->specialityTotalRealWorkHoursBeforeCurrentWeek = $aAn2 / $weeklyData->activitiesResume->activityProjectProgrammedWorkHours;
					$speciality->specialityTotalRealWorkHoursInCurrentWeek = $a2 / $weeklyData->activitiesResume->activityProjectProgrammedWorkHours;
				}

				$weeklyData->activities = $activities;
				$weeklyData->specialities = $specialities;
				$weeklyData->reportNumber = sizeof($weeklyReport) + 1;
				$milestones = $this->milestone->obtener_ordenado(
					[
						[
							'fk_building_site' => $building_site_id
						]
					]
				);

				$weeklyData->milestones = [];

				foreach ($milestones as $key => $value) {
					$weeklyData->milestones[$key] = new stdClass;
					$weeklyData->milestones[$key]->name = $value->name;
					$weeklyData->milestones[$key]->date = $value->date;
					$weeklyData->milestones[$key]->type = $value->type;
				}

				$control_date = $weeklyData->selectedDate->format('Y-m-d');
				$converted = clone $weeklyData;
				unset($converted->buildingSite);

				$new = $this->weekly_report->insertar($building_site_id, $control_date, $weeklyData->reportNumber, json_encode($converted));

				$fields = ['field1', 'field2', 'field3', 'field4'];

				$this->db->set('field1', $this->input->post('campo1'));
				$this->db->set('field2', $this->input->post('campo2'));
				$this->db->set('field3', $this->input->post('campo3'));
				$this->db->set('field4', $this->input->post('campo4'));
				$this->db->where('id', $new);
				$this->db->update('weekly_report');

				redirect('building_sites/weekly/' . $building_site_id);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Añadir reporte semanal', $add_lib));
			$this->load->view(SPATH . 'building_site_weekly_add_new_structure', array('user' => $user[0], 'data' => $data[0]));
			$this->load->view(CPATH . 'foot');
		}
	}

	/**
	 * 
	 */
	public function weekly_view_pdf($weekly_report_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('building_site');
		$this->load->model('weekly_report');
		$this->load->model('activity_data');
		$this->load->model('activity_registry');
		$this->load->model('speciality_role');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$user[0]->weekly_report_id = $weekly_report_id;
		$data = $this->weekly_report->obtener(
			array(
				array(
					'id' => $weekly_report_id
				)
			)
		);
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '../node_modules/datatables/datatables.min.js' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '../node_modules/datatables/datatables.min.css' ),
			)
		);
		$this->load->view(CPATH . 'head', $this->web->get_header('Obras', $add_lib));
		$this->load->view(SPATH . 'building_site_weekly_view_pdf_v2', array('user' => $user[0], 'data' => $data[0]));
		$this->load->view(CPATH . 'foot');
		/*
																																																																				$this->load->library('pdfgenerator');
																																																																				$filename = 'reporte-diario-' . $daily_report_id;
																																																																				$this->pdfgenerator->generate($html, $filename, true, 'A3', 'landscape');
																																																																				*/
	}

	public function weekly_view_pdf_new($weekly_report_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('building_site');
		$this->load->model('weekly_report');
		$this->load->model('activity_data');
		$this->load->model('activity_registry');
		$this->load->model('speciality_role');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$user[0]->weekly_report_id = $weekly_report_id;
		$data = $this->weekly_report->obtener(
			array(
				array(
					'id' => $weekly_report_id
				)
			)
		);
		d($data);

		$add_lib = array(
			'js_lib' => array(
				//asset_js( '../node_modules/datatables/datatables.min.js' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '../node_modules/datatables/datatables.min.css' ),
			)
		);
		$this->load->view(CPATH . 'head', $this->web->get_header('Obras', $add_lib));
		$this->load->view(SPATH . 'building_site_weekly_view_pdf_new', array('user' => $user[0], 'data' => $data[0]));
		$this->load->view(CPATH . 'foot');
	}

	public function weekly_view_pdf_new_pdo($weekly_report_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('building_site');
		$this->load->model('weekly_report');
		$this->load->model('activity_data');
		$this->load->model('activity_registry');
		$this->load->model('speciality_role');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$user[0]->weekly_report_id = $weekly_report_id;
		$data = $this->weekly_report->obtener(
			array(
				array(
					'id' => $weekly_report_id
				)
			)
		);

		$add_lib = array(
			'js_lib' => array(
				//asset_js( '../node_modules/datatables/datatables.min.js' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '../node_modules/datatables/datatables.min.css' ),
			)
		);
		$this->load->view(CPATH . 'head', $this->web->get_header('Obras', $add_lib));
		$this->load->view(SPATH . 'building_site_weekly_view_pdf_new_pdo', array('user' => $user[0], 'data' => $data[0]));
		$this->load->view(CPATH . 'foot');
	}

	public function report($building_site_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('building_site');
		$this->load->model('daily_report');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$user[0]->building_site_id = $building_site_id;
		$data = $this->daily_report->obtener_ordenado(
			array(
				array(
					'fk_building_site' => $building_site_id
				)
			)
		);
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '../node_modules/datatables/datatables.min.js' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '../node_modules/datatables/datatables.min.css' ),
			)
		);
		$this->load->view(CPATH . 'head', $this->web->get_header('Reportes', $add_lib));
		$this->load->view(SPATH . 'building_site_report_list_structure', array('user' => $user[0], 'data' => $data));
		$this->load->view(CPATH . 'foot');
	}
	public function report_add($building_site_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('building_site');
		$this->load->model('activity_registry');
		$this->load->model('worker_activity');
		$this->load->model('daily_report');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$user[0]->building_site_id = $building_site_id;
		$bs = $this->building_site->obtener(
			array(
				array(
					'id' => $building_site_id
				)
			)
		);
		$data = array();
		$data[0] = new stdClass;
		$data[0]->building_site = $bs[0];
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '...')
			)
		);
		$this->load->helper('form');
		if ($this->input->post('add')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('date', 'Fecha', 'trim|required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Añadir reporte diario', $add_lib));
				$this->load->view(SPATH . 'building_site_report_add_structure', array('user' => $user[0], 'data' => $data[0]));
				$this->load->view(CPATH . 'foot');
			} else {
				//$ts = DateTime::createFromFormat('!d/m/Y', $this->input->post('date') )->getTimestamp();
				//$activity_date = round( $ts / 86400 );
				$dt = explode('/', $this->input->post('date'));
				$dt = array_reverse($dt);
				$activity_date = implode('-', $dt);
				$dr = $this->daily_report->obtener(
					array(
						array(
							'fk_building_site' => $building_site_id,
							'activity_date' => $activity_date
						)
					)
				);
				$report_no = sizeof($dr) + 1;
				$contract_name = $bs[0]->name;
				$contract_no = $bs[0]->code;
				$important_activities = [];
				$activities_data = $this->activity_registry->obtener(
					array(
						array(
							'fk_building_site' => $building_site_id,
							'activity_date' => $activity_date
						)
					)
				);
				$activity_array = array();
				$c_hh = 0;
				$c_w = '';
				$r_w = array();
				foreach ($activities_data as $ad) {
					if (!isset($activity_array[$ad->activity->fk_zone])) {
						$activity_array[$ad->activity->fk_zone] = array();
					}
					if (!isset($r_w[$ad->activity->fk_speciality_role])) {
						$r_w[$ad->activity->fk_speciality_role] = new stdClass;
						$r_w[$ad->activity->fk_speciality_role]->hh = 0;
						$r_w[$ad->activity->fk_speciality_role]->workers = 0;
						$r_w[$ad->activity->fk_speciality_role]->comment = [];
						$r_w[$ad->activity->fk_speciality_role]->machinery = [];
					}
					$c_hh += $ad->hh;
					$activity_array[$ad->activity->fk_zone][] = $ad->activity;
					$r_w[$ad->activity->fk_speciality_role]->hh += $ad->hh;
					$r_w[$ad->activity->fk_speciality_role]->workers += $ad->workers;
					//if comment does not exist, add it
					if (!in_array($ad->comment, $r_w[$ad->activity->fk_speciality_role]->comment)) {
						$r_w[$ad->activity->fk_speciality_role]->comment[] = $ad->comment;
					}
					//$r_w[$ad->activity->fk_speciality_role]->machinery[] = $ad->machinery;
					if (!in_array($ad->machinery, $r_w[$ad->activity->fk_speciality_role]->machinery)) {
						$r_w[$ad->activity->fk_speciality_role]->machinery[] = $ad->machinery;
					}
				}
				$c_w = json_encode($r_w);
				foreach ($activity_array as $zones) {
					$ia = new stdClass;
					$name = $zones[0]->zone->name;
					$area = $this->db->select('name')->from('area')->where('id', $zones[0]->zone->fk_area)->get()->row();
					$ags = [];
					$ta = [];
					foreach ($zones as $activity) {
						$ag = $this->activity_registry->obtener(
							array(
								array(
									'fk_building_site' => $building_site_id,
									'activity_date' => $activity_date,
									'fk_activity' => $activity->id
								)
							)
						);
						foreach ($ag as $a) {
							if (!isset($ta[$a->activity->activity_code])) {
								$t = (object) [
									'activity' => $activity->name,
									'activity_code' => $activity->activity_code,
									'hh' => $a->hh,
									'p_avance' => $a->p_avance,
									'workers' => $a->workers,
									'comment' => trim($a->comment),
									'machinery' => trim($a->machinery),
									'worker_list' => []
								];

								$t->worker_list = $this->db->select('worker_activity.hh, worker_activity.date, worker_activity.code, worker.name, worker.email, worker.dni, worker.id as worker_id')->from('worker_activity')
									->where('worker_activity.fk_building_site', $building_site_id)
									->where('worker_activity.date', $activity_date)
									->where('worker_activity.code', $a->activity->activity_code)
									->join('worker', 'worker_activity.fk_worker = worker.id')
									//->join('speciality', 'worker.fk_speciality = speciality.id')
									//->join('speciality_role', 'worker.fk_speciality_role = speciality_role.id')
									->get()->result();

							} else {

								$t = $ta[$a->activity->activity_code];

								if ($a->comment != '' && $a->comment != null) {
									//$t->comment .= ', ' . $a->comment;
									//check that $a->comment does not exist in $t->comment string

									if (strpos($t->comment, trim($a->comment)) === false) {
										$t->comment .= ', ' . trim($a->comment);
									}
								}
								if ($a->machinery != '' && $a->machinery != null) {
									//$t->machinery .= ', ' . $a->machinery;
									//check that $a->machinery does not exist in $t->machinery string
									if (strpos($t->machinery, trim($a->machinery)) === false) {
										$t->machinery .= ', ' . trim($a->machinery);
									}
								}
							}
							$ta[$a->activity->activity_code] = $t;
						}
					}
					$ia->name = $name;
					$ia->activities = $ags;
					$important_activities[] = (object) [
						'area' => $area->name,
						'name' => $name,
						'activities' => $ta
					];
				}

				$dt = new DateTime('NOW', new DateTimeZone('America/Santiago'));
				$emission_date = $dt->format('Y-m-d');
				$control_date = $activity_date;

				$new = $this->daily_report->insertar($building_site_id, $activity_date, $report_no, $contract_name, $contract_no, json_encode($important_activities), $control_date, $emission_date);
				$this->daily_report->configurar_diario(
					$new,
					$this->input->post('b1_ne'),
					$this->input->post('b1_n'),
					$this->input->post('b1_c'),
					$this->input->post('b2_ne'),
					$this->input->post('b2_n'),
					$this->input->post('b2_c'),
					$this->input->post('b3_ne'),
					$this->input->post('b3_n'),
					$this->input->post('b3_c'),
					$this->input->post('b4_ne'),
					$this->input->post('b4_n'),
					$this->input->post('b4_c'),
					$c_hh,
					$c_w
				);
				//d( $this->daily_report->obtener( [['id' => $new]] ) );
				redirect('building_sites/report_gallery/' . $new);
				//redirect('building_sites/report_view/' . $new);
				//redirect('building_sites/report/' . $building_site_id );
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Añadir reporte diario', $add_lib));
			$this->load->view(SPATH . 'building_site_report_add_structure', array('user' => $user[0], 'data' => $data[0]));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function report_gallery($daily_report_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('building_site');
		$this->load->model('activity_registry');
		$this->load->model('daily_report');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$user[0]->daily_report_id = $daily_report_id;
		$dr = $this->daily_report->obtener(
			array(
				array(
					'id' => $daily_report_id
				)
			)
		);
		$data = array();
		$data[0] = new stdClass;
		$data[0]->daily_report = $dr[0];
		$ar = $this->activity_registry->obtener(
			[
				[
					'fk_building_site' => $dr[0]->fk_building_site,
					'activity_date' => $dr[0]->activity_date
				]
			]
		);
		$data[0]->activity_registry = $ar;
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '...')
			)
		);
		$this->load->helper('form');
		if ($this->input->post('add')) {
			$images = [];
			$r = $this->input->post('images');
			foreach ($r as $ar) {
				$images[] = $ar;
			}
			if (sizeof($images) > 0) {
				$this->daily_report->actualizar_galeria($daily_report_id, $images);
				redirect('building_sites/report_view_pdf/' . $daily_report_id);
			} else {
				redirect('building_sites/report_view_pdf/' . $daily_report_id);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Añadir reporte diario - galería', $add_lib));
			$this->load->view(SPATH . 'building_site_report_gallery_structure', array('user' => $user[0], 'data' => $data[0]));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function report_view($daily_report_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('building_site');
		$this->load->model('daily_report');
		$this->load->model('activity_data');
		$this->load->model('activity_registry');
		$this->load->model('speciality_role');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$user[0]->daily_report_id = $daily_report_id;
		$data = $this->daily_report->obtener(
			array(
				array(
					'id' => $daily_report_id
				)
			)
		);
		$activities_data = $this->activity_registry->obtener(
			array(
				array(
					'fk_building_site' => $data[0]->fk_building_site,
					'activity_date' => $data[0]->activity_date
				)
			)
		);
		$c_hh = 0;
		$p_hh = 0;
		$comments = array();
		$machines = array();
		$activity_array = array();
		foreach ($activities_data as $ad) {
			if (!isset($activity_array[$ad->activity->fk_zone])) {
				$activity_array[$ad->activity->fk_zone] = array();
			}

			$activity_array[$ad->activity->fk_zone][] = $ad->activity;
		}
		$spr_array = array();
		foreach ($activities_data as $ad) {
			$spr_array[$ad->fk_speciality_role] = new stdClass;
			$spr_array[$ad->fk_speciality_role]->name = $ad->speciality_role->name;
			$spr_array[$ad->fk_speciality_role]->hh = 0;
			$spr_array[$ad->fk_speciality_role]->workers = 0;
		}
		$r_hh = json_decode($data[0]->r_w);
		foreach ($r_hh as $k => $v) {
			$sr = $this->speciality_role->obtener([['id' => $k]]);
			$spr_array[$k]->name = $sr[0]->name;
			$spr_array[$k]->hh = $v->hh;
			$spr_array[$k]->workers = $v->workers;
		}
		//$photo_index = array_rand($activities_data, min(sizeof($activities_data), 4));
		$photos = array();
		$photosIds = array();
		if ($data[0]->images != "0") {
			$images = json_decode($data[0]->images);
			foreach ($images as $registry) {
				$i = $this->activity_registry->obtener([
					[
						'fk_building_site' => $data[0]->fk_building_site,
						'id' => $registry
					]
				]);
				$ad = $i[0];
				$t = new stdClass;
				$t->photo = $ad->image->url . $ad->image->id . "/" . $ad->image->name . $ad->image->ext;
				$photos[] = $t;
				$photosIds[] = $ad->image->id;
			}
		}
		$c_hh = $data[0]->r_hh;
		$activities_data_o = $this->daily_report->obtener(
			array(
				array(
					'fk_building_site' => $data[0]->fk_building_site,
					'activity_date <' => $data[0]->activity_date,
				)
			)
		);
		$dates = [];
		foreach ($activities_data_o as $ad) {
			if (!isset($dates[$ad->activity_date])) {
				$dates[$ad->activity_date] = 0;
			}
			$dates[$ad->activity_date] += 1;
		}
		foreach ($dates as $k => $date) {
			$t = $this->daily_report->obtener(
				array(
					array(
						'fk_building_site' => $data[0]->fk_building_site,
						'activity_date' => $k,
						'report_no' => $date
					)
				)
			);
			$p_hh += $t[0]->r_hh;
		}
		$data[0]->activity_data = $activity_array;
		$data[0]->c_hh = $c_hh;
		$data[0]->p_hh = $p_hh;
		$data[0]->photos_data = [];

		foreach ($photos as $kk => $photo) {

			$ac = $this->activity_registry->obtener(
				[
					[
						'fk_image' => $photosIds[$kk]
					]
				]
			);

			if (sizeof($ac) > 0) {
				$data[0]->photos_data[] = $ac[0]->activity;
			} else {
				$activity = new stdClass;
				$activity->name = "Sin actividad";
				$data[0]->photos_data[] = $activity;
			}
		}
		$data[0]->workers_in_site = $this->db->select('worker.id, worker.name, worker.email, worker.dni, worker.fk_speciality, worker.fk_speciality_role, speciality.name as speciality_name, speciality_role.name as speciality_role_name')->from('worker')
			->where('worker.fk_building_site', $data[0]->fk_building_site)
			->join('speciality', 'worker.fk_speciality = speciality.id')
			->join('speciality_role', 'worker.fk_speciality_role = speciality_role.id')
			->get()->result();
		$data[0]->photos = $photos;
		$data[0]->hh_role = $spr_array;
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '../node_modules/datatables/datatables.min.js' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '../node_modules/datatables/datatables.min.css' ),
			)
		);
		$this->load->view(CPATH . 'head', $this->web->get_header('Obras', $add_lib));
		$this->load->view(SPATH . 'building_site_report_view_structure', array('user' => $user[0], 'data' => $data[0]));
		$this->load->view(CPATH . 'foot');
	}
	public function report_view_pdf($daily_report_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('building_site');
		$this->load->model('daily_report');
		$this->load->model('activity_data');
		$this->load->model('activity_registry');
		$this->load->model('speciality_role');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$user[0]->daily_report_id = $daily_report_id;
		$data = $this->daily_report->obtener(
			array(
				array(
					'id' => $daily_report_id
				)
			)
		);
		$activities_data = $this->activity_registry->obtener(
			array(
				array(
					'fk_building_site' => $data[0]->fk_building_site,
					'activity_date' => $data[0]->activity_date
				)
			)
		);
		$c_hh = 0;
		$p_hh = 0;
		$activity_array = array();
		$comments = array();
		$machines = array();
		foreach ($activities_data as $ad) {
			if (!isset($activity_array[$ad->activity->fk_zone])) {
				$activity_array[$ad->activity->fk_zone] = array();
			}
			$activity_array[$ad->activity->fk_zone][] = $ad->activity;
		}

		$spr_array = array();
		foreach ($activities_data as $ad) {
			$spr_array[$ad->fk_speciality_role] = new stdClass;
			$spr_array[$ad->fk_speciality_role]->name = $ad->speciality_role->name;
			$spr_array[$ad->fk_speciality_role]->hh = 0;
			$spr_array[$ad->fk_speciality_role]->workers = 0;
		}

		$r_hh = json_decode($data[0]->r_w);
		foreach ($r_hh as $k => $v) {
			$sr = $this->speciality_role->obtener([['id' => $k]]);
			$spr_array[$k]->name = $sr[0]->name;
			$spr_array[$k]->hh = $v->hh;
			$spr_array[$k]->workers = $v->workers;
		}

		$photos = array();
		$photosIds = array();
		if ($data[0]->images != "0") {
			$images = json_decode($data[0]->images);
			foreach ($images as $registry) {
				$i = $this->activity_registry->obtener([
					[
						'fk_building_site' => $data[0]->fk_building_site,
						'id' => $registry
					]
				]);
				$ad = $i[0];
				$t = new stdClass;
				$t->photo = $ad->image->url . $ad->image->id . "/" . $ad->image->name . $ad->image->ext;
				$photos[] = $t;
				$photosIds[] = $ad->image->id;
			}
		}
		$c_hh = $data[0]->r_hh;
		$activities_data_o = $this->daily_report->obtener(
			array(
				array(
					'fk_building_site' => $data[0]->fk_building_site,
					'activity_date <' => $data[0]->activity_date,
				)
			)
		);
		$dates = [];
		foreach ($activities_data_o as $ad) {
			if (!isset($dates[$ad->activity_date])) {
				$dates[$ad->activity_date] = 0;
			}
			$dates[$ad->activity_date] += 1;
		}
		foreach ($dates as $k => $date) {
			$t = $this->daily_report->obtener(
				array(
					array(
						'fk_building_site' => $data[0]->fk_building_site,
						'activity_date' => $k,
						'report_no' => $date
					)
				)
			);
			$p_hh += $t[0]->r_hh;
		}
		$data[0]->activity_data = $activity_array;
		$data[0]->c_hh = $c_hh;
		$data[0]->p_hh = $p_hh;

		$data[0]->photos_data = [];

		foreach ($photos as $kk => $photo) {

			$ac = $this->activity_registry->obtener(
				[
					[
						'fk_image' => $photosIds[$kk]
					]
				]
			);

			if (sizeof($ac) > 0) {
				$data[0]->photos_data[] = $ac[0]->activity;
			} else {
				$activity = new stdClass;
				$activity->name = "Sin actividad";
				$data[0]->photos_data[] = $activity;
			}
		}

		$data[0]->workers_in_site = $this->db->select('worker.id, worker.name, worker.email, worker.dni, worker.fk_speciality, worker.fk_speciality_role, speciality.name as speciality_name, speciality_role.name as speciality_role_name')->from('worker')
			->where('worker.fk_building_site', $data[0]->fk_building_site)
			->join('speciality', 'worker.fk_speciality = speciality.id')
			->join('speciality_role', 'worker.fk_speciality_role = speciality_role.id')
			->get()->result();
		$data[0]->photos = $photos;
		$data[0]->hh_role = $spr_array;
		$add_lib = array(
			'js_lib' => array(
				asset_js('front.js'),
			),
			'css_lib' => array()
		);

		$this->load->view(CPATH . 'head', $this->web->get_header('Obras', $add_lib));
		$this->load->view(SPATH . 'building_site_report_view_pdf', array('user' => $user[0], 'data' => $data[0]));
		$this->load->view(CPATH . 'foot');
	}
	public function remove_speciality_role_user($speciality_role_id, $user_id)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $user_id == 0 || $speciality_role_id == 0) {
			redirect('login');
		}
		$this->load->model('speciality_role_user');
		$sru = $this->speciality_role_user->obtener(
			array(
				array(
					'fk_speciality_role' => $speciality_role_id
				),
				array(
					'fk_user' => $user_id
				)
			)
		);
		foreach ($sru as $v) {
			$this->speciality_role_user->borrar($v->id);
		}
		redirect('building_sites/edit_speciality_role/' . $speciality_role_id);
	}
	// WORKER
	// WORKER
	// WORKER
	/**
	 * add worker form
	 */
	public function add_worker($building_site_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('worker');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$building_site = $this->building_site->obtener(
			array(
				array(
					'id' => $building_site_id
				)
			)
		);
		$user[0]->building_site = $building_site[0];
		// $user[0]->speciality_role[0]->fk_building_site;
		// d($user[0]->speciality_role[0]->fk_building_site);
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '...')
			)
		);
		$this->load->helper('form');
		if ($this->input->post('add')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			// check check_email_unique id = 0 when add worker
			// validar que no existe el email en la base de datos
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_check_email_unique[' . $building_site_id . ']');
			$this->form_validation->set_rules('password', 'Contraseña', 'trim|required');
			$this->form_validation->set_rules('dni', 'RUT/DNI', 'trim|required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Trabajador', $add_lib));
				$this->load->view(
					SPATH . 'worker_add_structure',
					array(
						'user' => $user[0]
					)
				);
				$this->load->view(CPATH . 'foot');
			} else {
				$new = $this->worker->insertar();
				redirect('building_sites/edit/' . $building_site_id);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Trabajador', $add_lib));
			$this->load->view(SPATH . 'worker_add_structure', array('user' => $user[0]));
			$this->load->view(CPATH . 'foot');
		}
	}
	/**
	 * edit worker
	 */
	public function edit_worker($worker_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $worker_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('worker');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$data = $this->worker->obtener(
			array(
				array(
					'id' => $worker_id
				)
			)
		);
		// d($data);
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_js('...')
			),
		);
		$this->load->helper('form');
		if ($this->input->post('update')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('email', 'Email', "trim|required|valid_email|callback_check_email_unique[{$data[0]->email}|{$data[0]->fk_building_site}]");
			$this->form_validation->set_rules('dni', 'RUT/DNI', 'trim|required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Editar Trabajador', $add_lib));
				$this->load->view(SPATH . 'worker_edit_structure', array('user' => $user[0], 'data' => $data[0]));
				$this->load->view(CPATH . 'foot');
			} else {
				$this->worker->actualizar(
					$data[0]->id,
					$data[0]->fk_building_site,
					$this->input->post('name'),
					$this->input->post('email'),
					$this->input->post('dni'),
				);
				if (strlen($this->input->post('password')) > 0) {
					$this->worker->actualizar_llave($data[0]->id, $this->input->post('password'));
				}
				redirect('building_sites/edit/' . $data[0]->fk_building_site);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Editar Trabajador', $add_lib));
			$this->load->view(SPATH . 'worker_edit_structure', array('user' => $user[0], 'data' => $data[0]));
			$this->load->view(CPATH . 'foot');
		}
	}
	/**
	 * delete worker
	 */
	public function remove_worker($worker_id)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $worker_id == 0) {
			redirect('login');
		}
		$this->load->model('worker');
		$sru = $this->worker->obtener(
			array(
				array(
					'id' => $worker_id
				)
			)
		);
		foreach ($sru as $v) {
			$this->worker->borrar($v->id);
		}
		redirect('building_sites/edit/' . $v->fk_building_site);
	}
	/**
	 * validar que no exista el email en la BD en la tabla workers para esa obra = building_site_id
	 */
	public function check_email_unique($email, $worker)
	{
		$worker = explode("|", $worker);
		if (isset($worker) && 1 === count($worker)) {
			// add
			$email_exist = $this->worker->obtener(
				array(
					array(
						'fk_building_site' => $worker[0], // es el primer registro
						'email' => $email
					)
				)
			);
			// Si es el mismo worker, pero cambió el email y ese email no existe en la BD
			if (isset($email_exist) && count($email_exist) > 0)
				return false;
		} else {
			//edit
			// el email es el mismo
			if ($email === $worker[0])
				return true;
			// si no es el mismo email, entonces
			// busca si el email existe en Worker para la misma obra
			$email_exist = $this->worker->obtener(
				array(
					array(
						'fk_building_site' => $worker[1], // es el segundo registro
						'email' => $email
					)
				)
			);
			// Si es el mismo worker, pero cambió el email y ese email no existe en la BD
			if (isset($email_exist) && count($email_exist) > 0)
				return false;
		}
		// default
		return true;
	}
	/**
	 * add supervisor form
	 */
	public function add_supervisor($speciality_id = 0)
	{
		$ROLE = 8; // cambiar en PRODUCCION si es necesario
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('role');
		$this->load->model('speciality');
		$this->load->model('supervisor');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$error = array(
			'code' => 0,
			'message' => ""
		);
		$data = new stdClass;
		$data->speciality = $this->speciality->obtener(
			array(
				array(
					'id' => $speciality_id
				)
			)
		);
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '...')
			)
		);
		$this->load->helper('form');
		if ($this->input->post('add')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('first_name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('last_name', 'Apellido', 'trim|required');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('dni', 'RUT/DNI', 'trim|required');
			$this->form_validation->set_rules('address_1', 'Dirección 1', 'trim|required');
			$this->form_validation->set_rules('address_2', 'Dirección 2', 'trim');
			$this->form_validation->set_rules('phone_1', 'Fono 1', 'trim|required');
			$this->form_validation->set_rules('phone_2', 'Fono 2', 'trim');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Supervisor', $add_lib));
				$this->load->view(SPATH . 'supervisor_add_structure', array('user' => $user[0], 'data' => $data, 'error' => $error));
				$this->load->view(CPATH . 'foot');
			} else {
				$new_user = $this->user->insertar();
				$this->user->establecer_rol($new_user, $ROLE);
				$new_supervisor = $this->supervisor->insertar($speciality_id, $new_user);
				redirect('building_sites/edit_speciality/' . $speciality_id);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Supervisor', $add_lib));
			$this->load->view(SPATH . 'supervisor_add_structure', array('user' => $user[0], 'data' => $data, 'error' => $error));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function edit_supervisor($supervisor_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $supervisor_id == 0) {
			redirect('login');
		}
		$this->load->model('user');
		$this->load->model('supervisor');
		if (isset($logged_in->error) && $logged_in->error == 1) {
			$error = array(
				'code' => $logged_in->error_code,
				'message' => $logged_in->error_message
			);
			unset($logged_in->error);
			unset($logged_in->error_code);
			unset($logged_in->error_message);
			$this->session->set_userdata('logged_in', $logged_in);
		} else {
			$error = array(
				'code' => 0,
				'message' => ""
			);
		}
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);
		$supervisor = $this->supervisor->obtener(
			array(
				array(
					'id' => $supervisor_id
				)
			)
		);
		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '...')
			)
		);
		$this->load->helper('form');
		if ($this->input->post('update_1')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('first_name', 'Nombre', 'trim|required');
			$this->form_validation->set_rules('last_name', 'Apellido', 'trim|required');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('dni', 'RUT/DNI', 'trim|required');
			$this->form_validation->set_rules('address_1', 'Dirección 1', 'trim|required');
			$this->form_validation->set_rules('address_2', 'Dirección 2', 'trim');
			$this->form_validation->set_rules('phone_1', 'Fono 1', 'trim|required');
			$this->form_validation->set_rules('phone_2', 'Fono 2', 'trim');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Editar Supervisor', $add_lib));
				$this->load->view(SPATH . 'supervisor_edit_structure', array('user' => $user[0], 'data' => $supervisor[0], 'error' => $error));
				$this->load->view(CPATH . 'foot');
			} else {
				$this->supervisor->actualizar_usuario($supervisor);
				redirect('building_sites/edit_supervisor/' . $supervisor_id);
			}
		} else if ($this->input->post('update_2')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('password', 'Contraseña', 'trim|required');
			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Editar Supervisor', $add_lib));
				$this->load->view(SPATH . 'supervisor_edit_structure', array('user' => $user[0], 'data' => $supervisor[0], 'error' => $error));
				$this->load->view(CPATH . 'foot');
			} else {
				$this->supervisor->actualizar_llave($supervisor[0]->user->id);
				redirect('building_sites/edit_supervisor/' . $supervisor_id);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Editar Supervisor', $add_lib));
			$this->load->view(SPATH . 'supervisor_edit_structure', array('user' => $user[0], 'data' => $supervisor[0], 'error' => $error));
			$this->load->view(CPATH . 'foot');
		}
	}
	public function remove_supervisor($supervisor_id = 0, $speciality_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE || $supervisor_id == 0) {
			redirect('login');
		}
		$this->load->model('supervisor');
		$this->supervisor->borrar($supervisor_id);
		redirect('building_sites/edit_speciality/' . $speciality_id);
	}

	public function list_activities($building_site_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');
		if ($logged_in == FALSE) {
			redirect('login');
		}

		$this->load->model('user');
		$this->load->model('building_site');
		$this->load->model('activity_registry');
		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);

		$bs = $this->building_site->obtener(
			array(
				array(
					'id' => $building_site_id
				)
			)
		);

		$data = $this->activity_registry->obtener_ordenado(
			[
				[
					'fk_building_site' => $building_site_id
				]
			]
		);

		$add_lib = array(
			'js_lib' => array(
				asset_js('front.js'),
			),
			'css_lib' => array()
		);

		$this->load->view(CPATH . 'head', $this->web->get_header('Obras', $add_lib));
		$this->load->view(SPATH . 'building_site_activity_registries_list_structure', array('user' => $user[0], 'data' => $data, 'building_site' => $bs[0]));
		$this->load->view(CPATH . 'foot');
	}

	public function add_activities($building_site_id = 0)
	{
		$logged_in = $this->session->userdata('logged_in');

		if ($logged_in == FALSE) {
			redirect('login');
		}

		$this->load->model('user');
		$this->load->model('role');
		$this->load->model('building_site');

		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);

		$building_site = $this->building_site->obtener(
			array(
				array(
					'id' => $building_site_id
				)
			)
		);

		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '...')
			)
		);

		$this->load->helper('form');
		if ($this->input->post('add')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Nombre', 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Obra', $add_lib));
				$this->load->view(SPATH . 'building_site_activity_registries_add_structure', array('user' => $user[0], 'building_site' => $building_site[0]));
				$this->load->view(CPATH . 'foot');
			} else {
				$new = $this->building_site->insertar();
				redirect('building_sites/edit/' . $new);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Añadir Obra', $add_lib));
			$this->load->view(SPATH . 'building_site_activity_registries_add_structure', array('user' => $user[0], 'building_site' => $building_site[0]));
			$this->load->view(CPATH . 'foot');
		}
	}

	public function edit_activities($activity_registry = 0)
	{
		$logged_in = $this->session->userdata('logged_in');

		if ($logged_in == FALSE) {
			redirect('login');
		}

		$this->load->model('user');
		$this->load->model('role');
		$this->load->model('building_site');
		$this->load->model('activity_registry');

		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);

		$data = $this->activity_registry->obtener(
			array(
				array(
					'id' => $activity_registry
				)
			)
		);

		$data[0]->activity_report_file = "activity_report_file";
		$data[0]->activity_report_file_url = $data[0]->fk_image != 0 ? implode('/', [
			$data[0]->image->url,
			$data[0]->fk_image,
			$data[0]->image->name . $data[0]->image->ext,
		]) : '';

		$building_site = $this->building_site->obtener(
			array(
				array(
					'id' => $data[0]->fk_building_site
				)
			)
		);

		$add_lib = array(
			'js_lib' => array(
				//asset_js( '...' ),
				asset_js('front.js'),
			),
			'css_lib' => array(
				//asset_css( '...')
			)
		);

		$this->load->helper('form');
		if ($this->input->post('update')) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('hh', 'HH', 'trim|required');
			$this->form_validation->set_rules('workers', 'Trabajadores', 'trim|required');
			$this->form_validation->set_rules('activity_date', 'Fecha activdad', 'trim|required');
			$this->form_validation->set_rules('avance', 'Avance', 'trim|required');
			$this->form_validation->set_rules('comment', 'Notas', 'trim|required');
			$this->form_validation->set_rules('machinery', 'Maquinaria', 'trim|required');
			$this->form_validation->set_rules('activity_date', 'Fecha activdad', 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				$this->load->view(CPATH . 'head', $this->web->get_header('Editar actividad realizada', $add_lib));
				$this->load->view(
					SPATH . 'building_site_activity_registries_edit_structure',
					array(
						'user' => $user[0],
						'building_site' => $building_site[0],
						'data' => $data[0]
					)
				);
				$this->load->view(CPATH . 'foot');
			} else {

				$it = $this->image_type->obtener(
					[
						[
							'code_name' => 'activity_report'
						]
					]
				);

				if (!empty($_FILES[$data[0]->activity_report_file]['name'])) {

					$i = $this->image->insertar('', '', $it[0]->id);

					$config = $this->web->get_upload_config('activity_report');
					$config['upload_path'] = $config['upload_path'] . $i . '/';

					if (!is_dir($config['upload_path'])) {
						mkdir($config['upload_path'], 0777, true);
					}

					$name = "";
					$ext = "";

					$this->load->library('upload', $config);
					$this->upload->do_upload($data[0]->activity_report_file);

					if (is_string($this->upload->data('raw_name'))) {
						$name = utf8_encode($this->upload->data('raw_name'));
					}

					if (is_string($this->upload->data('file_ext'))) {
						$ext = utf8_encode($this->upload->data('file_ext'));
					}

					$this->image->actualizar($i, $name, $ext);

					$this->activity_registry->actualizar_imagen($data[0]->id, $i);
				}
				$this->activity_registry->actualizar_por_edicion($data[0]->id);
				redirect('building_sites/list_activities/' . $building_site[0]->id);
			}
		} else {
			$this->load->view(CPATH . 'head', $this->web->get_header('Editar actividad realizada', $add_lib));
			$this->load->view(
				SPATH . 'building_site_activity_registries_edit_structure',
				array(
					'user' => $user[0],
					'building_site' => $building_site[0],
					'data' => $data[0]
				)
			);
			$this->load->view(CPATH . 'foot');
		}
	}

	public function remove_activities($activity_registry = 0)
	{
		$logged_in = $this->session->userdata('logged_in');

		if ($logged_in == FALSE) {
			redirect('login');
		}

		$this->load->model('user');
		$this->load->model('role');
		$this->load->model('building_site');
		$this->load->model('activity_registry');

		$user = $this->user->obtener(
			array(
				array(
					'email' => $logged_in->email
				)
			)
		);

		$data = $this->activity_registry->obtener(
			array(
				array(
					'id' => $activity_registry
				)
			)
		);

		$building_site = $this->building_site->obtener(
			array(
				array(
					'id' => $data[0]->fk_building_site
				)
			)
		);

		$this->activity_registry->borrar($activity_registry);

		redirect('building_sites/list_activities/' . $building_site[0]->id);
	}

	public function reverse_report_activity_hh($building_site_id = 0)
	{
		$this->load->database();
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$activities = $this->db->select('activity_code')->from('activity')->where('fk_building_site', $building_site_id)->get()->result();
		$activity_codes = array_column($activities, 'activity_code');
		d($activity_codes);
	}

	public function reverse_report_activity_hh2($building_site_id = 0)
	{
		$this->load->model('user');
		$this->load->model('role');
		$this->load->model('zone');
		$this->load->model('building_site');
		$this->load->model('speciality');
		$this->load->model('speciality_role');
		$this->load->model('activity');
		$this->load->model('activity_registry');
		$this->load->model('activity_data');
		$this->load->model('daily_report');
		$tpa = 0;
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$row = 1;
		$activity_items = $this->activity->obtener(
			[
				[
					'fk_building_site' => $building_site_id
				]
			]
		);
		$registry = [];
		$registryDateCells = [];
		$min_rec = 0;
		$max_rec = 0;
		foreach ($activity_items as $activity) {
			$t = $this->activity_registry->obtener([
				[
					'fk_building_site' => $building_site_id,
					'fk_activity' => $activity->id
				]
			]);
			if (sizeof($t) > 0) {
				$registry[$activity->activity_code][$t[0]->speciality->id][$t[0]->speciality_role->id] = $t;
				foreach ($t as $record) {
					if ($min_rec == 0 || $min_rec > strtotime($record->activity_date)) {
						$min_rec = strtotime($record->activity_date);
					}
					if ($max_rec == $min_rec || $max_rec < strtotime($record->activity_date)) {
						$max_rec = strtotime($record->activity_date);
					}
				}
			}
		}
		$activity_data = $this->activity_data->obtener(
			[
				[
					'fk_building_site' => $building_site_id
				]
			]
		);
		$dataDateCells = [];
		foreach ($activity_data as $ad) {
			if (!array_search(gmdate("Ymd", $ad->activity_date * 86400), $dataDateCells) !== false) {
				$dataDateCells[] = gmdate("Ymd", $ad->activity_date * 86400);
				if ($min_rec == 0 || $min_rec > $ad->activity_date * 86400) {
					$min_rec = $ad->activity_date * 86400;
				}
				if ($max_rec == $min_rec || $max_rec < $ad->activity_date * 86400) {
					$max_rec = $ad->activity_date * 86400;
				}
			}
		}
		for ($i = $min_rec; $i <= $max_rec; $i += 86400) {
			$registryDateCells[] = gmdate("Ymd", $i);
		}
		$dataDateCells = array_merge($dataDateCells, $registryDateCells);
		sort($dataDateCells, SORT_NUMERIC);
		$dataDateCells = array_unique($dataDateCells);
		$dataDateCells = array_values($dataDateCells);
		$i = 0;
		foreach ($dataDateCells as $k => $date) {
			$sheet->setCellValueByColumnAndRow($i + 17, 1, substr($date, 6, 2) . '-' . substr($date, 4, 2) . '-' . substr($date, 0, 4));
			$i++;
		}
		$cat_ad = [];
		foreach ($activity_data as $ad) {
			if (!isset($cat_ad[$ad->activity->activity_code])) {
				$cat_ad[$ad->activity->activity_code] = [];
			}
			if (!isset($cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality])) {
				$cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality] = [];
			}
			if (!isset($cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality][$ad->activity->fk_speciality_role])) {
				$cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality][$ad->activity->fk_speciality_role] = [];
			}
			$cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality][$ad->activity->fk_speciality_role][] = $ad;
		}
		$C = "Q"; // define la columna donde empieza la fecha
		foreach ($cat_ad as $a) {
			$row++;
			$n = $a;
			sort($n);
			sort($n[0]);
			sort($n[0][0]);
			sort($n[0][0][0]);
			$n = $n[0][0][0]->activity;
			$titles = [
				$n->zone->name,
				"",
				$n->speciality->name,
				"",
				$n->activity_code,
				"",
				$n->name,
				"",
				$n->unt,
				$n->qty,
				$n->eff,
			];
			$spreadsheet->getActiveSheet()
				->fromArray(
					$titles,
					NULL,
					"A{$row}"
				)
				->setCellValueExplicit(
					"E{$row}",
					$n->activity_code,
					\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
				);
			//$row++;
			foreach ($a as $s) {
				$p_avance = [];
				$p_machinery = [];
				$p_comment = [];
				$roles = 0;
				$t_row = $row;
				$x = 0;
				foreach ($s as $k => $r) {
					$first = false;
					$first_column = 18;
					$sheet->getCell("H{$t_row}")->setValue($r[0]->speciality_role->name);
					$p_avance[$x] = [];
					foreach ($r as $d) {
						$p_avance[$x][gmdate("Ymd", $d->activity_date * 86400)] = floatval($d->p_avance);
						$p_machinery[$x][gmdate("Ymd", $d->activity_date * 86400)] = $d->machinery;
						$p_comment[$x][gmdate("Ymd", $d->activity_date * 86400)] = $d->comment;
						$tpa += $d->p_avance;
						if ($first == false) {
							if ($d->p_avance != 0)
								$first = true;
							else {
								$first_column++;
							}
						}
					}
					foreach ($dataDateCells as $date) {
						if (!isset($p_avance[$x][$date])) {
							$p_avance[$x][$date] = 0;
						}
						if (!isset($p_avance[$x][$date])) {
							$p_machinery[$x][$date] = '';
						}
						if (!isset($p_avance[$x][$date])) {
							$p_comment[$x][$date] = '';
						}
					}
					ksort($p_avance[$x], SORT_NUMERIC);
					ksort($p_machinery[$x], SORT_NUMERIC);
					ksort($p_comment[$x], SORT_NUMERIC);

					$x++;

					foreach ($dataDateCells as $date) {
						if (!isset($p_avance[$x][$date])) {
							$p_avance[$x][$date] = 0;
						}
						if (!isset($p_machinery[$x][$date])) {
							$p_machinery[$x][$date] = '';
						}
						if (!isset($p_comment[$x][$date])) {
							$p_comment[$x][$date] = '';
						}
						foreach ($r as $data) {
							if (isset($registry[$data->activity->activity_code][$data->fk_speciality][$data->fk_speciality_role])) {
								foreach ($registry[$data->activity->activity_code][$data->fk_speciality][$data->fk_speciality_role] as $round) {
									if ($date == str_replace("-", "", $round->activity_date)) {
										$p_avance[$x][$date] = $round->p_avance;
										$p_machinery[$x][$date] = $round->machinery;
										$p_comment[$x][$date] = $round->comment;
									}
								}
							}
						}
					}
					$x++;
				}
				$sh = [];

				$key_cero = 0;
				foreach ($p_avance as $key_value => $value) {
					$i = 0;
					$sum = 0;
					$nr = [];
					foreach ($value as $v) {
						if (is_string($v) && 0 <> (float) $v)
							$key_cero = $key_value;
						$nr[] = $v;
						$i++;
					}
					$sh[] = $nr;
				}
				$sh = $sh[$key_cero];
				$spreadsheet->getActiveSheet()
					->fromArray(
						$sh,
						0,
						$C . $row,
						TRUE,
						TRUE
					);

				$row += $roles;
			}
		}
		$row = 1;
		$titles = [
			"Área",
			"Sub-area",
			"Especialidad",
			"Supervisor",
			"Item",
			"Descripción",
			"Descripción2",
			"Rol de especialidad",
			"Unid",
			"Cant",
			"Rend",
			"Nº Trabajadores",
			"Trabajo",
			"Duracion",
			"Comienzo Programado",
			"Fin Programado",
		];
		$spreadsheet->getActiveSheet()
			->fromArray(
				$titles,
				NULL,
				"A1"
			);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Planilla Avances HH.xlsx"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
		exit;
	}

	public function reverse_report_activity_machinery($building_site_id = 0)
	{
		$this->load->model('user');
		$this->load->model('role');
		$this->load->model('zone');
		$this->load->model('building_site');
		$this->load->model('speciality');
		$this->load->model('speciality_role');
		$this->load->model('activity');
		$this->load->model('activity_registry');
		$this->load->model('activity_data');
		$this->load->model('daily_report');
		$tpa = 0;
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$row = 1;
		$activity_items = $this->activity->obtener(
			[
				[
					'fk_building_site' => $building_site_id
				]
			]
		);
		$registry = [];
		$registryDateCells = [];
		$min_rec = 0;
		$max_rec = 0;
		foreach ($activity_items as $activity) {
			$t = $this->activity_registry->obtener([
				[
					'fk_building_site' => $building_site_id,
					'fk_activity' => $activity->id
				]
			]);
			if (sizeof($t) > 0) {
				$registry[$activity->activity_code][$t[0]->speciality->id][$t[0]->speciality_role->id] = $t;
				foreach ($t as $record) {
					if ($min_rec == 0 || $min_rec > strtotime($record->activity_date)) {
						$min_rec = strtotime($record->activity_date);
					}
					if ($max_rec == $min_rec || $max_rec < strtotime($record->activity_date)) {
						$max_rec = strtotime($record->activity_date);
					}
				}
			}
		}
		$activity_data = $this->activity_data->obtener(
			[
				[
					'fk_building_site' => $building_site_id
				]
			]
		);
		$dataDateCells = [];
		foreach ($activity_data as $ad) {
			if (!array_search(gmdate("Ymd", $ad->activity_date * 86400), $dataDateCells) !== false) {
				$dataDateCells[] = gmdate("Ymd", $ad->activity_date * 86400);
				if ($min_rec == 0 || $min_rec > $ad->activity_date * 86400) {
					$min_rec = $ad->activity_date * 86400;
				}
				if ($max_rec == $min_rec || $max_rec < $ad->activity_date * 86400) {
					$max_rec = $ad->activity_date * 86400;
				}
			}
		}
		for ($i = $min_rec; $i <= $max_rec; $i += 86400) {
			$registryDateCells[] = gmdate("Ymd", $i);
		}
		$dataDateCells = array_merge($dataDateCells, $registryDateCells);
		sort($dataDateCells, SORT_NUMERIC);
		$dataDateCells = array_unique($dataDateCells);
		$dataDateCells = array_values($dataDateCells);
		$i = 0;
		foreach ($dataDateCells as $k => $date) {
			$sheet->setCellValueByColumnAndRow($i + 17, 1, substr($date, 6, 2) . '-' . substr($date, 4, 2) . '-' . substr($date, 0, 4));
			$i++;
		}
		$cat_ad = [];
		foreach ($activity_data as $ad) {
			if (!isset($cat_ad[$ad->activity->activity_code])) {
				$cat_ad[$ad->activity->activity_code] = [];
			}
			if (!isset($cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality])) {
				$cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality] = [];
			}
			if (!isset($cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality][$ad->activity->fk_speciality_role])) {
				$cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality][$ad->activity->fk_speciality_role] = [];
			}
			$cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality][$ad->activity->fk_speciality_role][] = $ad;
		}
		$C = "Q"; // define la columna donde empieza la fecha
		foreach ($cat_ad as $a) {
			$row++;
			$n = $a;
			sort($n);
			sort($n[0]);
			sort($n[0][0]);
			sort($n[0][0][0]);
			$n = $n[0][0][0]->activity;
			$titles = [
				$n->zone->name,
				"",
				$n->speciality->name,
				"",
				$n->activity_code,
				"",
				$n->name,
				"",
				$n->unt,
				$n->qty,
				$n->eff,
			];
			$spreadsheet->getActiveSheet()
				->fromArray(
					$titles,
					NULL,
					"A{$row}"
				)
				->setCellValueExplicit(
					"E{$row}",
					$n->activity_code,
					\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
				);
			//$row++;
			foreach ($a as $s) {
				$p_avance = [];
				$p_machinery = [];
				$p_comment = [];
				$roles = 0;
				$t_row = $row;
				$x = 0;
				foreach ($s as $k => $r) {
					$first = false;
					$first_column = 18;
					$sheet->getCell("H{$t_row}")->setValue($r[0]->speciality_role->name);
					$p_avance[$x] = [];
					foreach ($r as $d) {
						$p_avance[$x][gmdate("Ymd", $d->activity_date * 86400)] = floatval($d->p_avance);
						$p_machinery[$x][gmdate("Ymd", $d->activity_date * 86400)] = $d->machinery;
						$p_comment[$x][gmdate("Ymd", $d->activity_date * 86400)] = $d->comment;
						$tpa += $d->p_avance;
						if ($first == false) {
							if ($d->p_avance != 0)
								$first = true;
							else {
								$first_column++;
							}
						}
					}
					foreach ($dataDateCells as $date) {
						if (!isset($p_avance[$x][$date])) {
							$p_avance[$x][$date] = '';
						}
						if (!isset($p_machinery[$x][$date])) {
							$p_machinery[$x][$date] = '';
						}
						if (!isset($p_comment[$x][$date])) {
							$p_comment[$x][$date] = '';
						}
					}
					ksort($p_avance[$x], SORT_NUMERIC);
					ksort($p_machinery[$x], SORT_NUMERIC);
					ksort($p_comment[$x], SORT_NUMERIC);
					//$t_row++;
					$x++;
					//$sheet->getCell("G{$t_row}")->setValue("AVANCE DESCRITO EN TERRENO");
					//$t_row++;
					foreach ($dataDateCells as $date) {
						if (!isset($p_avance[$x][$date])) {
							$p_avance[$x][$date] = '';
						}
						if (!isset($p_machinery[$x][$date])) {
							$p_machinery[$x][$date] = '';
						}
						if (!isset($p_comment[$x][$date])) {
							$p_comment[$x][$date] = '';
						}
						foreach ($r as $data) {
							if (isset($registry[$data->activity->activity_code][$data->fk_speciality][$data->fk_speciality_role])) {
								foreach ($registry[$data->activity->activity_code][$data->fk_speciality][$data->fk_speciality_role] as $round) {
									if ($date == str_replace("-", "", $round->activity_date)) {
										$p_avance[$x][$date] = $round->p_avance;
										$p_machinery[$x][$date] = $round->machinery;
										$p_comment[$x][$date] = $round->comment;
									}
								}
							}
						}
					}
					$x++;
					//$roles++;
					//$roles++;
					//$roles++;
				}

				$sh = [];
				/**
				 * ricardo.munoz
				 * arregla problema con arreglos de 2 dimensiones
				 * se selecciona el arreglo que contenga "numeros" 
				 */

				/*
																																																																																																																																								$key_cero = 0;
																																																																																																																																								foreach ($p_avance as $key_value => $value) {
																																																																																																																																									$i = 0;
																																																																																																																																									$sum = 0;
																																																																																																																																									$nr = [];
																																																																																																																																									foreach ($value as $v) {
																																																																																																																																										if (is_string($v) && 0 <> (float)$v)
																																																																																																																																											$key_cero = $key_value;
																																																																																																																																										$nr[] = $v;
																																																																																																																																										$i++;
																																																																																																																																									}
																																																																																																																																									$sh[] = $nr;
																																																																																																																																								}
																																																																																																																																								$sh = $sh[$key_cero];
																																																																																																																																								$spreadsheet->getActiveSheet()
																																																																																																																																									->fromArray(
																																																																																																																																										$sh,
																																																																																																																																										0,
																																																																																																																																										$C . $row,
																																																																																																																																										TRUE,
																																																																																																																																										TRUE
																																																																																																																																									);

																																																																																																																																								*/


				$sh_machinery = [];
				foreach ($p_machinery as $key_value => $value) {
					$i = 0;
					$sum = 0;
					$nr_machinery = [];
					foreach ($value as $v) {
						$nr_machinery[] = $v;
						$i++;
					}
					$sh_machinery[] = $nr_machinery;
				}

				$spreadsheet->getActiveSheet()
					->fromArray(
						$sh_machinery,
						0,
						$C . ($row + 1),
						TRUE
					);

				/*
																																																																$sh_comment = [];
																																																																$row++;
																																																																foreach ($p_comment as $value) {
																																																																	$i = 0;
																																																																	$sum = 0;
																																																																	$nr_comment = [];
																																																																	foreach ($value as $v) {
																																																																		$nr_comment[] = $v;
																																																																		$i++;
																																																																	}
																																																																	$sh_comment[] = $nr_comment;
																																																																}
																																																																$spreadsheet->getActiveSheet()
																																																																	->fromArray(
																																																																		$sh_comment,
																																																																		0,
																																																																		$C . $row,
																																																																		TRUE
																																																																	);
																																																																*/
				$row += $roles;
			}
		}
		$row = 1;
		$titles = [
			"Área",
			"Sub-area",
			"Especialidad",
			"Supervisor",
			"Item",
			"Descripción",
			"Descripción2",
			"Rol de especialidad",
			"Unid",
			"Cant",
			"Rend",
			"Nº Trabajadores",
			"Trabajo",
			"Duracion",
			"Comienzo Programado",
			"Fin Programado",
		];
		$spreadsheet->getActiveSheet()
			->fromArray(
				$titles,
				NULL,
				"A1"
			);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Planilla Avances Maquinariasa.xlsx"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
		exit;
	}

	public function reverse_report_activity_comments($building_site_id = 0)
	{
		$this->load->model('user');
		$this->load->model('role');
		$this->load->model('zone');
		$this->load->model('building_site');
		$this->load->model('speciality');
		$this->load->model('speciality_role');
		$this->load->model('activity');
		$this->load->model('activity_registry');
		$this->load->model('activity_data');
		$this->load->model('daily_report');
		$tpa = 0;
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$row = 1;
		$activity_items = $this->activity->obtener(
			[
				[
					'fk_building_site' => $building_site_id
				]
			]
		);
		$registry = [];
		$registryDateCells = [];
		$min_rec = 0;
		$max_rec = 0;
		foreach ($activity_items as $activity) {
			$t = $this->activity_registry->obtener([
				[
					'fk_building_site' => $building_site_id,
					'fk_activity' => $activity->id
				]
			]);
			if (sizeof($t) > 0) {
				$registry[$activity->activity_code][$t[0]->speciality->id][$t[0]->speciality_role->id] = $t;
				foreach ($t as $record) {
					if ($min_rec == 0 || $min_rec > strtotime($record->activity_date)) {
						$min_rec = strtotime($record->activity_date);
					}
					if ($max_rec == $min_rec || $max_rec < strtotime($record->activity_date)) {
						$max_rec = strtotime($record->activity_date);
					}
				}
			}
		}
		$activity_data = $this->activity_data->obtener(
			[
				[
					'fk_building_site' => $building_site_id
				]
			]
		);
		$dataDateCells = [];
		foreach ($activity_data as $ad) {
			if (!array_search(gmdate("Ymd", $ad->activity_date * 86400), $dataDateCells) !== false) {
				$dataDateCells[] = gmdate("Ymd", $ad->activity_date * 86400);
				if ($min_rec == 0 || $min_rec > $ad->activity_date * 86400) {
					$min_rec = $ad->activity_date * 86400;
				}
				if ($max_rec == $min_rec || $max_rec < $ad->activity_date * 86400) {
					$max_rec = $ad->activity_date * 86400;
				}
			}
		}
		for ($i = $min_rec; $i <= $max_rec; $i += 86400) {
			$registryDateCells[] = gmdate("Ymd", $i);
		}
		$dataDateCells = array_merge($dataDateCells, $registryDateCells);
		sort($dataDateCells, SORT_NUMERIC);
		$dataDateCells = array_unique($dataDateCells);
		$dataDateCells = array_values($dataDateCells);
		$i = 0;
		foreach ($dataDateCells as $k => $date) {
			$sheet->setCellValueByColumnAndRow($i + 17, 1, substr($date, 6, 2) . '-' . substr($date, 4, 2) . '-' . substr($date, 0, 4));
			$i++;
		}
		$cat_ad = [];
		foreach ($activity_data as $ad) {
			if (!isset($cat_ad[$ad->activity->activity_code])) {
				$cat_ad[$ad->activity->activity_code] = [];
			}
			if (!isset($cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality])) {
				$cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality] = [];
			}
			if (!isset($cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality][$ad->activity->fk_speciality_role])) {
				$cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality][$ad->activity->fk_speciality_role] = [];
			}
			$cat_ad[$ad->activity->activity_code][$ad->activity->fk_speciality][$ad->activity->fk_speciality_role][] = $ad;
		}
		$C = "Q"; // define la columna donde empieza la fecha
		foreach ($cat_ad as $a) {
			$row++;
			$n = $a;
			sort($n);
			sort($n[0]);
			sort($n[0][0]);
			sort($n[0][0][0]);
			$n = $n[0][0][0]->activity;
			$titles = [
				$n->zone->name,
				"",
				$n->speciality->name,
				"",
				$n->activity_code,
				"",
				$n->name,
				"",
				$n->unt,
				$n->qty,
				$n->eff,
			];
			$spreadsheet->getActiveSheet()
				->fromArray(
					$titles,
					NULL,
					"A{$row}"
				)
				->setCellValueExplicit(
					"E{$row}",
					$n->activity_code,
					\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
				);
			//$row++;
			foreach ($a as $s) {
				$p_avance = [];
				$p_machinery = [];
				$p_comment = [];
				$roles = 0;
				$t_row = $row;
				$x = 0;
				foreach ($s as $k => $r) {
					$first = false;
					$first_column = 18;
					$sheet->getCell("H{$t_row}")->setValue($r[0]->speciality_role->name);
					$p_avance[$x] = [];
					foreach ($r as $d) {
						$p_avance[$x][gmdate("Ymd", $d->activity_date * 86400)] = floatval($d->p_avance);
						$p_machinery[$x][gmdate("Ymd", $d->activity_date * 86400)] = $d->machinery;
						$p_comment[$x][gmdate("Ymd", $d->activity_date * 86400)] = $d->comment;
						$tpa += $d->p_avance;
						if ($first == false) {
							if ($d->p_avance != 0)
								$first = true;
							else {
								$first_column++;
							}
						}
					}
					foreach ($dataDateCells as $date) {
						if (!isset($p_avance[$x][$date])) {
							$p_avance[$x][$date] = '';
						}
						if (!isset($p_machinery[$x][$date])) {
							$p_machinery[$x][$date] = '';
						}
						if (!isset($p_comment[$x][$date])) {
							$p_comment[$x][$date] = '';
						}
					}
					ksort($p_avance[$x], SORT_NUMERIC);
					ksort($p_machinery[$x], SORT_NUMERIC);
					ksort($p_comment[$x], SORT_NUMERIC);
					//$t_row++;
					$x++;
					//$sheet->getCell("G{$t_row}")->setValue("AVANCE DESCRITO EN TERRENO");
					//$t_row++;
					foreach ($dataDateCells as $date) {
						if (!isset($p_avance[$x][$date])) {
							$p_avance[$x][$date] = '';
						}
						if (!isset($p_machinery[$x][$date])) {
							$p_machinery[$x][$date] = '';
						}
						if (!isset($p_comment[$x][$date])) {
							$p_comment[$x][$date] = '';
						}
						foreach ($r as $data) {
							if (isset($registry[$data->activity->activity_code][$data->fk_speciality][$data->fk_speciality_role])) {
								foreach ($registry[$data->activity->activity_code][$data->fk_speciality][$data->fk_speciality_role] as $round) {
									if ($date == str_replace("-", "", $round->activity_date)) {
										$p_avance[$x][$date] = $round->p_avance;
										$p_machinery[$x][$date] = $round->machinery;
										$p_comment[$x][$date] = $round->comment;
									}
								}
							}
						}
					}
					$x++;
					//$roles++;
					//$roles++;
					//$roles++;
				}

				$sh = [];
				/**
				 * ricardo.munoz
				 * arregla problema con arreglos de 2 dimensiones
				 * se selecciona el arreglo que contenga "numeros" 
				 */

				/*
																																																																																																																																								$key_cero = 0;
																																																																																																																																								foreach ($p_avance as $key_value => $value) {
																																																																																																																																									$i = 0;
																																																																																																																																									$sum = 0;
																																																																																																																																									$nr = [];
																																																																																																																																									foreach ($value as $v) {
																																																																																																																																										if (is_string($v) && 0 <> (float)$v)
																																																																																																																																											$key_cero = $key_value;
																																																																																																																																										$nr[] = $v;
																																																																																																																																										$i++;
																																																																																																																																									}
																																																																																																																																									$sh[] = $nr;
																																																																																																																																								}
																																																																																																																																								$sh = $sh[$key_cero];
																																																																																																																																								$spreadsheet->getActiveSheet()
																																																																																																																																									->fromArray(
																																																																																																																																										$sh,
																																																																																																																																										0,
																																																																																																																																										$C . $row,
																																																																																																																																										TRUE,
																																																																																																																																										TRUE
																																																																																																																																									);

																																																																																																																																								*/

				/*
																																																																																																																																								$sh_machinery = [];
																																																																																																																																								$row++;
																																																																																																																																								foreach ($p_machinery as $value) {
																																																																																																																																									$i = 0;
																																																																																																																																									$sum = 0;
																																																																																																																																									$nr_machinery = [];
																																																																																																																																									foreach ($value as $v) {
																																																																																																																																										$nr_machinery[] = $v;
																																																																																																																																										$i++;
																																																																																																																																									}
																																																																																																																																									$sh_machinery[] = $nr_machinery;
																																																																																																																																								}
																																																																																																																																								$spreadsheet->getActiveSheet()
																																																																																																																																									->fromArray(
																																																																																																																																										$sh_machinery,
																																																																																																																																										0,
																																																																																																																																										$C . $row,
																																																																																																																																										TRUE
																																																																																																																																									);
																																																																																																																																								*/


				$sh_comment = [];
				$key_cero = 0;
				foreach ($p_comment as $key_value => $value) {
					$i = 0;
					$sum = 0;
					$nr_comment = [];
					foreach ($value as $v) {
						$nr_comment[] = $v;
						$i++;
					}
					$sh_comment[] = $nr_comment;
				}
				//filter $sh_comment set 0 to ''

				$spreadsheet->getActiveSheet()
					->fromArray(
						$sh_comment,
						0,
						$C . ($row + 1),
						TRUE
					);

				$row += $roles;
			}
		}
		$row = 1;
		$titles = [
			"Área",
			"Sub-area",
			"Especialidad",
			"Supervisor",
			"Item",
			"Descripción",
			"Descripción2",
			"Rol de especialidad",
			"Unid",
			"Cant",
			"Rend",
			"Nº Trabajadores",
			"Trabajo",
			"Duracion",
			"Comienzo Programado",
			"Fin Programado",
		];
		$spreadsheet->getActiveSheet()
			->fromArray(
				$titles,
				NULL,
				"A1"
			);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Planilla Avances Notas.xlsx"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
		exit;
	}
}