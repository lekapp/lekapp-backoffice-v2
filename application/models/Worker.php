<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Worker extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('building_site');
		$this->load->model('speciality_role');
		$this->load->model('speciality');
	}
	public function insertar(){
		$worker							    =	new stdClass();
		$worker->fk_building_site		    =	$this->input->post('fk_building_site');
		$worker->fk_speciality			    =	$this->input->post('fk_speciality');
		$worker->fk_speciality_role		    =	$this->input->post('fk_speciality_role');
        $worker->name						=	$this->input->post('name');
        $worker->email						=	$this->input->post('email');
        $worker->dni						=	$this->input->post('dni');
        $worker->password					=	pack('H*', hash( 'sha512', $this->input->post('password') ));
		$this->db->insert('worker', $worker);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}
	public function generar( $building_site_id = 0, $speciality_id = 0, $speciality_role_id = 0, $name = "", $email = "", $dni = "", $password = ""){
		$worker								=	new stdClass();
		$worker->fk_building_site			=	$building_site_id;
		$worker->fk_speciality		    	=	$speciality_id;
        $worker->fk_speciality_role	    	=	$speciality_role_id;
        $worker->name						=	$name;
        $worker->email						=	$email;
        $worker->dni						=	$dni;
        $worker->password					=	$password;
		$this->db->insert('worker', $worker);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}
	public function actualizar($id = '', $building_site_id, $speciality_id, $speciality_role_id, $name, $email, $dni){
		$worker						    	=	new stdClass();
		$worker->fk_building_site			=	$building_site_id;
		$worker->fk_speciality		    	=	$speciality_id;
		$worker->fk_speciality_role	    	=	$speciality_role_id;
        $worker->name						=	$name;
        $worker->email						=	$email;
        $worker->dni						=	$dni;
		$this->db->update('worker', $worker, array(
			'id'	=>	$id
			));
    }
    public function actualizar_llave($id = '', $pwd){
		$worker						    	=	new stdClass();
        $worker->password					=	pack('H*', hash('sha512', $pwd));
		$this->db->update('worker', $worker, array(
			'id'	=>	$id
			));
	}
	public function borrar($id = ''){
		$this->db->where('id', $id);
		$this->db->delete('worker');
	}
	public function obtener($conditions = null, $limit = null, $offset = null){
		$building_sites = $this->building_site->obtener_ordenado();
		$specialities = $this->speciality->obtener_ordenado();
		$speciality_roles = $this->speciality_role->obtener_ordenado();
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
		
		$query = $this->db->get('worker');
		$Data = array();
		foreach ($query->result() as $row){
			$row->building_site 	    = $building_sites[$row->fk_building_site];
			$row->speciality 		    = $specialities[$row->fk_speciality];
			$row->speciality_role 	    = $speciality_roles[$row->fk_speciality_role];
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