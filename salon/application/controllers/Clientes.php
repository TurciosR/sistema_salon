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

class Clientes extends yidas\rest\Controller {

	private $table = "clientes";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->model("Clientes_model", "clientes");
		//permissions_user($this,"clientes");
	}

	public function index()
	{
		$data = array(
			"titulo"=> "Clientes",
			"icono"=> "mdi mdi-account-group",
			"buttons" =>array(
				0 => array(
					"icon"=> "mdi mdi-plus",
					'url' => 'clientes/agregar',
					'txt' => ' Agregar cliente',
					'modal' => false,
				),
			),
			"table"=>array(
				"No."=>1,
				"Nombre"=>2,
				"Dirección"=>2,
				"NRC"=>2,
				"Teléfonos"=>2,
				"Estado"=>2,
				"Acciones"=>1,
			),
		);
		$extras = array(
			'css' => array(),
			'js'  => array(
				"js/scripts/clientes.js"
			),
		);
		layout("template/admin",$data,$extras);
	}
	function get_data()
	{
		$valid_columns = array(
			0 => 'id_cliente',
			1 => 'nombre_comercial',
			2 => 'direccion',
			3 => 'nrc',
			4 => 'telefono1',
			5 => 'telefono2',

		);
		// Create query based on mariadb tables required
		$query_val  = $this->clientes->create_dt_query();
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
				$filename  = base_url("clientes/editar/");
				$menudrop .= "<li><a role='button' href='" . $filename . $rows->id_cliente;
				$menudrop .= "' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";
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
				$menudrop .= "<li><a class='state_change' data-state='$txt'  id=" . $rows->id_cliente;
				$menudrop .= " ><i class='$icon'></i> $txt</a></li>";
				$menudrop .= "<li><a class='delete_row'  id=" . $rows->id_cliente;
				$menudrop .= " ><i class='mdi mdi-trash-can'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";

				$data[] = array(
					$rows->id_cliente,
					$rows->nombre_comercial,
					$rows->direccion,
					$rows->nrc,
					$rows->telefono1." | ".$rows->telefono2,
					$show_text,
					$menudrop,
				);
			}
			$total  = generate_dt("UtilsModel", $options_dt, TRUE);
			$output = array(
				"draw"            => $draw,
				"recordsTotal"    => $total,
				"recordsFiltered" => $total,
				"data"            => $data
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
				"data"            => $data
			);
		}
		echo json_encode($output);
		exit();
	}
	function agregar(){
		if($this->input->method(TRUE) == "GET"){
			$departamentos = $this->clientes->get_departamentos();
			$giro = $this->clientes->get_giro();
			$categoria_cliente = $this->clientes->get_categoria_cliente();
			$tipo_cliente = $this->clientes->get_tipo_cliente();
			$clasifica_cliente = $this->clientes->get_clasifica_cliente();
			$tipo_doc = $this->clientes->get_tipo_doc();
			$porcentajes = $this->clientes->get_porcentajes();
		   $data = array(
				"departamentos"=>$departamentos,
				"giro"=>$giro,
				"categoria_cliente"=>$categoria_cliente,
				"tipo_cliente"=>$tipo_cliente,
				 "clasifica_cliente"=>$clasifica_cliente,
				"tipo_doc"=>$tipo_doc,
				"porcentajes"=>$porcentajes,
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"js/scripts/clientes.js"
				),
			);
			layout("clientes/agregar_cliente",$data,$extras);
		}
		else if($this->input->method(TRUE) == "POST"){
			//$nombre = $this->input->post("nombre");
			$nombre_comercial = $this->input->post("nombre_comercial");
			$direccion = $this->input->post("direccion");
			$departamento = $this->input->post("departamento");
			$municipio = $this->input->post("municipio");
			$dui = $this->input->post("dui");
			$nit = $this->input->post("nit");
			$nrc = $this->input->post("nrc");
			$giro = $this->input->post("giro");
			//$categoria = $this->input->post("categoria");
			$tipo = $this->input->post("tipo");
			$clasifica = $this->input->post("clasifica");
			$telefono1 = $this->input->post("telefono1");
			$telefono2 = $this->input->post("telefono2");
			$fax = $this->input->post("fax");
			$correo = $this->input->post("correo");
			$descuento = $this->input->post("descuento");
			//$dias_credito = $this->input->post("dias_credito");
			//$tipo_documento = $this->input->post("tipo_documento");
			$contacto = $this->input->post("contacto");
			$contacto_telefono = $this->input->post("contacto_telefono");
			$contacto_correo = $this->input->post("contacto_correo");
			$observaciones = $this->input->post("observaciones");

			$form = array(
				"nombre"=>$nombre_comercial,
				"email"=>$correo,
				"nombre_comercial"=>$nombre_comercial,
				"direccion"=>$direccion,
				"departamento"=>$departamento,
				"municipio"=>$municipio,
				"dui"=>$dui,
				"nit"=>$nit,
				"nrc"=>$nrc,
				"giro"=>$giro,
				"categoria"=>"",
				"tipo"=>$tipo,
				 "clasifica"=>$clasifica,
				"telefono1"=>$telefono1,
				"telefono2"=>$telefono2,
				"fax"=>$fax,
				"descuento"=>$descuento,
				"dias_credito"=>0,
				"tipo_documento"=>"",
				"contacto"=>$contacto,
				"contacto_telefono"=>$contacto_telefono,
				"contacto_correo"=>$contacto_correo,
				"observaciones"=>$observaciones,
				"activo"=>1,
			);
			$this->utils->begin();
			$insert = $this->utils->insert($this->table,$form);

			if($insert){
				$this->utils->commit();
				$xdatos["type"]="success";
				$xdatos['title']='Exito';
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
		$municipios = $this->clientes->get_municipio($id_departamento);
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
			$row = $this->clientes->get_row_info_editar($id);
			if($row && $id!=""){
				$departamentos = $this->clientes->get_departamentos();
				$giro = $this->clientes->get_giro();
				$categoria_cliente = $this->clientes->get_categoria_cliente();
				$tipo_cliente = $this->clientes->get_tipo_cliente();
				$clasifica_cliente = $this->clientes->get_clasifica_cliente();
				$tipo_doc = $this->clientes->get_tipo_doc();
				$porcentajes = $this->clientes->get_porcentajes();
				$municipios = $this->clientes->get_municipio($row->departamento);
				$data = array(
					"row"=>$row,
					"departamentos"=>$departamentos,
					"municipios"=>$municipios,
					"giro"=>$giro,
					"categoria_cliente"=>$categoria_cliente,
					"tipo_cliente"=>$tipo_cliente,
					"clasifica_cliente"=>$clasifica_cliente,
					"tipo_doc"=>$tipo_doc,
					"porcentajes"=>$porcentajes,
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"js/scripts/clientes.js"
					),
				);
				layout("clientes/editar_cliente",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){

			$id_cliente = $this->input->post("id_cliente");
			$nombre = $this->input->post("nombre");
			$nombre_comercial = $this->input->post("nombre_comercial");
			$direccion = $this->input->post("direccion");
			$departamento = $this->input->post("departamento");
			$municipio = $this->input->post("municipio");
			$dui = $this->input->post("dui");
			$nit = $this->input->post("nit");
			$nrc = $this->input->post("nrc");
			$giro = $this->input->post("giro");
			$categoria = $this->input->post("categoria");
			$tipo = $this->input->post("tipo");
			$clasifica = $this->input->post("clasifica");
			$telefono1 = $this->input->post("telefono1");
			$telefono2 = $this->input->post("telefono2");
			$fax = $this->input->post("fax");
			$correo = $this->input->post("correo");
			$descuento = $this->input->post("descuento");
			$dias_credito = $this->input->post("dias_credito");
			$tipo_documento = $this->input->post("tipo_documento");
			$contacto = $this->input->post("contacto");
			$contacto_telefono = $this->input->post("contacto_telefono");
			$contacto_correo = $this->input->post("contacto_correo");
			$observaciones = $this->input->post("observaciones");

			$form = array(
				"nombre"=>$nombre_comercial,
				"email"=>$correo,
				"nombre_comercial"=>$nombre_comercial,
				"direccion"=>$direccion,
				"departamento"=>$departamento,
				"municipio"=>$municipio,
				"dui"=>$dui,
				"nit"=>$nit,
				"nrc"=>$nrc,
				"giro"=>$giro,
				"categoria"=>$categoria,
				"tipo"=>$tipo,
				 "clasifica"=>$clasifica,
				"telefono1"=>$telefono1,
				"telefono2"=>$telefono2,
				"fax"=>$fax,
				"descuento"=>$descuento,
				"dias_credito"=>$dias_credito,
				"tipo_documento"=>$tipo_documento,
				"contacto"=>$contacto,
				"contacto_telefono"=>$contacto_telefono,
				"contacto_correo"=>$contacto_correo,
				"observaciones"=>$observaciones,
			);

			$where = " id_cliente='".$id_cliente."'";
			$this->utils->begin();
			$update = $this->utils->update($this->table,$form,$where);

			if($update){
				$this->utils->commit();
				$xdatos["type"]="success";
				$xdatos['title']='Exito';
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
			$form = array(
				"activo" =>0,
				"deleted" =>1,
			);
			$where = " id_cliente ='".$id."'";
			$this->utils->begin();
			$delete = $this->utils->update($this->table,$form,$where);
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
		//permissions_user($this,"productos");

		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$active = $this->clientes->get_state($id);
			if($active==0){
				$state = 1;
				$text = 'activado';
			}else{
				$state = 0;
				$text = 'desactivado';
			}
			$form = array(
				"activo" =>$state,
			);
			$where = " id_cliente ='".$id."'";
			$this->utils->begin();
			$update = $this->utils->update("clientes",$form,$where);
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
			//exit();
		}
	}
	/*=================API=================*/
	public function login(){
		$data = $this->request->getBodyParams();
		$result = $this->clientes->auth($data);
		if($result['success'])
		{
			unset($result['success']);
			$result['token'] = Authorization::generateToken($result['user_id']);
			unset($result['user_id']);
			$res = $this->pack($result, OK, "Login Successfully");
			return $this->response->json($res, OK);
		}

		$res = $this->pack($this->request->getBodyParams(), NOT_FOUND, "Login Fail");
		return $this->response->json($res, OK);
	}
	public function signup(){
		$data = $this->request->getBodyParams();
		$valid_fields = array('nombre','email','telefono','password');
		allowed_to_use($valid_fields,$data);
		$result = $this->clientes->save($data);

		if($result > 0){
			return $this->response->json($this->pack(null, OK, "Register Successfully"));
		}
		else if($result == -1)
		{
			return $this->response->json($this->pack(null, OK, "Email all ready exist"));
		}
		return $this->response->json($this->pack(null, NO_CONTENT, "Register Fail"));
	}
	public function direcciones(){
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$data = $this->request->getBodyParams();
		$valid_fields = array('id_cliente','tipo','direccion','referencia','predefinida','id_departamento','id_municipio','longitud','latitud');
		$data["id_cliente"] = $result['data'];
		allowed_to_use($valid_fields,$data);
		$result = $this->clientes->save_dir($data);

		if($result > 0){
			return $this->response->json($this->pack($result, OK, "Register Successfully"));
		}
		else if($result == -1)
		{
			return $this->response->json($this->pack(null, OK, "Address all ready exist"));
		}

		return $this->response->json($this->pack(null, NO_CONTENT, "Register Fail"));
	}
	public function getdirecciones(){
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$data["id_cliente"] = $result['data'];

		$result = $this->clientes->getDireccion($data);

		if($result > 0)
		{
			$dir_completa = array();
			foreach ($result as $item)
			{
					$id_departamento = $item["id_departamento"];
					$id_municipio = $item["id_municipio"];
					$depto = $this->clientes->getDepto($id_departamento);
					$mun = $this->clientes->getMun($id_municipio);
					if($depto != null)
					{
						$item["departamento"] = array('id' => $id_departamento, 'nombre' => $depto->nombre);
					}
					if($mun != null)
					{
						$item["municipio"] = array('id' => $id_municipio, 'nombre' => $mun->nombre);
					}
					unset($item["id_departamento"]);
					unset($item["id_municipio"]);
					array_push($dir_completa,$item);
			}
			return $this->response->json($this->pack($dir_completa, OK, "Direcciones"));
		}
		else {
			return $this->response->json($this->pack(null, NOT_FOUND, "Direcciones"));
		}

	}
	public function direccionesUpd()
	{
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$data = $this->request->getBodyParams();
		$valid_fields = array('id_cliente','tipo','direccion','referencia','predefinida','id_departamento','id_municipio','longitud','latitud');
		$data["id_cliente"] = $result['data'];
		$id_dir = $data["id"];
		allowed_to_use($valid_fields,$data);

		$result = $this->clientes->update_dir($id_dir,$data);

		if($result > 0){
			return $this->response->json($this->pack($result, OK, "Update Successfully"));
		}
		else if($result == -1)
		{
			return $this->response->json($this->pack(null, OK, "Address all ready exist"));
		}

		return $this->response->json($this->pack(null, NO_CONTENT, "Update Fail"));
	}
	public function update()
	{
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$data = $this->request->getBodyParams();
		$id_cliente = $result['data'];
		$valid_fields = array('nombre','email','telefono','password');
		allowed_to_use($valid_fields,$data);
		$result = $this->clientes->update($id_cliente,$data);

		if($result > 0){
			return $this->response->json($this->pack(null, OK, "Update Successfully"));
		}
		else if($result == -1)
		{
			return $this->response->json($this->pack(null, OK, "Email all ready exist"));
		}
		return $this->response->json($this->pack(null, NO_CONTENT, "Update Fail"));
	}
	public function updatePwd()
	{
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$data = $this->request->getBodyParams();
		$id_cliente = $result['data'];
		$valid_fields = array('password');
		allowed_to_use($valid_fields,$data);
		$result = $this->clientes->updatePwd($id_cliente,$data);

		if($result > 0)
		{
			$token = str_replace("Bearer ","",$this->input->get_request_header("Authorization"));
			$banned_arr = array("token" => $token);
			$banned = $this->clientes->blacklist($banned_arr);
			$responose["new_token"] = Authorization::generateToken($id_cliente);
			return $this->response->json($this->pack($responose, OK, "Update Successfully"));
		}

		return $this->response->json($this->pack(null, NO_CONTENT, "Update Fail"));
	}
	public function verifyToken(){
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$token = str_replace("Bearer ","",$this->input->get_request_header("Authorization"));
		$black = $this->clientes->blacklistToken($token);
		if($black >0)
		{
			$res = $this->pack(null, OK, "Banned Token");
			return $this->response->json($res, OK);
		}
		$result = $this->clientes->get($result['data']);
		unset($result->id_user);
		unset($result->password);
		parse($result);
		$res = $this->pack($result, OK, "Valid Token");
		return $this->response->json($res, OK);
	}
	public function departamentos(){
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$result = $this->clientes->get_departamentos();
		if($result != null)
		{
			parse($result);
		}
		$res = $this->pack($result, OK, "Departamentos");
		return $this->response->json($res, OK);
	}
	public function municipios($resourceID =-1){
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		if (is_numeric($resourceID))
		{
			$result = $this->clientes->get_municipios();
			if($result != null)
			{
				parse($result);
			}
		}
		else {
			$result = $this->clientes->get_municipios($resourceID);
			if($result != null)
			{
				parse($result);
			}
		}
			$res = $this->pack($result, OK, "Municipios");
		return $this->response->json($res, OK);
	}

}
