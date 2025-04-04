<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Activity_registry extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('activity');
		$this->load->model('image');
		$this->load->model('building_site');
	}

	public function insertar($activity, $activity_date)
	{

		$activity_registry = new stdClass();
		$activity_registry->fk_building_site = $activity->fk_building_site;
		$activity_registry->fk_activity = $activity->id;
		$activity_registry->activity_code = $activity->activity_code;
		$activity_registry->fk_speciality = $activity->speciality->id;
		$activity_registry->fk_speciality_role = $this->input->post('fk_speciality_role');
		$activity_registry->activity_date = $activity_date;
		$ad = strtotime($activity_date) / 86400;
		$activity_registry->activity_date_f = $ad;
		$activity_registry->comment = $this->input->post('comment');
		$activity_registry->machinery = $this->input->post('machinery');
		$activity_registry->checked = null;
		//$activity_registry->hh							=	$this->input->post('hh');
		//$activity_registry->workers						=	$this->input->post('workers');
		//$activity_registry->p_avance					=	$this->input->post('p_avance');

		$avance = floatval($this->input->post('avance'));
		$activity_registry->avance = $avance;
		$p_avance = $avance * 100 / floatval($activity->qty);
		return;
		$activity_registry->p_avance = $p_avance;

		$this->db->insert('activity_registry', $activity_registry);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function generate($activity, $activity_date, $hh)
	{
		$activity_registry = new stdClass();
		$activity_registry->fk_building_site = $activity->fk_building_site;
		$activity_registry->fk_activity = $activity->id;
		$activity_registry->activity_code = $activity->activity_code;
		$activity_registry->fk_speciality = $activity->speciality->id;
		$activity_registry->fk_speciality_role = $activity->speciality_role->id;
		$activity_registry->activity_date = $activity_date;
		$ad = strtotime($activity_date) / 86400;
		$activity_registry->activity_date_f = $ad;
		$activity_registry->comment = "-";
		$activity_registry->machinery = "-";
		$activity_registry->hh = $hh;
		$activity_registry->workers = 1;
		$activity_registry->p_avance = 0;
		$activity_registry->checked = null;
		$this->db->insert('activity_registry', $activity_registry);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function start($activity, $activity_date)
	{
		$activity_registry = new stdClass();
		$activity_registry->fk_building_site = $activity->fk_building_site;
		$activity_registry->fk_activity = $activity->id;
		$activity_registry->activity_code = $activity->activity_code;
		$activity_registry->fk_speciality = $activity->speciality->id;
		$activity_registry->fk_speciality_role = $activity->speciality_role->id;
		$activity_registry->activity_date = $activity_date;
		$ad = strtotime($activity_date);
		$activity_registry->activity_date_f = $ad / 86400;
		$activity_registry->comment = "-";
		$activity_registry->machinery = "-";
		$activity_registry->hh = 0;
		$activity_registry->workers = 0;
		$activity_registry->p_avance = 0;
		$activity_registry->checked = null;
		$this->db->insert('activity_registry', $activity_registry);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function actualizar_por_edicion($id = '')
	{
		$t = $this->db->where('id', $id)->get('activity_registry')->row();
		$t = $this->db->where('id', $t->fk_activity)->get('activity')->row();
		$activity_registry = new stdClass();
		$activity_registry->comment = $this->input->post('comment');
		$activity_registry->machinery = $this->input->post('machinery');
		$activity_registry->avance = $this->input->post('avance');
		$activity_registry->p_avance = floatval($activity_registry->avance * 100) / floatval($t->qty);
		$activity_registry->hh = $this->input->post('hh');
		$activity_registry->workers = $this->input->post('workers');
		$activity_registry->activity_date = $this->input->post('activity_date');
		$ad = strtotime($activity_registry->activity_date);
		$activity_registry->activity_date_f = $ad / 86400;
		$activity_registry->activity_code = $this->input->post('activity_code');
		$dtz = new DateTimeZone('America/Santiago');
		$dt = new DateTime('NOW', $dtz);
		$activity_registry->checked = $dt->format('Y-m-d H:i:s');
		$this->db->update(
			'activity_registry',
			$activity_registry,
			array(
				'id' => $id
			)
		);
	}

	public function actualizar($id = '')
	{

		$avance = $this->input->post('avance');

		$currentActivityRegistry = $this->db->get_where(
			'activity_registry',
			array(
				'id' => $id
			)
		)->row();

		$activity_registry = new stdClass();
		$activity_registry->comment = $this->input->post('comment');
		$activity_registry->machinery = $this->input->post('machinery');
		$activity_registry->avance = $avance;
		$dtz = new DateTimeZone('America/Santiago');
		$dt = new DateTime('NOW', $dtz);
		$activity_registry->checked = $dt->format('Y-m-d H:i:s');

		$activity = $this->db->get_where(
			'activity',
			array(
				'id' => $currentActivityRegistry->fk_activity
			)
		)->row();

		$p_avance = floatval($avance) * 100 / floatval($activity->qty);

		$activity_registry->p_avance = $p_avance;

		$this->db->update(
			'activity_registry',
			$activity_registry,
			array(
				'id' => $id
			)
		);
	}

	public function actualizar_por_app($id = '', $comment, $machinery, $avance)
	{

		$currentActivityRegistry = $this->db->from('activity_registry')
			->where('id', $id)
			->get()->row();

		$currentActivityRegistry->activity = $this->db->from('activity')
			->where('id', $currentActivityRegistry->fk_activity)
			->get()->row();

		if ($currentActivityRegistry->activity->qty > 0)
			$p_avance = $avance / $currentActivityRegistry->activity->qty * 100;
		else
			$p_avance = 0;

		$activity_registry = new stdClass();
		$activity_registry->comment = $comment;
		$activity_registry->machinery = $machinery;
		$activity_registry->avance = $avance;
		$activity_registry->p_avance = $p_avance;
		$activity_registry->checked = null;
		$this->db->update(
			'activity_registry',
			$activity_registry,
			array(
				'id' => $id
			)
		);
	}

	public function actualizar_hh($id = '', $new_hh = 0, $workers = 0)
	{
		$activity_registry = new stdClass();
		$activity_registry->hh = $new_hh;
		$activity_registry->workers = $workers;
		$this->db->update(
			'activity_registry',
			$activity_registry,
			array(
				'id' => $id
			)
		);
	}

	public function actualizar_imagen($id = '', $image_id = 0)
	{
		$activity_registry = new stdClass();
		$activity_registry->fk_image = $image_id;
		$this->db->update(
			'activity_registry',
			$activity_registry,
			array(
				'id' => $id
			)
		);
	}

	public function borrar($id = '')
	{
		$this->db->where('id', $id);
		$this->db->delete('activity_registry');
	}

	public function obtener($conditions = null, $limit = null, $offset = null)
	{

		$images = $this->image->obtener_ordenado();
		$activities = $this->activity->obtener_ordenado();
		$specialities = $this->speciality->obtener_ordenado();
		$speciality_roles = $this->speciality_role->obtener_ordenado();
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

		$query = $this->db->get('activity_registry');
		$Data = array();
		foreach ($query->result() as $row) {
			$row->activity = $activities[$row->fk_activity];
			$row->speciality = $specialities[$row->fk_speciality];
			$row->speciality_role = $speciality_roles[$row->fk_speciality_role];
			$row->building_site = $building_sites[$row->fk_building_site];
			if ($row->fk_image != 0) {
				$row->image = $images[$row->fk_image];
			}
			$Data[] = $row;
		}
		return $Data;
	}

	public function obtener_ordenado_esp($conditions = null, $limit = null, $offset = null, $criteria = "id", $order = "asc")
	{

		$images = $this->image->obtener_ordenado();
		$activities = $this->activity->obtener_ordenado();
		$specialities = $this->speciality->obtener_ordenado();
		$speciality_roles = $this->speciality_role->obtener_ordenado();
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

		$this->db->order_by($criteria, $order);

		$query = $this->db->get('activity_registry');
		$Data = array();
		foreach ($query->result() as $row) {
			$row->activity = $activities[$row->fk_activity];
			$row->speciality = $specialities[$row->fk_speciality];
			$row->speciality_role = $speciality_roles[$row->fk_speciality_role];
			$row->building_site = $building_sites[$row->fk_building_site];
			if ($row->fk_image != 0) {
				$row->image = $images[$row->fk_image];
			}
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