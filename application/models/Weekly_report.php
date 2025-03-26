<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Weekly_report extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('building_site');
	}

	public function insertar($building_site_id, $activity_date, $report_no, $json_data)
	{
		$weekly_report	=	new stdClass();
		$weekly_report->fk_building_site	=	$building_site_id;
		$weekly_report->activity_date	=	$activity_date;
        $weekly_report->report_no	=	$report_no;
        $weekly_report->json_data	=	$json_data;
		$weekly_report->images	=	0;
		$this->db->insert('weekly_report', $weekly_report);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function actualizar_galeria($id = '', $images = [])
	{
		$weekly_report	=	new stdClass();
		$weekly_report->images	=	json_encode($images);
		/*
	Función innecesaria
	*/
		$this->db->update('weekly_report', $weekly_report, array(
			'id'	=>	$id
		));
	}

	public function actualizar($id = '')
	{
		$weekly_report	=	new stdClass();
		$weekly_report->fk_building_site	=	$this->input->post('fk_building_site');
		/*
	Función innecesaria
	*/
		$this->db->update('weekly_report', $weekly_report, array(
			'id'	=>	$id
		));
	}

	public function borrar($id = '')
	{
		$this->db->where('id', $id);
		$this->db->delete('weekly_report');
	}

	public function obtener($conditions = null, $limit = null, $offset = null)
	{

		$building_sites = $this->building_site->obtener_ordenado();

		$this->db->select("*");
		if (is_array($conditions) && sizeof($conditions) > 0) {
			foreach ($conditions as $condition) {
				$this->db->where($condition);
			}
		}

		if ($limit !== null) {
			if ($offset !== null) {
				$this->db->limit($offset, $limit);
			} else {
				$this->db->limit($limit);
			}
		}

		$query = $this->db->get('weekly_report');
		$Data = array();
		foreach ($query->result() as $row) {
			$row->building_site	 = $building_sites[$row->fk_building_site];
			$Data[] = $row;
		}
		return $Data;
	}

	private function ordenar($array)
	{
		$Data = array();
		if (is_array($array) && sizeof($array) > 0) {
			foreach ($array as $value) {
				$Data[$value->id] = $value;
			}
		}
		return $Data;
	}

	public function obtener_ordenado($conditions = null, $limit = null, $offset = null)
	{
		$array = $this->obtener($conditions, $limit, $offset);
		return $this->ordenar($array);
	}
}
