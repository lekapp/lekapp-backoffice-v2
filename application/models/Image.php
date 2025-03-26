<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Image extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('image_type');
	}

	public function insertar($name, $ext, $type)
	{
		$image = new stdClass();
		$image->name = $name;
		$image->ext = $ext;
		$image->fk_image_type = $type;
		$this->db->insert('image', $image);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function actualizar($id = '', $name, $ext)
	{
		$image = new stdClass();
		$image->name = $name;
		$image->ext = $ext;
		$this->db->update('image', $image, array(
			'id' => $id
		)
		);
	}

	public function actualizar_tipo_imagen($id = '')
	{
		$image = new stdClass();
		$image->fk_image_type = $this->input->post('fk_image_type');
		$this->db->update('image', $image, array(
			'id' => $id
		)
		);
	}

	public function borrar($id = '')
	{
		$this->db->where('id', $id);
		$this->db->delete('image');
	}

	public function obtener($conditions = null, $limit = null, $offset = null)
	{

		$image_types = $this->image_type->obtener_ordenado();

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

		$query = $this->db->get('image');
		$Data = array();
		foreach ($query->result() as $row) {
			$row->image_type = $image_types[$row->fk_image_type];

			if (in_array($row->image_type->code_name, array('default_avatar', 'custom_avatar'))) {
				$row->url = '../' . UPLOAD . IMG . USER;
			} elseif (in_array($row->image_type->code_name, array('activity_report'))) {
				$row->url = '../' . UPLOAD . IMG . REPORT;
			} else {
				$row->url = '';
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

?>