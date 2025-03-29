<?php

defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('role');
		$this->load->model('gender');
		$this->load->model('image');
	}

	public function insertar()
	{
		$user						=	new stdClass();
		$user->email				=	$this->input->post('email');
		$user->password				=	pack('H*', hash('sha512', $this->input->post('password')));
		//bin2hex( textoBinario )
		$user->dni					=	$this->input->post('dni');
		$user->first_name			=	$this->input->post('first_name');
		$user->last_name			=	$this->input->post('last_name');
		$user->address_1			=	$this->input->post('address_1');
		$user->address_2			=	$this->input->post('address_2');
		$user->phone_1				=	$this->input->post('phone_1');
		$user->phone_2				=	$this->input->post('phone_2');
		$this->db->insert('user', $user);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function actualizar($id = '')
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
		$this->db->update('user', $user, array(
			'id'	=>	$id
		));
	}

	public function generar($email)
	{
		$user						=	new stdClass();
		$user->email				=	$email;
		$user->password				=	pack('H*', hash('sha512', $email));
		$user->dni					=	'';
		$user->first_name			=	'';
		$user->last_name			=	'';
		$user->address_1			=	'';
		$user->address_2			=	'';
		$user->phone_1				=	'';
		$user->phone_2				=	'';
		$this->db->insert('user', $user);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;
		return $result;
	}

	public function actualizar_llave($id = '')
	{
		$user						=	new stdClass();
		$user->password				=	pack('H*', hash('sha512', $this->input->post('password')));
		$this->db->update('user', $user, array(
			'id'	=>	$id
		));
	}

	public function establecer_llave($id = '', $llave = '')
	{
		$user						=	new stdClass();
		$user->password				=	pack('H*', $llave);
		$this->db->update('user', $user, array(
			'id'	=>	$id
		));
	}

	public function actualizar_rol($id = '')
	{
		$user						=	new stdClass();
		$user->fk_role				=	$this->input->post('fk_role');
		$this->db->update('user', $user, array(
			'id'	=>	$id
		));
	}

	public function establecer_rol($id = '', $rol = 4)
	{
		$user						=	new stdClass();
		$user->fk_role				=	$rol;
		$this->db->update('user', $user, array(
			'id'	=>	$id
		));
	}

	public function actualizar_genero($id = '')
	{
		$user						=	new stdClass();
		$user->fk_gender			=	$this->input->post('fk_gender');
		$this->db->update('user', $user, array(
			'id'	=>	$id
		));
	}

	public function establecer_genero($id = '', $genero = 1)
	{
		$user						=	new stdClass();
		$user->fk_gender			=	$genero;
		$this->db->update('user', $user, array(
			'id'	=>	$id
		));
	}

	public function actualizar_imagen($id = 0, $file_input = false, $img_id = 0, $errors = "")
	{
		$user						=	new stdClass();
		if ($file_input != false && $errors == "") {
			$user->has_image		=	1;
			$user->fk_image			=	$img_id;
		} else {
			$user->has_image		=	0;
			$user->fk_image			=	1;
		}
		$this->db->update('user', $user, array(
			'id'	=>	$id
		));
	}

	public function establecer_imagen($id = '')
	{
		$user						=	new stdClass();
		$user->has_image			=	0;
		$user->fk_image				=	0;
		$this->db->update('user', $user, array(
			'id'	=>	$id
		));
	}

	public function insertar_inicial()
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
		$this->db->insert('user', $user);
		$result = $this->db->insert_id() > 0 ? $this->db->insert_id() : FALSE;

		$this->establecer_llave($result, $user->dni);

		$gender = $this->gender->obtener_ordenado(
			array(
				array(
					'value'	=>	'Desconocido'
				)
			)
		);

		$this->establecer_genero($result, $gender[0]->id);

		$image = $this->image->obtener_ordenado(
			array(
				array(
					'name'	=>	'avatar_u'
				)
			)
		);

		$this->establecer_imagen($result);

		return $result;
	}

	public function borrar($id = '')
	{

		$dt = new DateTime('now', new DateTimeZone('America/Santiago'));

		$this->db->where('id', $id);
		$this->db->set('deleted_at', $dt->format('Y-m-d H:i:s'));
		$this->db->update('user');

		//$this->db->delete('user');
	}

	public function obtener($conditions = null, $limit = null, $offset = null)
	{

		$roles = $this->role->obtener_ordenado();
		$genders = $this->gender->obtener_ordenado();
		//$imagenes = $this->image->obtener_ordenado();

		$it = $this->image_type->obtener(
			array(
				array(
					'code_name'	=>	'default_avatar'
				)
			)
		);

		$itda = $it[0];

		$it = $this->image_type->obtener(
			array(
				array(
					'code_name'	=>	'custom_avatar'
				)
			)
		);

		$itca = $it[0];

		$ica = $this->image->obtener_ordenado(
			array(
				array(
					'fk_image_type'	=>	$itca->id
				)
			)
		);

		$this->db->select("*");
		if (is_array($conditions) && sizeof($conditions) > 0) {
			foreach ($conditions as $condition) {
				$this->db->where($condition);
			}
		}
		$this->db->where('deleted_at', null);

		if ($limit !== null) {
			if ($offset !== null) {
				$this->db->limit($offset, $limit);
			} else {
				$this->db->limit($limit);
			}
		}

		$query = $this->db->get('user');
		$Data = array();
		foreach ($query->result() as $row) {
			$row->gender = $genders[$row->fk_gender];
			$row->role = $roles[$row->fk_role];
			switch ($row->gender->code) {
				case 'f':
					$row->role->name = $row->role->value_f;
					break;
				case 'm':
					$row->role->name = $row->role->value_m;
					break;
				case 'u':
					$row->role->name = $row->role->value_p;
					break;
			}
			if ($row->has_image == 0) {
				$row->image = null;
				$row->avatar_url = null;
			} else {
				$row->image = $ica[$row->fk_image];
				$row->avatar_url = $row->image->url . $row->id . '/' . $row->image->name . $row->image->ext;
			}
			$Data[] = $row;
		}
		return $Data;
	}

	public function obtenerTodos($conditions = null, $limit = null, $offset = null)
	{

		$roles = $this->role->obtener_ordenado();
		$genders = $this->gender->obtener_ordenado();
		//$imagenes = $this->image->obtener_ordenado();

		$it = $this->image_type->obtener(
			array(
				array(
					'code_name'	=>	'default_avatar'
				)
			)
		);

		$itda = $it[0];

		$it = $this->image_type->obtener(
			array(
				array(
					'code_name'	=>	'custom_avatar'
				)
			)
		);

		$itca = $it[0];

		$ica = $this->image->obtener_ordenado(
			array(
				array(
					'fk_image_type'	=>	$itca->id
				)
			)
		);

		$this->db->select("*");
		if (is_array($conditions) && sizeof($conditions) > 0) {
			foreach ($conditions as $condition) {
				$this->db->where($condition);
			}
		}

		if ($limit !== null) {
			if ($offset !== null) {
				$this->db->limit($offset, $limit);
			} else {
				$this->db->limit($limit);
			}
		}

		$query = $this->db->get('user');
		$Data = array();
		foreach ($query->result() as $row) {
			$row->gender = $genders[$row->fk_gender];
			$row->role = $roles[$row->fk_role];
			switch ($row->gender->code) {
				case 'f':
					$row->role->name = $row->role->value_f;
					break;
				case 'm':
					$row->role->name = $row->role->value_m;
					break;
				case 'u':
					$row->role->name = $row->role->value_p;
					break;
			}
			if ($row->has_image == 0) {
				$ida = $this->image->obtener(
					array(
						array(
							'fk_image_type'	=>	$itda->id
						),
						array(
							'name'	=>	'avatar_' . $row->gender->code
						)
					)
				);
				$row->image = $ida[0];
				$row->avatar_url = $row->image->url . $row->image->name . $row->image->ext;
			} else {
				$row->image = $ica[$row->fk_image];
				$row->avatar_url = $row->image->url . $row->id . '/' . $row->image->name . $row->image->ext;
			}
			$Data[] = $row;
		}
		return $Data;
	}

	private function ordenar($array)
	{
		$Data = array();
		if (is_array($array) && sizeof($array) > 0) {
			foreach ($array as $value) {
				$Data[$value->id] = $value;
			}
		}
		return $Data;
	}

	public function obtener_ordenado($conditions = null, $limit = null, $offset = null)
	{
		$array = $this->obtener($conditions, $limit, $offset);
		return $this->ordenar($array);
	}
}
