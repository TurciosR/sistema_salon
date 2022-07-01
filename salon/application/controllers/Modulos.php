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

class Modulos extends CI_Controller {

    /*
    Global table name
    */
    private $file = "roles";
    private $table = "roles";
    private $pk = "id_rol";

    function __construct()
    {
        parent::__construct();
        $this->load->Model("RolesModel","roles");
        $this->load->Model("UtilsModel","utils");
    }

    public function index()
    {
        $data = array(
            "titulo"=> " Roles",
            "icono"=> "mdi mdi-account-key",
            "buttons" => array(
                0 => array(
                    "icon"=> "mdi mdi-plus",
                    'url' => '/agregar',
                    'txt' => 'Agregar Rol',
                    'modal' => false,
                ),
            ),
            "table"=>array(
                "ID"=>1,
                "Nombre"=>4,
                "Descripcion"=>4,
                "Estado"=>2,
                "Acciones"=>1,
            ),
        );
        $extras = array(
            'css' => array(
            ),
            'js' => array(
                //"js/scripts/roles.js",
            ),
        );
        layout("config/modulos",$data,$extras);
    }

   /* function get_data(){
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
            0 => 'id_rol',
            1 => 'nombre',
            2 => 'descripcion',
        );
        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }

        $row = $this->roles->get_collection($order, $search, $valid_columns, $length, $start, $dir);

        if ($row != 0) {
            $data = array();
            foreach ($row as $rows) {

                $menudrop = "<div class='btn-group'>
					<button data-toggle='dropdown' class='btn btn-primary dropdown-toggle' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
					<ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";
                $filename = base_url($this->file."/editar/".$rows->id_rol);
                $menudrop .= "<li><a role='button' href='" . $filename. "' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";


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
                $menudrop .= "<li><a  class='state_change' data-state='$txt'  id=" . $rows->id_rol . " ><i class='$icon'></i> $txt</a></li>";

                $menudrop .= "<li><a  class='delete_row'  id=" . $rows->id_rol . " ><i class='mdi mdi-trash-can-outline'></i> Eliminar</a></li>";
                $menudrop .= "</ul></div>";

                $data[] = array(
                    $rows->id_rol,
                    $rows->nombre,
                    $rows->descripcion,
                    $show_text,
                    $menudrop,
                );
            }
            $total = $this->roles->total_rows();
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
    }*/

    function agregar(){

        if($this->input->method(TRUE) == "GET"){
            $this->load->Model("UsuariosModel","usuarios");
            $menus = $this->usuarios->get_menu();
            $controller_base = $this->usuarios->get_controller();
            $controller = array();
            foreach ($menus as $menu)
            {
                $id_menu = $menu->id_menu;
                $controller[$id_menu] = array_filter($controller_base, function($controller) use ($id_menu)
                {
                    return $controller->id_menu == $id_menu;
                });
            }
            $data = array(
                'controller'=>$controller,
                'menu'=>$menus,
            );
            $extras = array(
                'css' => array(
                ),
                'js' => array(
                    "js/scripts/roles.js",
                ),
            );
            layout("config/agregar_rol",$data,$extras);
        }

        else if($this->input->method(TRUE) == "POST"){

            $descripcion = strtoupper($this->input->post("descripcion"));
            $nombre = strtoupper($this->input->post("nombre"));
            $modules = $this->input->post("modules");
            $data = array(
                "descripcion"=>$descripcion,
                "nombre"=>$nombre,
                "activo"=>1,
            );
            $this->utils->begin();
            $id_rol = $this->roles->insert_rol($data);
            if($id_rol!=NULL){

                foreach ($modules as $mod){

                    $form_data = array(
                        "id_rol"=>$id_rol,
                        "id_modulo"=>$mod["module"],
                    );
                    $this->utils->insert("roles_detalle",$form_data);
                }
                $this->utils->commit();
                $xdatos["type"]="success";
                $xdatos['title']='Información';
                $xdatos["msg"]="Registo ingresado correctamente!";
            }
            else {
                $this->utils->rollback();
                $xdatos["type"]="error";
                $xdatos['title']='Alerta';
                $xdatos["msg"]="Error al ingresar el registro!";
            }
            echo json_encode($xdatos);
        }
    }

    function editar($id=-1){
        if($this->input->method(TRUE) == "GET"){
            $id = $this->uri->segment(3);
            $row = $this->roles->get_row_info($id);
            if($row && $id!=""){
                $this->load->Model("UsuariosModel","usuarios");
                $menus = $this->usuarios->get_menu();
                $controller_base = $this->usuarios->get_controller();
                $roles = $this->roles->get_roles($id);
                $controller = array();
                foreach ($menus as $menu)
                {
                    $id_menu = $menu->id_menu;
                    $controller[$id_menu] = array_filter($controller_base, function($controller) use ($id_menu)
                    {
                        return $controller->id_menu == $id_menu;
                    });
                }
                $data = array(
                    'controller'=>$controller,
                    'menu'=>$menus,
                    "row"=>$row,
                    "roles"=>$roles,
                );
                $extras = array(
                    'css' => array(
                    ),
                    'js' => array(
                        "js/scripts/roles.js"
                    ),
                );
                layout("config/editar_rol",$data,$extras);
            }else{
                redirect('errorpage');
            }
        }
        else if($this->input->method(TRUE) == "POST"){
            $descripcion = strtoupper($this->input->post("descripcion"));
            $nombre = strtoupper($this->input->post("nombre"));
            $id_rol = strtoupper($this->input->post("id_rol"));
            $modules = $this->input->post("modules");
            $where = $this->pk."='".$id_rol."'";
            $data = array(
                "descripcion"=>$descripcion,
                "nombre"=>$nombre,
            );
            $this->utils->begin();
            $insert = $this->utils->update($this->table,$data,$where);
            if($insert){

                //Verificacion si los modulos vienen vacios
                if(count($modules)>0){
                    $wherep = " id_rol='".$id_rol."'";
                    $delete = $this->utils->delete("roles_detalle",$wherep);
                    if($delete){
                        foreach ($modules as $mod){

                            $form_data = array(
                                "id_rol"=>$id_rol,
                                "id_modulo"=>$mod["module"],
                            );
                            $this->utils->insert("roles_detalle",$form_data);
                        }
                        $this->utils->commit();
                        $xdatos["type"]="success";
                        $xdatos['title']='Información';
                        $xdatos["msg"]="Registo editado correctamente!";
                    }else{
                        $xdatos["type"]="error";
                        $xdatos['title']='Alerta';
                        $xdatos["msg"]="Error al editar el registro!";
                    }
                }else{
                    $this->utils->commit();
                    $xdatos["type"]="success";
                    $xdatos['title']='Información';
                    $xdatos["msg"]="Registo editado correctamente!";
                }
            }
            else {
                $this->utils->rollback();
                $xdatos["type"]="error";
                $xdatos['title']='Alerta';
                $xdatos["msg"]="Error al editar el registro!";
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
            $active = $this->roles->get_state($id);
            $response = change_state($this->table,$this->pk,$id,$active);
            echo json_encode($response);
        }
    }

}

/* End of file Productos.php */
