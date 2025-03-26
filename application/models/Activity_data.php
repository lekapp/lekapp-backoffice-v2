<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Activity_data extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('activity');
		$this->load->model('image');
	}

	public function insertar()
	{
		$activity_data							=	new stdClass();
		$activity_data->fk_activity				=	$this->input->post('fk_activity');
		$activity_data->fk_building_site		=	$this->input->post('fk_building_site');
		$activity_data->fk_speciality			=	$this->input->post('fk_speciality');
		$activity_data->fk_speciality_role		=	$this->input->post('fk_speciality_role');
		$activity_data->fk_zone					=	$this->input->post('fk_zone');
		$activity_data->activity_date			=	$this->input->post('activity_date');

		$theDate = gmdate("Y-m-d", ($activity_data->activity_date) * 86400);
		$dt = new DateTime($theDate, new DateTimeZone('America/Santiago'));
		$activity_data->activity_date_dt = $dt->format('Y-m-d H:i:s');

		$activity_data->hh						=	$this->input->post('hh');
		$activity_data->comment					=	'';
		$this->db->insert('activity_data', $activity_data);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function generar($activity_id = 0, $building_site_id = 0, $speciality_id = 0, $speciality_role_id = 0, $zone_id = 0, $activity_date = 0, $hh = 0)
	{
		$activity_data								=	new stdClass();
		$activity_data->fk_activity					=	$activity_id;
		$activity_data->fk_building_site			=	$building_site_id;
		$activity_data->fk_speciality				=	$speciality_id;
		$activity_data->fk_speciality_role			=	$speciality_role_id;
		$activity_data->fk_zone						=	$zone_id;
		$activity_data->activity_date				=	$activity_date;

		$theDate = gmdate("Y-m-d", ($activity_data->activity_date) * 86400);
		$dt = new DateTime($theDate, new DateTimeZone('America/Santiago'));
		$activity_data->activity_date_dt = $dt->format('Y-m-d H:i:s');

		$activity_data->hh							=	$hh;
		$activity_data->comment						=	'';
		$this->db->insert('activity_data', $activity_data);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function actualizar($id = '')
	{
		$activity_data							=	new stdClass();
		$activity_data->fk_activity				=	$this->input->post('fk_activity');
		$activity_data->fk_building_site		=	$this->input->post('fk_building_site');
		$activity_data->fk_speciality			=	$this->input->post('fk_speciality');
		$activity_data->fk_speciality_role		=	$this->input->post('fk_speciality_role');
		$activity_data->fk_zone					=	$this->input->post('fk_zone');
		$activity_data->activity_date			=	$this->input->post('activity_date');

		$theDate = gmdate("Y-m-d", ($activity_data->activity_date) * 86400);
		$dt = new DateTime($theDate, new DateTimeZone('America/Santiago'));
		$activity_data->activity_date_dt = $dt->format('Y-m-d H:i:s');

		$activity_data->hh						=	$this->input->post('hh');
		$this->db->update('activity_data', $activity_data, array(
			'id'	=>	$id
		));
	}

	public function actualizar_comentario($id = '')
	{
		$activity_data							=	new stdClass();
		$activity_data->comment					=	$this->input->post('comment');
		$this->db->update('activity_data', $activity_data, array(
			'id'	=>	$id
		));
	}

	public function actualizar_imagen($id = 0, $file_input = false, $img_id = 0, $errors = "")
	{
		$activity_data							=	new stdClass();
		if ($file_input != false && $errors == "") {
			$activity_data->has_image			=	1;
			$activity_data->fk_image			=	$img_id;
		} else {
			$activity_data->has_image			=	0;
			$activity_data->fk_image			=	1;
		}
		$this->db->update('activity_data', $activity_data, array(
			'id'	=>	$id
		));
	}

	public function establecer_imagen($id = '')
	{
		$activity_data							=	new stdClass();
		$activity_data->has_image				=	0;
		$activity_data->fk_image				=	0;
		$this->db->update('activity_data', $activity_data, array(
			'id'	=>	$id
		));
	}

	public function borrar($id = '')
	{
		$this->db->where('id', $id);
		$this->db->delete('activity_data');
	}

	public function obtener($conditions = null, $limit = null, $offset = null)
	{

		$activities = $this->activity->obtener_ordenado();
		$speciality_roles = $this->speciality_role->obtener_ordenado();

		$it = $this->image_type->obtener(
			array(
				array(
					'code_name'	=>	'activity_report'
				)
			)
		);
		$itrp = $it[0];
		$irp = $this->image->obtener_ordenado(
			array(
				array(
					'fk_image_type'	=>	$itrp->id
				)
			)
		);

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

		$query = $this->db->get('activity_data');
		$Data = array();
		foreach ($query->result() as $row) {

			if ($row->has_image == 0) {
				$row->image = null;
			} else {
				$row->image = $irp[$row->fk_image];
				$row->photo_url = $row->image->url . $row->id . '/' . $row->image->name . $row->image->ext;
			}

			$row->activity 				= $activities[$row->fk_activity];
			$row->speciality_role 		= $speciality_roles[$row->fk_speciality_role];
			$Data[] = $row;
		}
		return $Data;
	}

	public function obtener_ordenado_esp($conditions = null, $limit = null, $offset = null, $criteria = "id", $order = "asc")
	{

		$activities = $this->activity->obtener_ordenado();

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

		$this->db->order_by($criteria, $order);

		$query = $this->db->get('activity_data');
		$Data = array();
		foreach ($query->result() as $row) {
			$row->activity 	= $activities[$row->fk_activity];
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

	public function buscar_ordenado($hc, $sc, $order_by = 'id', $ordering = "asc", $limit = null, $offset = null)
	{

		$array = $this->buscar($hc, $sc, $order_by, $ordering, $limit, $offset);
		return $this->ordenar($array);
	}

	public function obtener_ordenado($conditions = null, $limit = null, $offset = null)
	{

		$array = $this->obtener($conditions, $limit, $offset);
		return $this->ordenar($array);
	}
}
