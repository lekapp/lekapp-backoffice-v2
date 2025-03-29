<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Zone extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('building_site');
		$this->load->model('area');
	}

	public function insertar(){
		$zone						=	new stdClass();
		$zone->fk_building_site		=	$this->input->post('fk_building_site');
		$zone->fk_area				=	$this->input->post('fk_area');
		$zone->name					=	$this->input->post('name');
		$this->db->insert('zone', $zone);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function generar( $building_site_id = 0, $area_id = 0, $name = '' ){
		$zone						=	new stdClass();
		$zone->fk_building_site		=	$building_site_id;
		$zone->fk_area		=	$area_id;
		$zone->name					=	$name;
		$this->db->insert('zone', $zone);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function actualizar($id = ''){
		$zone						=	new stdClass();
		$zone->fk_building_site		=	$this->input->post('fk_building_site');
		$zone->fk_area				=	$this->input->post('fk_area');
		$zone->name					=	$this->input->post('name');
		$this->db->update('zone', $zone, array(
			'id'	=>	$id
			));
	}

	public function borrar($id = ''){
		$this->db->where('id', $id);
		$this->db->delete('zone');
	}

	public function obtener($conditions = null, $limit = null, $offset = null){

		$building_sites = $this->building_site->obtener_ordenado();
		$areas = $this->area->obtener_ordenado();

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
		
		$query = $this->db->get('zone');
		$Data = array();
		foreach ($query->result() as $row){
			$row->building_site = $building_sites[$row->fk_building_site];
			$row->area = $areas[$row->fk_area];
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