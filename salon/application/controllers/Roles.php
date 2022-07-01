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

class Roles extends CI_Controller {

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
		"titulo"  => " Roles",
		"icono"   => "mdi mdi-account-key",
		"buttons" => array(
			0 => array(
				"icon"  => "mdi mdi-plus",
				'url'   => $this->file.'/agregar',
				'txt'   => 'Agregar rol',
				'modal' => false,
			),
		),
		"table" => array(
			"ID"          => 1,
			"Nombre"      => 4,
			"Descripción" => 4,
			"Estado"      => 2,
			"Acciones"    => 1,
		),
	);
	$extras = array(
			'css' => array(
		),
			'js'  => array(
			"js/scripts/roles.js",
		),
	);
	layout("template/admin", $data, $extras);
	}

	function get_data()
	{
		$valid_columns = array(
			0 => 'id_rol',
			1 => 'nombre',
			2 => 'descripcion',
		);

		// Create query based on mariadb tables required
		$query_val  = $this->roles->create_dt_query();

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
				$menudrop  = "<div class='btn-group'><button data-toggle='dropdown'";
				$menudrop .= " class='btn btn-success dropdown-toggle' aria-expanded='false'>";
				$menudrop .= "<i class='mdi mdi-menu' aria-haspopup='false'></i> Menú</button>";
				$menudrop .= "<ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";
				$filename  = base_url($this->file."/editar/".$rows->id_rol);
				$menudrop .= "<li><a role='button' href='" . $filename. "' >";
				$menudrop .= "<i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";
				$state     = $rows->activo;
				if ($state == 1) {
					$txt       = "Desactivar";
					$show_text = "<span class='badge badge-success font-bold'>Activo<span>";
					$icon      = "mdi mdi-toggle-switch-off";
				} else {
					$txt       = "Activar";
					$show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$icon      = "mdi mdi-toggle-switch";
				}
				$menudrop .= "<li><a  class='state_change' data-state='$txt'  id=" . $rows->id_rol;
				$menudrop .= " ><i class='$icon'></i> $txt</a></li>";
				$menudrop .= "<li><a  class='delete_row'  id=" . $rows->id_rol;
				$menudrop .= " ><i class='mdi mdi-trash-can-outline'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";
				$data[]    = array(
					$rows->id_rol,
					$rows->nombre,
					$rows->descripcion,
					$show_text,
					$menudrop,
				);
			}
			$total  = generate_dt("UtilsModel", $options_dt, TRUE);
			$output = array(
				"draw"            => $draw,
				"recordsTotal"    => $total,
				"recordsFiltered" => $total,
				"data"            => $data,
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
				"draw"            => $draw,
				"recordsTotal"    => 0,
				"recordsFiltered" => 0,
				"data"            => $data,
			);
		}
		echo json_encode($output);
	}

	function agregar()
	{
		if ($this->input->method(TRUE) == "GET") {
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
				'js'  => array(
					"js/scripts/roles.js",
				),
			);
			layout("config/agregar_rol",$data,$extras);
		} else if ($this->input->method(TRUE) == "POST") {

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
			if ($id_rol != NULL) {
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
			} else {
				$this->utils->rollback();
				$xdatos["type"]="error";
				$xdatos['title']='Alerta';
				$xdatos["msg"]="Error al ingresar el registro!";
			}
			echo json_encode($xdatos);
		}
	}

	function editar($id=-1)
	{
	if ($this->input->method(TRUE) == "GET") {
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
	} else if ($this->input->method(TRUE) == "POST") {
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
		if ($insert) {

			//Verificacion si los modulos vienen vacios
			if(count($modules)>0){
				$wherep = " id_rol='".$id_rol."'";
				$delete = $this->utils->delete("roles_detalle",$wherep);
				if ($delete) {
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
				} else {
					$xdatos["type"]="error";
					$xdatos['title']='Alerta';
					$xdatos["msg"]="Error al editar el registro!";
				}
			} else {
				$this->utils->commit();
				$xdatos["type"]="success";
				$xdatos['title']='Información';
				$xdatos["msg"]="Registo editado correctamente!";
			}
		} else {
			$this->utils->rollback();
			$xdatos["type"]="error";
			$xdatos['title']='Alerta';
			$xdatos["msg"]="Error al editar el registro!";
		}
		echo json_encode($xdatos);
		}
	}

	function delete()
	{
		if ($this->input->method(TRUE) == "POST") {
			$id = $this->input->post("id");
			$response = safe_delete($this->table,$this->pk,$id);
			echo json_encode($response);
		}
	}

	function state_change()
	{
		if ($this->input->method(TRUE) == "POST") {
			$id = $this->input->post("id");
			$active = $this->roles->get_state($id);
			$response = change_state($this->table,$this->pk,$id,$active);
			echo json_encode($response);
		}
	}

}

/* End of file Productos.php */
