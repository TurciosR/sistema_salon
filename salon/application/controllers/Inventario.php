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

class Inventario extends CI_Controller {
	/**
	 * Inventario Controller
	 *
	 * This display module Inventario
	 *
	 * @package		OpenPyme2
	 * @subpackage	Controllers
	 * @category	Controllers
	 * @author		OpenPyme Dev Team
	 * @link		https://docs.apps-oss.com/inventario_controller
	 */
	private $table = "stock";
	private $pk = "id_producto";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->model("InventarioModel","inventario");
		$this->load->model("VentasModel","ventas");
		$this->load->model("Movimiento_producto_model", "Movimiento_producto");
	}
	/*********************************************************/
	/*********************************************************/
	/************************CARGAS***************************/
	/*********************************************************/
	/*********************************************************/
	public function cargas()
	{
		$id_usuario=$this->session->id_usuario;
		$id_sucursal=$this->session->id_sucursal;
		$usuario_tipo =	$this->ventas->get_one_row("usuario", array('id_usuario' => $id_usuario,));
		if($usuario_tipo!=NULL){
			if($usuario_tipo->admin==1 || $usuario_tipo->super_admin==1){
					$sucursales=$this->inventario->get_detail_rows("sucursales",array('1' => 1, ));
			}else {
					$sucursales=$this->inventario->get_detail_rows("sucursales", array('id_sucursal' => $id_sucursal,));
			}
	 }else {
			$sucursales=$this->inventario->get_detail_rows("sucursales",array('1' => 1, ));
	 }

		$data = array(
			"titulo"=> "Cargas de inventario",
			"icono"=> "mdi mdi-archive",
			"buttons" => array(
				0 => array(
					"icon"=> "mdi mdi-plus",
					'url' => 'inventario/cargar',
					'txt' => ' Carga de inventario',
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
					"selected" => $id_sucursal,
				),
			),
			"table"=>array(
				"ID"=>4,
				"Fecha"=>10,
				"Hora"=>10,
				"Concepto"=>30,
				"Correlativo"=>10,
				"Total"=>10,
				"Responsable"=>15,
				"Acciones"=>10,
			),
			"proceso" => 'carga',
		);
		$extras = array(
			'css' => array(
			),
			'js' => array(
				"js/scripts/inventario.js"
			),
		);
		layout("template/admin",$data,$extras);
	}

	function get_data_carga()
	{
		$valid_columns = array(
			0 => 'cp.fecha',
			1 => 'cp.concepto',
			2 => 'cp.correlativo',
			3 => 'cp.total',
			4 => 'u.nombre',
		);
		// Create query based on mariadb tables required
		$query_val  = $this->inventario->create_dt_query_carga();
		$where  = array(
			'cp.id_sucursal' => $this->input->post("id_sucursal"),
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

				// ! hide access to edit and remove upload
				// $filename = base_url("inventario/editar/");
				// if ($rows->imei_ingresado==0) {
				// 	// code...
				// 	$menudrop .= "<li><a role='button' href='" . $filename.$rows->id_carga. "' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";
				// 	if ($rows->requiere_imei==1) {
				// 		// code...
				// 		$menudrop .= "<li><a role='button' href='" .  base_url("inventario/imei/").$rows->id_carga. "' ><i class='mdi mdi-text-box-check' ></i> Ingresar IMEI's</a></li>";
				// 	}
				// 	$menudrop .= "<li><a  class='delete_row'  id=" . $rows->id_carga . " ><i class='mdi mdi-trash-can-outline'></i> Eliminar</a></li>";
				// }
				// else {
				// 	// code...
				// 	$menudrop .= "<li><a role='button' href='" .  base_url("inventario/editarimei/").$rows->id_carga. "' ><i class='mdi mdi-file-document-edit-outline' ></i> Editar IMEI's</a></li>";

				// }
				$menudrop .= "<li><a  data-toggle='modal' data-target='#viewModal' data-refresh='true'  role='button' class='detail' data-id=".$rows->id_carga."><i class='mdi mdi-eye-check' ></i> Detalles</a></li>";

				$menudrop .= "</ul></div>";


				$data[] = array(
					$rows->id_carga,
					$rows->fecha,
					$rows->hora,
					$rows->concepto,
					$rows->correlativo,
					$rows->total,
					$rows->nombre,
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
				"",
				"No se encontraron registros",
				"",
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
		exit();
	}

	function detalle($id=-1){
			if($this->input->method(TRUE) == "GET"){
					$id = $this->uri->segment(3);
					$rows = $this->inventario->get_detail_ci($id);
					//procedemos a validar si se mostraran los precios
					if($this->session->admin==1 || $this->session->super_admin==1){
						$validarCostos = "";
					}
					else {
						$validarCostos = "hidden";
					}
					if($rows && $id!=""){
							$data = array(
									"rows"=>$rows,
									"process" => "cargas",
									"ocultar" => $validarCostos
							);
							$this->load->view("inventario/ver_detalle.php",$data);
					}else{
							redirect('errorpage');
					}
			}
	}
	function detalle_des($id=-1){
			if($this->input->method(TRUE) == "GET"){
					$id = $this->uri->segment(3);
					$rows = $this->inventario->get_detail_di($id);
					//procedemos a validar si se mostraran los precios
					if($this->session->admin==1 || $this->session->super_admin==1){
						$validarCostos = "";
					}
					else {
						$validarCostos = "hidden";
					}
					if($rows && $id!=""){
							$data = array(
									"rows"=>$rows,
									"process" => "descarga",
									"ocultar" => $validarCostos
							);
							$this->load->view("inventario/ver_detalle.php",$data);
					}else{
							redirect('errorpage');
					}
			}
	}

	function cargar(){
		if($this->input->method(TRUE) == "GET"){
			$id_usuario=$this->session->id_usuario;
			$id_sucursal=$this->session->id_sucursal;
			$usuario_tipo =	$this->ventas->get_one_row("usuario", array('id_usuario' => $id_usuario,));
			if($usuario_tipo!=NULL){
	 		  if($usuario_tipo->admin==1 || $usuario_tipo->super_admin==1){
	 					$sucursales=$this->ventas->get_detail_rows("sucursales",array('1' => 1, ));
	 			}else {
	 					$sucursales=$this->ventas->get_detail_rows("sucursales", array('id_sucursal' => $id_sucursal,));
	 			}
	 	 }else {
	 	 	 	$sucursales=$this->ventas->get_detail_rows("sucursales",array('1' => 1, ));
	 	 }

			$data = array(
				"sucursal"=>$sucursales,
				"id_sucursal" => $id_sucursal,
			);

			$extras = array(
				'css' => array(
				),
				'js' => array(
					"js/scripts/inventario.js"
				),
			);

			layout("inventario/carga",$data,$extras);
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->load->model("ProductosModel","productos");
			$this->utils->begin();
			$concepto = strtoupper($this->input->post("concepto"));
			$fecha = Y_m_d($this->input->post("fecha"));
			$total = $this->input->post("total");
			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$id_sucursal = $this->input->post("sucursal");
			$id_usuario = $this->session->id_usuario;
			$hora = date("H:i:s");

			$correlativo = $this->inventario->get_max_correlative('ci',$id_sucursal);

			$data = array(
				'fecha' => $fecha,
				'hora' => $hora,
				'concepto' => $concepto,
				'total' => $total,
				'id_sucursal' => $id_sucursal,
				'correlativo' => $correlativo,
				'id_usuario' => $id_usuario,
				'requiere_imei ' => 0,
				'imei_ingresado' => 0,
			);

			$imei_required = false;

			$id_carga = $this->inventario->inAndCon('inventario_carga',$data);

			$movimiento_header = [
			  "tipo"		=> "ENTRADA",
			  "proceso"		=> "CARGA DE INVENTARIO",
			  "num_doc"		=> "",
			  "correlativo" => $data['correlativo'],
			  "total"		=> $data['total'],
			  "id_despacho" => $data['id_sucursal'],
			  "id_destino"	=> $data['id_sucursal'],
			  "id_proceso"	=> $id_carga,
			  "concepto"	=> $data['concepto']
			];

			$id_movimiento_producto = $this->Movimiento_producto

			->insertar_movimiento_producto($movimiento_header);
			if($id_carga!=NULL){

				foreach ($data_ingreso as $fila)
				{
					$id_producto = $fila['id_producto'];
					$costo = $fila['costo'];
					$cantidad = $fila['cantidad'];
					$precio_sugerido = $fila['precio_sugerido'];
					$subtotal = $fila['subtotal'];
					$color = $fila['color'];

					$form_data = array(
						'id_carga' => $id_carga,
						'id_producto' => $id_producto,
						'id_color' => $color,
						'costo' => $costo,
						'precio' => $precio_sugerido,
						'cantidad' => $cantidad,
						'subtotal' => $subtotal,
					);
					$id_detalle = $this->inventario->inAndCon('inventario_carga_detalle',$form_data);
					$this->utils->update(
						"producto",
						array(
							'precio_sugerido' => $precio_sugerido,
							'costo_s_iva' => $costo,
							'costo_c_iva' => round($costo*1.13),
						 ),
						 "id_producto=$id_producto"
						);
					$stock_data = $this->inventario->get_stock($id_producto,$color,$id_sucursal);
					$newstock = ($stock_data->cantidad)+$cantidad;
					$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);
					$precios = $this->productos->get_precios_exis($id_producto);
					$this->inventario->update_cost($costo,$id_producto,$precios);



					// insert product movement detail
					$movimiento_detalle = [
					  'id_movimiento' => $id_movimiento_producto,
					  'id_producto'  => $form_data['id_producto'],
					  'id_color'=> $form_data['id_color'],
					  'costo'  => $form_data['costo'],
					  'precio'  => $form_data['precio'],
					  'cantidad'  => $form_data['cantidad'],
					];

					  $this->Movimiento_producto
					->insertar_movimiento_detalle($movimiento_detalle);
				}

				if ($imei_required) {
					//$this->utils->update("inventario_carga",array('requiere_imei' => 1, ),"id_carga=$id_carga");
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


			echo json_encode($xdatos);
		}
	}

	function editar($id=-1){

		// we block access to edit downloads
		redirect('inventario/descargas');

		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->inventario->get_one_row("inventario_carga", array('id_carga' => $id,));
			$id_usuario=$this->session->id_usuario;
			$id_sucursal=$this->session->id_sucursal;
			$usuario_tipo =	$this->ventas->get_one_row("usuario", array('id_usuario' => $id_usuario,));
			if($usuario_tipo!=NULL){
	 		  if($usuario_tipo->admin==1 || $usuario_tipo->super_admin==1){
	 					$sucursales=$this->ventas->get_detail_rows("sucursales",array('1' => 1, ));
	 			}else {
	 					$sucursales=$this->ventas->get_detail_rows("sucursales", array('id_sucursal' => $id_sucursal,));
	 			}
	 	 }else {
	 	 	 	$sucursales=$this->ventas->get_detail_rows("sucursales",array('1' => 1, ));
	 	 }

			if($row && $id!=""){
				$data = array(
					"row"=>$row,
					"detalles"=>$this->inventario->get_detail_ci($id),
					"sucursal"=>$sucursales,
					"id_sucursal" => $row->id_sucursal,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/inventario.js"
					),
				);
				layout("inventario/editar",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->load->model("ProductosModel","productos");
			$this->utils->begin();
			$concepto = strtoupper($this->input->post("concepto"));
			$fecha = Y_m_d($this->input->post("fecha"));
			$total = $this->input->post("total");
			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$id_usuario = $this->session->id_usuario;
			$id_sucursal = $this->input->post("sucursal");
			$id_carga = $this->input->post("id_carga");
			$hora = date("H:i:s");
			$row = $this->inventario->get_one_row("inventario_carga", array('id_carga' => $id_carga,));
			$id_sucursalO = $row->id_sucursal;

			if ($id_sucursalO==$id_sucursal) {
				$data = array(
					'fecha' => $fecha,
					'hora' => $hora,
					'concepto' => $concepto,
					'total' => $total,
					'id_sucursal' => $id_sucursal,
					'id_usuario' => $id_usuario,
					'requiere_imei ' => 0,
					'imei_ingresado' => 0,
				);
			}
			else {
				$correlativo = $this->inventario->get_max_correlative('ci',$id_sucursal);
				$data = array(
					'fecha' => $fecha,
					'hora' => $hora,
					'concepto' => $concepto,
					'total' => $total,
					'id_sucursal' => $id_sucursal,
					'id_usuario' => $id_usuario,
					'requiere_imei ' => 0,
					'imei_ingresado' => 0,
					'correlativo' => $correlativo,
				);
			}
			$imei_required = false;
			/*editar encabezado*/
			$this->utils->update('inventario_carga',$data,"id_carga=$id_carga");

			/*descargar los detalles previos*/
			$detalles_previos = $this->inventario->get_detail_ci($id_carga);
			foreach ($detalles_previos as $key) {
				$stock_data = $this->inventario->get_stock($key->id_producto,$key->id_color,$id_sucursalO);
				$newstock = ($stock_data->cantidad)-($key->cantidad);
				$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);
			}
			/*eliminar detalles previos*/
			$this->utils->delete("inventario_carga_detalle","id_carga=$id_carga");

			/*nuevos detalles*/
			foreach ($data_ingreso as $fila)
			{
				$id_producto = $fila['id_producto'];
				$costo = $fila['costo'];
				$cantidad = $fila['cantidad'];
				$precio_sugerido = $fila['precio_sugerido'];
				$subtotal = $fila['subtotal'];
				$color = $fila['color'];

				$form_data = array(
					'id_carga' => $id_carga,
					'id_producto' => $id_producto,
					'id_color' => $color,
					'costo' => $costo,
					'precio' => $precio_sugerido,
					'cantidad' => $cantidad,
					'subtotal' => $subtotal,
				);
				$id_detalle = $this->inventario->inAndCon('inventario_carga_detalle',$form_data);
				$this->utils->update(
					"producto",
					array(
						'precio_sugerido' => $precio_sugerido,
						'costo_s_iva' => $costo,
						'costo_c_iva' => round($costo*1.13),
					 ),
					 "id_producto=$id_producto"
					);
				$stock_data = $this->inventario->get_stock($id_producto,$color,$id_sucursal);
				$newstock = ($stock_data->cantidad)+$cantidad;
				$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);
				$precios = $this->productos->get_precios_exis($id_producto);
				$this->inventario->update_cost($costo,$id_producto,$precios);

			}

			if ($imei_required) {
				//$this->utils->update("inventario_carga",array('requiere_imei' => 1, ),"id_carga=$id_carga");
			}
			$this->utils->commit();
			$xdatos["type"]="success";
			$xdatos['title']='Información';
			$xdatos["msg"]="Registo ingresado correctamente!";

			echo json_encode($xdatos);
		}
	}

	function imei($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->inventario->get_one_row("inventario_carga", array('id_carga' => $id,));
			if($row && $id!=""){
				$data = array(
					"row"=>$row,
					"detalles"=>$this->inventario->get_detail_ci($id),
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/ingreso_imei.js"
					),
				);
				layout("inventario/cargaimei",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->utils->begin();

			$errors = false;
			$array_error= array("Log");
			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$id_carga = $this->input->post("id_carga");
			foreach ($data_ingreso as $fila) {
				$form_data = array(
					'id_producto' => $fila['id_producto'],
					'imei' => $fila['imei'],
					'id_detalle' => $fila['id_detalle'],
					'chain' => $fila['chain'],
					'id_carga' => $id_carga,
					'vendido' => 0,
				);

				$id_detalle = $this->inventario->inAndCon('inventario_carga_imei',$form_data);

				if ($id_detalle==NULL) {
					$errors = true;
				}
			}

			if ($errors==true) {
				$this->utils->rollback();
				$xdatos["type"]="error";
				$xdatos['title']='Alerta';
				$xdatos["msg"]="Error al ingresar el registro";
			}
			else {
				$this->utils->update("inventario_carga",array('imei_ingresado' => 1, ),"id_carga=$id_carga");
				$this->utils->commit();
				$xdatos["type"]="success";
				$xdatos['title']='Información';
				$xdatos["msg"]="Registo ingresado correctamente!";
			}

			echo json_encode($xdatos);

		}
	}

	function editarimei($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->inventario->get_one_row("inventario_carga", array('id_carga' => $id,));

			$info = $this->inventario->get_imei_ci($id);
			$detalles = array();
			$c=0;
			foreach ($info as $key) {
				$detalles[$c]=array(
					'id_carga' => $key->id_carga,
					'id_producto' => $key->id_producto,
					'id_detalle' => $key->id_detalle,
					'nombre' => $key->nombre,
					'chain' => $key->chain,
					'data' => $this->inventario->get_imei_ci_det($key->chain),
				 );
				$c++;
			}

			if($row && $id!=""){
				$data = array(
					"row"=>$row,
					"detalles"=>$detalles,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/ingreso_imei.js"
					),
				);
				layout("inventario/editarimei",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->utils->begin();

			$errors = false;
			$array_error= array("Log");
			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$id_carga = $this->input->post("id_carga");
			foreach ($data_ingreso as $fila) {
				$form_data = array(
					'imei' => $fila['imei'],
				);

				$this->utils->update("inventario_carga_imei",$form_data,"id_imei=$fila[id_imei]");
			}

			$this->utils->commit();
			$xdatos["type"]="success";
			$xdatos['title']='Información';
			$xdatos["msg"]="Registo ingresado correctamente!";


			echo json_encode($xdatos);

		}
	}
	function delete(){
		// we block access to edit downloads
		redirect('inventario/descargas');

		if($this->input->method(TRUE) == "POST"){
			$id_carga = $this->input->post("id");
			$this->utils->begin();
			$row = $this->inventario->get_one_row("inventario_carga", array('id_carga' => $id_carga,));
			$id_sucursal = $row->id_sucursal;
			/*descargar los detalles previos*/
			$detalles_previos = $this->inventario->get_detail_ci($id_carga);
			foreach ($detalles_previos as $key) {
				$stock_data = $this->inventario->get_stock($key->id_producto,$key->id_color,$id_sucursal);
				$newstock = ($stock_data->cantidad)-($key->cantidad);
				$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);
			}
			/*eliminar detalles previos*/
			$this->utils->delete("inventario_carga_detalle","id_carga=$id_carga");
			$this->utils->delete("inventario_carga","id_carga=$id_carga");


			$this->utils->commit();
			$response["type"] = "success";
			$response["title"] = "Información";
			$response["msg"] = "Registro eliminado con éxito!";

			echo json_encode($response);
		}
	}
	/*********************************************************/
	/*********************************************************/
	/************************CARGAS***************************/
	/*********************************************************/
	/*********************************************************/

	/*********************************************************/
	/*********************************************************/
	/************************DESCARGAS************************/
	/*********************************************************/
	/*********************************************************/
	public function descargas()
	{
		$id_usuario=$this->session->id_usuario;
		$id_sucursal=$this->session->id_sucursal;
		$usuario_tipo =	$this->ventas->get_one_row("usuario", array('id_usuario' => $id_usuario,));
		if($usuario_tipo!=NULL){
			if($usuario_tipo->admin==1 || $usuario_tipo->super_admin==1){
					$sucursales=$this->inventario->get_detail_rows("sucursales",array('1' => 1, ));
			}else {
					$sucursales=$this->inventario->get_detail_rows("sucursales", array('id_sucursal' => $id_sucursal,));
			}
	 }else {
			$sucursales=$this->inventario->get_detail_rows("sucursales",array('1' => 1, ));
	 }
		$data = array(
			"titulo"=> "Descargas de inventario",
			"icono"=> "mdi mdi-archive",
			"buttons" => array(
				0 => array(
					"icon"=> "mdi mdi-plus",
					'url' => 'inventario/descargar',
					'txt' => ' Descarga de inventario',
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
					"selected" => $id_sucursal,
				),
			),
			"table"=>array(
				"ID"=>4,
				"Fecha"=>10,
				"Hora"=>10,
				"Concepto"=>30,
				"Correlativo"=>10,
				"Total"=>10,
				"Responsable"=>15,
				"Acciones"=>10,
			),
			"proceso" => 'descarga',
		);
		$extras = array(
			'css' => array(
			),
			'js' => array(
				"js/scripts/inventario.js"
			),
		);
		layout("template/admin",$data,$extras);
	}

	function get_data_descarga(){
		$id_sucursal = $this->input->post("id_sucursal");

		$valid_columns = array(
			0 => 'cp.fecha',
			1 => 'cp.concepto',
			2 => 'cp.correlativo',
			3 => 'cp.total',
			4 => 'u.nombre',
		);
		// Create query based on mariadb tables required
		$query_val  = $this->inventario->create_dt_query_descarga();
		$where  = array(
			'cp.id_sucursal' => $this->input->post("id_sucursal"),
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


				// ! hide access to edit and remove downloas

				// $filename = base_url("inventario/editar_descarga/");
				// if ($rows->imei_ingresado==0) {
				// 	// code...
				// 	$menudrop .= "<li><a role='button' href='" . $filename.$rows->id_descarga. "' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";
				// 	if ($rows->requiere_imei==1) {
				// 		// code...
				// 		$menudrop .= "<li><a role='button' href='" .  base_url("inventario/imei_descarga/").$rows->id_descarga. "' ><i class='mdi mdi-text-box-check' ></i> Ingresar IMEI's</a></li>";
				// 	}
				// 	$menudrop .= "<li><a  class='delete_row_des'  id=" . $rows->id_descarga . " ><i class='mdi mdi-trash-can-outline'></i> Eliminar</a></li>";
				// }
				// else {
				// 	// code...
				// 	$menudrop .= "<li><a role='button' href='" .  base_url("inventario/editarimei_descarga/").$rows->id_descarga. "' ><i class='mdi mdi-file-document-edit-outline' ></i> Editar IMEI's</a></li>";

				// }

				$menudrop .= "<li><a  data-toggle='modal' data-target='#viewModal' data-refresh='true'  role='button' class='detail_des' data-id=".$rows->id_descarga."><i class='mdi mdi-eye-check' ></i> Detalles</a></li>";

				$menudrop .= "</ul></div>";


				$data[] = array(
					$rows->id_descarga,
					$rows->fecha,
					$rows->hora,
					$rows->concepto,
					$rows->correlativo,
					$rows->total,
					$rows->nombre,
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
				"",
				"No se encontraron registros",
				"",
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
		exit();
	}

	function descargar(){
		if($this->input->method(TRUE) == "GET"){
			$extras = array(
				'css' => array(
				),
				'js' => array(
					"js/scripts/inventario.js"
				),
			);
			$data = array(
				"sucursal"=>$this->inventario->get_detail_rows("sucursales",array('1' => 1, )),
				"id_sucursal" => $this->session->id_sucursal,);
			layout("inventario/descarga",$data,$extras);
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->load->model("ProductosModel","productos");
			$this->utils->begin();
			$concepto = strtoupper($this->input->post("concepto"));
			$fecha = Y_m_d($this->input->post("fecha"));
			$total = $this->input->post("total");
			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$id_sucursal =$this->input->post("sucursal");
			$id_usuario = $this->session->id_usuario;
			$hora = date("H:i:s");

			$correlativo = $this->inventario->get_max_correlative('di',$id_sucursal);

			$data = array(
				'fecha' => $fecha,
				'hora' => $hora,
				'concepto' => $concepto,
				'total' => $total,
				'id_sucursal' => $id_sucursal,
				'correlativo' => $correlativo,
				'id_usuario' => $id_usuario,
				'requiere_imei ' => 0,
				'imei_ingresado' => 0,
			);

			$imei_required = false;

			$id_carga = $this->inventario->inAndCon('inventario_descarga',$data);

			$movimiento_header = [
			  "tipo"		=> "SALIDA",
			  "proceso"   	=> "DESCARGA DE INVENTARIO",
			  "num_doc"   	=> "",
			  "correlativo" => $data['correlativo'],
			  "total"  		=> $data['total'],
			  "id_despacho" => $data['id_sucursal'],
			  "id_destino" 	=> $data['id_sucursal'],
			  "id_proceso" 	=> $id_carga,
			  "concepto" 	=> $data['concepto']
			];

			$id_movimiento_producto = $this->Movimiento_producto
			->insertar_movimiento_producto($movimiento_header);


			if($id_carga != NULL){

				foreach ($data_ingreso as $fila)
				{
					$id_producto = $fila['id_producto'];
					$costo = $fila['costo'];
					$cantidad = $fila['cantidad'];
					$precio_sugerido = $fila['precio_sugerido'];
					$subtotal = $fila['subtotal'];
					$color = $fila['color'];

					$form_data = array(
						'id_descarga' => $id_carga,
						'id_producto' => $id_producto,
						'id_color' => $color,
						'costo' => $costo,
						'precio' => $precio_sugerido,
						'cantidad' => $cantidad,
						'subtotal' => $subtotal,
					);
					$id_detalle = $this->inventario->inAndCon('inventario_descarga_detalle',$form_data);

					// insert product movement detail
					$movimiento_detalle = [
					  'id_movimiento' => $id_movimiento_producto,
					  'id_producto'  => $form_data['id_producto'],
					  'id_color'=> $form_data['id_color'],
					  'costo'  => $form_data['costo'],
					  'precio'  => $form_data['precio'],
					  'cantidad'  => $form_data['cantidad'],
					];

					$this->Movimiento_producto
					->insertar_movimiento_detalle($movimiento_detalle);


					/*$this->utils->update(
						"producto",
						array(
							'precio_sugerido' => $precio_sugerido,
							'costo_s_iva' => $costo,
							'costo_c_iva' => round($costo*1.13),
						 ),
						 "id_producto=$id_producto"
					 );*/
					$stock_data = $this->inventario->get_stock($id_producto,$color,$id_sucursal);
					$newstock = ($stock_data->cantidad)-$cantidad;
					$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);
					$precios = $this->productos->get_precios_exis($id_producto);

				}

				if ($imei_required) {
					//$this->utils->update("inventario_descarga",array('requiere_imei' => 1, ),"id_descarga=$id_carga");
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


			echo json_encode($xdatos);
		}
	}

	function editar_descarga($id=-1){

		// we block access to edit downloads
		redirect('inventario/descargas');

		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->inventario->get_one_row("inventario_descarga", array('id_descarga' => $id,));
			$detalles = $this->inventario->get_detail_di($id);
			$detalles1 = array();
			//procedemos a validar si se mostraran los precios
			if($this->session->admin==1 || $this->session->super_admin==1){
				$validarCostos = "";
			}
			else {
				$validarCostos = "hidden";
			}

			foreach ($detalles as $detalle)
			{
				$id_producto = $detalle->id_producto;
				$precio = $detalle->precio;
				$detallesp = $this->precios_producto($id_producto, $precio);
				if($detallesp != 0)
				{
					$stock_data = $this->inventario->get_stock($id_producto,$detalle->id_color,$row->id_sucursal);
					$detalle->precios = $detallesp["precios"];
					$detalle->stock = $stock_data->cantidad;
					$detalle->id_stock = $stock_data->id_stock;
					$detalle->id_color = $stock_data->id_color;
					$detalle->validar = $validarCostos;
				}
				array_push($detalles1,$detalle);
			}
			if($row && $id!=""){
				$data = array(
					"row"=>$row,
					"detalles"=>$detalles1
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/inventario.js"
					),
				);
				layout("inventario/editar_descarga",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->utils->begin();
			$concepto = strtoupper($this->input->post("concepto"));
			$fecha = Y_m_d($this->input->post("fecha"));
			$total = $this->input->post("total");
			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$id_sucursal =$this->input->post("sucursal");
			$id_usuario = $this->session->id_usuario;
			$id_carga = $this->input->post("id_carga");
			$hora = date("H:i:s");

			//$correlativo = $this->inventario->get_max_correlative('di',$id_sucursal);

			$data = array(
				'fecha' => $fecha,
				'hora' => $hora,
				'concepto' => $concepto,
				'total' => $total,
				'id_sucursal' => $id_sucursal,
				'id_usuario' => $id_usuario,
				'requiere_imei ' => 0,
				'imei_ingresado' => 0,
			);

			$imei_required = false;

			/*editar encabezado*/
			$this->utils->update('inventario_descarga',$data,"id_descarga=$id_carga");

			// prepare motion header
			$movimiento_header = [
			  "proceso"   => "EDICION DESCARGA",
			  "num_doc"   => "",
			  "correlativo" => "",
			  "total"  => $data['total'],
			  "id_despacho" => $data['id_sucursal'],
			  "id_destino" => $data['id_sucursal'],
			  "id_proceso" => $id_carga,
			  "concepto" => $data['concepto']
			];

			/*descargar los detalles previos*/
			$detalles_previos = $this->inventario->get_detail_di($id_carga);
			foreach ($detalles_previos as $key) {
				$stock_data = $this->inventario->get_stock($key->id_producto,$key->id_color,$id_sucursal);
				$newstock = ($stock_data->cantidad)+($key->cantidad);
				$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);
			}
			/*eliminar detalles previos*/
			$this->utils->delete("inventario_descarga_detalle","id_descarga=$id_carga");

			/*nuevos detalles*/
			foreach ($data_ingreso as $fila)
			{
				$id_producto = $fila['id_producto'];
				$costo = $fila['costo'];
				$cantidad = $fila['cantidad'];
				$precio_sugerido = $fila['precio_sugerido'];
				$subtotal = $fila['subtotal'];
				$color = $fila['color'];

				$form_data = array(
					'id_descarga' => $id_carga,
					'id_producto' => $id_producto,
					'id_color' => $color,
					'costo' => $costo,
					'precio' => $precio_sugerido,
					'cantidad' => $cantidad,
					'subtotal' => $subtotal,
				);
				$id_detalle = $this->inventario->inAndCon('inventario_descarga_detalle',$form_data);
				$stock_data = $this->inventario->get_stock($id_producto,$color,$id_sucursal);
				$newstock = ($stock_data->cantidad)-$cantidad;
				$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);
			}

			if ($imei_required) {
				$this->utils->update("inventario_descarga",array('requiere_imei' => 1, ),"id_descarga=$id_carga");
			}
			$this->utils->commit();
			$xdatos["type"]="success";
			$xdatos['title']='Información';
			$xdatos["msg"]="Registo ingresado correctamente!";

			echo json_encode($xdatos);
		}
	}

	function imei_descarga($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->inventario->get_one_row("inventario_descarga", array('id_descarga' => $id,));
			if($row && $id!=""){
				$data = array(
					"row"=>$row,
					"detalles"=>$this->inventario->get_detail_di($id),
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/descargo_imei.js"
					),
				);
				layout("inventario/cargaimei_descarga",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->utils->begin();

			$errors = false;
			$array_error= array("Log");
			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$id_carga = $this->input->post("id_carga");
			foreach ($data_ingreso as $fila) {
				$form_data = array(
					'id_producto' => $fila['id_producto'],
					'imei' => $fila['imei'],
					'id_detalle' => $fila['id_detalle'],
					'chain' => $fila['chain'],
					'id_descarga' => $id_carga,
					'vendido' => 0,
				);

				$id_detalle = $this->inventario->inAndCon('inventario_descarga_imei',$form_data);

				if ($id_detalle==NULL) {
					$errors = true;
				}
			}

			if ($errors==true) {
				$this->utils->rollback();
				$xdatos["type"]="error";
				$xdatos['title']='Alerta';
				$xdatos["msg"]="Error al ingresar el registro";
			}
			else {
				$this->utils->update("inventario_descarga",array('imei_ingresado' => 1, ),"id_descarga=$id_carga");
				$this->utils->commit();
				$xdatos["type"]="success";
				$xdatos['title']='Información';
				$xdatos["msg"]="Registo ingresado correctamente!";
			}

			echo json_encode($xdatos);

		}
	}

	function editarimei_descarga($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->inventario->get_one_row("inventario_descarga", array('id_descarga' => $id,));

			$info = $this->inventario->get_imei_di($id);
			$detalles = array();
			$c=0;
			foreach ($info as $key) {
				$detalles[$c]=array(
					'id_carga' => $key->id_descarga,
					'id_producto' => $key->id_producto,
					'id_detalle' => $key->id_detalle,
					'nombre' => $key->nombre,
					'chain' => $key->chain,
					'data' => $this->inventario->get_imei_di_det($key->chain),
				 );
				$c++;
			}

			if($row && $id!=""){
				$data = array(
					"row"=>$row,
					"detalles"=>$detalles,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/descargo_imei.js"
					),
				);
				layout("inventario/editarimei_descarga",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->utils->begin();

			$errors = false;
			$array_error= array("Log");
			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$id_carga = $this->input->post("id_carga");
			foreach ($data_ingreso as $fila) {
				$form_data = array(
					'imei' => $fila['imei'],
				);

				$this->utils->update("inventario_descarga_imei",$form_data,"id_imei=$fila[id_imei]");
			}

			$this->utils->commit();
			$xdatos["type"]="success";
			$xdatos['title']='Información';
			$xdatos["msg"]="Registo ingresado correctamente!";


			echo json_encode($xdatos);

		}
	}
	function delete_descarga(){
		// we block access to delete downloads
		redirect('inventario/descargas');

		if($this->input->method(TRUE) == "POST"){
			$id_carga = $this->input->post("id");
			$this->utils->begin();
			$row = $this->inventario->get_one_row("inventario_descarga", array('id_descarga' => $id_carga,));
			$id_sucursal = $row->id_sucursal;
			/*descargar los detalles previos*/
			$detalles_previos = $this->inventario->get_detail_di($id_carga);
			foreach ($detalles_previos as $key) {
				$stock_data = $this->inventario->get_stock($key->id_producto,$key->id_color,$id_sucursal);
				$newstock = ($stock_data->cantidad)+($key->cantidad);
				$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);
			}
			/*eliminar detalles previos*/
			$this->utils->delete("inventario_descarga_detalle","id_descarga=$id_carga");
			$this->utils->delete("inventario_descarga","id_descarga=$id_carga");


			$this->utils->commit();
			$response["type"] = "success";
			$response["title"] = "Información";
			$response["msg"] = "Registro eliminado con éxito!";

			echo json_encode($response);
		}
	}
	/*********************************************************/
	/*********************************************************/
	/***********************DESCARGAS*************************/
	/*********************************************************/
	/*********************************************************/
	public function detalle_producto_stock($id=0)
	{
		//$id_sucursal = $this->session->id_sucursal;
		if($id == 0)
		{
			$id_producto = $this->input->post("id");
			$id_color = $this->input->post("id_s");
			$id_sucursal = $this->input->post("id_sucursal");
		}
		$lista = "";
		$reservado = $this->ventas->get_reserved_stock($id_sucursal, $id_producto, $id_color);
		$stock_data = $this->inventario->get_stock($id_producto,$id_color,$id_sucursal);
		$prods = $this->inventario->get_producto($id_producto);
		$precios = $this->inventario->get_precios_exis($id_producto);
		$colores = $this->inventario->get_detail_rows("producto_color", array('id_producto' => $id_producto,));

		$color_select="";
		if ($colores) {
			$color_select.="<select class='form-control color' style='width:100%;'>";
			foreach ($colores as $key) {
				$color_select.="<option value='".$key->id_color."'>".$key->color."</option>";
			}
			$color_select.="/<select>";
		}
		else {
			$color_select.="<select class='form-control color sel' style='width:100%;'>";
			$color_select.="<option value='0'>SIN COLOR</option>";
			$color_select.="/<select>";
		}

		//procedemos a validar si se mostraran los precios
		if($this->session->admin==1 || $this->session->super_admin==1){
			$validarCostos = "";
		}
		else {
			$validarCostos = "hidden";
		}

		$lista .= "<div $validarCostos><select class='form-control precios sel' style='width:100%;'>";
		$costo = 0;
		$costo_iva = 0;
		foreach ($precios as $row_por)
		{
			$id_porcentaje = $row_por->id_precio;
			$costo = $row_por->costo;
			$costo_iva = $row_por->costo_iva;
			$precio = $row_por->total_iva;

			$lista .= "<option value='".$precio."' precio='".$precio."'>$".number_format($precio,2,".",",")."</option>";
		}
		$lista .= "</select></div>";


		$xdatos["precio_sugerido"]=$prods->precio_sugerido;
		$xdatos["precios"] = $lista;
		$xdatos["stock"] = $stock_data->cantidad - $reservado;
		$xdatos["id_s"] = $stock_data->id_stock;
		$xdatos["colores"]=$color_select;
		$xdatos["costo"] = number_format($costo,2,".","");
		$xdatos["costo_iva"] = number_format($costo_iva,2,".","");
		$xdatos["ocultar"] = $validarCostos;
		echo json_encode($xdatos);
	}

	public function detalle_producto($id=0)
	{
		$id_sucursal = $this->session->id_sucursal;
		if($id == 0)
		{
			$id_producto = $this->input->post("id");
		}
		$lista = "";
		$prods = $this->inventario->get_producto($id_producto);
		$precios = $this->inventario->get_precios_exis($id_producto);
		$colores = $this->inventario->get_detail_rows("producto_color", array('id_producto' => $id_producto,));
		$color_select="";
		if ($colores) {
			$color_select.="<select class='form-control color' style='width:100%;'>";
			foreach ($colores as $key) {
				$color_select.="<option value='".$key->id_color."'>".$key->color."</option>";
			}
			$color_select.="/<select>";
		}
		else {
			$color_select.="<select class='form-control color sel' style='width:100%;'>";
			$color_select.="<option value='0'>SIN COLOR</option>";
			$color_select.="/<select>";
		}
		//procedemos a validar si se mostraran los precios
		if($this->session->admin==1 || $this->session->super_admin==1){
			$validarCostos = "";
		}
		else {
			$validarCostos = "hidden";
		}
		$lista .= "<div $validarCostos><select class='form-control precios sel' style='width:100%;'>";
		$costo = 0;
		$costo_iva = 0;
		foreach ($precios as $row_por)
		{
			$id_porcentaje = $row_por->id_precio;
			$costo = $row_por->costo;
			$costo_iva = $row_por->costo_iva;
			$precio = $row_por->total_iva;

			$lista .= "<option value='".$precio."' precio='".$precio."'>$".number_format($precio,2,".",",")."</option>";
		}
		$lista .= "</select></div>";
		//procedemos a validar si se mostraran los precios
		if($this->session->admin==1 || $this->session->super_admin==1){
			$validarCostos = "";
		}
		else {
			$validarCostos = "hidden";
		}
		$xdatos["precio_sugerido"]=$prods->precio_sugerido;
		$xdatos["precios"] = $lista;
		$xdatos["colores"]=$color_select;
		$xdatos["costo"] = number_format($costo,2,".","");
		$xdatos["costo_iva"] = number_format($costo_iva,2,".","");
		$xdatos["ocultar"] = $validarCostos;
		echo json_encode($xdatos);
	}
	public function precios_producto($id=0,$precioe=0)
	{
		$id_sucursal = $this->session->id_sucursal;
		//procedemos a validar si se mostraran los precios
		if($this->session->admin==1 || $this->session->super_admin==1){
			$validarCostos = "";
		}
		else {
			$validarCostos = "hidden";
		}

		$precios = $this->inventario->get_precios_exis($id);
		$lista= "";
		$lista .= "<div $validarCostos><select class='form-control precios sel' style='width:100%;'>";
		$costo = 0;
		$costo_iva = 0;
		foreach ($precios as $row_por)
		{
			$id_porcentaje = $row_por->id_precio;
			$costo = $row_por->costo;
			$costo_iva = $row_por->costo_iva;
			$precio = $row_por->total_iva;

			$lista .= "<option value='".$precio."' precio='".$precio."'";
			if($precio == $precioe)
			{
				$lista.= " selected ";
			}
			$lista.= ">$".number_format($precio,2,".",",")."</option>";
		}
		$lista .= "</select></div>";
		$xdatos["precios"] = $lista;
		return $xdatos;
	}


