<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Area extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('building_site');
	}

	public function insertar(){
		$area						=	new stdClass();
		$area->fk_building_site		=	$this->input->post('fk_building_site');
		$area->name					=	$this->input->post('name');
		$this->db->insert('area', $area);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function generar( $building_site_id = 0, $name = '' ){
		$area						=	new stdClass();
		$area->fk_building_site		=	$building_site_id;
		$area->name					=	$name;
		$this->db->insert('area', $area);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function actualizar($id = ''){
		$area						=	new stdClass();
		$area->fk_building_site		=	$this->input->post('fk_building_site');
		$area->name					=	$this->input->post('name');
		$this->db->update('area', $area, array(
			'id'	=>	$id
			));
	}

	public function borrar($id = ''){
		$this->db->where('id', $id);
		$this->db->delete('area');
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
		
		$query = $this->db->get('area');
		$Data = array();
		foreach ($query->result() as $row){
			$row->building_site = $building_sites[$row->fk_building_site];
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