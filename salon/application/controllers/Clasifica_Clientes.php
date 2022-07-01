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

class Clasifica_clientes extends yidas\rest\Controller {

    /*
    Global table name
    */
    private $table = "clasifica_cliente";
    private $pk = "id_clasifica";

    function __construct()
    {
        parent::__construct();
        $this->load->model('UtilsModel',"utils");
        $this->load->Model("ClasificaclienteModel","clasifica");
    }

    public function index()
    {
        $data = array(
            "titulo"=> "Clasificaciond de clientes",
            "icono"=> "mdi mdi-cash-usd",
            "buttons" => array(
                0 => array(
                    "icon"=> "mdi mdi-plus",
                    'url' => 'clasifica_clientes/agregar',
                    'txt' => 'Agregar Porcentaje',
                    'modal' => true,
                ),
            ),
            "table"=>array(
                "ID"=>1,
                "Descripcion"=>4,
                "Porcentaje de Utilidad"=>4,
                "Estado"=>2,
                "Acciones"=>1,
            ),
        );
        $extras = array(
            'css' => array(
            ),
            'js' => array(
                "js/scripts/clasifica_clientes.js",
            ),
        );
        layout("template/admin",$data,$extras);
    }

    function get_data(){
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));

        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id_clasifica',
            1 => 'descripcion',

        );
        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }

        $row = $this->clasifica->get_collection($order, $search, $valid_columns, $length, $start, $dir);

        if ($row != 0) {
            $data = array();
            foreach ($row as $rows) {

                $menudrop = "<div class='btn-group'>
					<button data-toggle='dropdown' class='btn btn-success dropdown-toggle' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
					<ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";
                $menudrop .= "<li><a  data-toggle='modal' data-target='#viewModal' data-refresh='true'  role='button' class='modal_edit_porcentaje' data-id=".$rows->id_clasifica."><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";
                $state = $rows->activo;
                if($state==1){
                    $txt = "Desactivar";
                    $show_text = "<span class='badge badge-success font-bold'>Activo<span>";
                    $icon = "mdi mdi-toggle-switch-off";
                }
                else{
                    $txt = "Activar";
                    $show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
                    $icon = "mdi mdi-toggle-switch";
                }
                $menudrop .= "<li><a  class='state_change' data-state='$txt'  id=" . $rows->id_clasifica . " ><i class='$icon'></i> $txt</a></li>";
                $menudrop .= "<li><a  class='delete_row'  id=" . $rows->id_clasifica . " ><i class='mdi mdi-trash-can-outline'></i> Eliminar</a></li>";
                $menudrop .= "</ul></div>";

                $data[] = array(
                    $rows->id_clasifica,
                    $rows->descripcion,
                    $rows->porcentaje,
                    $show_text,
                    $menudrop,
                );
            }
            $total = $this->clasifica->total_rows();
            $output = array(
                "draw" => $draw,
                "recordsTotal" => $total,
                "recordsFiltered" => $total,
                "data" => $data
            );
        } else {
            $data[] = array(
                "",
                "",
                "No se encontraron registros",
                "",
                "",
            );
            $output = array(
                "draw" => 0,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => $data
            );
        }
        echo json_encode($output);
    }

    function agregar(){

        if($this->input->method(TRUE) == "GET"){
            $data = array();
            $this->load->view("clientes/agregar_porcentaje",$data);
        }
        else if($this->input->method(TRUE) == "POST"){
            $descripcion = strtoupper($this->input->post("descripcion"));
            $porcentaje = $this->input->post("porcentaje");

            $data = array(
                "descripcion"=>$descripcion,
                "porcentaje"=>$porcentaje,
                "activo"=>1,
            );
            $insert = $this->utils->insert($this->table,$data);
            if($insert){
                $this->utils->commit();
                $xdatos["type"]="success";
                $xdatos['title']='Información';
                $xdatos["msg"]="Registo ingresado correctamente!";
            }
            else {
                $this->utils->rollback();
                $xdatos["type"]="error";
                $xdatos['title']='Alerta';
                $xdatos["msg"]="Error al ingresar el registro";
            }
            echo json_encode($xdatos);
        }
    }

    function editar($id=-1){
        if($this->input->method(TRUE) == "GET"){
            $id = $this->uri->segment(3);
            $row = $this->clasifica->get_row_info($id);
            if($row && $id!=""){
                $data = array(
                    "row"=>$row,
                );
                $this->load->view("clientes/editar_porcentaje",$data);
            }else{
                redirect('errorpage');
            }
        }
        else if($this->input->method(TRUE) == "POST"){
            $descripcion = strtoupper($this->input->post("descripcion"));
            $porcentaje = $this->input->post("porcentaje");
            $id_clasifica = strtoupper($this->input->post("id_clasifica"));
            $where = " id_clasifica='".$id_clasifica."'";

            $data = array(
                "descripcion"=>$descripcion,
                "porcentaje"=>$porcentaje,
            );
            $this->utils->begin();
            $insert = $this->utils->update($this->table,$data,$where);
            if($insert){
                $this->utils->commit();
                $xdatos["type"]="success";
                $xdatos['title']='Información';
                $xdatos["msg"]="Registo editado correctamente!";
            }
            else {
                $this->utils->rollback();
                $xdatos["type"]="error";
                $xdatos['title']='Alerta';
                $xdatos["msg"]="Error al editar el registro";
            }
            echo json_encode($xdatos);
        }
    }

    function delete(){
        if($this->input->method(TRUE) == "POST"){
            $id = $this->input->post("id");
            $response = safe_delete($this->table,$this->pk,$id);
            echo json_encode($response);
        }
    }

    function state_change(){
        if($this->input->method(TRUE) == "POST"){
            $id = $this->input->post("id");
            $active = $this->clasifica->get_state($id);
            $response = change_state($this->table,$this->pk,$id,$active);
            echo json_encode($response);
        }
    }

}

/* End of file Clasifica_clientes.php */
