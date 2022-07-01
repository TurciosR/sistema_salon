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
 * Model ComprasModel
 *
 * Contains all queries to the database for the Buys Module (Compras)
 *
 * @package		OpenPyme2
 * @subpackage	Models
 * @category	Models
 * @author		OpenPyme Dev Team
 * @link		sftp://docs.apps-oss.com/classes/ComprasModel.html
 */
class ComprasModel extends CI_Model
{
	var $table = "producto";
	var $pk = "id_producto";

	function create_query(){
		//table and sql query string
		$table = 'compra as c';
		$query  = "c.id_compra,DATE_FORMAT(c.fecha,'%d-%m-%Y') as fecha,";
		$query .= "TIME_FORMAT(hora,'%h:%i %p') as hora,  ";
		$query .= "FORMAT(c.total,2) as total, c.concepto,";
		$query .= " c.correlativo, u.nombre";

		$sql_array =array('table'=>$table,'query'=>$query);
		//create join array
		/*
		First parameter: table name
		Second parameter: keys for join
		Third parameter: join types based on sql, optional
		join Options are: left, right, outer, inner, left outer, and right outer.
		*/
		$j1=array('usuario as u', 'c.id_usuario = u.id_usuario');

		$join = array($j1);
		//add join parameters if exist join
		if(isset($join) && !empty($join)){
				$sql_array['join']=$join;
		}
		return $sql_array;
	}


