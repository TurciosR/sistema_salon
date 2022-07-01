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

class Usuarios extends CI_Controller {

	/*
	Global table name
	*/
	private $table = "usuario";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->Model("UsuariosModel","usuarios");
		//permissions_user($this,"usuarios");
	}

	public function index()
	{
		$data = array(
			"titulo"=> "Usuarios",
			"icono"=> "mdi mdi-account-group",
			"buttons" => array(
				0 => array(
					"icon"=> "mdi mdi-plus",
					'url' => 'usuarios/agregar',
					'txt' => 'Agregar usuario',
					'modal' => false,
				),
			),
			"table"=>array(
				"ID"=>1,
				"Usuario"=>2,
				"Nombre"=>2,
				"Tipo de Usuario"=>2,
				"Estado"=>2,
				"Rol"=>2,
				"Acciones"=>1,
			),
		);
		$extras = array(
			'css' => array(),
			'js' => array(
				"js/scripts/usuarios.js"
			),
		);
		layout("template/admin",$data,$extras);
	}

	function get_data()
	{
		$valid_columns = array(
			0 => 'id_usuario',
			1 => 'nombre',
			2 => 'usuario',
		);
		// Create query based on mariadb tables required
		$query_val  = $this->usuarios->create_dt_query();
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
				$filename  = base_url("usuarios/editar/");
				$menudrop .= "<li><a role='button' href='" . $filename . $rows->id_usuario;
				$menudrop .= "' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";
				$filename  = base_url("usuarios/permisos/");
				$menudrop .= "<li><a role='button' href='" . $filename . $rows->id_usuario;
				$menudrop .= "' ><i class='mdi mdi-database-lock' ></i> Permisos</a></li>";
				$state     = $rows->activo;
				if ($state == 1) {
					$txt       = "Desactivar";
					$show_text = "<span class='badge badge-primary font-bold'>Activo<span>";
					$icon      = "mdi mdi-toggle-switch-off";
				} else {
					$txt       = "Activar";
					$show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$icon      = "mdi mdi-toggle-switch";
				}

				if ($rows->admin == 1) {
					$type_user = "<span class='badge badge-warning font-bold'>Administrador<span>";
				} else {
					$type_user = "<span class='badge badge-info font-bold'>Normal<span>";
				}

				$menudrop .= "<li><a class='state_change' data-state='$txt'  id=" . $rows->id_usuario;
				$menudrop .= " ><i class='$icon'></i> $txt</a></li>";
				$menudrop .= "<li><a class='delete_row'  id=" . $rows->id_usuario;
				$menudrop .= " ><i class='mdi mdi-trash-can-outline'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";
				$rol       = "";
				if ($rows->id_rol > 0) {
					$roles = $this->utils->getRol($rows->id_rol);
					$rol   = $roles->nombre;
				}

				$data[] = array(
					$rows->id_usuario,
					$rows->nombre,
					$rows->usuario,
					$type_user,
					$show_text,
					$rol,
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
		exit();
	}

	function agregar(){
		if($this->input->method(TRUE) == "GET"){

			$extras = array(
				'css' => array(
				),
				'js' => array(
					"js/scripts/usuarios.js"
				),
			);
			$sucursales = $this->utils->get_sucursales();
			$roles = $this->utils->get_roles();
			$data = array(
				'sucursales' => $sucursales,
				'roles' => $roles
			);
			layout("usuarios/agregar_usuario",$data,$extras);
		}
		else if($this->input->method(TRUE) == "POST"){
			$nombre = $this->input->post("nombre");
			$usuario = $this->input->post("usuario");
			$password = $this->input->post("password");
			$pass = encrypt($password);
			$admin = $this->input->post("tipo_usuario");
			$rol = $this->input->post("rol");
			$sucursal = $this->input->post("sucursal");
			$existe = $this->usuarios->exits_row($usuario);
				$tablep = "permisos_usuario";
			if($existe==0){
				$data = array(
					"nombre"=>$nombre,
					"usuario"=>$usuario,
					"admin"=>$admin,
					"password"=>$pass,
					"activo"=>1,
					"id_rol"=>$rol,
					"id_sucursal"=>$sucursal,
				);
				$this->utils->begin();
				$insert = $this->utils->insert($this->table,$data);
				$id_usuario=$this->utils->insert_id();
				if($insert)
				{
					if($rol>0)
					{
						$roles = $this->utils->get_roles_detalle($rol);

						foreach($roles as $rold)
						{
							$form_data = array(
								"id_usuario"=>$id_usuario,
								"id_modulo"=>$rold->id_modulo,
							);
							$insert1 = $this->utils->insert($tablep,$form_data);
						}

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
					$xdatos["msg"]="Error al ingresar el registro";
				}
			}
			else{
				$xdatos["type"]="error";
				$xdatos['title']='Erro';
				$xdatos["msg"]="Ya existe un registro con el mismo nombre de usuario!";
			}
			echo json_encode($xdatos);
		}
	}

	function editar($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$sucursales = $this->utils->get_sucursales();
			$roles = $this->utils->get_roles();
			$row = $this->usuarios->get_row_info($id);
			if($row && $id!=""){
				$password = decrypt($row->password);
				$data = array(
					"row"=>$row,
					"password"=>$password,
					"sucursales" => $sucursales,
					"roles" => $roles
				);

				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/usuarios.js"
					),
				);
				layout("usuarios/editar_usuario",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$nombre = $this->input->post("nombre");
			$usuario = $this->input->post("usuario");
			$password = $this->input->post("password");
			$pass = encrypt($password,"eNcRiPt_K3Y");
			$admin = $this->input->post("tipo_usuario");
			$id_usuario = $this->input->post("id_usuario");
			$rol = $this->input->post("rol");
			$sucursal = $this->input->post("sucursal");
			$where = " id_usuario='".$id_usuario."'";
			$existe = $this->usuarios->exits_row_edit($usuario,$id_usuario);

			if($existe==0){
				$data = array(
					"nombre"=>$nombre,
					"usuario"=>$usuario,
					"admin"=>$admin,
					"password"=>$pass,
					"id_rol"=>$rol,
					"id_sucursal"=>$sucursal,
				);
				$this->utils->begin();
				$insert = $this->utils->update($this->table,$data,$where);
				if($insert)
				{
					if($rol >0)
					{
						$tablep = "permisos_usuario";
						$wherep = "id_usuario='".$id_usuario."'";
						$delete = $this->utils->delete($tablep,$wherep);
						if($delete)
						{
							$roles = $this->utils->get_roles_detalle($rol);

							foreach($roles as $rold)
							{
								$form_data = array(
									"id_usuario"=>$id_usuario,
									"id_modulo"=>$rold->id_modulo,
								);
								$insert1 = $this->utils->insert($tablep,$form_data);
							}
						}
					}
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
			}
			else{
				$xdatos["type"]="error";
				$xdatos['title']='Erro';
				$xdatos["msg"]="Ya existe un registro con el mismo nombre de usuario!";
			}
			echo json_encode($xdatos);
		}
	}

	function delete(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$where = " id_usuario ='".$id."'";
			$this->utils->begin();
			$delete = $this->utils->delete($this->table,$where);
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

	function state_change(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$active = $this->usuarios->get_state($id);
			if($active==0){
				$state = 1;
				$text = 'activado';
			}else{
				$state = 0;
				$text = 'desactivado';
			}
			$form = array(
				"activo" =>$state
			);
			$where = " id_usuario ='".$id."'";
			$this->utils->begin();
			$update = $this->utils->update($this->table,$form,$where);
			if($update) {
				$this->utils->commit();
				$data["type"] = "success";
				$data["title"] = "Información";
				$data["msg"] = "Registro $text con exito!";
			}
			else {
				$this->utils->rollback();
				$data["type"] = "Error";
				$data["title"] = "Alerta!";
				$data["msg"] = "Registro no pudo ser $text!";
			}
			echo json_encode($data);
			exit();
		}
	}

	function permisos(){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->usuarios->get_row_info($id);
			if($row && $id!=""){

				$permissions = $this->usuarios->get_permissions($id);
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
					'row'=>$row,
					'controller'=>$controller,
					'menu'=>$menus,
					'permissions_user'=>$permissions,
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"js/scripts/usuarios.js"
					),
				);

				layout("usuarios/permisos",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$id_usuario = $this->input->post("id_usuario");
			$module = $this->input->post("modules");
			$modules = explode(",",$module);
			$admin = intval($this->input->post("admin"));

			$this->utils->begin();
			if($admin==1)
			{
				$table  = "usuario";
				$form = array("admin"=>1);
				$where = " id_usuario ='".$id_usuario."'";
				$update = $this->utils->update($table,$form,$where);
				if($update){
					$this->utils->commit();
					$data['type'] = 'success';
					$data['title'] = 'Éxito';
					$data['msg'] = 'Permisos asignados exitosamente!';
				}else {
					$this->utils->rollback();
					$data['type'] = 'error';
					$data['title'] = 'Error';
					$data['msg'] = 'No se pudo guardar los permisos!';
				}
			}
			else{
				$table  = "usuario";
				$form = array("admin"=>0);
				$where = " id_usuario ='".$id_usuario."'";
				$update = $this->utils->update($table,$form,$where);
				if($update){
					$tablep = "permisos_usuario";
					$wherep = " id_usuario='".$id_usuario."'";
					$delete = $this->utils->delete($tablep,$wherep);
					if($delete){
						for ($i=0;$i<count($modules);$i++){
							$form_data = array(
								"id_usuario"=>$id_usuario,
								"id_modulo"=>$modules[$i],
							);
							$insert = $this->utils->insert($tablep,$form_data);
						}
						if($insert){
							$this->utils->commit();
							$data['type'] = 'success';
							$data['title'] = 'Éxito';
							$data['msg'] = 'Permisos asignados exitosamente!';
						}
					}else{
						$this->utils->rollback();
						$data['type'] = 'error';
						$data['title'] = 'Error';
						$data['msg'] = 'No se pudo guardar los permisos!';
					}
				}else {
					$this->utils->rollback();
					$data['type'] = 'error';
					$data['title'] = 'Error';
					$data['msg'] = 'No se pudo guardar los permisos!';
				}
			}
			echo json_encode($data);
		}
	}

}

/* End of file Usuarios.php */
