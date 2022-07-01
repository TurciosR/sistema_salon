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

class Proveedores extends yidas\rest\Controller {
    /*
    Global table name
    */
    private $table = "proveedores";
    private $pk = "id_proveedor";

    function __construct()
    {
        parent::__construct();
        $this->load->model('UtilsModel',"utils");
        $this->load->model("ProveedoresModel","proveedores");
    }

    public function index()
    {
        $data = array(
            "titulo"=> "Proveedores",
            "icono"=> "mdi mdi-truck",
            "buttons" => array(
                0 => array(
                    "icon"=> "mdi mdi-plus",
                    'url' => 'proveedores/agregar',
                    'txt' => ' Agregar Proveedor',
                    'modal' => false,
                ),
            ),
            "table"=>array(
                "ID"=>10,
                "Nombre"=>20,
                "Dirección"=>20,
                "Tipo"=>20,
                "Estado"=>10,
                "Acciones"=>10,
            ),
        );
        $extras = array(
            'css' => array(
            ),
            'js' => array(
                "js/scripts/proveedores.js"
            ),
        );
        layout("template/admin",$data,$extras);
    }

    function get_data()
    {
        $valid_columns = array(
            0 => 'id_proveedor',
            1 => 'nombre',
            2 => 'direccion',
        );
        // Create query based on mariadb tables required
        $query_val  = $this->proveedores->create_dt_query();

        /* You can pass where and join clauses as necessary or include it on model
         * function as necessary. If no join includ it set to NULL.
         */
        $options_dt = array(
                'valid_columns' => $valid_columns,
        );
        $options_dt = array_merge($query_val, $options_dt);
        $row        = generate_dt("UtilsModel", $options_dt, FALSE);
        $draw       = intval($this->input->post("draw"));
        if ($row != 0) {
            $data = array();
            foreach ($row as $rows) {

                $menudrop = "<div class='btn-group'>
                <button data-toggle='dropdown' class='btn btn-success dropdown-toggle' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menú</button>
                <ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";
                $filename = base_url("proveedores/editar/");
                $menudrop .= "<li><a role='button' href='" . $filename.$rows->id_proveedor. "' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";

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
                $menudrop .= "<li><a  class='state_change' data-state='$txt'  id=" . $rows->id_proveedor . " ><i class='$icon'></i> $txt</a></li>";

                $menudrop .= "<li><a  class='delete_row'  id=" . $rows->id_proveedor . " ><i class='mdi mdi-trash-can-outline'></i> Eliminar</a></li>";
                $menudrop .= "</ul></div>";

                if($rows->tipo==1)  $tipo = "<p>Nacional</p>";
                else $tipo= "<p>Internacional</p>";

                $data[] = array(
                    $rows->id_proveedor,
                    $rows->nombre,
                    $rows->direccion,
                    $tipo,
                    $show_text,
                    $menudrop,
                );
            }
            $total = generate_dt("UtilsModel", $options_dt, TRUE);
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
                "",
            );
            $output = array(
                "draw" => $draw,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => $data
            );
        }
        echo json_encode($output);
    }

    function agregar(){
        if($this->input->method(TRUE) == "GET"){
            $departamentos = $this->proveedores->get_departamentos();
            $giro = $this->proveedores->get_giro();
            $categoria_proveedor = $this->proveedores->get_categoria_proveedor();
            $tipo_proveedor = $this->proveedores->get_tipo_proveedor();
            $data = array(
                "departamentos"=>$departamentos,
                "giro"=>$giro,
                "categoria_proveedor"=>$categoria_proveedor,
                "tipo_proveedor"=>$tipo_proveedor,
            );
            $extras = array(
                'css' => array(
                ),
                'js' => array(
                    "js/scripts/proveedores.js"
                ),
            );
            layout("proveedores/agregar_proveedor",$data,$extras);
        }
        else if($this->input->method(TRUE) == "POST"){

            $nombre = strtoupper($this->input->post("nombre"));
            $direccion = strtoupper($this->input->post("direccion"));
            $tipo = $this->input->post("tipo");
            $departamento = $this->input->post("departamento");
            $municipio = $this->input->post("municipio");
            $dui = $this->input->post("dui");
            $nit = $this->input->post("nit");
            $nrc = $this->input->post("nrc");
            $giro = $this->input->post("giro");
            $categoria = $this->input->post("categoria");
            $tipo_proveedor = $this->input->post("tipo_proveedor");
            $nombre1 = $this->input->post("nombre1");
            $telefono1 = $this->input->post("telefono1");
            $correo1 = $this->input->post("correo1");
            $comentario1 = $this->input->post("comentario1");
            $nombre2 = $this->input->post("nombre2");
            $telefono2 = $this->input->post("telefono2");
            $correo2 = $this->input->post("correo2");
            $comentario2 = $this->input->post("comentario2");


            $data = array(
                "nombre"=>$nombre,
                "direccion"=>$direccion,
                "tipo"=>$tipo,
                "municipio"=>$municipio,
                "dui"=>$dui,
                "nit"=>$nit,
                "nrc"=>$nrc,
                "giro"=>$giro,
                "departamento"=>$departamento,
                "categoria"=>$categoria,
                "tipo_proveedor"=>$tipo_proveedor,
                "nombre1"=>$nombre1,
                "telefono1"=>$telefono1,
                "correo1"=>$correo1,
                "comentario1"=>$comentario1,
                "nombre2"=>$nombre2,
                "telefono2"=>$telefono2,
                "correo2"=>$correo2,
                "comentario2"=>$comentario2,
                "activo"=>1,
            );
            $this->utils->begin();
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

    function get_municipios()
    {
        $id_departamento = $this->input->post("id_departamento");
        $municipios = $this->proveedores->get_municipio($id_departamento);
        $option = "";
        $option .= "<option value='0'>Seleccione un municipio</option>";
        foreach ($municipios as  $value) {
            $option .= "<option value='".$value->id_municipio."'>".$value->nombre."</option>";
        }
        echo $option;
    }

    function editar($id=-1){

        if($this->input->method(TRUE) == "GET"){
            $id = $this->uri->segment(3);
            $row = $this->proveedores->get_row_info($id);
            if($row && $id!=""){
                $departamentos = $this->proveedores->get_departamentos();
                $municipios = $this->proveedores->get_municipio($row->departamento);
                $giro = $this->proveedores->get_giro();
                $categoria_proveedor = $this->proveedores->get_categoria_proveedor();
                $tipo_proveedor = $this->proveedores->get_tipo_proveedor();
                $data = array(
                    "row"=>$row,
                    "departamentos"=>$departamentos,
                    "giro"=>$giro,
                    "categoria_proveedor"=>$categoria_proveedor,
                    "tipo_proveedor"=>$tipo_proveedor,
                    "municipios"=>$municipios,
                );
                $extras = array(
                    'css' => array(
                    ),
                    'js' => array(
                        "js/scripts/proveedores.js"
                    ),
                );
                layout("proveedores/editar_proveedor",$data,$extras);
            }else{
                redirect('errorpage');
            }
        }
        else if($this->input->method(TRUE) == "POST"){
            $id_proveedor = $this->input->post("id_proveedor");
            $nombre = strtoupper($this->input->post("nombre"));
            $direccion = strtoupper($this->input->post("direccion"));
            $tipo = $this->input->post("tipo");
            $departamento = $this->input->post("departamento");
            $municipio = $this->input->post("municipio");
            $dui = $this->input->post("dui");
            $nit = $this->input->post("nit");
            $nrc = $this->input->post("nrc");
            $giro = $this->input->post("giro");
            $categoria = $this->input->post("categoria");
            $tipo_proveedor = $this->input->post("tipo_proveedor");
            $nombre1 = $this->input->post("nombre1");
            $telefono1 = $this->input->post("telefono1");
            $correo1 = $this->input->post("correo1");
            $comentario1 = $this->input->post("comentario1");
            $nombre2 = $this->input->post("nombre2");
            $telefono2 = $this->input->post("telefono2");
            $correo2 = $this->input->post("correo2");
            $comentario2 = $this->input->post("comentario2");

            $where = "id_proveedor='".$id_proveedor."'";

            $data = array(
                "nombre"=>$nombre,
                "direccion"=>$direccion,
                "tipo"=>$tipo,
                "municipio"=>$municipio,
                "dui"=>$dui,
                "nit"=>$nit,
                "nrc"=>$nrc,
                "giro"=>$giro,
                "departamento"=>$departamento,
                "categoria"=>$categoria,
                "tipo_proveedor"=>$tipo_proveedor,
                "nombre1"=>$nombre1,
                "telefono1"=>$telefono1,
                "correo1"=>$correo1,
                "comentario1"=>$comentario1,
                "nombre2"=>$nombre2,
                "telefono2"=>$telefono2,
                "correo2"=>$correo2,
                "comentario2"=>$comentario2,
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



    function get_proveedor_autocomplete(){
        $query = $this->input->post("query");
        $rows = $this->productos->get_proveedor_autocomplete($query);
        $output = array();
        if($rows!=NULL) {
            foreach ($rows as $row) {
                $output[] = array(
                    'proveedor' => $row->id_proveedor . " | " . $row->nombre
                );
            }
        }
        echo json_encode($output);
    }

    function eliminar_proveedor(){
        if($this->input->method(TRUE) == "POST"){
            $id = $this->input->post("id");
            $where = " id_pp ='".$id."'";
            $this->utils->begin();
            $delete = $this->utils->delete("producto_proveedor",$where);
            if($delete) {
                $this->utils->commit();
                $data["type"] = "success";
                $data["title"] = "Información";
                $data["msg"] = "Registro eliminado con exito!";
            }
            else {
                $this->utils->rollback();
                $data["type"] = "Error";
                $data["title"] = "Alerta!";
                $data["msg"] = "Registro no pudo ser eliminado!";
            }
            echo json_encode($data);
        }
    }

    function get_images(){
        if($this->input->method(TRUE) == "POST"){
            $id = $this->input->post("id");
            $productos = $this->productos->get_images($id);
            $new = [];
            foreach ($productos as $row){
                array_push($new, array('id'=>$row->id_imagen, 'imagen'=>base_url($row->url)));
            }
            echo json_encode($new);
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
            $active = $this->proveedores->get_state($id);
            $response = change_state($this->table,$this->pk,$id,$active);
            echo json_encode($response);
        }
    }

}

/* End of file Productos.php */
