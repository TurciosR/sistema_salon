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

class Cuentas_cobrarModel extends CI_Model {
	private $table = "cuentas_por_cobrar";
	private $pk = "id_cuentas_por_cobrar";

	//create query for get datatables
	function create_dt_query()
	{
		//table and sql query string
		$table  = 'cuentas_por_cobrar as cobrar';
		$query  = 'cobrar.*, c.nombre, v.tipo_doc, v.serie, v.total,';
		$query .='IF(estado=0, "pendiente", "finalizado") as estado';
		$where  = NULL;
		$j1     = array('ventas as v', 'v.id_venta = cobrar.id_venta');
		$j2     = array('clientes as c', 'c.id_cliente = v.id_cliente');
		$join   = array($j1, $j2);

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

	function get_row($id)
	{
		//$this->db->where('cobrar.estado', 0);
		$this->db->where('cobrar.id_cuentas', $id);
		$this->db->select('cobrar.*, c.nombre, v.tipo_doc, v.serie, v.total, SUM(abono.abono) as abono_total');
		$this->db->join('ventas as v', 'v.id_venta = cobrar.id_venta');
		$this->db->join('cuentas_por_cobrar_abonos as abono', 'abono.id_cuentas_por_cobrar = cobrar.id_cuentas');
		$this->db->join('clientes as c', 'c.id_cliente = v.id_cliente');
		$rows = $this->db->get('cuentas_por_cobrar as cobrar');
		if ($rows->num_rows() > 0) {
			return $rows->row();
		} else {
			return NULL;
		}
	}

	function get_row_abonos($id)
	{
		$this->db->where('id_cuentas_por_cobrar', $id);
		$this->db->select('abono.*, cobrar.saldo as saldo_total, cobrar.abono as abono_total');
		$this->db->join('cuentas_por_cobrar as cobrar', 'cobrar.id_cuentas = abono.id_cuentas_por_cobrar');
		//$this->db->join('ventas as v', 'v.id_venta = cobrar.id_venta');
		$rows = $this->db->get('cuentas_por_cobrar_abonos as abono');
		if ($rows->num_rows() > 0) {
			return $rows->result();
		} else {
			return NULL;
		}
	}

	function get_row_info($id)
	{
		$this->db->where($this->pk, $id);
		$rows = $this->db->get($this->table);
		if ($rows->num_rows() > 0) {
			return $rows->row();
		} else {
			return NULL;
		}
	}

	function get_state($id)
	{
		$this->db->where('activo', 1);
		$this->db->where($this->pk, $id);
		$rows = $this->db->get($this->table);
		if ($rows->num_rows() > 0) {
			return 1;
		} else {
			return NULL;
		}
	}
}

/* End of file ClientModel.php */
