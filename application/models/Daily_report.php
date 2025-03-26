<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Daily_report extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('building_site');
	}

	public function insertar( $building_site_id, $activity_date, $report_no, $contract_name, $contract_no, $important_activities, $control_date, $emission_date ){
		$daily_report							=	new stdClass();
		$daily_report->fk_building_site			=	$building_site_id;
		$daily_report->activity_date			=	$activity_date;
		$daily_report->report_no				=	$report_no;
		$daily_report->admin_name				=	$this->input->post('admin_name');
		$daily_report->office_chief				=	$this->input->post('office_chief');
		$daily_report->terrain_chief			=	$this->input->post('terrain_chief');
		$daily_report->security_speech			=	$this->input->post('security_speech');
		$daily_report->quality					=	$this->input->post('quality');
		$daily_report->interferences			=	$this->input->post('interferences');
		$daily_report->visits					=	$this->input->post('visits');
		$daily_report->others					=	$this->input->post('others');
		$daily_report->contract_name			= 	$contract_name;
		$daily_report->contract_no				= 	$contract_no;
		$daily_report->control_date				=	$control_date;
		$daily_report->emission_date			=	$emission_date;
		$daily_report->important_activities		=	$important_activities;
		$daily_report->images					=	0;
		$this->db->insert('daily_report', $daily_report);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function configurar_diario($id = '', $b1_ne, $b1_n, $b1_c, $b2_ne, $b2_n, $b2_c, $b3_ne, $b3_n, $b3_c, $b4_ne, $b4_n, $b4_c, $r_hh, $r_w){

		$daily_report					=	new stdClass();

		$daily_report->b1_ne			=	$b1_ne;
		$daily_report->b1_n				=	$b1_n;
		$daily_report->b1_c				=	$b1_c;

		$daily_report->b2_ne			=	$b2_ne;
		$daily_report->b2_n				=	$b2_n;
		$daily_report->b2_c				=	$b2_c;

		$daily_report->b3_ne			=	$b3_ne;
		$daily_report->b3_n				=	$b3_n;
		$daily_report->b3_c				=	$b3_c;

		$daily_report->b4_ne			=	$b4_ne;
		$daily_report->b4_n				=	$b4_n;
		$daily_report->b4_c				=	$b4_c;

		$daily_report->r_hh				=	$r_hh;
		$daily_report->r_w				=	$r_w;

		$this->db->update('daily_report', $daily_report, array(
			'id'	=>	$id
			));
		
	}

	public function actualizar_galeria($id = '', $images = []){
		$daily_report					=	new stdClass();
		$daily_report->images			=	json_encode( $images );
		/*
			Función innecesaria
		*/
		$this->db->update('daily_report', $daily_report, array(
			'id'	=>	$id
			));
	}

	public function actualizar($id = ''){
		$daily_report							=	new stdClass();
		$daily_report->fk_building_site			=	$this->input->post('fk_building_site');
		/*
			Función innecesaria
		*/
		$this->db->update('daily_report', $daily_report, array(
			'id'	=>	$id
			));
	}

	public function borrar($id = ''){
		$this->db->where('id', $id);
		$this->db->delete('daily_report');
	}

	public function obtener($conditions = null, $limit = null, $offset = null){

		$building_sites = $this->building_site->obtener_ordenado();

		$this->db->select("*");
		if(is_array($conditions) && sizeof($conditions) > 0){
			foreach($conditions as $condition){
				$this->db->where($condition);
			}
		}

		if($limit !== null){
			if($offset !== null){
				$this->db->limit($offset, $limit);
			} else {
				$this->db->limit($limit);
			}
		}
		
		$query = $this->db->get('daily_report');
		$Data = array();
		foreach ($query->result() as $row){
			$row->building_site 	= $building_sites[$row->fk_building_site];

			$Data[] = $row;
		}
		return $Data;

	}

	private function ordenar( $array ){
		$Data = array();
		if( is_array($array) && sizeof($array) > 0 ){
			foreach( $array as $value ){
				$Data[ $value->id ] = $value;
			}
		} 		
		return $Data;
	}

	public function obtener_ordenado( $conditions = null, $limit = null, $offset = null ){
		$array = $this->obtener( $conditions, $limit, $offset );
		return $this->ordenar( $array );
		
	}

}

?>