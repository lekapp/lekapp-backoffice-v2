<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Speciality_role extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('speciality');
		$this->load->model('building_site');
	}

	public function insertar(){
		$speciality_role					=	new stdClass();
		$speciality_role->fk_building_site	=	$this->input->post('fk_building_site');
		$speciality_role->fk_speciality		=	$this->input->post('fk_speciality');
		$speciality_role->name				=	$this->input->post('name');
		$speciality_role->hh				=	0;
		$this->db->insert('speciality_role', $speciality_role);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function generar( $building_site_id = 0, $speciality_id = 0, $name = '', $hh = 0 ){
		$speciality_role						=	new stdClass();
		$speciality_role->fk_building_site		=	$building_site_id;
		$speciality_role->fk_speciality			=	$speciality_id;
		$speciality_role->name					=	$name;
		$speciality_role->hh					=	$hh;
		$this->db->insert('speciality_role', $speciality_role);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function actualizar($id = ''){
		$speciality_role						=	new stdClass();
		$speciality_role->fk_building_site		=	$this->input->post('fk_building_site');
		$speciality_role->fk_speciality			=	$this->input->post('fk_speciality');
		$speciality_role->name					=	$this->input->post('name');
		$speciality_role->hh					=	$this->input->post('hh');
		$this->db->update('speciality_role', $speciality_role, array(
			'id'	=>	$id
			));
	}

	public function agregar_hh($id = '', $hh = 0){
		$speciality_role					=	new stdClass();
		$speciality_role->hh				=	$hh ;
		$this->db->update('speciality_role', $speciality_role, array(
			'id'	=>	$id
			));
	}

	public function borrar($id = ''){
		$this->db->where('id', $id);
		$this->db->delete('speciality_role');
	}

	public function obtener($conditions = null, $limit = null, $offset = null){

		$specialities = $this->speciality->obtener_ordenado();
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
		
		$query = $this->db->get('speciality_role');
		$Data = array();
		foreach ($query->result() as $row){
			$row->speciality = $specialities[$row->fk_speciality];
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