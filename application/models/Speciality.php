<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Speciality extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('building_site');
	}

	public function insertar(){
		$speciality							=	new stdClass();
		$speciality->fk_building_site		=	$this->input->post('fk_building_site');
		$speciality->name					=	$this->input->post('name');
		$this->db->insert('speciality', $speciality);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function generar( $building_site_id = 0, $name = '' ){
		$zone						=	new stdClass();
		$zone->fk_building_site		=	$building_site_id;
		$zone->name					=	$name;
		$this->db->insert('speciality', $zone);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function actualizar($id = ''){
		$speciality							=	new stdClass();
		$speciality->fk_building_site		=	$this->input->post('fk_building_site');
		$speciality->name					=	$this->input->post('name');
		$this->db->update('speciality', $speciality, array(
			'id'	=>	$id
			));
	}

	public function borrar($id = ''){
		$this->load->model('supervisor');

		$supervisors = $this->supervisor->obtener(
			array(
				array(
					'fk_speciality'	=>	$id
				)
			)
		);

		foreach($supervisors as $supervisor){
			$this->supervisor->borrar( $supervisor->id );
		}

		$this->db->where('id', $id);
		$this->db->delete('speciality');
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
		
		$query = $this->db->get('speciality');
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

	public function buscar_ordenado( $hc, $sc, $order_by = 'id', $ordering = "asc", $limit = null, $offset = null ){

		$array = $this->buscar( $hc, $sc, $order_by, $ordering, $limit, $offset );
		return $this->ordenar( $array );

	}

	public function obtener_ordenado( $conditions = null, $limit = null, $offset = null ){

		$array = $this->obtener( $conditions, $limit, $offset );
		return $this->ordenar( $array );
		
	}

}

?>