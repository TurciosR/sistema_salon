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

class Traslados_pendientesModel extends CI_Model {

	var $table = "producto";
	var $pk = "id_producto";

	//create query for get datatables
	function create_dt_query()
	{
		//table and sql query string
		$table  = 'traslado as v';
		$query  = "CONCAT(s1.nombre) as suc1,
    CONCAT(s2.nombre) as suc2,v.requiere_imei,v.imei_ingresado,
    v.id_traslado,DATE_FORMAT(v.fecha,'%d-%m-%Y') as fecha, FORMAT(v.total,2) as total,v.guia,
    CASE WHEN v.estado=0 THEN 'PENDIENTE' WHEN v.estado=1 THEN 'FINALIZADO'
    WHEN v.estado=2 THEN 'ANULADO' END as estados_p, v.id_sucursal_despacho, v.id_sucursal_destino, v.estado";
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
  	//$this->db->order_by('p.n_imei', 'ASC');
  	$data = $this->db->get();
  	if ($data->num_rows() > 0) {
  		return $data->result();
  	} else {
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
