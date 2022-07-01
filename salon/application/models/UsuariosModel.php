<?php
/**
 * This file is part of the OpenPyme2.
 *
 * (c) Open Solution Systems <operaciones@tumundolaboral.com.sv>
 *
 * For the full copyright and license information, please refere to LICENSE file
 * that has been distributed with this source code.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class UsuariosModel extends CI_Model {

	private $table = "usuario";

	//create query for get datatables
	function create_dt_query()
	{
		//table and sql query string
		$table = $this->table;
		$query = "*";
		$where = array(
			"id_usuario>" => 0,
		);
		$join  = NULL;

		$sql_array = array(
			'table' => $table,
			'query' => $query,
			'where' => $where,
			);
		//add join parameters if exist join
		if (isset($join) && !empty($join)) {
			$sql_array['join'] = $join;
		}
		return $sql_array;
	}

	function total_rows(){
		$row = $this->db->get($this->table);
		if ($row->num_rows() > 0) {
			return $row->num_rows();
		} else {
			return 0;
		}
	}

	function exits_row($usuario){
		$this->db->where('usuario', $usuario);
		$row = $this->db->get($this->table);
		if ($row->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	function exits_row_edit($usuario,$id){
		$this->db->where('usuario', $usuario);
		$this->db->where("id_usuario!='".$id."'");
		$row = $this->db->get($this->table);
		if ($row->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	function get_row_info($id){
		$this->db->where('id_usuario', $id);
		$row = $this->db->get($this->table);
		if ($row->num_rows() > 0) {
			return $row->row();
		} else {
			return 0;
		}
	}

	function get_state($id){
		$this->db->where('activo', 1);
		$this->db->where('id_usuario', $id);
		$row = $this->db->get($this->table);
		if ($row->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}
	function get_controller(){
		$this->db->where('mostrarmenu', 1);
		$row = $this->db->get("modulo");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return 0;
		}
	}
	function get_menu(){
		$this->db->where('visible', 1);
		$row = $this->db->get("menu");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return 0;
		}
	}
	function get_permissions($id){
		$this->db->where('id_usuario', $id);
		$row = $this->db->get("permisos_usuario");
		return $row->result();
	}

}

/* End of file UsuariosModel.php */
