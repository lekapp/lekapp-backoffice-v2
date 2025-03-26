<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Building_site extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('user');
	}

	public function insertar(){
		$building_site						=	new stdClass();
		$building_site->fk_client			=	$this->input->post('fk_client');
		$building_site->fk_contractor		=	$this->input->post('fk_contractor');
		$building_site->name				=	$this->input->post('name');
		$building_site->code				=	$this->input->post('code');
		$building_site->address_street		=	$this->input->post('address_street');
		$building_site->address_number		=	$this->input->post('address_number');
		$building_site->address_city		=	$this->input->post('address_city');
		$building_site->address_city		=	$this->input->post('address_city');
		$building_site->current_version		=	0;
		$this->db->insert('building_site', $building_site);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function actualizar($id = ''){
		$building_site						=	new stdClass();
		$building_site->fk_client			=	$this->input->post('fk_client');
		$building_site->fk_contractor		=	$this->input->post('fk_contractor');
		$building_site->name				=	$this->input->post('name');
		$building_site->code				=	$this->input->post('code');
		$building_site->address_street		=	$this->input->post('address_street');
		$building_site->address_number		=	$this->input->post('address_number');
		$building_site->address_city		=	$this->input->post('address_city');
		$this->db->update('building_site', $building_site, array(
			'id'	=>	$id
			));
	}

	public function configurar_diario($id = '', $b1_ne, $b1_n, $b1_c, $b2_ne, $b2_n, $b2_c, $b3_ne, $b3_n, $b3_c, $b4_ne, $b4_n, $b4_c){

		$building_site					=	new stdClass();

		$building_site->b1_ne			=	$b1_ne;
		$building_site->b1_n			=	$b1_n;
		$building_site->b1_c			=	$b1_c;

		$building_site->b2_ne			=	$b2_ne;
		$building_site->b2_n			=	$b2_n;
		$building_site->b2_c			=	$b2_c;

		$building_site->b3_ne			=	$b3_ne;
		$building_site->b3_n			=	$b3_n;
		$building_site->b3_c			=	$b3_c;

		$building_site->b4_ne			=	$b4_ne;
		$building_site->b4_n			=	$b4_n;
		$building_site->b4_c			=	$b4_c;

		$this->db->update('building_site', $building_site, array(
			'id'	=>	$id
			));

	}

	public function borrar($id = ''){
		$this->db->where('id', $id);
		$this->db->delete('building_site');
	}

	public function obtener($conditions = null, $limit = null, $offset = null){

		$users = $this->user->obtener_ordenado();

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
		
		$query = $this->db->get('building_site');
		$Data = array();
		foreach ($query->result() as $row){
			$row->client = $users[$row->fk_client];
			$row->contractor = $users[$row->fk_contractor];
			$Data[] = $row;
		}
		return $Data;

	}

	public function obtener_usuario($conditions = null, $limit = null, $offset = null){

		$users = $this->user->obtener_ordenado();
		$building_sites = $this->obtener_ordenado();

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
		
		$query = $this->db->get('building_site_user');
		$Data = array();
		foreach ($query->result() as $row){
			$row->user = $users[$row->fk_user];
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