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

class InventarioModel extends CI_Model {

	var $table = "producto";
	var $pk = "id_producto";

	//create query for get datatables
	function create_dt_query_carga()
	{
		//table and sql query string
		$table  = 'inventario_carga as cp';
		$query  = "cp.requiere_imei, cp.imei_ingresado, cp.id_carga,";
		$query .= "DATE_FORMAT(cp.fecha,'%d-%m-%Y') as fecha,";
		$query .= "TIME_FORMAT(hora,'%h:%i %p') as hora, FORMAT(cp.total,2) as total,";
		$query .= "cp.concepto, cp.correlativo, u.nombre";
		$j1     = array('usuario as u', 'cp.id_usuario = u.id_usuario');
		$join   = array($j1);

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
	$this->db->select('id_producto,codigo_barra, nombre');
	$this->db->where("(nombre LIKE '%".$query."%'  OR codigo_barra LIKE '%".$query."%')", NULL, FALSE);
	$this->db->where('activo', 1);
	$this->db->where('deleted', '0');
	$query = $this->db->get('producto');
	if ($query->num_rows() > 0) return $query->result();
	else return NULL;
}

function get_productos_stock($query,$id_sucursal){
	//echo $id_sucursal;
	$this->db->select('stock.id_stock,stock.id_color,producto.id_producto, nombre, codigo_barra, stock.cantidad,producto_color.color');
	$this->db->from("stock");
	$this->db->join("producto","producto.id_producto=stock.id_producto");
	$this->db->join("producto_color","stock.id_color=producto_color.id_color","left");
	$this->db->where("(nombre LIKE '%".$query."%' OR codigo_barra LIKE '%".$query."%' OR color LIKE '%".$query."%')", NULL, FALSE);
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




/*cargar de productos*/
/*insertar a tabla con confirmacion*/
function inAndCon($table,$data){
	$this->db->insert($table, $data);
	if($this->db->affected_rows() > 0){
		return $this->db->insert_id();
	}else{
		return NULL;
	}
}
/*obtener el valor del correlativo y actualizar la tabla*/
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
		// code...
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
		// code...
		$this->db->where($key, $value);
	}
	$detail = $this->db->get($tabla);
	if ($detail->num_rows() > 0) {
		return $detail->result();
	} else {
		return 0;
	}
}

function get_detail_ci($id_carga){


	$this->db->select("producto_color.color,icd.*, p.nombre, p.codigo_barra");
	$this->db->from('inventario_carga_detalle AS icd');
	$this->db->join('producto as p', 'p.id_producto=icd.id_producto');
	$this->db->join('producto_color', 'producto_color.id_color=icd.id_color',"left");
	$this->db->where('id_carga',$id_carga);
	$this->db->order_by('p.id_producto', 'ASC');
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}

function get_imei_ci($id_carga){

	$this->db->select("ici.id_detalle,ici.id_carga,ici.id_producto,p.nombre, p.modelo,ici.chain");
	$this->db->from('inventario_carga_imei as ici ');
	$this->db->join(' producto as p ', 'ici.id_producto=p.id_producto');
	$this->db->group_by("chain");
	$this->db->order_by('p.n_imei', 'ASC');
	$this->db->where('id_carga',$id_carga);
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}

function get_imei_ci_det($chain){

	$this->db->select("ici.id_imei,ici.imei");
	$this->db->from('inventario_carga_imei as ici ');
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
	$this->db->where('imei', '1');
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
/**************************************Descargas****************************************/
/***************************************************************************************/
/***************************************************************************************/
/***************************************************************************************/
/***************************************************************************************/
	//create query for get datatables
	function create_dt_query_descarga()
	{
		//table and sql query string
		$table  = 'inventario_descarga as cp';
		$query  = "cp.requiere_imei, cp.imei_ingresado, cp.id_descarga,";
		$query .= "DATE_FORMAT(cp.fecha,'%d-%m-%Y') as fecha, TIME_FORMAT(hora,'%h:%i %p') as hora,";
		$query .= "FORMAT(cp.total,2) as total, cp.concepto, cp.correlativo, u.nombre";
		$j1     = array('usuario as u', 'cp.id_usuario = u.id_usuario');
		$join   = array($j1);

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

function get_detail_di($id_carga){

	$this->db->select("producto_color.color,icd.*, p.nombre");
	$this->db->from('inventario_descarga_detalle AS icd');
	$this->db->join('producto as p', 'p.id_producto=icd.id_producto');
	$this->db->join('producto_color', 'producto_color.id_color=icd.id_color',"left");
	$this->db->where('id_descarga',$id_carga);
	$this->db->order_by('p.id_producto', 'ASC');
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}
function get_imei_di($id_carga){

	$this->db->select("ici.id_detalle,ici.id_descarga,ici.id_producto,p.nombre, p.modelo,ici.chain");
	$this->db->from('inventario_descarga_imei as ici ');
	$this->db->join(' producto as p ', 'ici.id_producto=p.id_producto');
	$this->db->group_by("chain");
	$this->db->order_by('p.n_imei', 'ASC');
	$this->db->where('id_descarga',$id_carga);
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}

function get_imei_di_det($chain){

	$this->db->select("ici.id_imei,ici.imei");
	$this->db->from('inventario_descarga_imei as ici ');
	$this->db->where('chain',$chain);
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}

function get_collection_ajuste(array $collection = array())
{
	$args = array(
		'order'         => NULL,
		'search'        => NULL,
		'valid_columns' => NULL,
		'length'        => NULL,
		'start'         => NULL,
		'dir'           => NULL,
		'count'         => NULL,
	);
	$collection = array_merge($args, $collection);
	extract($collection);

	if ($order != NULL) {
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

	$query  = "cp.requiere_imei, cp.imei_ingresado, cp.id_ajuste,";
	$query .= "DATE_FORMAT(cp.fecha,'%d-%m-%Y') as fecha,";
	$query .= "TIME_FORMAT(hora,'%h:%i %p') as hora,";
	$query .= "FORMAT(cp.total,2) as total, cp.concepto, cp.correlativo,";
	$query .= "u.nombre";

	$this->db->select($query);
	if ($count != TRUE) {
		$this->db->limit($length, $start);
	}

	$this->db->from('inventario_ajuste as cp');
	$this->db->join('usuario as u', 'cp.id_usuario = u.id_usuario');
	$clients = $this->db->get();
	if ($clients->num_rows() > 0) {
		if ($count == TRUE) {
			return $clients->num_rows();
		} else {
			return $clients->result();
		}
	} else {
		return 0;
	}
}

function total_rows_ajuste(){
	$clients = $this->db->get("inventario_ajuste");
	if ($clients->num_rows() > 0) {
		return $clients->num_rows();
	} else {
		return 0;
	}
}
function get_detail_aj($id_carga){

	$this->db->select("producto_color.color,icd.*, p.nombre");
	$this->db->from('inventario_ajuste_detalle AS icd');
	$this->db->join('producto as p', 'p.id_producto=icd.id_producto');
	$this->db->join('producto_color', 'producto_color.id_color=icd.id_color',"left");
	$this->db->where('id_ajuste',$id_carga);
	$this->db->order_by('p.id_producto', 'ASC');
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}

}
/* End of file InventarioModel.php */
