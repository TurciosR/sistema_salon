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

class Cuentas_pagarModel extends CI_Model {
	private $table = "cuentas_por_pagar";
	private $pk = "id_cuentas_por_pagar";

	//create query for get datatables
	function create_dt_query()
	{
		//table and sql query string
		$table  = 'cuentas_por_pagar as pagar';
		$query  = 'pagar.*, p.nombre as proveedor';
		$where  = array(
			'estado' => 0,
		);
		$j1     = array('proveedores as p', 'p.id_proveedor = pagar.id_proveedor');
		$join   = array($j1);

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
		$this->db->where('pagar.estado', 0);
		$this->db->where('pagar.id_cuentas', $id);
		$this->db->select('pagar.*, SUM(abono.abono) as abono_total');
		$this->db->join('cuentas_por_pagar_abonos as abono', 'abono.id_cuentas_por_pagar = pagar.id_cuentas');
		$rows = $this->db->get('cuentas_por_pagar as pagar');
		if ($rows->num_rows() > 0) {
			return $rows->row();
		} else {
			return NULL;
		}
	}

	function get_row_abonos($id)
	{
		$this->db->where('id_cuentas_por_pagar', $id);
		$this->db->select('abono.*, pagar.saldo as saldo_total, pagar.abono as abono_total');
		$this->db->join('cuentas_por_pagar as pagar', 'pagar.id_cuentas = abono.id_cuentas_por_pagar');
		//$this->db->join('ventas as v', 'v.id_venta = cobrar.id_venta');
		$rows = $this->db->get('cuentas_por_pagar_abonos as abono');
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

	public function obtener_total($id_proveedor = 0)
	{
		$this->db->select("saldo");
		if ($id_proveedor != 0) {
			$this->db->where("id_proveedor", $id_proveedor);
		}

		$resultado = $this->db->get("cuentas_por_pagar")->result();

		$saldo = 0.0000;
		foreach($resultado as $item){
			$saldo += $item->saldo;
		}

		return number_format($saldo, 4);
	}

	public function nombre_proveedor($id_proveedor)
	{
		$proveedor = $this->db->select("nombre")->where("id_proveedor", $id_proveedor)
		->get("proveedores")->row();

		return $proveedor->nombre;
	}

	public function obtener_cuentas_por_proveedores($id_proveedor){

	}

}

/* End of file ClientModel.php */
