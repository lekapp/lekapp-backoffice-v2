<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Worker_activity extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('building_site');
		$this->load->model('worker');
	}
	public function insertar(){
		$worker_activity							=	new stdClass();
        $worker_activity->fk_building_site		    =	$this->input->post('fk_building_site');
        $worker_activity->fk_worker		            =	$this->input->post('fk_worker');
        $worker_activity->hh                        =   $this->input->post('hh');
        $worker_activity->date                      =   $this->input->post('date');
        $worker_activity->code                      =   $this->input->post('code');
		$this->db->insert('worker_activity', $worker_activity);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}
	public function generar( $building_site_id = 0, $worker_id = 0, $hh = "", $date = "", $code = "" ){
		$worker_activity							=	new stdClass();
		$worker_activity->fk_building_site			=	$building_site_id;
        $worker_activity->fk_worker			        =	$worker_id;
        $worker_activity->hh                        =   $hh;
        $worker_activity->date                      =   $date;
        $worker_activity->code                      =   $code;
		$this->db->insert('worker_activity', $worker_activity);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}
	public function actualizar($id = '', $building_site_id, $worker_id, $hh, $date, $code ){
		$worker_activity						    	=	new stdClass();
		$worker_activity->fk_building_site			=	$building_site_id;
        $worker_activity->fk_worker			        =	$worker_id;
        $worker_activity->hh                        =   $hh;
        $worker_activity->date                      =   $date;
        $worker_activity->code                      =   $code;
		$this->db->update('worker_activity', $worker_activity, array(
			'id'	=>	$id
			));
    }
    public function actualizar_llave($id = '', $pwd){
		$worker_activity						    	=	new stdClass();
        $worker_activity->password					=	$pwd;
		$this->db->update('worker_activity', $worker_activity, array(
			'id'	=>	$id
			));
	}
	public function borrar($id = ''){
		$this->db->where('id', $id);
		$this->db->delete('worker_activity');
	}
	public function obtener($conditions = null, $limit = null, $offset = null){
        $building_sites = $this->building_site->obtener_ordenado();
        $workers = $this->worker->obtener_ordenado();

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
		
		$query = $this->db->get('worker_activity');
		$Data = array();
		foreach ($query->result() as $row){
			$row->building_site 	    = $building_sites[$row->fk_building_site];
            $row->worker 	    = $workers[$row->fk_worker];
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
