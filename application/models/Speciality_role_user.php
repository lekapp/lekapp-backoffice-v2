<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Speciality_role_user extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('speciality_role');
		$this->load->model('user');
	}

	public function insertar( $speciality_role_id, $user_id ){
		$speciality_role_user						=	new stdClass();
		$speciality_role_user->fk_speciality_role	=	$speciality_role_id;
		$speciality_role_user->fk_user				=	$user_id;
		$this->db->insert('speciality_role_user', $speciality_role_user);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function borrar($id = ''){
		$this->db->where('id', $id);
		$this->db->delete('speciality_role_user');
	}

	public function obtener($conditions = null, $limit = null, $offset = null){

		$speciality_roles = $this->speciality_role->obtener_ordenado();
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
		
		$query = $this->db->get('speciality_role_user');
		$Data = array();
		foreach ($query->result() as $row){
			$row->speciality_role = $speciality_roles[$row->fk_speciality_role];
			$row->user = $users[$row->fk_user];
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