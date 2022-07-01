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

class CorteModel extends CI_Model {

	var $table = "controlcaja";
	var $pk = "id_corte";

	function get_collection($order, $search, $valid_columns, $length, $start, $dir,$id_sucursal,$f1,$f2)
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
						$this->db->where("c.fecha_corte BETWEEN '$f1' AND '$f2' ",NULL,FALSE);


				} else {
					$this->db->or_like($sterm, $search);
					$this->db->where("c.id_sucursal",$id_sucursal);
						$this->db->where("c.fecha_corte BETWEEN '$f1' AND '$f2' ",NULL,FALSE);
				}
				$x++;
			}
		}
		$sql="c.id_corte, c.fecha_corte, c.hora_corte, c.id_empleado, c.id_apertura, c.tipo_corte, c.cashfinal, c.diferencia, c.turno ";
		$this->db->select("c.id_corte, c.fecha_corte, c.hora_corte, c.id_empleado, c.id_apertura, c.tipo_corte, c.cashfinal, c.diferencia, c.turno ");
		$this->db->from('controlcaja as c');
		$this->db->where("c.id_sucursal",$id_sucursal);
		$this->db->where("c.fecha_corte BETWEEN '$f1' AND '$f2' ",NULL,FALSE);
		$this->db->limit($length, $start);
		$clients = $this->db->get();
		if ($clients->num_rows() > 0) {
			return $clients->result();
		} else {
			return 0;//$this->db->last_query();
		}
	}
		function total_rows($id_sucursal,$f1,$f2){
		$this->db->where("id_sucursal",$id_sucursal);
		$this->db->where("fecha_corte BETWEEN '$f1' AND '$f2' ",NULL,FALSE);
		$clients = $this->db->get("controlcaja");
		if ($clients->num_rows() > 0) {
			return $clients->num_rows();
		} else {
			return 0;
		}
	}

	function get_totalrow_params($tabla,$where){
		foreach ($where as $key => $value) {
			$this->db->where($key, $value);
		}
		$data = $this->db->get($tabla);
		if ($data->num_rows() > 0) {
			return $data->num_rows();
		} else {
			return 0;
		}
	}
	function get_one_value($tabla,$where,$field){
		$this->db->select($field);
		foreach ($where as $key => $value) {
			$this->db->where($key, $value);
		}
		$query = $this->db->get($tabla);
		if ($query->num_rows() > 0) {
			$correlativo= $query->row();
			$num = $correlativo->$field;
			return $num;
		} else {
			return 0;
		}
	}


	function get_consolidate($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,$tipo_doc){
		//esta funcion se ejecutara desde el controller 3 veces para obtener los correlativos de ticket ,
		//factura y ccf pasando los parametros requeridos
		$this->db->select("MIN(correlativo) as minimo, MAX(correlativo) as maximo");
		$this->db->where("id_sucursal",$id_sucursal);
		$this->db->where("id_apertura",$id_apertura);
		$this->db->where("tipo_doc",$tipo_doc);
		$this->db->where("id_estado",$id_estado);
		$this->db->where("fecha",$fecha_apertura);
		$this->db->where("hora BETWEEN '$hora_ap' AND '$hora_actual' ",NULL,FALSE);
		$row=$this->db->get("ventas");
		if ($row->num_rows() > 0) {
			return $row->row();
		} else {
			return NULL;
		}
	}
	function get_max_min_corr($hora_ap,$hora_actual,$valor,$where){
		//esta funcion se ejecutara desde el controller 3 veces para obtener los correlativos de ticket ,
		//factura y ccf pasando los parametros requeridos
		$this->db->select("MIN(correlativo) as minimo, MAX(correlativo) as maximo");
		foreach ($where as $key => $value) {
			if($key!='null'){
				$this->db->where($key, $value);
			}
		}
		$this->db->where("hora BETWEEN '$hora_ap' AND '$hora_actual' ",NULL,FALSE);
		$row=$this->db->get("ventas");
		if ($row->num_rows() > 0) {
			return $row->row();
		} else {
			return NULL;
		}
	}
	function get_total_num_docs($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,$tipo_doc)
	{
	//	$this->db->select('count(*)');
	  $this->db->select('id_venta');
		$this->db->where("id_sucursal",$id_sucursal);
		$this->db->where("id_apertura",$id_apertura);
		$this->db->where("tipo_doc",$tipo_doc);
		$this->db->where("id_estado",$id_estado);
		$this->db->where("fecha",$fecha_apertura);
		$this->db->where("hora BETWEEN '$hora_ap' AND '$hora_actual' ",NULL,FALSE);
		//$row=$this->db->get("ventas");
		$this->db->from("ventas");
		$row=$this->db->count_all_results();
			return $row;
	}
	//rango de documentos puede ser efectivo y tarjeta
	function get_total_docs_tp($hora_ap,$hora_actual,$valor,$where)
	{
 		$this->db->select('id_venta');
		foreach ($where as $key => $value) {
			if($key!='null'){
				$this->db->where($key, $value);
			}
		}
		$this->db->where("hora BETWEEN '$hora_ap' AND '$hora_actual' ",NULL,FALSE);
		//$row=$this->db->get("ventas");
		$this->db->from("ventas");
		$row=$this->db->count_all_results();
			return $row;
	}
	function get_total_dinero_corte($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,$tipo_doc,$valor)
	{
		$this->db->select_sum($valor);
		$this->db->where("id_sucursal",$id_sucursal);
		$this->db->where("id_apertura",$id_apertura);
		$this->db->where("tipo_doc",$tipo_doc);
		$this->db->where("id_estado",$id_estado);
		$this->db->where("fecha",$fecha_apertura);
		$this->db->where("hora BETWEEN '$hora_ap' AND '$hora_actual' ",NULL,FALSE);
		$row=$this->db->get("ventas");
		if ($row->num_rows() > 0) {
			return $row->row();
		} else {
			return NULL;
		}
	}
	//funcion mejorada para sumar totales de facturacion utiles en el corte de caja
	function get_totales_dinero_corte($hora_ap,$hora_actual,$valor,$where)
																		//($id_sucursal,$hora_ap,$hora_actual,$valor,$where)
	{
		$this->db->select_sum($valor);
		foreach ($where as $key => $value) {
			if($key!='null'){
				$this->db->where($key, $value);
			}
		}
		//
		$this->db->where("hora BETWEEN '$hora_ap' AND '$hora_actual' ",NULL,FALSE);
		$row=$this->db->get("ventas");
		if ($row->num_rows() > 0) {
			$valor_total=$row->row()->$valor;
		} else {
			$valor_total= 0;
		}
			return $valor_total ;
	}
	function get_total_mov_caja($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$tipo_mov)
	{
		$this->db->select_sum("valor");
		$this->db->where("id_sucursal",$id_sucursal);
		//$this->db->where("id_apertura",$id_apertura);
		$this->db->where("anulado",0);
		$this->db->where($tipo_mov,1);
		$this->db->where("fecha",$fecha_apertura);
		$this->db->where("hora BETWEEN '$hora_ap' AND '$hora_actual' ",NULL,FALSE);
		$row=$this->db->get("mov_caja");
		if ($row->num_rows() > 0) {
			return $row->row();
		} else {
			return NULL;
		}
	}
	function get_caja_activa($id_usuario,$fecha){
	//	$this->db->where("id_usuario",$id_usuario);
		$this->db->where('fecha', $fecha);
		$this->db->where('vigente', '1');
		$query = $this->db->get("apertura_caja");
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		else {
			return NULL;
		}
	}
	function get_cajas_activa_sucursal($id_sucursal,$fecha){
	   $this->db->where("id_sucursal",$id_sucursal);
		$this->db->where('fecha', $fecha);
		$this->db->where('vigente', '1');
		$query = $this->db->get("apertura_caja");
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		else {
			return NULL;
		}
	}
	function insert_row($data){
        $this->db->insert('servicio', $data);
        if($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }else{
            return NULL;
        }
    }
		function get_row_info($id){
			$this->db->where('id_corte', $id);
			$clients = $this->db->get($this->table);
			if ($clients->num_rows() > 0) {
				return $clients->row();
			} else {
				return 0;
			}
		}
	function get_state($id){
		$this->db->select("activa");
		$this->db->where('id_corte', $id);
		$clients = $this->db->get($this->table);
		if ($clients->num_rows() > 0) {
			return $clients->row();
		} else {
			return 0;
		}
	}

	function inAndCon($table,$data){
		$this->db->insert($table, $data);
		if($this->db->affected_rows() > 0){
			return $this->db->insert_id();
		}else{
			return NULL;
		}
	}

	function get_apertura_activa($caja,$fecha){
		$this->db->where("caja",$caja);
	  $this->db->where('fecha', $fecha);
		$this->db->order_by('id_apertura',"DESC");
		$this->db->limit(1);
		$query = $this->db->get("apertura_caja");
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		else {
			return NULL;
		}
	}

	function get_apertura($id){
		$this->db->where("id_apertura",$id);
		$this->db->limit(1);
		$query = $this->db->get("apertura_caja");
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		else {
			return NULL;
		}
	}

	function get_turno_desc($tabla,$where){
			foreach ($where as $key => $value) {
				$this->db->where($key, $value);
			}
		$this->db->order_by('turno',"DESC");
		$this->db->limit(1);
		$query = $this->db->get($tabla);
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		else {
			return NULL;
		}
	}
	function get_precios(){
		$porc = $this->db->get("porcentajes");
		return $porc->result();
	}
	function get_porcent_desc_cliente($id_clasifica){
		$this->db->where("deleted","0");
	  $this->db->where('id_clasifica', $id_clasifica);
		$query = $this->db->get("clasifica_cliente");
		return $porc->result();
	}
	function get_clasifica_cliente($id_cliente){
		$this->db->where("deleted","0");
	  $this->db->where('id_clasifica', $id_clasifica);
		$query = $this->db->get("clasifica_cliente");
		return $porc->result();
	}
	function get_precios_exis($id){
		$porc = $this->db->where("id_producto",$id)->get("producto_precio");
		return $porc->result();
	}


