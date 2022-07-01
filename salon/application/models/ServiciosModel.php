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

class ServiciosModel extends CI_Model {

	var $table = "servicio";
	var $pk = "id_servicio";

	//create query for get datatables
	function create_dt_query()
	{
		//table and sql query string
		$table = $this->table;
		$query = "servicio.*, categoria_servicio.nombre as categoria";
		$where = array("servicio.deleted" => 0);
		$j1    = array('categoria_servicio', 'categoria_servicio.id_categoria = servicio.id_categoria', 'left');
		$join  = array($j1);

		$sql_array = array(
			'table' => $table,
			'query' => $query,
			);
		//add join parameters if exist join
		if (isset($join) && !empty($join)) {
			$sql_array['join'] = $join;
		}
		return $sql_array;
	}

	function get_collection_stock($order, $search, $valid_columns, $length, $start, $dir,$id_sucursal)
	{
		if ($order !=	 null) {
			$this->db->order_by($order, $dir);
		}
		if (!empty($search)) {
			$x = 0;
			foreach ($valid_columns as $sterm) {
				if ($x == 0) {
					$this->db->like($sterm, $search);
					$this->db->where("stock.id_sucursal",$id_sucursal);
				} else {
					$this->db->or_like($sterm, $search);
					$this->db->where("stock.id_sucursal",$id_sucursal);
				}
				$x++;
			}
		}
		$this->db->select("stock.id_stock,servicio.*, categoria.nombre as categoria, if(stock.cantidad is NULL,0,stock.cantidad) as stock");
		$this->db->where("servicio.deleted",'0');
		$this->db->where("stock.id_sucursal",$id_sucursal);
		$this->db->limit($length, $start);
		$this->db->join('categoria', 'categoria.id_categoria = servicio.id_categoria', 'left');
		$this->db->join('stock', 'stock.id_servicio = servicio.id_servicio', 'left');
		$clients = $this->db->get($this->table);
		if ($clients->num_rows() > 0) {
			return $clients->result();
		} else {
			return 0;
		}
	}

	function exits_row($name,$address,$cellphone){
		$this->db->where('name', $name);
		$this->db->where('address', $address);
		$this->db->where('cellphone', $cellphone);
		$clients = $this->db->get("clients");
		if ($clients->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	function get_row_info($id){
		$this->db->where('id_servicio', $id);
		$clients = $this->db->get($this->table);
		if ($clients->num_rows() > 0) {
			return $clients->row();
		} else {
			return 0;
		}
	}

	function get_state($id){
		$this->db->where('activo', 1);
		$this->db->where('id_servicio', $id);
		$clients = $this->db->get($this->table);
		if ($clients->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}
	function get_empresas(){
		$this->db->where('estado', 1);
		$clients = $this->db->get("empresa");
		if ($clients->num_rows() > 0) {
			return $clients->result();
		} else {
			return 0;
		}
	}
	function get_categorias(){
		$this->db->where('activo', 1);
		$clients = $this->db->get("categoria_servicio");
		if ($clients->num_rows() > 0) {
			return $clients->result();
		} else {
			return 0;
		}
	}
	function get_proveedores($id){
        $this->db->select('proveedores.nombre, servicio_proveedor.*');
	    $this->db->where('id_servicio', $id);
        $this->db->join('proveedores', 'proveedores.id_proveedor = servicio_proveedor.id_proveedor');
		$clients = $this->db->get("servicio_proveedor");
		if ($clients->num_rows() > 0) {
			return $clients->result();
		} else {
			return NULL;
		}
	}
	function get_precios(){
	  $porc = $this->db->where("deleted","0")->get("porcentajes");
		return $porc->result();
	}
	function get_precios_exis($id){
	  $porc = $this->db->where("id_servicio",$id)->get("servicio_precio");
		return $porc->result();
	}
	function get_colores_exis($id){
	  $porc = $this->db->where("id_servicio",$id)->get("servicio_color");
		return $porc->result();
	}
	/*function get_precios($id){
	    $this->db->where('id_servicio', $id);
        $this->db->join('proveedores', 'proveedores.id_proveedor = servicio_proveedor.id_proveedor');
		$clients = $this->db->get("servicio_proveedor");
		if ($clients->num_rows() > 0) {
			return $clients->result();
		} else {
			return NULL;
		}
	}*/
	function get_proveedor_autocomplete($query){
        $this->db->select('id_proveedor, nombre');
        $this->db->like('nombre', $query);
        $this->db->where('activo', 1);
        $query = $this->db->get('proveedores');
        if ($query->num_rows() > 0) return $query->result();
        else return NULL;
    }

	function insertar_servicio($data){
        $this->db->insert('servicio', $data);
        if($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }else{
            return NULL;
        }
    }

    function insertar_imagen($data){
        $this->db->insert('servicio_imagen', $data);
        if($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }else{
            return NULL;
        }
    }
    function eliminar_imagen($data,$id_servicio){
	    $this->db->where('id_servicio', $id_servicio);
	    for ($i=0;$i<count($data);$i++){
            $this->db->where("id_imagen != '".$data[$i]["id_imagen"]."'");
        }
        $this->db->delete('servicio_imagen');
    }
    function get_images($id){
        $this->db->where('id_servicio', $id);
        $clients = $this->db->get("servicio_imagen");
        if ($clients->num_rows() > 0) {
            return $clients->result();
        } else {
            return NULL;
        }
    }

    function get_idColor($id_servicio,$color){

		$this->db->select('id_color');
        $this->db->where('id_servicio', $id_servicio);
		$this->db->where('color', $color);
		$row =$this->db->get('servicio_color');
        //$row = $this->db->row();
        if ($row->num_rows() > 0) {
            return $row->row();
        } else {
            return NULL;
        }
    }
    function get_color_stock($id_servicio,$id_color){
		$this->db->select('id_stock,cantidad');
        $this->db->where('id_servicio', $id_servicio);
		$this->db->where('id_color', $id_color);
        $row =$this->db->get('stock');
        if ($row->num_rows() > 0) {
            return $row->row();
        } else {
            return NULL;
        }
    }
    
    function get_impuestos(){
		$this->db->select('iva,cesc');
        $row =$this->db->get('configuracion');
        if ($row->num_rows() > 0) {
            return $row->row();
        } else {
            return NULL;
        }
    }    
}

/* End of file ClientModel.php */
