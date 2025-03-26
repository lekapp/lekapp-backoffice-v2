<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Image_type extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	public function insertar(){
		$image_type						=	new stdClass();
		$image_type->name				=	$this->input->post('name');
		$image_type->code_name			=	$this->input->post('code_name');
		$this->db->insert('image_type', $image_type);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function actualizar($id = ''){
		$image_type						=	new stdClass();
		$image_type->name				=	$this->input->post('name');
		$image_type->code_name			=	$this->input->post('code_name');
		$this->db->update('image_type', $image_type, array(
			'id'	=>	$id
			));
	}

	public function borrar($id = ''){
		$this->db->where('id', $id);
		$this->db->delete('image_type');
	}

	public function buscar($hc, $sc, $order_by = 'id', $ordering = "asc", $limit = null, $offset = null){
		$this->db->select("*");

		if(is_array($hc) && sizeof($hc) > 0){
			foreach($hc as $condition){
				$this->db->where($condition);
			}
		}

		if(is_array($sc) && sizeof($sc) > 0){
			foreach($sc as $condition){
				$this->db->like($condition);
			}
		}

		$this->db->order_by( $order_by , $ordering ); 
		
		if($limit !== null){
			if($offset !== null){
				$this->db->limit($offset, $limit);
			} else {
				$this->db->limit($limit);
			}
		}
		
		$query = $this->db->get('image_type');
		$data = array();
		foreach ($query->result() as $row){
			$data[] = $row;
		}
		return $data;
	}

	public function obtener($conditions = null, $limit = null, $offset = null){

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
		
		$query = $this->db->get('image_type');
		$Data = array();
		foreach ($query->result() as $row){
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