<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Milestone extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('building_site');
	}

	public function insertar(){
		$milestone						=	new stdClass();
		$milestone->fk_building_site		=	$this->input->post('fk_building_site');
		$milestone->name					=	$this->input->post('name');
		$milestone->type                 =   $this->input->post('type');
		$milestone->date                 =   $this->input->post('date');
		$this->db->insert('milestone', $milestone);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function generar( $building_site_id = 0, $name = '', $type = '', $date = '' ){
		$milestone						=	new stdClass();
		$milestone->fk_building_site		=	$building_site_id;
		$milestone->name                 =   $name;
		$milestone->type                 =   $type;
		$milestone->date                 =   $date;
		$this->db->insert('milestone', $milestone);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function actualizar($id = ''){
		$milestone						=	new stdClass();
		$milestone->fk_building_site		=	$this->input->post('fk_building_site');
		$milestone->name                 =   $this->input->post('name');
		$milestone->type                 =   $this->input->post('type');
		$milestone->date                 =   $this->input->post('date');
		$this->db->update('milestone', $milestone, array(
			'id'	=>	$id
			));
	}

	public function borrar($id = ''){
		$this->db->where('id', $id);
		$this->db->delete('milestone');
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
		
		$query = $this->db->get('milestone');
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
