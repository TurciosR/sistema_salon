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

class MovcajaModel extends CI_Model
{
	var $table = "mov_caja";
	var $pk = "id_mov";

	//create query for get datatables
	function create_dt_query()
	{
		//table and sql query string
		$table = 'mov_caja as mc';
		$query = "mc.id_mov,mc.concepto,mc.fecha,mc.valor,mc.nombre_recibe,mc.id_tipo,mc.anulado";
		$join  = NULL;

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

	function insert_row($data)
	{
		$this->db->insert('servicio', $data);
		if ($this->db->affected_rows() > 0) {
			return $this->db->insert_id();
		} else {
			return NULL;
		}
	}
		function get_row_info($id){
			$this->db->where('id_mov', $id);
			$clients = $this->db->get($this->table);
			if ($clients->num_rows() > 0) {
				return $clients->row();
			} else {
				return 0;
			}
		}
		function get_row_info_user($id){
			$this->db->where('id_usuario', $id);
			$row = $this->db->get("usuario");
			if ($row->num_rows() > 0) {
				return $row->row();
			} else {
				return NULL;
			}
		}
	function get_state($id){
		$this->db->select("activa");
		$this->db->where('id_mov_caja', $id);
		$clients = $this->db->get($this->table);
		if ($clients->num_rows() > 0) {
			return $clients->row();
		} else {
			return 0;
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
	function get_caja_activa($id_usuario,$fecha){
		//$this->db->where("id_usuario",$id_usuario);
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

	function get_movcaja_id($id_mov){
		$this->db->where('id_mov', $id_mov);
		$query = $this->db->get("mov_caja");
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		else {
			return NULL;
		}
	}
	/*insertar a tabla con confirmacion*/
	function inAndCon($table,$data){
		$this->db->insert($table, $data);
		if($this->db->affected_rows() > 0){
			return $this->db->insert_id();
		}else{
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


function get_caja($id_caja)
{
	$this->db->select("clientes.nombre, caja.fecha,caja.id_sucursal_despacho");
	$this->db->from("caja");
	$this->db->join("clientes","clientes.id_cliente = caja.id_cliente");
	$this->db->where('id_caja', $id_caja);
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
	$this->db->from('caja_detalle AS icd');
	$this->db->join('producto as p', 'p.id_producto=icd.id_producto');
	$this->db->join('producto_color', 'producto_color.id_color=icd.id_color',"left");
	$this->db->where('id_caja',$id_carga);
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
	$this->db->from('caja_detalle AS icd');
	$this->db->join('servicio as s', 's.id_servicio=icd.id_producto');
	$this->db->where('icd.id_caja',$id_carga);
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
}
/* End of file MovcajaModel.php */
