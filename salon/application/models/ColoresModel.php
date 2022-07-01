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

class ColoresModel extends CI_Model {
	private $table = "colores";
	private $pk = "id_color";

	function create_dt_query()
	{
		//table and sql query string
		$table = $this->table;
		$query = "*";
		$where = NULL;
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
	function get_row_color($id)
	{
		$this->db->where('id_color', $id);
		$clients = $this->db->get("colores");
		if ($clients->num_rows() > 0) {
			return $clients->row();
		} else {
			return 0;
		}
	}
	function get_existe($color)
	{
		$this->db->where('color', $color);
		$clients = $this->db->get("colores");
		if ($clients->num_rows() > 0) {
			return $clients->num_rows();
		} else {
			return 0;
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
	function get_colores()
	{
		$clients = $this->db->get("colores");
		if ($clients->num_rows() > 0) {
			return $clients->result();
		} else {
			return 0;
		}
	}

}

/* End of file ClientModel.php */
?>
