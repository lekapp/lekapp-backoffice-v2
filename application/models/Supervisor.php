<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Supervisor extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('speciality');
		$this->load->model('user');
	}

	public function insertar( $speciality_id, $user_id )
	{
		$supervisor						=	new stdClass();
		$supervisor->fk_speciality		=	$speciality_id;
		$supervisor->fk_user			=	$user_id;
		$this->db->insert('supervisor', $supervisor);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}


	public function actualizar_usuario( $supervisor = null )
	{
		$user						=	new stdClass();
		$user->email				=	$this->input->post('email');
		$user->dni					=	$this->input->post('dni');
		$user->first_name			=	$this->input->post('first_name');
		$user->last_name			=	$this->input->post('last_name');
		$user->address_1			=	$this->input->post('address_1');
		$user->address_2			=	$this->input->post('address_2');
		$user->phone_1				=	$this->input->post('phone_1');
		$user->phone_2				=	$this->input->post('phone_2');
		$result = $this->db->update('user', $user, array(
			'id'	=>	$supervisor[0]->fk_user
			));
		return $result;
	}

	public function actualizar_llave($id = ''){
		$user						=	new stdClass();
		$user->password				=	pack('H*', hash( 'sha512', $this->input->post('password') ));
		$this->db->update('user', $user, array(
			'id'	=>	$id
			));
	}


	public function borrar($id = ''){
		$this->db->where('id', $id);
		$this->db->delete('supervisor');
	}

	public function obtener($conditions = null, $limit = null, $offset = null){

		$specialities = $this->speciality->obtener_ordenado();
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
		
		$query = $this->db->get('supervisor');
		$Data = array();
		foreach ($query->result() as $row){
			$row->speciality = $specialities[$row->fk_speciality];
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
