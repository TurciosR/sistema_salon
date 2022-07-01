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

class ProveedoresModel extends CI_Model {

	var $table = "proveedores";
	var $pk = "id_proveedor";

	function create_dt_query()
	{
		//table and sql query string
		$table = $this->table;
		$query = "*";
		$where = array('deleted' => 0);
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

	function exits_row($name,$address,$cellphone){
		$this->db->where('name', $name);
		$this->db->where('address', $address);
		$this->db->where('cellphone', $cellphone);
		$row = $this->db->get("clients");
		if ($row->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	function get_row_info($id){
		$this->db->where($this->pk, $id);
		$row = $this->db->get($this->table);
		if ($row->num_rows() > 0) {
			return $row->row();
		} else {
			return 0;
		}
	}

	function get_state($id){
		$this->db->where('activo', 1);
		$this->db->where($this->pk, $id);
		$row = $this->db->get($this->table);
		if ($row->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}
	function get_empresas(){
		$this->db->where('estado', 1);
		$row = $this->db->get("empresa");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return 0;
		}
	}

	function get_departamentos(){
		$row = $this->db->get("departamento");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return 0;
		}
	}
	function get_giro(){
		$row = $this->db->get("giro");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return 0;
		}
	}
	function get_categoria_proveedor(){
		$row = $this->db->get("categoria_proveedor");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return 0;
		}
	}
	function get_tipo_proveedor(){
		$row = $this->db->get("tipo_proveedor");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return 0;
		}
	}
    function get_municipio($id_departamento){
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
	/*function get_proveedores($id){
        $this->db->select('proveedores.nombre, producto_proveedor.*');
	    $this->db->where('id_producto', $id);
        $this->db->join('proveedores', 'proveedores.id_proveedor = producto_proveedor.id_proveedor');
		$row = $this->db->get("producto_proveedor");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return NULL;
		}
	}
	function get_proveedor_autocomplete($query){
        $this->db->select('id_proveedor, nombre');
        $this->db->like('nombre', $query);
        $this->db->where('activo', 1);
        $query = $this->db->get('proveedores');
        if ($query->num_rows() > 0) return $query->result();
        else return NULL;
    }

	function insertar_producto($data){
        $this->db->insert('producto', $data);
        if($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }else{
            return NULL;
        }
    }
    function insertar_imagen($data){
        $this->db->insert('producto_imagen', $data);
        if($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }else{
            return NULL;
        }
    }*/
    function eliminar_imagen($data,$id_producto){
	    $this->db->where('id_producto', $id_producto);
	    for ($i=0;$i<count($data);$i++){
            $this->db->where("id_imagen != '".$data[$i]["id_imagen"]."'");
        }
        $this->db->delete('producto_imagen');
    }
    function get_images($id){
        $this->db->where('id_producto', $id);
        $row = $this->db->get("producto_imagen");
        if ($row->num_rows() > 0) {
            return $row->result();
        } else {
            return NULL;
        }
    }
}

/* End of file ClientModel.php */
