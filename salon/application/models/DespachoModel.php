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

class DespachoModel extends CI_Model {

	var $table = "producto";
	var $pk = "id_producto";

	function get_collection($order, $search, $valid_columns, $length, $start, $dir,$id_sucursal)
	{
		if ($order !=	 null) {
			$this->db->order_by($order, $dir);
		}
		if (!empty($search)) {
			$x = 0;
			foreach ($valid_columns as $sterm) {
				if ($x == 0) {
					$this->db->like($sterm, $search);
					$this->db->where("v.id_sucursal",$id_sucursal);
				} else {
					$this->db->or_like($sterm, $search);
					$this->db->where("v.id_sucursal",$id_sucursal);
				}
				$x++;
			}
		}
		$this->db->select("v.requiere_imei,v.imei_ingresado,v.id_venta,DATE_FORMAT(v.fecha,'%d-%m-%Y') as fecha, FORMAT(v.total,2) as total, v.guia, c.nombre, c.dui, e.descripcion, v.correlativo");
		$this->db->from('ventas as v');
		$this->db->join('clientes as c', 'v.id_cliente = c.id_cliente', 'left');
		$this->db->join('estado as e', 'v.id_estado = e.id_estado', 'left');
		$this->db->where("v.id_sucursal",$id_sucursal);
		$this->db->limit($length, $start);
		$clients = $this->db->get();
		if ($clients->num_rows() > 0) {
			return $clients->result();
		} else {
			return 0;//$this->db->last_query();
		}
	}
	function total_rows(){
		$clients = $this->db->get("ventas");
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

function get_productos($query,$id_sucursal){
	$this->db->select('producto.id_producto, nombre, marca, modelo,cantidad');
	$this->db->from("producto");
	$this->db->join('stock', 'stock.id_producto=producto.id_producto');
	$this->db->like('nombre', $query);
	$this->db->where('activo', 1);
	$this->db->where('stock.id_sucursal',$id_sucursal);
	$this->db->where('deleted', '0');
	$this->db->where('cantidad>0');
	$query = $this->db->get();
	if ($query->num_rows() > 0) return $query->result();
	else return NULL;
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
function get_stock($id_producto,$id_sucursal)
{
	$this->db->where('id_sucursal', $id_sucursal);
	$this->db->where('id_producto', $id_producto);
	$query = $this->db->get('stock');
	if ($query->num_rows() > 0) {
		return $query->row();
	}
	else {
		$data = array(
			'id_producto' => $id_producto,
			'id_sucursal' => $id_sucursal,
			'cantidad' => 0,
		);
		$this->db->insert('stock', $data);
		if($this->db->affected_rows() > 0){
			$this->db->where('id_sucursal', $id_sucursal);
			$this->db->where('id_producto', $id_producto);
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

	$this->db->select("icd.*, p.nombre,p.imei,p.n_imei,p.marca,p.modelo");
	$this->db->from('ventas_detalle AS icd');
	$this->db->join('producto as p', 'p.id_producto=icd.id_producto');
	$this->db->where('id_venta',$id_carga);
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

function get_reservado($id_producto,$id_venta){
	$this->db->select("sum(ventas_detalle.cantidad) as reservado");
	$this->db->from("ventas_detalle");
	$this->db->where("id_producto",$id_producto);
	$this->db->where("id_venta",$id_venta);
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->row();
	} else {
		return 0;
	}
}


}
/* End of file VentasModel.php */
