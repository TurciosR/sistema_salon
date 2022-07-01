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
 * Model MarcasModel
 *
 * Contains all queries to the database for the Module Brands (Marcas)
 *
 * @package		OpenPyme2
 * @subpackage	Models
 * @category	Models
 * @author		OpenPyme Dev Team
 * @link		sftp://docs.apps-oss.com/classes/MarcasModel.html
 */
class MarcasModel extends CI_Model {
	private $table = "marca";
	private $pk    = "id_marca";

	/**
	 * Get a collection of records from the 'marca' table
	 *
	 * @return array|null Returns the rows found in the database,
	 * if does not find data returns NULL.
	 */
	function get_collection($order, $search, $valid_columns, $length, $start, $dir)
	{
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
		$this->db->limit($length, $start);
		$this->db->where('deleted', 0);
		$rows = $this->db->get($this->table);
		if ($rows->num_rows() > 0) {
			return $rows->result();
		} else {
			return NULL;
		}
	}

	//create query for get datatables
		function create_query(){
			//table and sql query string
			$table  = 'marca as ma';
			$query  = "ma.*";
			$sql_array =array('table'=>$table,'query'=>$query);
			//add join parameters if exist join
			if(isset($join) && !empty($join)){
					$sql_array['join']=$join;
			}
			return $sql_array;
		}

	/**
	 * Get only one record of the 'marca' table
	 *
	 * @return array|null Returns a record in array format
	 * if does not find data returns NULL
	 */
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

	/**
	 * Gets status active|inactive of the 'marca' table
	 *
	 * @return int|null Return Integer 1 if the record is 'active'
	 * if it is inactive return NULL
	 */
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

	function get_all_marcas()
	{
		$this->db->select('id_marca, nombre');
		$query = $this->db->get($this->table);
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return 0;
		}
	}

}

/* fin ./application/models/MarcasModel.php */
