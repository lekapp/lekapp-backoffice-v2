<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Activity extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('building_site');
		$this->load->model('speciality_role');
		$this->load->model('zone');
	}
	public function insertar()
	{
		$activity							=	new stdClass();
		$activity->fk_building_site			=	$this->input->post('fk_building_site');
		$activity->fk_speciality			=	$this->input->post('fk_speciality');
		$activity->fk_speciality_role		=	$this->input->post('fk_speciality_role');
		$activity->fk_zone					=	$this->input->post('fk_zone');
		$activity->name						=	$this->input->post('name');
		$activity->f_data					=	0;
		$activity->unt						=	'';
		$activity->qty						=	'';
		$activity->eff						=	'';
		$activity->activity_code			=	'';
		$this->db->insert('activity', $activity);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}
	public function generar($building_site_id = 0, $speciality_id = 0, $speciality_role_id = 0, $zone_id = 0, $name = '', $f_data = 0, $unt = '', $qty = '', $eff = '', $code = '')
	{
		$activity								=	new stdClass();
		$activity->fk_building_site			=	$building_site_id;
		$activity->fk_speciality			=	$speciality_id;
		$activity->fk_speciality_role		=	$speciality_role_id;
		$activity->fk_zone					=	$zone_id;
		$activity->name						=	$name;
		$activity->f_data					=	$f_data;
		$activity->unt						=	$unt;
		$activity->qty						=	$qty;
		$activity->eff						=	$eff;
		$activity->activity_code			=	$code;
		$this->db->insert('activity', $activity);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}
	public function actualizar($id = '')
	{
		$activity							=	new stdClass();
		$activity->fk_building_site			=	$this->input->post('fk_building_site');
		$activity->fk_speciality			=	$this->input->post('fk_speciality');
		$activity->fk_speciality_role		=	$this->input->post('fk_speciality_role');
		$activity->fk_zone					=	$this->input->post('fk_zone');
		$activity->name						=	$this->input->post('name');
		$this->db->update('activity', $activity, array(
			'id'	=>	$id
		));
	}
	public function borrar($id = '')
	{
		$this->db->where('id', $id);
		$this->db->delete('activity');
	}
	public function obtener($conditions = null, $limit = null, $offset = null)
	{
		$building_sites = $this->building_site->obtener_ordenado();
		$specialities = $this->speciality->obtener_ordenado();
		$speciality_roles = $this->speciality_role->obtener_ordenado();
		$zones = $this->zone->obtener_ordenado();
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

		$query = $this->db->get('activity');
		$Data = array();
		foreach ($query->result() as $row) {
			if ($row->fk_building_site != 0)
				$row->building_site 	= $building_sites[$row->fk_building_site];
			if ($row->fk_speciality != 0)
				$row->speciality 		= $specialities[$row->fk_speciality];
			if ($row->fk_speciality_role != 0)
				$row->speciality_role 	= $speciality_roles[$row->fk_speciality_role];
			if ($row->fk_zone != 0)
				$row->zone 				= $zones[$row->fk_zone];
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
