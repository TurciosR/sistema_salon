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

class ClasificaClienteModel extends CI_Model
{
//  var $table = "clasifica_cliente";
  /*
    public function __construct() {
          parent::__construct();
          $table = "clasifica_cliente";
           $pk = "id_clasifica";
          $this->table=$table;
       }*/
       protected $table = 'clasifica_cliente';
//  protected $primaryKey = 'id_clasifica';
protected $pk= 'id_clasifica';
    function get_collection($order, $search, $valid_columns, $length, $start, $dir)
    {
        if ($order !=	 null) {
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
        $clients = $this->db->get($this->table);
        if ($clients->num_rows() > 0) {
            return $clients->result();
        } else {
            return 0;
        }
    }
    function total_rows(){
        $clients = $this->db->get($this->table);
        if ($clients->num_rows() > 0) {
            return $clients->num_rows();
        } else {
            return 0;
        }
    }

    function get_row_info($id){
        $this->db->where($this->pk, $id);
        $clients = $this->db->get($this->table);
        if ($clients->num_rows() > 0) {
            return $clients->row();
        } else {
            return 0;
        }
    }
    function get_state($id){
        $this->db->where('activo', 1);
        $this->db->where($this->pk, $id);
        $clients = $this->db->get($this->table);
        if ($clients->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

}

/* End of file ClientModel.php */
