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
 * Model ModelosModel
 *
 * Contains all queries to the database for the Module Models (Modelos)
 *
 * @package		OpenPyme2
 * @subpackage	Models
 * @category	Models
 * @author		OpenPyme Dev Team
 * @link		sftp://docs.apps-oss.com/classes/modelosModel.html
 */
class ModelosModel extends CI_Model {
	private $table = "modelo";
	private $pk    = "id_modelo";

	
//create query for get datatables
	function create_query(){
		//table and sql query string
		$table  = 'modelo as mo';
		$query  = "mo.*,ma.nombre as nombremarca";
		$sql_array =array('table'=>$table,'query'=>$query);
		//create join array
		/*
		First parameter: table name
		Second parameter: keys for join
		Third parameter: join types based on sql, optional
		join Options are: left, right, outer, inner, left outer, and right outer.
		example $j1=array('clientes as c', 'v.id_cliente = c.id_cliente');
			$join = array($j1);
		*/
		$j1=array('marca as ma', 'mo.id_marca = ma.id_marca');
			$join = array($j1);
		//add join parameters if exist join
		if(isset($join) && !empty($join)){
				$sql_array['join']=$join;
		}
		return $sql_array;
	}

	/**
	 * Get only one record of the 'modelo' table
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
	 * Gets status active|inactive of the 'modelo' table
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

}

/* fin ./application/models/ModelosModel.php */
