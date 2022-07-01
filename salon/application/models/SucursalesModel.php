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

class SucursalesModel extends CI_Model {

	var $table = "sucursales";
	var $pk = "id_sucursal";

	//create query for get datatables
		function create_dt_query(){
			//table and sql query string
			$table  = 'sucursales';
			$query  = "*";
			$sql_array =array('table'=>$table,'query'=>$query);
			//add join parameters if exist join
			if(isset($join) && !empty($join)){
					$sql_array['join']=$join;
			}
			return $sql_array;
		}

	function total_rows(){
		$rows = $this->db->get($this->table);
		if ($rows->num_rows() > 0) {
			return $rows->num_rows();
		} else {
			return 0;
		}
	}


	function get_row_info($id){
		$this->db->where($this->pk, $id);
		$rows = $this->db->get($this->table);
		if ($rows->num_rows() > 0) {
			return $rows->row();
		} else {
			return NULL;
		}
	}
	function get_state($id){
		$this->db->where('activo', 1);
		$this->db->where($this->pk, $id);
		$rows = $this->db->get($this->table);
		if ($rows->num_rows() > 0) {
			return 1;
		} else {
			return NULL;
		}
	}
	function get_sucursal($id){
		$this->db->where("id_rol", $id);
		$rows = $this->db->get("sucursal_detalle");
		if ($rows->num_rows() > 0) {
			return $rows->result();
		} else {
			return NULL;
		}
	}
	function insert_rol($data){
		$this->db->insert('sucursal', $data);
		if($this->db->affected_rows() > 0){
			return $this->db->insert_id();
		}else{
			return NULL;
		}
	}
}
