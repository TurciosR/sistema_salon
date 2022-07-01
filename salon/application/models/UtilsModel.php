<?php
/**
 * This file is part of the OpenPyme2.
 *
 * (c) Open Solution Systems <operaciones@tumundolaboral.com.sv>
 *
 * For the full copyright and license information, please refere to LICENSE file
 * that has been distributed with this source code.
 */

defined('BASEPATH') or exit('No direct script access allowed');

class UtilsModel extends CI_Model
{
  function begin()
  {
    $this->db->trans_begin();
  }
  function rollback()
  {
    $this->db->trans_rollback();
  }
  function commit()
  {
    $this->db->trans_commit();
  }
  function error()
  {
    $this->db->error();
  }
  function insert($table_name, $form_data)
  {
    // retrieve the keys of the array (column titles)
    $form_data2 = array();
    $variable = '';
    // retrieve the keys of the array (column titles)
    $fields = array_keys($form_data);
    // join as string fields and variables to insert
    $fieldss = implode(',', $fields);
    //$variables = implode ( "','", $form_data ); U+0027
    foreach ($form_data as $variable) {
      $var1 = preg_match('/\x{27}/u', $variable);
      $var2 = preg_match('/\x{22}/u', $variable);
      if ($var1 == true || $var2 == true) {
        $variable = addslashes($variable);
      }
      array_push($form_data2, $variable);
    }
    $variables = implode("','", $form_data2);

    // build the query
    $sql = "INSERT INTO " . $table_name . "(" . $fieldss . ")";
    $sql .= "VALUES('" . $variables . "')";
    return $this->db->query($sql);
  }
  function insert_id()
  {
    return $this->db->insert_id();
  }
  function update($table_name, $form_data, $where_clause)
  {
    // check for optional where clause
    $whereSQL = '';
    $form_data2 = array();
    $variable = '';
    if (!empty($where_clause)) {
      // check to see if the 'where' keyword exists
      if (substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
        // not found, add key word
        $whereSQL = " WHERE " . $where_clause;
      } else {
        $whereSQL = " " . trim($where_clause);
      }
    }
    // start the actual SQL statement
    $sql = "UPDATE " . $table_name . " SET ";

    // loop and build the column /
    $sets = array();
    //begin modified
    foreach ($form_data as $index => $variable) {
      $var1 = preg_match('/\x{27}/u', $variable);
      $var2 = preg_match('/\x{22}/u', $variable);
      if ($var1 == true || $var2 == true) {
        $variable = addslashes($variable);
      }
      $form_data2[$index] = $variable;
    }
    foreach ($form_data2 as $column => $value) {
      $sets[] = $column . " = '" . $value . "'";
    }
    $sql .= implode(', ', $sets);

    // append the where statement
    $sql .= $whereSQL;
    return $this->db->query($sql);
  }

  function delete($table_name, $where_clause = '')
  {
    // check for optional where clause
    $whereSQL = '';
    if (!empty($where_clause)) {
      // check to see if the 'where' keyword exists
      if (substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
        // not found, add keyword
        $whereSQL = " WHERE " . $where_clause;
      } else {
        $whereSQL = " " . trim($where_clause);
      }
    }
    // build the query
    $sql = "DELETE FROM " . $table_name . $whereSQL;
    return $this->db->query($sql);
  }
  function get_sucursales()
  {
    $row = $this->db->get("sucursales");
    return $row->result();
  }
  function get_roles()
  {
    $this->db->where("deleted", "0");
    $row = $this->db->get("roles");
    return $row->result();
  }
  function getRol($id)
  {
    $this->db->where("id_rol", $id);
    $row = $this->db->get("roles");
    return $row->row();
  }
  function get_roles_detalle($id)
  {
    $this->db->where("id_rol", $id);
    $row = $this->db->get("roles_detalle");
    return $row->result();
  }
  //otras consultas
  function get_one_row($tabla, $where)
  {
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

  /**
   * Returns all data from a database table
   *
   * It consults the table that has been indicated and returns
   * all the data, additionally it can be passed an array with the
   * data for a where
   *
   * @param string  $tabla name of table
   * @param array   $where where data
   *
   * @param object
   */
  function get_detail_rows($tabla, $where = array())
  {

    if (!empty($where)) {
      foreach ($where as $key => $value) {
        if ($key != NULL) {
          $this->db->where($key, $value);
        }
      }
    }

    $detail = $this->db->get($tabla);
    if ($detail->num_rows() > 0) {
      return $detail->result();
    } else {
      return 0;
    }
  }
  /**
   * Returns all data from a query, for datatables populate
   *
   * It consults the table that has been indicated and returns
   * all the data, additionally it can be passed an array with the
   * data for a where
   *
   *
   * @param array  contain parameters for set datatables and query sql
   * from order to dir parameters for datatables
   * from table to join parameters for query to sql
   */
  function get_collection(array $options_dt = array())
  {
    $args = array(
      'order'         => NULL,
      'search'        => NULL,
      'valid_columns' => NULL,
      'length'        => NULL,
      'start'         => NULL,
      'dir'           => NULL,
      'table'         => NULL,
      'query'         => NULL,
      'where'         => NULL,
      'count'         => NULL,
      'join'          => NULL,
    );
    $options_dt = array_merge($args, $options_dt);
    extract($options_dt);
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
        if (isset($where) && !empty($where)) {
            foreach ($where as $key => $value) {
              if ($key != NULL) {
                $this->db->where($key, $value);
              }
            }
        }
        $x++;
      }
    }
    $this->db->select($query);
    $this->db->from($table);
    //$k[2]  Options are: left, right, outer, inner, left outer, and right outer.
    if (isset($join) && !empty($join) && $join != NULL) {
      foreach ($join as $k) {
        if (!isset($k[2])) {
          $k[2] = 'left';
        }
        $this->db->join($k[0],$k[1],$k[2]);
      }
    }

    if (isset($where) && !empty($where)) {
        foreach ($where as $key => $value) {
          if($key != NULL){
            $this->db->where($key, $value);
          }
        }
    }
    if ($count != TRUE) {
      $this->db->limit($length, $start);
    }
    $rows = $this->db->get();
    if ($count != TRUE) {
      if ($rows->num_rows() > 0) {
        return $rows->result();
      } else {
        return 0;
      }
    } else {
      if ($rows->num_rows() > 0) {
        return $rows->num_rows();
      } else {
        return 0;
      }
    }
  }
  //apertura caja
  function get_aperturascaja_activa($id_sucursal, $fecha)
  {
    $this->db->where('id_sucursal', $id_sucursal);
    $this->db->where('fecha', $fecha);
    $this->db->where('vigente', '1');
    $query = $this->db->get("apertura_caja");
    if ($query->num_rows() > 0) {
      return $query->row();
    } else {
      return NULL;
    }
  }

  /**
   * Get logo path
   */
  function getLogo(){
    $result = $this->db->select("logo_empresa")->get("configuracion")->row();
    return $result->logo_empresa;

  }
}

/* End of file UtilsModel.php */
