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

/**
 * Modelo ProductosModel
 *
 * Contiene todas las consultas a la base de datos para el módulo Productos
 *
 * @package		OpenPyme2
 * @subpackage	Models
 * @category	Models
 * @author		OpenPyme Dev Team
 * @link		sftp://docs.apps-oss.com/classes/ProductosModel.html
 */

class ProductosModel extends CI_Model {

	var $table = "producto";
	var $pk = "id_producto";


	//create query for get datatables
		function create_query(){
			//table and sql query string
			$table  = 'producto as p';
			$query  = "p.*, c.nombre as categoria";
			$sql_array =array('table'=>$table,'query'=>$query);
			//create join array
			/*
			First parameter: table name
			Second parameter: keys for join
			Third parameter: join types based on sql, optional
			join Options are: left, right, outer, inner, left outer, and right outer.
			*/
			$j1=array('categoria AS c', 'c.id_categoria = p.id_categoria', 'left');
				$join = array($j1);
			//add join parameters if exist join
			if(isset($join) && !empty($join)){
					$sql_array['join']=$join;
			}
			return $sql_array;
		}

	//create query for get datatables
	function create_dt_query_stock()
	{
		//table and sql query string
		$table  = $this->table;
		$query  = "stock.id_stock, stock.id_color, producto.*, categoria.nombre as categoria, if(stock.cantidad is NULL,0,stock.cantidad) as stock";
		$where = array("producto.deleted" => 0);
		$j1    = array('categoria', 'categoria.id_categoria = producto.id_categoria', 'left');
		$j2    = array('stock', 'stock.id_producto = producto.id_producto', 'left');
		$join  = array($j1, $j2);

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
		$clients = $this->db->get("clients");
		if ($clients->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	function get_row_info($id){
		$this->db->where('id_producto', $id);
		$clients = $this->db->get($this->table);
		if ($clients->num_rows() > 0) {
			return $clients->row();
		} else {
			return 0;
		}
	}

	function get_state($id){
		$this->db->where('activo', 1);
		$this->db->where('id_producto', $id);
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
		$clients = $this->db->get("categoria");
		if ($clients->num_rows() > 0) {
			return $clients->result();
		} else {
			return 0;
		}
	}
	/* traer las marcas*/
	function get_marcas(){
		$this->db->where('activo', 1);
		$this->db->where('deleted', 0);
		$datos = $this->db->get("marca");
		if ($datos->num_rows() > 0) {
			return $datos->result();
		} else {
			return 0;
		}
	}
	/* traer las modelos */
	function get_modelos($id_marca){
		$this->db->where('id_marca', $id_marca);
		$this->db->where('activo', 1);
		$this->db->where('deleted', 0);
		$datos = $this->db->get("modelo");
		if ($datos->num_rows() > 0) {
			return $datos->result();
		} else {
			return 0;
		}
	}
	function get_proveedores($id){
        $this->db->select('proveedores.nombre, producto_proveedor.*');
	    $this->db->where('id_producto', $id);
        $this->db->join('proveedores', 'proveedores.id_proveedor = producto_proveedor.id_proveedor');
		$clients = $this->db->get("producto_proveedor");
		if ($clients->num_rows() > 0) {
			return $clients->result();
		} else {
			return NULL;
		}
	}
	function get_precios(){
	$porc = $this->db->where("deleted", "0")
					->where("activo", "1")
					->get("listaprecios");
		return $porc->result();
	}
	function get_precios_exis($id){
	  $porc = $this->db->where("id_producto",$id)->get("producto_precio");
		if ($porc->num_rows() > 0) {
			return $porc->result();
		} else {
			return NULL;
		}

	}
	function get_colores_exis($id){
	  $porc = $this->db->where("id_producto",$id)->get("producto_color");
		return $porc->result();
	}
	/*function get_precios($id){
	    $this->db->where('id_producto', $id);
        $this->db->join('proveedores', 'proveedores.id_proveedor = producto_proveedor.id_proveedor');
		$clients = $this->db->get("producto_proveedor");
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
    }
    function eliminar_imagen($data,$id_producto){
	    $this->db->where('id_producto', $id_producto);
	    for ($i=0;$i<count($data);$i++){
            $this->db->where("id_imagen != '".$data[$i]["id_imagen"]."'");
        }
        $this->db->delete('producto_imagen');
    }
    function get_images($id){
        $this->db->where('id_producto', $id);
        $clients = $this->db->get("producto_imagen");
        if ($clients->num_rows() > 0) {
            return $clients->result();
        } else {
            return NULL;
        }
    }

    function get_idColor($id_producto,$color){

		$this->db->select('id_color');
        $this->db->where('id_producto', $id_producto);
		$this->db->where('color', $color);
		$row =$this->db->get('producto_color');
        //$row = $this->db->row();
        if ($row->num_rows() > 0) {
            return $row->row();
        } else {
            return NULL;
        }
    }
    function get_color_stock($id_producto,$id_color){
		$this->db->select('id_stock,cantidad');
        $this->db->where('id_producto', $id_producto);
		$this->db->where('id_color', $id_color);
        $row =$this->db->get('stock');
        if ($row->num_rows() > 0) {
            return $row->row();
        } else {
            return NULL;
        }
    }
		function get_stock_data($id_producto,$id_color){
				$this->db->select('id_stock,cantidad, producto.*, c.color');
        $this->db->where('stock.id_producto', $id_producto);
				$this->db->where('stock.id_color', $id_color);
				$this->db->join('producto', 'producto.id_producto = stock.id_producto', 'left');
				$this->db->join('producto_color as c', 'c.id_color = stock.id_color', 'left');
        $row =$this->db->get('stock');
        if ($row->num_rows() > 0) {
            return $row->row();
        } else {
            return NULL;
        }
    }
		function get_stock_r($id_sucursal){
			$this->db->select("stock.id_stock,producto.*, categoria.nombre as categoria, if(stock.cantidad is NULL,0,stock.cantidad) as stock, c.color, c.id_color");
			$this->db->where("producto.deleted",'0');
			$this->db->where("stock.id_sucursal",$id_sucursal);
			$this->db->join('producto', 'producto.id_producto = stock.id_producto', 'left');
			$this->db->join('producto_color as c', 'c.id_color = stock.id_color', 'left');
			$this->db->join('categoria', 'categoria.id_categoria = producto.id_categoria', 'left');
			$clients = $this->db->get("stock");
			if ($clients->num_rows() > 0) {
				//var_dump($clients->result());
				return $clients->result();
			} else {
				return 0;
			}
		}
}

/* End of file ClientModel.php */
