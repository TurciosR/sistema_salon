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

class Banco extends CI_Controller {
  /*
  Global table name
  */
  private $table = "stock";
  private $pk = "id_producto";

  function __construct()
  {
    parent::__construct();
    $this->load->model("BancoModel","banco");
    $this->load->helper("upload_file");
    $this->load->model('UtilsModel', "utils");
  }

  function get_data()
  {
    $valid_columns = array(
      0 => 'id',
      1 => 'nombre',
      2 => 'deleted',
    );

    $where = array(
        "id_sucursal" => $this->session->id_sucursal,
    );

    // Create query based on mariadb tables required
    $query_val  = $this->banco->create_dt_query();

    /* You can pass where and join clauses as necessary or include it on model
     * function as necessary. If no join includ it set to NULL.
     */
    $options_dt = array(
            'valid_columns' => $valid_columns,
            'where'         => $where,
            'join'          => NULL,
    );
    $options_dt = array_merge($query_val, $options_dt);
    $draw       = intval($this->input->post("draw"));
    $row        = generate_dt("UtilsModel", $options_dt, FALSE);
    if ($row != 0) {
      $data = array();
      foreach ($row as $rows) {

        $state = $rows->deleted;
        if ($state==0) {
            $txt = "Desactivar";
            $show_text = "<span class='badge badge-success font-bold'>Activo<span>";
            $icon = "mdi mdi-toggle-switch-off";
        } else {
            $txt = "Activar";
            $show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
            $icon = "mdi mdi-toggle-switch";
        }

        $menudrop = "<div class='btn-group'>
        <button data-toggle='dropdown' class='btn btn-success dropdown-toggle' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
        <ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";

        $filename = base_url("banco/editar/");
        $menudrop .= "<li><a role='button' href='" . $filename.$rows->id. "' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";
        $filename = base_url("banco/planes/");
        $menudrop .= "<li><a role='button' href='" . $filename.$rows->id. "' ><i class='mdi mdi-check-box-multiple-outline' ></i> Planes</a></li>";

        $menudrop .= "<li><a  class='state_change' data-state='$txt'  id=" . $rows->id . " ><i class='$icon'></i> $txt</a></li>";
        $menudrop .= "</ul></div>";

        $data[] = array(
          $rows->id,
          $rows->nombre,
          $show_text,
          $menudrop,
        );
      }
      $total = generate_dt("UtilsModel", $options_dt, TRUE);
      $output = array(
          "draw"            => $draw,
          "recordsTotal"    => $total,
          "recordsFiltered" => $total,
          "data"            => $data,
      );
    } else {
      $data[] = array(
        "",
        "No se encontraron registros",
        "",
        "",
      );

      $output = array(
        "draw"            => $draw,
        "recordsTotal"    => 0,
        "recordsFiltered" => 0,
        "data"            => $data
      );
    }
    echo json_encode($output);
    exit();
  }

  public function admin()
  {
    $data = array(
      "titulo"  => "Admin",
      "icono"   => "mdi mdi-archive",
      "buttons" => array(
          0 => array(
            "icon"  => "mdi mdi-plus",
            'url'   => 'Banco/agregar',
            'txt'   => ' Agregar Banco',
            'modal' => false,
        ),
      ),
      "table"=>array(
          "ID"       => 1,
          "Nombre"   => 30,
          "Estado"   => 5,
          "Acciones" => 5,
      ),
    );
    $extras = array(
      'css' => array(
      ),
      'js' => array(
        "js/scripts/banco.js"
      ),
    );
    layout("template/admin", $data, $extras);
  }

  function agregar(){
    if($this->input->method(TRUE) == "GET"){

      $data = array(
      );

      $extras = array(
        'css' => array(
        ),
        'js' => array(
          "js/scripts/banco.js"
        ),
      );

      layout("banco/agregar",$data,$extras);
    }
    else if($this->input->method(TRUE) == "POST"){
      $nombre = strtoupper($this->input->post("banco"));
      $path = "assets/img/productos/";

      if ($_FILES["foto"]["name"] != "") {
        $imagen = upload_image("foto",$path);
        $url=$path.$imagen;
      }
      else $url = "";

      $data = array(
        "nombre"=>$nombre,
        "imagen"=>$url,
        "id_sucursal"=>$this->session->id_sucursal,
      );
      $response = insert_row("banco",$data);
      echo json_encode($response);
    }
  }

