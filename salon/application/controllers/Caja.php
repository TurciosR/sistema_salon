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

class Caja extends CI_Controller {

	/*
	Enviroment variables
	*/
	private $table = "caja";
	private $pk = "id_caja";

	function __construct()
	{
		parent::__construct();
		$this->load->Model("CajaModel","caja");
		$this->load->model('UtilsModel',"utils");
	}

	public function index()
	{
		$id_usuario=$this->session->id_usuario;
		$id_sucursal=$this->session->id_sucursal;
		$usuario_tipo =	$this->utils->get_one_row("usuario", array('id_usuario' => $id_usuario,));
		 if($usuario_tipo!=NULL){
				if($usuario_tipo->admin==1 || $usuario_tipo->super_admin==1){
						$sucursales=$this->utils->get_detail_rows("sucursales",array('1' => 1, ));
				}else {
						$sucursales=$this->utils->get_detail_rows("sucursales", array('id_sucursal' => $id_sucursal,));
				}
		 }else {
				$sucursales=$this->utils->get_detail_rows("sucursales",array('1' => 1, ));
		 }
		$data = array(
			"titulo"=> "Caja",
			"icono"=> "mdi mdi-format-list-bulleted",
			"buttons" => array(
				0 => array(
					"icon"=> "mdi mdi-plus",
					'url' => 'caja/agregar',
					'txt' => 'Agregar caja',
					'modal' => false,
				),
			),
			"selects" => array(
				0 => array(
					"name" => "sucursales",
					"data" => $sucursales,
					"id" => "id_sucursal",
					"text" => array(
						"nombre",
						"direccion",
					),
					"separator" => " ",
					"selected" => $this->session->id_sucursal,
				),
			),
			"table"=>array(
				"ID"=>1,
				"Nombre"=>2,
				"Serie"=>5,
				"Desde"=>5,
				"Hasta"=>5,
				"Corr. Disp"=>3,
				"Resolucion"=>3,

				"Fecha"=>4,
				"Estado"=>10,
				"Acciones"=>6,
			),
		);

		$extras = array(
			'css' => array(
			),
			'js' => array(
				"js/scripts/caja.js",
			),
		);

		layout("template/admin",$data,$extras);
	}