function get_productos(){
	$query = $this->input->post("query");
	$rows = $this->inventario->get_productos($query);
	$output = array();
	if($rows!=NULL) {
		foreach ($rows as $row) {
			$output[] = array(

				'producto' => $row->id_producto."|".$row->codigo_barra."|".$row->nombre,
			);
		}
	}
	echo json_encode($output);
}
function get_productos_stock(){
	$query = $this->input->post("query");
	$id_sucursal = $this->input->post("id_sucursal");
	$rows = $this->inventario->get_productos_stock($query,$id_sucursal);
	$output = array();
	if($rows!=NULL) {
		foreach ($rows as $row) {
			$output[] = array(

				'producto' => $row->id_producto."|".$row->id_color."|".$row->codigo_barra."|".$row->nombre."|".$row->color,
			);
		}
	}
	echo json_encode($output);
}
function descargar_prod_esp(){
	$id_usuario=$this->session->id_usuario;
	$correlativo = $this->inventario->get_max_correlative('di',1);

	$data = array(
		'fecha' => date("Y-m-d"),
		'hora' => date("H:i:s"),
		'concepto' => "POR AJUSTE REALIZADO POR ADMIN",
		'total' => 0,
		'id_sucursal' => 1,
		'correlativo' => $correlativo,
		'id_usuario' => $id_usuario,
		'requiere_imei ' => 0,
		'imei_ingresado' => 0,
	);

	$imei_required = false;

	$id_carga = $this->inventario->inAndCon('inventario_descarga',$data);
	if($id_carga!=NULL){
		$query = $this->db->query("SELECT s.*, p.nombre, p.costo_s_iva, p.precio_sugerido FROM stock as s
		INNER JOIN producto as p ON p.id_producto = s.id_producto
		WHERE p.nombre LIKE '%SILICON CASE%' AND id_sucursal=1");
		foreach ($query->result() as $arrStock) {
			//echo $arrStock->nombre."#";
			//$cantidad = $arrStock->cantidad;
			//$subtotal = $cantidad * $arrStock->costo_s_iva;
			$cantidad = 0;
			$subtotal = 0;

			$form_data = array(
				'id_descarga' => $id_carga,
				'id_producto' => $arrStock->id_producto,
				'id_color' => $arrStock->id_color,
				'costo' => $arrStock->costo_s_iva,
				'precio' => $arrStock->precio_sugerido,
				'cantidad' => $cantidad,
				'subtotal' => $subtotal,
			);
			$id_detalle = $this->inventario->inAndCon('inventario_descarga_detalle',$form_data);
			$this->utils->update("stock",array('cantidad' => 0, ),"id_stock=".$arrStock->id_stock);
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
	echo json_encode($xdatos);
}
}

/* End of file Inventario.php */