  function editar($id=-1){
    if($this->input->method(TRUE) == "GET"){
      $id = $this->uri->segment(3);
      $row = $this->banco->get_row_info($id);
      if($row && $id!=""){
        $data = array(
          "row"=>$row,
        );
        $extras = array(
          'css' => array(
          ),
          'js' => array(
              "js/scripts/banco.js"
          ),
        );
        layout("banco/editar",$data,$extras);
      }else{
        redirect('errorpage');
      }
    }
    else if($this->input->method(TRUE) == "POST"){
      $nombre = strtoupper($this->input->post("banco"));
      $id = strtoupper($this->input->post("id"));

      $row = $this->banco->get_row_info($id);
      $where = "id"."='".$id."'";

      $path = "assets/img/productos/";
            if ($_FILES["foto"]["name"] != "") {
                $imagen = upload_image("foto",$path);
                $url=$path.$imagen;
            }
            else{
                $url = $row->imagen;
            }

            $data = array(
              "nombre"=>$nombre,
              "imagen"=>$url,
              "id_sucursal"=>$this->session->id_sucursal,
            );
            $response = edit_row("banco",$data,$where);
      echo json_encode($response);
    }
  }

  function planes($id=-1){
    if($this->input->method(TRUE) == "GET"){
      $id = $this->uri->segment(3);
      $row = $this->banco->get_row_info($id);
      if($row && $id!=""){
        $data = array(
          "row"=>$row,
        );
        $extras = array(
          'css' => array(
          ),
          'js' => array(
              "js/scripts/planes.js"
          ),
        );
        layout("banco/plan",$data,$extras);
      }else{
        redirect('errorpage');
      }
    }
    else if($this->input->method(TRUE) == "POST"){

      $id_banco = $this->input->post("id_banco");
      $numero = $this->input->post("numero");
      $porcentaje = $this->input->post("porcentaje");

      $data = array(
        "id_banco "=> $id_banco,
        "cuotas"=> $numero,
        "porcentage"=>$porcentaje,
      );
      $response = insert_row("banco_plan",$data);
      echo json_encode($response);
    }
  }

  function get()
  {
    // code...
    if($this->input->method(TRUE) == "POST"){

      $id_banco = $this->input->post("id_banco");
      $response="";
      $this->db->select("*");
      $this->db->from("banco_plan");
      $this->db->where("id_banco",$id_banco);
      $query = $this->db->get();
      if ($query->num_rows()>0) {
        // code...
        $d = $query->result();

        foreach ($d as $key) {
          $response.="<tr>";
          $response.="
          <td>
            <input type='text' value='".$key->cuotas."' class='form-control nux nu' placeholder='Ingrese el numero de cuotas ej: 12'>
          </td>
          <td>
            <input type='text' value='".$key->porcentage."' class='form-control dex por' placeholder='Ingrese un porcentaje de division ej: 0.9'>
          </td>
          <td>
            <button det='".$key->id."' type='button' class='btn btn-info editp' name='button'><i class='mdi mdi-content-save-edit-outline'></i></button>
            <button det='".$key->id."' type='button' class='btn btn-danger delp'  name='button'><i class='mdi mdi-trash-can-outline'></i></button>
          </td>";
          $response.="</tr>";
        }

      }
      $respon['data'] = $response;
      echo json_encode($respon);
    }
  }


  function delp(){
        if($this->input->method(TRUE) == "POST"){
            $id = $this->input->post("det");
      $this->db->delete('banco_plan', array('id' => $id));
      $data["type"]="success";
      $data['title']='Información';
      $data["msg"]="Registo eliminado correctamente!";
            echo json_encode($data);
        }
    }
  function editp(){
        if($this->input->method(TRUE) == "POST"){

            $id = $this->input->post("det");
      $numero = $this->input->post("numero");
      $porcentaje = $this->input->post("porcentaje");

      $this->db->set('cuotas', $numero);
      $this->db->set('porcentage', $porcentaje);
      $this->db->where('id', $id);
      $this->db->update('banco_plan');

      $data["type"]="success";
      $data['title']='Información';
      $data["msg"]="Registo editado correctamente!";
            echo json_encode($data);
        }
    }
  function state_change(){
        if($this->input->method(TRUE) == "POST"){
            $id = $this->input->post("id");
            $active = $this->banco->get_state($id);

      $this->db->set('deleted', $active);
      $this->db->where('id', $id);
      $this->db->update('banco'); // gives UPDATE `mytable` SET `field` = 'field+1' WHERE `id` = 2

      $data["type"]="success";
      $data['title']='Información';
      $data["msg"]="Registo editado correctamente!";
            echo json_encode($data);
        }
    }

}
?>