	function get_data()
	{
		$valid_columns = array(
			0 => 'c.id_caja',
			1 => 'c.nombre',
			2 => 'c.serie',
			3 => 'c.desde',
			4 => 'c.hasta',
			5 => 'c.resolucion',
			6 => 'c.fecha',
		);
		// Create query based on mariadb tables required
		$query_val  = $this->caja->create_dt_query();
		$where  = array(
			'c.id_sucursal' => $this->input->post("id_sucursal"),
		);
		/* You can pass where and join clauses as necessary or include it on model
		 * function as necessary. If no join includ it set to NULL.
		 */
		$options_dt = array(
				'valid_columns' => $valid_columns,
				'where'         => $where
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
				$filename = base_url("caja/editar/");
				$menudrop .= "<li><a role='button' href='" . $filename.$rows->id_caja. "' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";
				$state = $rows->activa;
				if ($state == 1) {
					$txt = "Desactivar";
					$show_text = "<span class='badge badge-success font-bold'>Activo<span>";
					$icon = "mdi mdi-toggle-switch-off";
				} else {
					$txt = "Activar";
					$show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$icon = "mdi mdi-toggle-switch";
				}
				$menudrop .= "<li><a  class='state_change' data-state='$txt'  id=" . $rows->id_caja . " ><i class='$icon'></i> $txt</a></li>";
				$menudrop .= "</ul></div>";

				//id_caja, nombre, serie, desde, hasta, correlativo_dispo, resolucion, fecha, id_sucursal, activa
				$data[] = array(
					$rows->id_caja,
					$rows->nombre,
					$rows->serie,
					$rows->desde,
					$rows->hasta,
					$rows->correlativo_dispo,
					$rows->resolucion,
					$rows->fecha,
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
				"",
				"No se encontraron registros",
				"",
				"",
				"",
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
			$id_usuario=$this->session->id_usuario;
			$id_sucursal=$this->session->id_sucursal;
			$usuario_tipo =	$this->utils->get_one_row("usuario", array('id_usuario' => $id_usuario,));
			 if($usuario_tipo!=NULL){
					if($usuario_tipo->admin==1 || $usuario_tipo->super_admin==1){
							$sucursales=$this->utils->get_detail_rows("sucursales",array('1' => 1, ));
					}else {
							$sucursales=$this->utils->get_detail_rows("sucursales", array('id_sucursal' => $id_sucursal,));
					}
			 }else {
					$sucursales=$this->utils->get_detail_rows("sucursales",array('1' => 1, ));
			 }
			$data = array(
					"sucursales"=>$sucursales,
			);
			$extras = array(
				'css' => array(
				),
				'js' => array(
					"js/scripts/caja.js",
				),
			);
			layout("caja/agregar_caja",$data,$extras);
		}
		else if($this->input->method(TRUE) == "POST"){
		//id_caja, nombre, serie, desde,
			$nombre = strtoupper($this->input->post("nombre"));
			$serie = strtoupper($this->input->post("serie"));
 			$desde = strtoupper($this->input->post("hasta"));
			$hasta = strtoupper($this->input->post("hasta"));
			$correlativo_dispo= strtoupper($this->input->post("correlativo_dispo"));
			$resolucion = strtoupper($this->input->post("resolucion"));
			$fecha = Y_m_d($this->input->post("fecha"));
			$id_sucursal=$this->input->post("sucursal");

			$data = array(
				"nombre"=>$nombre,
				"serie"=>$serie,
				"desde"=>$desde,
								"hasta"=>$hasta,
								"correlativo_dispo"=>$correlativo_dispo,
								"fecha"=>$fecha,
								"resolucion"=>$resolucion,
								"id_sucursal"=>$id_sucursal,
				"activa"=>1,
			);
			$response = insert_row($this->table,$data);
			echo json_encode($response);
		}
	}

	function editar($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->caja->get_row_info($id);
			$id_usuario=$this->session->id_usuario;
			$id_sucursal=$this->session->id_sucursal;
			$usuario_tipo =	$this->utils->get_one_row("usuario", array('id_usuario' => $id_usuario,));
			 if($usuario_tipo!=NULL){
					if($usuario_tipo->admin==1 || $usuario_tipo->super_admin==1){
							$sucursales=$this->utils->get_detail_rows("sucursales",array('1' => 1, ));
					}else {
							$sucursales=$this->utils->get_detail_rows("sucursales", array('id_sucursal' => $id_sucursal,));
					}
			 }else {
					$sucursales=$this->utils->get_detail_rows("sucursales",array('1' => 1, ));
			 }
			if($row && $id!=""){
				$data = array(
					"row"=>$row,
					"sucursales"=>$sucursales,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/caja.js"
					),
				);
				layout("caja/editar_caja",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$id_caja = $this->input->post("id_caja");
			$nombre = strtoupper($this->input->post("nombre"));
			$serie = strtoupper($this->input->post("serie"));
 			$desde = strtoupper($this->input->post("desde"));
			$hasta = strtoupper($this->input->post("hasta"));
			$correlativo_dispo= strtoupper($this->input->post("correlativo_dispo"));
			$resolucion = strtoupper($this->input->post("resolucion"));
			$fecha = Y_m_d($this->input->post("fecha"));
			$id_sucursal=$this->input->post("sucursal");

	$data = array(
		"nombre"=>$nombre,
		"serie"=>$serie,
		"desde"=>$desde,
					"hasta"=>$hasta,
					"correlativo_dispo"=>$correlativo_dispo,
					"fecha"=>$fecha,
					"resolucion"=>$resolucion,
					"id_sucursal"=>$id_sucursal,
		"activa"=>1,
	);
			$where="id_caja='".$id_caja."'";
	$response = edit_row($this->table,$data,$where);
			echo json_encode($response);
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
			$active = $this->caja->get_state($id);
			if($active->activa==0){
				$active->activa=1;
			}

			else {
				$active->activa=0;
			}
			$where="id_caja='".$id."'";
			$data = array(
					"activa"=>$active->activa,
			);
			$response = edit_row($this->table,$data,$where);

			echo json_encode($response);
		}
	}
	function apertura(){

		if($this->input->method(TRUE) == "GET"){
			$id_usuario = $this->session->id_usuario;
			$id_sucursal = $this->session->id_sucursal;
			$fecha = date('Y-m-d');
			$hora_actual= date("H:i:s");
			$usuario =	$this->caja->get_one_row("usuario", array('id_usuario' => $id_usuario,));
			//$caja=	$this->caja->get_detail_rows("caja", array('activa' => 1,'id_sucursal'=>$id_sucursal, ));

			$role_user=	$this->utils->get_one_row("roles", array('id_rol' => $usuario->id_rol,));
			$rol_usuario="";
			if($role_user!=NULL){
				$rol_usuario=strtoupper($role_user->nombre);
			}

		$caja=NULL;
			$caja1=$this->caja->get_aperturascaja_activa($id_sucursal,$fecha);
			if($caja1==NULL){
				$caja=$this->caja->get_cajas_disponibles($id_sucursal,$fecha);
			}
			if($caja==NULL && $caja1==NULL){
				$caja=$this->caja->get_detail_rows("caja", array("id_sucursal" => $id_sucursal,"activa"=>"1",));
			}
			//Datos extra que verifican el estado de Apertura

			//fin datos apertura
			$data = array(
				"usuario"=>$usuario,
				"id_sucursal" => $this->session->id_sucursal,
				"caja" => $caja,
				//"usuario_ap"=>$usuario_ap,
			);
			$extras = array(
				'css' => array(
				),
				'js' => array(
					"js/scripts/caja.js",
				),
			);
			//echo $rol_usuario;
			if($rol_usuario=="VENDEDOR" || $rol_usuario=="VENDEDORA" ){
				//echo "djjdfhf";
				redirect('ventas', 'refresh');
			}
			else{
				layout("caja/apertura_caja",$data,$extras);
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$id_caja = $this->input->post("caja");
			$id_usuario = $this->session->id_usuario;
			$monto_apertura = $this->input->post("monto_apertura");
			$fecha = Y_m_d($this->input->post("fecha"));
			$id_sucursal=$this->session->id_sucursal;
			$hora = date("H:i:s");
			$tabla='apertura_caja';
			$data = array(
					"id_usuario"=>$id_usuario,
					"monto_apertura"=>$monto_apertura,
					"fecha"=>$fecha,
					"hora"=>$hora,
					"caja"=>$id_caja,
					"vigente"=>1,
					"id_sucursal"=>$id_sucursal,
			);
			$row_ap = $this->caja->get_apertura_activa($id_caja,$fecha);
			if($row_ap==NULL){
					 $data['turno'] = 1;
						$id_apertura = $this->caja->inAndCon($tabla,$data);
						if($id_apertura!=NULL){


				$tabla1 = "detalle_apertura";
				$form_data1 = array(
					'id_apertura' => $id_apertura,
					'turno' => 1,
					'id_usuario' => $id_usuario,
					'fecha' => $fecha,
					'hora' => $hora,
					'vigente' => 1,
					'caja' =>$id_caja,

					);
				$insert_de =$this->caja->inAndCon($tabla1,$form_data1);
							$xdatos["type"]="success";
							$xdatos['title']='Información';
							$xdatos["msg"]="Registo Apertura ingresado correctamente!";
						}

			}else{
				if($row_ap->vigente==0){
					$sigue_turno = $row_ap->turno + 1;
					 $data['turno'] = $sigue_turno;

					$id_apertura = $this->caja->inAndCon($tabla,$data);
					if($id_apertura!=NULL){
						$tabla1 = "detalle_apertura";
						$form_data1 = array(
								'id_apertura' => $id_apertura,
								'turno' => $sigue_turno,
								'id_usuario' => $id_usuario,
								'fecha' => $fecha,
								'hora' => $hora,
								'vigente' => 1,
								'caja' =>$id_caja,

								);
						$insert_de =$this->caja->inAndCon($tabla1,$form_data1);
						$xdatos["type"]="success";
						$xdatos['title']='Información';
						$xdatos["msg"]="Registo Apertura ingresado correctamente!";
					}
				}
				else{
					$xdatos["type"]="error";
					$xdatos['title']='Alerta';
					$xdatos["msg"]="Error Ya existe una apertura Vigente en esta caja";
				}
			}
			echo json_encode($xdatos);
		}
	}
}

/* End of file Productos.php */