	function get_collection_compra($order, $search, $valid_columns, $length, $start, $dir,$id_sucursal)
	{
		if ($order !=	 null) {
			$this->db->order_by($order, $dir);
		}
		if (!empty($search)) {
			$x = 0;
			foreach ($valid_columns as $sterm) {
				if ($x == 0) {
					$this->db->like($sterm, $search);
					$this->db->where("c.id_sucursal",$id_sucursal);
				} else {
					$this->db->or_like($sterm, $search);
					$this->db->where("c.id_sucursal",$id_sucursal);
				}
				$x++;
			}
		}
		$this->db->select("c.id_compra,DATE_FORMAT(c.fecha,'%d-%m-%Y') as fecha, TIME_FORMAT(hora,'%h:%i %p') as hora, FORMAT(c.total,2) as total, c.concepto, c.correlativo, u.nombre");
		$this->db->limit($length, $start);
		$this->db->from('compra as c');
		$this->db->join('usuario as u', 'c.id_usuario = u.id_usuario');
		$this->db->where("c.id_sucursal",$id_sucursal);
		$this->db->order_by("c.id_compra", "DESC");
		$clients = $this->db->get();
		if ($clients->num_rows() > 0) {
			return $clients->result();
		} else {
			return 0;
		}
	}
	function total_rows_compra(){
		$clients = $this->db->get("compra");
		if ($clients->num_rows() > 0) {
			return $clients->num_rows();
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


	function get_precios(){
		$porc = $this->db->get("porcentajes");
		return $porc->result();
	}
	function get_precios_exis($id){
		$porc = $this->db->where("id_producto",$id)->get("producto_precio");
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
function get_productos($query){
	$this->db->select('id_producto, nombre, codigo_barra');
	$this->db->where("( nombre LIKE '%".$query."%'  OR codigo_barra LIKE '%".$query."%')", NULL, FALSE);
	$this->db->where('activo', 1);
	$this->db->where('deleted', '0');
	$query = $this->db->get('producto');
	if ($query->num_rows() > 0) return $query->result();
	else return NULL;
}

function get_productos_stock($query,$id_sucursal){
	$this->db->select('stock.id_stock,stock.id_color,producto.id_producto, nombre, stock.cantidad,producto_color.color');
	$this->db->from("stock");
	$this->db->join("producto","producto.id_producto=stock.id_producto");
	$this->db->join("producto_color","stock.id_color=producto_color.id_color","left");
	$this->db->like('nombre', $query);
	$this->db->where('activo', 1);
	$this->db->where("stock.id_sucursal",$id_sucursal);
	$this->db->where('deleted', '0');
	$query = $this->db->get();
	if ($query->num_rows() > 0) return $query->result();
	else return NULL;
}

function get_producto($id){
	$this->db->select('id_producto, nombre,  precio_sugerido');
	$this->db->where('id_producto', $id);
	$query = $this->db->get('producto');
	if ($query->num_rows() > 0) return $query->row();
	else return NULL;
}

/**
 * Insert and confirm
 *
 * Insert to a table and return id
 *
 * @param string	$table Name of table
 * @param array 	$data Relational arrangement with the data to be inserted
 *
 * @return int|null
 */
function inAndCon($table,$data)
{
	$this->db->insert($table, $data);
	if ($this->db->affected_rows() > 0) {
		return $this->db->insert_id();
	} else {
		return NULL;
	}
}

/**
 * Get the value of the correlative and update the table
 *
 * @param string $corr Name of the column
 * @param int $id_sucursal Name of the branch (sucursal)
 *
 * @return int|null
 */
function get_max_correlative($corr,$id_sucursal){
	$this->db->select($corr);
	$this->db->where('id_sucursal', $id_sucursal);
	$query = $this->db->get('correlativo');
	if ($query->num_rows() > 0) {
		$correlativo = $query->row();
		$this->db->set($corr, "$corr+1", FALSE);
		$this->db->where('id_sucursal', $id_sucursal);
		$this->db->update('correlativo'); // gives UPDATE mytable SET field = field+1 WHERE id = 2

		$num = $correlativo->$corr;
		$num = $num+1;
		return $num;
	}
	else {
		return NULL;
	}
}

function get_stock($id_producto,$id_color,$id_sucursal)
{
	$this->db->where('id_sucursal', $id_sucursal);
	$this->db->where('id_producto', $id_producto);
	$this->db->where('id_color', $id_color);
	$query = $this->db->get('stock');
	if ($query->num_rows() > 0) {
		return $query->row();
	}
	else {
		$data = array(
			'id_producto' => $id_producto,
			'id_sucursal' => $id_sucursal,
			'id_color' => $id_color,
			'cantidad' => 0,
		);
		$this->db->insert('stock', $data);
		if($this->db->affected_rows() > 0){
			$this->db->where('id_sucursal', $id_sucursal);
			$this->db->where('id_producto', $id_producto);
			$this->db->where('id_color', $id_color);
			$query = $this->db->get('stock');
			if ($query->num_rows() > 0) {
				return $query->row();
			}
		}
	}
}

function update_cost($costo,$id_producto,$precios){
	$costo_iva = round($costo * 1.13, 2);
	$ivaT = round($costo * 0.13, 2);

	foreach ($precios as $row_por)
	{
		$id_precio = $row_por->id_precio;
		$porcentaje = $row_por->porcentaje;

		$resultado = round($costo * ($porcentaje / 100) , 2);
		$resultado1 = $costo + $resultado;
		$resultado2 = round($resultado1 * 1.13, 2);


		/*$resultado = $row_por->ganancia;
		$resultado1 = $row_por->total;
		$resultado2 = $row_por->total_iva;*/

		$this->db->set('costo', "$costo");
		$this->db->set('costo_iva', "$ivaT");
		$this->db->set('ganancia', "$resultado");
		//$this->db->set('total', "$resultado1");
		$this->db->set('total_iva', "$costo_iva");
		$this->db->where('id_producto', $id_producto);
		$this->db->where('id_precio', $id_precio);
		$this->db->update('producto_precio');
	}

}

function get_one_row($tabla,$where){
	foreach ($where as $key => $value) {
		$this->db->where($key, $value);
	}
	$data = $this->db->get($tabla);
	if ($data->num_rows() > 0) {
		return $data->row();
	} else {
		return 0;
	}
}

function get_detail_rows($tabla,$where){
	foreach ($where as $key => $value) {
		$this->db->where($key, $value);
	}
	$detail = $this->db->get($tabla);
	if ($detail->num_rows() > 0) {
		return $detail->result();
	} else {
		return 0;
	}
}

function get_detail_ci($id_compra){
	$this->db->select("producto_color.color,dc.*, p.nombre");
	$this->db->from('detalle_compra AS dc');
	$this->db->join('producto as p', 'p.id_producto=dc.id_producto');
	$this->db->join('producto_color', 'producto_color.id_color=dc.id_color',"left");
	$this->db->where('id_compra',$id_compra);
	$this->db->order_by('p.id_producto', 'ASC');
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}

function get_imei_ci_det($chain){

	$this->db->select("ici.id_imei,ici.imei");
	$this->db->from('compras_imei as ici ');
	$this->db->where('chain',$chain);
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}





function has_imei_required($id_producto){
	$this->db->where('id_producto', $id_producto);
	$data = $this->db->get("producto");
	if ($data->num_rows() > 0) {
		return true;
	} else {
		return false;
	}
}

/***************************************************************************************/
/***************************************************************************************/
/***************************************************************************************/
/***************************************************************************************/
/**************************************Descompras****************************************/
/***************************************************************************************/
/***************************************************************************************/
/***************************************************************************************/
/***************************************************************************************/
function get_collection_descompra($order, $search, $valid_columns, $length, $start, $dir)
{
	if ($order !=	 null) {
		$this->db->order_by($order, $dir);
	}
	if (!empty($search)) {
		$x = 0;
		foreach ($valid_columns as $sterm) {
			if ($x == 0) {
				$this->db->like($sterm, $search);
			} else {
				$this->db->or_like($sterm, $search);
			}
			$x++;
		}
	}
	$this->db->select("c.requiere_imei,c.imei_ingresado,c.id_descompra,DATE_FORMAT(c.fecha,'%d-%m-%Y') as fecha, TIME_FORMAT(hora,'%h:%i %p') as hora, FORMAT(c.total,2) as total, c.concepto, c.correlativo, u.nombre");
	$this->db->limit($length, $start);
	$this->db->from('compra as cp');
	$this->db->join('usuario as u', 'c.id_usuario = u.id_usuario');
	$clients = $this->db->get();
	if ($clients->num_rows() > 0) {
		return $clients->result();
	} else {
		return 0;
	}
}
function total_rows_descompra(){
	$clients = $this->db->get("compra");
	if ($clients->num_rows() > 0) {
		return $clients->num_rows();
	} else {
		return 0;
	}
}
function get_detail_di($id_compra){

	$this->db->select("producto_color.color,dc.*, p.nombre");
	$this->db->from('detalle_compra AS dc');
	$this->db->join('producto as p', 'p.id_producto=dc.id_producto');
	$this->db->join('producto_color', 'producto_color.id_color=dc.id_color',"left");
	$this->db->where('id_descompra',$id_compra);
	$this->db->order_by('p.id_producto', 'ASC');
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}
function get_imei_di($id_compra){

	$this->db->select("ici.id_detalle,ici.id_descompra,ici.id_producto,p.nombre,ici.chain");
	$this->db->from('detalle_compra as ici ');
	$this->db->join(' producto as p ', 'ici.id_producto=p.id_producto');
	$this->db->group_by("chain");
	$this->db->order_by('p.id_producto', 'ASC');
	$this->db->where('id_descompra',$id_compra);
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}

function get_imei_di_det($chain){

	$this->db->select("ici.id_imei,ici.imei");
	$this->db->from('detalle_compra as ici ');
	$this->db->where('chain',$chain);
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}

function get_collection_ajuste($order, $search, $valid_columns, $length, $start, $dir)
{
	if ($order !=	 null) {
		$this->db->order_by($order, $dir);
	}
	if (!empty($search)) {
		$x = 0;
		foreach ($valid_columns as $sterm) {
			if ($x == 0) {
				$this->db->like($sterm, $search);
			} else {
				$this->db->or_like($sterm, $search);
			}
			$x++;
		}
	}
	$this->db->select("c.requiere_imei,c.imei_ingresado,c.id_ajuste,DATE_FORMAT(c.fecha,'%d-%m-%Y') as fecha, TIME_FORMAT(hora,'%h:%i %p') as hora, FORMAT(c.total,2) as total, c.concepto, c.correlativo, u.nombre");
	$this->db->limit($length, $start);
	$this->db->from('compras_ajuste as cp');
	$this->db->join('usuario as u', 'c.id_usuario = u.id_usuario');
	$clients = $this->db->get();
	if ($clients->num_rows() > 0) {
		return $clients->result();
	} else {
		return 0;
	}
}
function total_rows_ajuste(){
	$clients = $this->db->get("compras_ajuste");
	if ($clients->num_rows() > 0) {
		return $clients->num_rows();
	} else {
		return 0;
	}
}
function get_detail_aj($id_compra){

	$this->db->select("producto_color.color,dc.*, p.nombre");
	$this->db->from('detalle_compra AS dc');
	$this->db->join('producto as p', 'p.id_producto=dc.id_producto');
	$this->db->join('producto_color', 'producto_color.id_color=dc.id_color',"left");
	$this->db->where('id_ajuste',$id_compra);
	$this->db->order_by('p.id_producto', 'ASC');
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}

}
/* end of file ./application/models/ComprasModel.php */