function get_corte($id_corte)
{
	$this->db->select("clientes.nombre, corte.fecha,corte.id_sucursal_despacho");
	$this->db->from("corte");
	$this->db->join("clientes","clientes.id_cliente = corte.id_cliente");
	$this->db->where('id_corte', $id_corte);
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

function get_data_dev($id_devolucion){
	$this->db->select("v.id_venta as doc_afecta,v.correlativo as corr_afecta,v.tipo_doc,t.nombredoc");
	$this->db->from('ventas AS v');
	$this->db->join('devoluciones AS d', 'v.id_venta=d.id_venta',"left");
	$this->db->join('tipodoc AS t', 't.idtipodoc=v.tipo_doc',"left");
	$this->db->where('id_dev', $id_devolucion);
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->row();
	} else {
		return NULL;
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
	$this->db->from('corte_detalle AS icd');
	$this->db->join('producto as p', 'p.id_producto=icd.id_producto');
	$this->db->join('producto_color', 'producto_color.id_color=icd.id_color',"left");
	$this->db->where('id_corte',$id_carga);
	$this->db->order_by('icd.id_detalle', 'ASC');
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return 0;
	}
}
function get_detail_serv($id_carga){

	$this->db->select("icd.*, s.nombre,s.precio_sugerido,s.precio_minimo");
	$this->db->from('corte_detalle AS icd');
	$this->db->join('servicio as s', 's.id_servicio=icd.id_producto');
	$this->db->where('icd.id_corte',$id_carga);
	$this->db->where('icd.tipo_prod',1);
	$this->db->order_by('icd.id_detalle', 'ASC');
	$data = $this->db->get();
	if ($data->num_rows() > 0) {
		return $data->result();
	} else {
		return NULL;
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

	function get_porcent_client($clasifica){
		$this->db->select('porcentaje');
		$this->db->where('id_clasifica', $clasifica);
		$this->db->where('deleted',0);
		$row =$this->db->get('clasifica_cliente');
			if ($row->num_rows() > 0) {
					return $row->row();
			} else {
					return NULL;
			}
	}
	function get_tipodoc(){
		$this->db->where('cliente',1);
		$row =$this->db->get('tipodoc');
			if ($row->num_rows() > 0) {
					return $row->result();

			} else {
					return NULL;
			}
	}
	function get_corte_id($id,$id_sucursal){
		$this->db->where("id_sucursal",$id_sucursal);
		$this->db->where("id_corte",$id);
		$row = $this->db->get('controlcaja');
		if ($row->num_rows() > 0) {
			return $row->row();
		} else {
			return 0;//$this->db->last_query();
		}

	}

	//funcion para obtener los abonos diarios
	function get_totales_abonos($fecha,$where)
	{
		//var_dump($where);
		$this->db->select("SUM(cpca.abono) as sumatoria");
		foreach ($where as $key => $value) {
			if($key!='null'){
				$this->db->where($key, $value);
			}
		}

		$this->db->where("v.fecha", $fecha);
		$this->db->join("cuentas_por_cobrar as cpc", "cpc.id_cuentas=cpca.id_cuentas_por_cobrar");
		$this->db->join("ventas as v", "v.id_venta=cpc.id_venta");
		$row=$this->db->get("cuentas_por_cobrar_abonos cpca");
		if ($row->num_rows() > 0) {
			$valor_total=$row->row()->sumatoria;
		} else {
			$valor_total= 0;
		}
			return $valor_total ;
	}
	function get_caja_corte($id_usuario,$fecha,$caja,$turno){
		$this->db->where("caja",$caja);
		$this->db->where("turno",$turno);
		$this->db->where('fecha', $fecha);
		$query = $this->db->get("apertura_caja");
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		else {
			return NULL;
		}
	}
}
/* End of file CorteModel.php */
