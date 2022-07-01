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

class TrasladoModel extends CI_Model {

	var $table = "producto";
	var $pk = "id_producto";

	//create query for get datatables
	function create_dt_query()
	{
		//table and sql query string
		$table  = 'traslado as v';
		$query  = "CONCAT(s1.nombre) as suc1,CONCAT(s2.nombre) as suc2,v.requiere_imei,v.imei_ingresado,v.id_traslado,DATE_FORMAT(v.fecha,'%d-%m-%Y') as fecha, FORMAT(v.total,2) as total, v.guia";
		$j1    = array('sucursales as s1', 's1.id_sucursal = v.id_sucursal_despacho', 'left');
		$j2    = array('sucursales as s2', 's2.id_sucursal = v.id_sucursal_destino', 'left');
		$join  = array($j1, $j2);

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

function get_productos($query,$id_sucursal){
	$this->db->select('stock.id_stock,stock.id_color,producto.id_producto, nombre, marca, codigo_barra, modelo,stock.cantidad,producto_color.color');
	$this->db->from("stock");
	$this->db->join("producto","producto.id_producto=stock.id_producto");
	$this->db->join("producto_color","stock.id_color=producto_color.id_color","left");
	$this->db->where("(modelo LIKE '%".$query."%' OR nombre LIKE '%".$query."%' OR marca LIKE '%".$query."%' OR codigo_barra LIKE '%".$query."%' OR color LIKE '%".$query."%')", NULL, FALSE);
	//$this->db->or_like('nombre', $query);
	//$this->db->or_like('marca', $query);
	//$this->db->or_like('codigo_barra', $query);
	$this->db->where('activo', 1);
	$this->db->where("stock.id_sucursal",$id_sucursal);
	$this->db->where('cantidad>0');
	$this->db->where('deleted', '0');
	$query = $this->db->get();
	if ($query->num_rows() > 0){ return $query->result();}
	else{ return NULL;}
}
function get_clientes($query){
	$this->db->select('id_cliente, nombre');
	$this->db->like('nombre', $query);
	$this->db->where('deleted', '0');
	$this->db->where('activo', '1');
	$query = $this->db->get('clientes');
	if ($query->num_rows() > 0) return $query->result();
	else return NULL;
}
function get_producto($id){
	$this->db->select('id_producto, nombre, marca, modelo, precio_sugerido');
	$this->db->where('id_producto', $id);
	$query = $this->db->get('producto');
	if ($query->num_rows() > 0) return $query->row();
	else return NULL;
}




/*Venta de productos*/
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

function get_venta($id_venta)
{
	// code...
	$this->db->select("clientes.nombre, ventas.fecha,ventas.id_sucursal_despacho");
	$this->db->from("ventas");
	$this->db->join("clientes","clientes.id_cliente = ventas.id_cliente");
	$this->db->where('id_venta', $id_venta);
	$query = $this->db->get();
	if ($query->num_rows() > 0) {
		return $query->row();
	}
	else {
		return 0;
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

	foreach ($precios as $row_por)
	{
		$id_precio = $row_por->id_precio;
		$porcentaje = $row_por->porcentaje;

		$resultado = round($costo * ($porcentaje / 100) , 2);
		$resultado1 = $costo + $resultado;
		$resultado2 = round($resultado1 * 1.13, 2);

		$this->db->set('costo', "$costo");
		$this->db->set('costo_iva', "$costo_iva");
		$this->db->set('ganancia', "$resultado");
		$this->db->set('total', "$resultado1");
		$this->db->set('total_iva', "$resultado2");
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

	$this->db->select("producto_color.color,icd.*, p.nombre,p.imei,p.n_imei,p.marca,p.modelo");
	$this->db->from('traslado_detalle AS icd');
	$this->db->join('producto as p', 'p.id_producto=icd.id_producto');
	$this->db->join('producto_color', 'producto_color.id_color=icd.id_color',"left");
	$this->db->where('id_traslado',$id_carga);
	$this->db->order_by('p.n_imei', 'ASC');
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}

function get_imei_ci($id_venta){

	$this->db->select("ici.id_detalle,ici.id_venta,ici.id_producto,p.nombre,ici.chain");
	$this->db->from('ventas_imei as ici ');
	$this->db->join(' producto as p ', 'ici.id_producto=p.id_producto');
	$this->db->group_by("chain");
	$this->db->order_by('p.n_imei', 'ASC');
	$this->db->where('id_venta',$id_venta);
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}

function get_imei_ci_det($chain){

	$this->db->select("ici.id_imei,ici.imei");
	$this->db->from('ventas_imei as ici ');
	$this->db->where('chain',$chain);
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}

function get_imei_productos($id_detalle){

	$this->db->select("ici.id_imei,ici.imei");
	$this->db->from('ventas_imei as ici ');
	$this->db->where('id_detalle',$id_detalle);
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

function getGarantia($id_producto,$estado)
{
	$this->db->where('id_producto', $id_producto);
	$data = $this->db->get("producto");
	if ($data->num_rows() > 0) {
		$dat = $data->row();
		if ($estado=="NUEVO") {
			// code...
			return $dat->dias_garantia;
		}
		else {
			return $dat->dias_garantia_usado;
		}
	} else {
		return 0;
	}
}

function get_reservado($id_producto,$id_venta,$id_color){
	$this->db->select("sum(ventas_detalle.cantidad) as reservado");
	$this->db->from("ventas_detalle");
	$this->db->where("id_producto",$id_producto);
	$this->db->where("id_color",$id_color);
	$this->db->where("id_venta",$id_venta);
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->row();
	} else {
		return 0;
	}
}

function get_detalle_traslado($id){
	$sql = $this->db->query("SELECT GROUP_CONCAT(CONCAT_WS(' ' , p.nombre , p.modelo, c.color) SEPARATOR '<br><br>')
	as detalle_t FROM traslado_detalle as td INNER JOIN producto as p ON p.id_producto = td.id_producto
	INNER JOIN producto_color as c ON c.id_color = td.id_color WHERE td.id_traslado = $id");
	if ($sql->num_rows() > 0) {
		return $sql->row();
	} else {
		return 0;
	}
}


}
/* End of file VentasModel.php */
