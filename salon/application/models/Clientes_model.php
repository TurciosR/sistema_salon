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

class Clientes_model extends CI_Model {
	var $table_name = "clientes";
	var $pk = "id_cliente";

	//create query for get datatables
	function create_dt_query()
	{
		//table and sql query string
		$table = $this->table_name;
		$query = "*";
		$where = array(
			'deleted' => 0,
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

	function get_row_info($id)
	{
		$this->db->where($this->pk, $id);
		$rows = $this->db->get($this->table_name);
		if ($rows->num_rows() > 0) {
			return $rows->row();
		} else {
			return NULL;
		}
	}

	function get_row_info_editar($id)
	{
		$this->db->where($this->pk, $id);
		$rows = $this->db->get($this->table_name);
		if ($rows->num_rows() > 0) {
			return $rows->row();
		} else {
			return NULL;
		}
	}

	function get_direcciones($id)
	{
		$this->db->where($this->pk, $id);
		$rows = $this->db->get("cliente_direccion");
		if ($rows->num_rows() > 0) {
			return $rows->result();
		} else {
			return NULL;
		}
	}
	function get_departamentos()
	{
		$row = $this->db->get("departamento");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return 0;
		}
	}
	function get_giro()
	{
		$row = $this->db->get("giro");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return 0;
		}
	}
	function get_categoria_cliente()
	{
		$row = $this->db->get("categoria_cliente");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return 0;
		}
	}
	function get_tipo_cliente()
	{
		$row = $this->db->get("tipo_cliente");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return 0;
		}
	}
	function get_clasifica_cliente()
	{
		$this->db->where('activo', 1);
		$this->db->where('deleted', '0');

		$row = $this->db->get("listaprecios");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return 0;
		}
	}
	function get_tipo_doc()
	{
		$this->db->where('cliente', 1);
		$row = $this->db->get("tipodoc");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
		return 0;
		}
	}
	function get_porcentajes()
	{
		$this->db->where('activo', 1);
		$row = $this->db->get("porcentajes");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
		return 0;
		}
	}
	function get_municipio($id_departamento)
	{
		$this->db->select('id_municipio,nombre');
		if($id_departamento>0){
			$this->db->where('id_departamento', $id_departamento);
		}
		$cars = $this->db->get("municipio");
		if ($cars->num_rows() > 0) {
			return $cars->result();
		} else {
			return 0;
		}
	}
	function get_state($id)
	{
		$this->db->where('activo', 1);
		$this->db->where($this->pk, $id);
		$rows = $this->db->get($this->table_name);
		if ($rows->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}
	function insertar_cliente($data)
	{
		$this->db->insert($this->table_name, $data);
		if ($this->db->affected_rows() > 0) {
			return $this->db->insert_id();
		} else {
			return NULL;
		}
	}

}
