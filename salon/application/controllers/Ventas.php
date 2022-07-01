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
error_reporting(E_ALL);
ini_set('display_errors', '1');
// Libraries loading
include APPPATH . 'libraries/NumeroALetras.php';
include APPPATH . 'libraries/AlignMarginText.php';
include APPPATH . 'libraries/Rasteformat.php';
class Ventas extends CI_Controller {


	/**
	 * Ventas Controller
	 *
	 * This display module Ventas
	 *
	 * @package		OpenPyme2
	 * @subpackage	Controllers
	 * @category	Controllers
	 * @author		OpenPyme Dev Team
	 * @link		https://docs.apps-oss.com/ventas_controller
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel', "utils");
		$this->load->model("VentasModel", "ventas");
		$this->load->model("Clientes_model", "clientes");
		$this->load->library('user_agent');
		$this->load->model("InventarioModel", "inventario");
		$this->load->model("Movimiento_producto_model", "Movimiento_producto");
		$this->load->model("ProductosModel", "productos");
		//	$this->load->helper('print_helper');
	}


	/**
	 *
	 */
	public function index()
	{
		$id_usuario = $this->session->id_usuario;
		$id_sucursal = $this->session->id_sucursal;
		$usuario_tipo =	$this->ventas->get_one_row("usuario", array('id_usuario' => $id_usuario,));
		if ($usuario_tipo != NULL) {
			if ($usuario_tipo->admin == 1 || $usuario_tipo->super_admin == 1) {
				$sucursales = $this->ventas->get_detail_rows("sucursales", array('1' => 1,));
			} else {
				$sucursales = $this->ventas->get_detail_rows("sucursales", array('id_sucursal' => $id_sucursal,));
			}
		} else {
			$sucursales = $this->ventas->get_detail_rows("sucursales", array('1' => 1,));
		}

		$data = array(
			"titulo" => "Ventas",
			"icono" => "mdi mdi-cart",
			"buttons" => array(
				0 => array(
					"icon" => "mdi mdi-plus",
					'url' => 'ventas/finalizaref',
					'txt' => ' Nueva venta',
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
			"table" => array(
				"Id" => 5,
				"Fecha" => 5,
				"Cliente" => 20,
				"Total $" => 10,
				"Tipo" => 10,
				"Estado" => 5,
				"Tipo Pago" => 10,
				"Detalle" => 35,
				"Acciones" => 10,
			),
		);
		$extras = array(
			'css' => array(),
			'js' => array(
				"js/scripts/ventas.js"
			),
		);
		layout("template/admin", $data, $extras);
	}

	function get_data()
	{


		$valid_columns = array(
			0 => 'v.id_venta',
			1 => 'v.fecha',
			2 => 'c.nombre',
			3 => 't.nombredoc',
			4 => 'v.total',
			5 => 'tp.descripcion',
		);

		$where=array(
				'v.id_sucursal'=>	$this->input->post("id_sucursal"),
		);
		//create string query based on mariadb tables required
		$query_val=$this->ventas->create_dt_query();
		$options_dt = array(
						'valid_columns' => $valid_columns,
		);
		if(isset($where)){
				$options_dt['where'] = $where;
		}
		if(isset($query_val['join'])){
				$options_dt['join'] = $query_val['join'];
		}
		$options_dt = array_merge($query_val, $options_dt);
		$draw       = intval($this->input->post("draw"));
    //call helper for join query and datatable values, which is then queried in db
		$row = generate_dt("UtilsModel", $options_dt, FALSE);
		if ($row != 0) {
			$data = array();
			foreach ($row as $rows) {
				//procedemos a obtener el detalle de la venta
				$detalleV = $this->ventas->get_detalle_venta($rows->id_venta);
				$detalleVS = $this->ventas->get_detalle_serv($rows->id_venta);
				//$detalleVPS= array_merge($detalleV,$detalleVS);
				$menudrop = "<div class='btn-group'>
				<button data-toggle='dropdown' class='btn btn-success dropdown-toggle' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menú</button>
				<ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";
				$filename = base_url("ventas/editar/");
				$icon = "mdi mdi-toggle-switch";
				if ($rows->id_estado == 1) {
					$menudrop .= "<li><a role='button' href='" . $filename . $rows->id_venta . "' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";
					$filename = base_url("ventas/finalizar/");
					$menudrop .= "<li><a role='button' href='" . $filename . $rows->id_venta . "' ><i class='mdi mdi-square-edit-outline' ></i> Finalizar</a></li>";
					$menudrop .= "<li><a  class='delete_row'  id=" . $rows->id_venta . " ><i class='mdi mdi-trash-can-outline'></i> Eliminar</a></li>";
					$menudrop .= "<li><a  class='state_change'  data-state='Anular'  id=" . $rows->id_venta . " ><i class='$icon'></i> Anular</a></li>";
				}
				if ($rows->id_estado == 2 && $rows->nombredoc != "DEVOLUCION") {
					$filename = base_url("ventas/devolver/");
					$menudrop .= "<li><a role='button' href='" . $filename . $rows->id_venta . "' ><i class='mdi mdi-square-edit-outline' ></i> Devolución</a></li>";
				}
				$menudrop .= "<li><a  data-toggle='modal' data-target='#viewModal' data-refresh='true'  role='button' class='detail' data-id=" . $rows->id_venta . "><i class='mdi mdi-eye-check' ></i> Detalles</a></li>";
				$menudrop .= "</ul></div>";
				$det=$detalleV->detalle_v." ".$detalleVS->detalle_v;
				$data[] = array(
					$rows->id_venta,
					$rows->fecha,
					$rows->nombre,
					$rows->total,
					$rows->nombredoc,
					$rows->descripcion,
					$rows->tipopago,
					$det,
					//$detalleVPS->detalle_v,
					$menudrop,
				);
			}
			//solo contar las filas, en caso que  devuelva mas de cero, el valor TRUE es para validar que devuelva el total
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

	function detalle($id = -1)
	{
		if ($this->input->method(TRUE) == "GET") {
			$id = $this->uri->segment(3);
			$rowvta = $this->ventas->get_one_row("ventas", array('id_venta' => $id,));
			$rows = $this->ventas->get_detail_ci($id);
			$rowserv = $this->ventas->get_detail_serv($id);
			//if($rows && $id!=""){
			if ($id != "" ) {
				$tipodoc=$this->ventas->get_one_row("tipodoc", array('idtipodoc' => $rowvta->tipo_doc,));
				$cliente=$this->ventas->get_one_row("clientes", array('id_cliente' => $rowvta->id_cliente,));
				$data = array(
					"id" => $id,
					"rowvta"=>$rowvta,
					"rows" => $rows,
					"rowserv" => $rowserv,
					"tipodoc"=>$tipodoc,
					"process" => "venta",
				);
				if($cliente){
					$data['cliente']=$cliente;
				}
				$this->load->view("ventas/ver_detalle.php", $data);
			} else {
				//redirect('errorpage');
			}
		}
	}

	function state_change()
	{
		if ($this->input->method(TRUE) == "POST") {
			$id = $this->input->post("id");
			$anular = 1;
			$where = "id_venta='" . $id . "'";
			$data = array(
				"id_estado" => 3,
			);

			$response = $this->utils->update("ventas", $data, $where);
			//
			$detalles = $this->ventas->get_detail_ci($id);
			$row = $this->ventas->get_one_row("ventas", array('id_venta' => $id,));
			//procedemos a realizar la carga del inventario
			$correlativo = $this->inventario->get_max_correlative('ci', $row->id_sucursal);
			$id_usuario = $this->session->id_usuario;
			$data = array(
				'fecha' => $row->fecha,
				'hora' => $row->hora,
				'concepto' => "POR DEVOLUCION DE PRODUCTOS",
				'total' => $row->total,
				'id_sucursal' => $row->id_sucursal,
				'correlativo' => $correlativo,
				'id_usuario' => $id_usuario,
				'requiere_imei ' => 0,
				'imei_ingresado' => 0,
			);



			$id_carga = $this->inventario->inAndCon('inventario_carga', $data);

			$movimiento_header = [
				"tipo" => "ENTRADA",
				"proceso"   => "DEVOLUCION",
				"num_doc"   => "",
				"correlativo" => $data['correlativo'],
				"total"  => $data['total'],
				"id_despacho" => $data['id_sucursal'],
				"id_destino" => $data['id_sucursal'],
				"id_proceso" => $id_carga,
				"concepto" => $data['concepto']
			];

			$id_movimiento_producto = $this->Movimiento_producto
				->insertar_movimiento_producto($movimiento_header);

			$id_sucursal = $row->id_sucursal;
			if ($detalles != NULL) {
				foreach ($detalles as $detalle) {
					$id_producto = $detalle->id_producto;
					$color = $detalle->id_color;
					$costo = $detalle->costo;
					$precio_sugerido = $detalle->precio;
					$cantidad = $detalle->cantidad;
					$subtotal = ($detalle->cantidad * $detalle->costo);
					$form_data = array(
						'id_carga' => $id_carga,
						'id_producto' => $id_producto,
						'id_color' => $color,
						'costo' => $costo,
						'precio' => $precio_sugerido,
						'cantidad' => $cantidad,
						'subtotal' => $subtotal,
					);
					$id_detalle = $this->inventario->inAndCon('inventario_carga_detalle', $form_data);

					if ($detalle->tipo_prod == 0) {
						$stock_data = $this->ventas->get_stock($id_producto, $detalle->id_color, $id_sucursal);
						$newstock = ($stock_data->cantidad) + $cantidad;
						$this->utils->update("stock", array('cantidad' => $newstock,), "id_stock=" . $stock_data->id_stock);
					}

					// insert product movement detail
					$movimiento_detalle = [
						'id_movimiento' => $id_movimiento_producto,
						'id_producto'  => $form_data['id_producto'],
						'id_color' => $form_data['id_color'],
						'costo'  => $form_data['costo'],
						'precio'  => $form_data['precio'],
						'cantidad'  => $form_data['cantidad'],
					];

					$this->Movimiento_producto
						->insertar_movimiento_detalle($movimiento_detalle);
				}
			}
			///
			if ($response) {
				$xdatos["type"] = "success";
				$xdatos['title'] = 'Información';
				$xdatos["msg"] = "Registo  anulado correctamente!";
			} else {

				$xdatos["type"] = "error";
				$xdatos['title'] = 'Alerta';
				$xdatos["msg"] = "Registo no pudo ser  anulado";
			}
			echo json_encode($xdatos);
		}
	}

	function change_state($id = -1)
	{
		if ($this->input->method(TRUE) == "GET") {
			$id = $this->uri->segment(3);
			$row = $this->ventas->get_one_row("ventas", array('id_venta' => $id,));
			$rows = $this->ventas->get_detail_rows("estado", array('1' => 1,));
			if ($rows && $id != "") {
				$data = array(
					"row" => $row,
					"rows" => $rows,
				);
				$this->load->view("ventas/change_state.php", $data);
			} else {
				redirect('errorpage');
			}
		}
	}
	function get_data_cliente($id = -1)
	{
		if ($this->input->method(TRUE) == "GET") {
			$id = $this->uri->segment(3);
			$giro = $this->clientes->get_giro();
			$row = $this->ventas->get_one_row("ventas", array('id_venta' => $id,));
			$rowcte = $this->ventas->get_one_row("clientes", array('id_cliente' => $row->id_cliente,));
			//$id_cliente=$row->id_cliente;
			$row_tipopago = $this->ventas->get_detail_rows("tipo_pago", array('null' => -1,));
			if ($row && $id != "") {
				$data = array(
					"row" => $row,
					"rowcte" => $rowcte,
					"giro" => $giro,
					"tipo_pago" => $row_tipopago,
				);
				$this->load->view("ventas/data_client.php", $data);
			} else {
				redirect('errorpage');
			}
		}
	}

	/**
	 * Show the interface to create 'preventa"
	 *
	 * @return void
	 */
	function agregar()
	{

		// check the request method
		if ($this->input->method(TRUE) == "GET") {

			// Get the server time, user id and server id to search for
			// cash opening
			$fecha = date('Y-m-d');
			$id_usuario = $this->session->id_usuario;
			$id_sucursal = $this->session->id_sucursal;

			// search for cash opening
			$row_ap = $this->ventas->get_caja_activa($id_sucursal, $fecha);
			$usuario_ap = NULL;
			if ($row_ap != NULL) {
				$usuario_ap =	$this->ventas->get_one_row(
					"usuario",
					array('id_usuario' => $row_ap->id_usuario)
				);
			}

			// ?
			$row_clientes = $this->ventas->get_detail_rows(
				"clientes",
				array('activo' => 1, 'deleted' => 0,)
			);
			$row_client_select = $this->ventas->get_one_row(
				"clientes",
				array('activo' => 1, 'deleted' => 0,)
			);

			// Prepare the information to be displayed in the view
			$data = array(
				"id_sucursal"		=> $this->session->id_sucursal,
				"row_clientes"		=> $row_clientes,
				"id_usuario"		=> $id_usuario,
				"row_ap"			=> $row_ap,
				"usuario_ap"		=> $usuario_ap,
				"row_client_select"	=> $row_client_select,
				"sucursal"			=> $this->ventas->get_detail_rows(
					"sucursales",
					array(
						'1' => 1,
					)
				),
			);

			// indicate js and css files to use
			$extras = array(
				'css' => array("css/scripts/ventas.css"),
				'js' => array("js/scripts/ventas.js"),
			);

			// show view
			layout("ventas/guardar", $data, $extras);
		} else if ($this->input->method(TRUE) == "POST") {

			// Start Transact-SQL
			$this->utils->begin();

			$id_sucursal 	= $this->input->post("sucursal");
			$id_usuario 	= $this->session->id_usuario;
			$fecha 			= date("Y-m-d");
			$data_ingreso 	= json_decode(
				$this->input->post("data_ingreso"),
				true
			);

			// get the global correlative and the current date
			$correlativo	= $this->ventas->get_max_correlative('ven', $id_sucursal);
			$fecha_corr		= $this->ventas->get_date_correlative($id_sucursal);

			// update the correlative of the references
			if ($fecha == $fecha_corr) {

				$referencia = $this->ventas->get_correlative(
					'refdia',
					$id_sucursal
				);

				$this->utils->update(
					"correlativo",
					array("refdia" => $referencia,),
					"id_sucursal=" . $id_sucursal
				);
			} else {
				$referencia = 1;
				$this->utils->update(
					"correlativo",
					array('fecha' => $fecha, "refdia" => $referencia,),
					"id_sucursal= $id_sucursal"
				);
			}

			// Get the form data
			// prepare the data for the header of the sale
			$data = array(
				'fecha'					=> $fecha,
				'hora'					=> date("H:i:s"),
				'concepto'				=> strtoupper($this->input->post("concepto")),
				'indicaciones'			=> "AGREGAR VENTA",
				'id_cliente'			=> $this->input->post("client"),
				'id_estado'				=> 1,
				'id_sucursal_despacho'	=> $id_sucursal,
				'referencia'			=> $referencia,
				'correlativo'			=> $correlativo,
				'total'					=> $this->input->post("total"),
				'id_sucursal'			=> $id_sucursal,
				'id_usuario'			=> $this->session->id_usuario,
				'requiere_imei '		=> 0,
				'imei_ingresado'		=> 0,
				'guia' => "",
			);


			// insert header
			$id_venta = $this->ventas->inAndCon('ventas', $data);

			if ($id_venta != NULL) {
				if ($data_ingreso != NULL) {

					foreach ($data_ingreso as $fila) {
						// Prepare the data to record the detail
						($fila['tipo'] == 0)
							? $id_precio = $fila['id_precio'] : $id_precio = 0;

						$form_data = array(
							'id_venta'			=> $id_venta,
							'id_producto' 		=> $fila['id_producto'],
							'id_color' 			=> $fila['color'],
							'costo' 			=> $fila['costo'],
							'precio' 			=> $fila['precio_sugerido'],
							'precio_fin' 		=> $fila['precio_final'],
							'descuento' 		=> $fila['descuento'],
							'cantidad' 			=> $fila['cantidad'],
							'subtotal' 			=> $fila['subtotal'],
							'condicion' 		=> $fila['est'],
							'tipo_prod' 		=> $fila['tipo'],
							'id_precio_producto' => $id_precio,
							'garantia' 			=> 0,
						);

						// insert detail
						$this->ventas->inAndCon('ventas_detalle', $form_data);


					}
				}


				$this->utils->commit();

				// Return success data
				$xdatos = [
					"type"       => "success",
					"referencia" => $referencia,
					'title'      => 'Información',
					"msg"        => "Registo ingresado correctamente!"
				];
			} else {
				$this->utils->rollback();

				// Return success data
				$xdatos = [
					"type"       => "error",
					"title"      => "Alerta",
					"referencia" => -1,
					"msg"        => "Error al ingresar el registro"
				];
			}

			echo json_encode($xdatos);
		}
	}

	function editar($id = -1)
	{

		if ($this->input->method(TRUE) == "GET") {
			$id = $this->uri->segment(3);
			//apertura de caja
			$id_usuario = $this->session->id_usuario;
			$fecha = date('Y-m-d');
			$id_sucursal = $this->session->id_sucursal;
			$row_ap = $this->ventas->get_caja_activa($id_sucursal, $fecha);
			$usuario_ap = NULL;
			if ($row_ap != NULL) {
				$id_apertura = $row_ap->id_apertura;
				$usuario_ap =	$this->ventas->get_one_row("usuario", array('id_usuario' => $row_ap->id_usuario,));
			}
			$row_clientes = $this->ventas->get_detail_rows("clientes", array('activo' => 1, 'deleted' => 0,)); //
			$row_client_select = $this->ventas->get_one_row("clientes", array('activo' => -1, 'deleted' => 0,));
			//fin apertura caja
			$row = $this->ventas->get_one_row("ventas", array('id_venta' => $id,));
			$rowc = $this->ventas->get_one_row("clientes", array('id_cliente' => $row->id_cliente,));
			$rowpc = $this->ventas->get_porcent_client($rowc->clasifica);

			$detalles = $this->ventas->get_detail_ci($id);
			$detalleservicios = $this->ventas->get_detail_serv($id);

			$detalles1 = array();
			if ($detalles != NULL) {

				$clasifica = $rowc->clasifica;
				foreach ($detalles as $detalle) {
					$id_producto = $detalle->id_producto;
					$precio = $detalle->precio;
					$qty_sold = $detalle->cantidad;
					if ($detalle->id_color != -1) {
						//$precios=$this->ventas->get_detail_rows("producto_precio",array('id_producto' =>$id_producto,'id_listaprecio'=>$clasifica));
						$precios = $this->ventas->get_detail_rows("producto_precio", array('id_producto' => $id_producto));

						$stock_data = $this->ventas->get_stock($id_producto, $detalle->id_color, $row->id_sucursal);
						//$detalle->precios = $detallesp["precios"];
						$lista = "";
						$lista .= "<select class='form-control precios sel' style='width:100%;'>";
						$costo = 0;
						$costo_iva = 0;
						foreach ($precios as $row_por) {
							$id_porcentaje = $row_por->id_precio;
							$costo = $row_por->costo;
							$costo_iva = $row_por->costo_iva;

							$precio = $row_por->porcentaje;
							if ($detalle->id_precio_producto == $row_por->id_precio) {
								// code...
								$lista .= "<option value='" . $precio . "' precio='" . $precio . "' id_precio='" . $row_por->id_precio . "' selected>" . number_format($precio, 2, ".", ",") . "</option>";
							} else {
								// code...
								$lista .= "<option value='" . $precio . "' precio='" . $precio . "' id_precio='" . $row_por->id_precio . "'>" . number_format($precio, 2, ".", ",") . "</option>";
							}
						}
						$lista .= "</select>";
						$detalle->precios = $lista;
						$detalle->stock = $stock_data->cantidad + $qty_sold;
						$detalle->id_stock = $stock_data->id_stock;
						$detalle->id_color = $stock_data->id_color;

						$d = $this->ventas->get_reservado($id_producto, $id, $detalle->id_color);
						$detalle->reservado = $d->reservado;

						$estado = "<select class='est'>";
						if ($detalle->condicion == "NUEVO") {
							$estado .= "<option selected value='NUEVO'>NUEVO</option>";
							$estado .= "<option value='USADO'>USADO</option>";
						} else {
							$estado .= "<option value='NUEVO'>NUEVO</option>";
							$estado .= "<option selected value='USADO'>USADO</option>";
						}
						$estado .= "</select>";

						$detalle->estado = $estado;
						//}
					}
					array_push($detalles1, $detalle);
				}
			}
			if ($row && $id != "") {
				$data = array(
					"row" => $row,
					"detalles" => $detalles1,
					"detalleservicios" => $detalleservicios,
					'rowpc' => $rowpc,
					"sucursal" => $this->ventas->get_detail_rows("sucursales", array('1' => 1,)),
					"id_sucursal" => $row->id_sucursal,
					"rowc" => $rowc,
					"id_usuario" => $id_usuario,
					"row_ap" => $row_ap,
					"usuario_ap" => $usuario_ap,
					"row_clientes" => $row_clientes,
					"row_client_select" => $row_client_select,
				);
				$extras = array(
					'css' => array(
						"css/scripts/ventas.css"
					),
					'js' => array(
						"js/scripts/ventas.js"
					),
				);
				layout("ventas/editar", $data, $extras);
			} else {
				redirect('errorpage');
			}
		} else if ($this->input->method(TRUE) == "POST") {
			$this->utils->begin();
			$id_venta = $this->input->post("id_venta");
			$concepto = strtoupper($this->input->post("concepto"));
			$fecha = Y_m_d($this->input->post("fecha"));
			$instrucciones = $this->input->post("instrucciones");
			$total = $this->input->post("total");
			$id_cliente = $this->input->post("client");
			$data_ingreso = json_decode($this->input->post("data_ingreso"), true);
			$id_sucursal = $this->input->post("sucursal");
			$envio = $this->input->post("envio");
			$id_usuario = $this->session->id_usuario;
			$id_sucursal = $this->session->id_sucursal;
			$hora = date("H:i:s");

			$row = $this->ventas->get_one_row("ventas", array('id_venta' => $id_venta,));

			$data = array(
				'fecha' => $fecha,
				'hora' => $hora,
				'concepto' => $concepto,
				'indicaciones' => "EDITAR VENTA",
				'id_cliente' => $id_cliente,
				'id_estado' => 1,
				'id_sucursal_despacho' => $id_sucursal,
				'total' => $total,
				'id_sucursal' => $id_sucursal,
				'id_usuario' => $id_usuario,
				'requiere_imei ' => 0,
				'imei_ingresado' => 0,
				'guia' => "",
			);


			/*editar encabezado*/
			$this->utils->update('ventas', $data, "id_venta=$id_venta");

			/*Cargo los detalles previos*/
			$detalles_previos = $this->ventas->get_detail_ci($id_venta);
			if ($detalles_previos != NULL) {
				foreach ($detalles_previos as $key) {
					if ($key->tipo_prod == 0) {
						$stock_data = $this->ventas->get_stock($key->id_producto, $key->id_color, $id_sucursal);
						$newstock = ($stock_data->cantidad) + ($key->cantidad);

						$this->utils->update("stock", array('cantidad' => $newstock,), "id_stock=" . $stock_data->id_stock);
					}
				}
			}
			/*eliminar detalles previos*/
			$this->utils->delete("ventas_detalle", "id_venta=$id_venta");

			/*nuevos detalles*/
			if ($data_ingreso != NULL) {
				foreach ($data_ingreso as $fila) {
					$id_producto = $fila['id_producto'];
					$costo = $fila['costo'];
					$cantidad = $fila['cantidad'];
					$precio_sugerido = $fila['precio_sugerido'];
					$descuento = $fila['descuento'];
					$precio_final = $fila['precio_final'];
					$subtotal = $fila['subtotal'];
					$color = $fila['color'];
					$estado = $fila['est'];
					$tipo = $fila['tipo']; //"0:PRODUCTO,1:SERVICIO"
					($tipo == 0) ? $id_precio = $fila['id_precio'] : $id_precio = 0;

					$form_data = array(
						'id_venta' => $id_venta,
						'id_producto' => $id_producto,
						'id_color' => $color,
						'costo' => $costo,
						'precio' => $precio_sugerido,
						'precio_fin' => $precio_final,
						'descuento' => $descuento,
						'cantidad' => $cantidad,
						'subtotal' => $subtotal,
						'condicion' => $estado,
						'tipo_prod' => $tipo,
						'garantia' =>  0,
						'id_precio_producto' => $id_precio,
					);
					$id_detalle = $this->ventas->inAndCon('ventas_detalle', $form_data);
					$this->utils->update(
						"producto",
						array(
							'precio_sugerido' => $precio_sugerido,
							'costo_s_iva' => $costo,
							'costo_c_iva' => round($costo * 1.13),
						),
						"id_producto=$id_producto"
					);
					$stock_data = $this->ventas->get_stock($id_producto, $color, $id_sucursal);
					$newstock = ($stock_data->cantidad) - $cantidad;
					if ($tipo == 0 && $estado != "SERVICIO") {
						$this->utils->update("stock", array('cantidad' => $newstock,), "id_stock=" . $stock_data->id_stock);
					}
				}
			}

			$this->utils->commit();
			$xdatos["type"] = "success";
			$xdatos['title'] = 'Información';
			$xdatos["msg"] = "Registo ingresado correctamente!";

			echo json_encode($xdatos);
		}
	}
	function finalizar($id = -1)
	{
		if ($this->input->method(TRUE) == "GET") {
			$id = $this->uri->segment(3);
			//apertura de caja
			$id_usuario = $this->session->id_usuario;
			$fecha = date('Y-m-d');
			$id_sucursal = $this->session->id_sucursal;
			$row_ap = $this->ventas->get_caja_activa($id_sucursal, $fecha);
			$usuario_ap = NULL;
			if ($row_ap != NULL) {
				$id_apertura = $row_ap->id_apertura;
				$usuario_ap =	$this->ventas->get_one_row("usuario", array('id_usuario' => $row_ap->id_usuario,));
			}
			$row_clientes = $this->ventas->get_detail_rows("clientes", array('null' => -1,)); //
			//fin apertura caja
			$row = $this->ventas->get_one_row("ventas", array('id_venta' => $id,));
			$rowc = $this->ventas->get_one_row("clientes", array('id_cliente' => $row->id_cliente,));
			$rowpc = $this->ventas->get_porcent_client($rowc->clasifica);
			$clasifica = $rowc->clasifica;
			$detalles = $this->ventas->get_detail_ci($id);
			$detalleservicios = $this->ventas->get_detail_serv($id);
			$tipodoc =	$this->ventas->get_tipodoc();
			$detalles1 = array();
			if ($detalles != NULL) {
				foreach ($detalles as $detalle) {
					$id_producto = $detalle->id_producto;
					$precio = $detalle->precio;
					$qty_sold = $detalle->cantidad;

					$precios = $this->ventas->get_detail_rows("producto_precio", array('id_producto' => $id_producto));
					$stock_data = $this->ventas->get_stock($id_producto, $detalle->id_color, $row->id_sucursal);
					$lista = "";
					$lista .= "<select class='form-control precios sel' style='width:100%;'>";
					$costo = 0;
					$costo_iva = 0;
					foreach ($precios as $row_por) {
						$id_porcentaje = $row_por->id_precio;
						$costo = $row_por->costo;
						$costo_iva = $row_por->costo_iva;
						$precio = $row_por->porcentaje;
						if ($detalle->id_precio_producto == $row_por->id_precio) {
							// code...
							$lista .= "<option value='" . $precio . "' precio='" . $precio . "' id_precio='" . $row_por->id_precio . "' selected>" . number_format($precio, 2, ".", ",") . "</option>";
						} else {
							// code...
							$lista .= "<option value='" . $precio . "' precio='" . $precio . "' id_precio='" . $row_por->id_precio . "'>" . number_format($precio, 2, ".", ",") . "</option>";
						}
					}
					$lista .= "</select>";
					$detalle->precios = $lista;
					$detalle->stock = $stock_data->cantidad + $qty_sold;
					$detalle->id_stock = $stock_data->id_stock;
					$detalle->id_color = $stock_data->id_color;

					$d = $this->ventas->get_reservado($id_producto, $id, $detalle->id_color);
					$detalle->reservado = $d->reservado;

					$estado = "<select class='est'>";
					if ($detalle->condicion == "NUEVO") {
						$estado .= "<option selected value='NUEVO'>NUEVO</option>";
						$estado .= "<option value='USADO'>USADO</option>";
					} else {
						$estado .= "<option value='NUEVO'>NUEVO</option>";
						$estado .= "<option selected value='USADO'>USADO</option>";
					}
					$estado .= "</select>";

					$detalle->estado = $estado;
					//}
					array_push($detalles1, $detalle);
				}
			}
			if ($row && $id != "") {
				$id_usuario = $this->session->id_usuario;
				$fecha = date('Y-m-d');
				$data = array(
					"row" => $row,
					"detalles" => $detalles1,
					"detalleservicios" => $detalleservicios,
					'tipodoc' => $tipodoc,
					'rowpc' => $rowpc,
					"sucursal" => $this->ventas->get_detail_rows("sucursales", array('1' => 1,)),
					"id_sucursal" => $row->id_sucursal,
					"rowc" => $rowc,
					"row_clientes" => $row_clientes,
					"id_usuario" => $id_usuario,
					"row_ap" => $row_ap,
					"usuario_ap" => $usuario_ap,
				);
				$extras = array(
					'css' => array(
						"css/scripts/ventas.css"
					),
					'js' => array(
						"js/scripts/ventas.js"
					),
				);
				layout("ventas/finalizar", $data, $extras);
			} else {
				redirect('errorpage');
			}
		} else if ($this->input->method(TRUE) == "POST") {
			$this->utils->begin();
			$id_venta = $this->input->post("id_venta");
			$concepto = strtoupper($this->input->post("concepto"));
			$fecha = Y_m_d($this->input->post("fecha"));
			$total = $this->input->post("total");
			$id_cliente = $this->input->post("client");
			$data_ingreso = json_decode($this->input->post("data_ingreso"), true);
			$tipodoc = $this->input->post("tipodoc");
			$id_sucursal = $this->session->id_sucursal;
			$envio = $this->input->post("envio");
			$id_usuario = $this->session->id_usuario;
			$hora = date("H:i:s");
			$fechahoy = date('Y-m-d');
			$row = $this->ventas->get_one_row("ventas", array('id_venta' => $id_venta,));
			$id_sucursalO = $row->id_sucursal;
			$fecha_corr = $this->ventas->get_date_correlative($id_sucursal);
			$referencia =  $row->referencia;


			switch ($tipodoc) {
				case 1:
					$correlativo = $this->ventas->get_correlative('tik', $id_sucursal);
					$correlativo1 = $this->ventas->update_correlative('tik', $correlativo, $id_sucursal);
					break;
				case 2:
					$correlativo = $this->ventas->get_correlative('cof', $id_sucursal);
					$correlativo1 = $this->ventas->update_correlative('cof', $correlativo, $id_sucursal);
					break;
				case 3:
					$correlativo = $this->ventas->get_correlative('ccf', $id_sucursal);
					$correlativo1 = $this->ventas->update_correlative('ccf', $correlativo, $id_sucursal);
					break;
			}

			$row_ap = $this->ventas->get_caja_activa($id_sucursal, $fechahoy);
			$id_apertura = $row_ap->id_apertura;
			$caja = $row_ap->caja;
			$data = array(
				'fecha' => $fechahoy,
				'hora' => $hora,
				'concepto' => "VENTA FIN",
				'indicaciones' => "VENTA FIN",
				'id_cliente' => $id_cliente,
				'envio' => $envio,
				'id_estado' => 2,
				'id_sucursal_despacho' => $id_sucursal,
				'total' => $total,
				'id_sucursal' => $id_sucursal,
				'id_usuario' => $id_usuario,
				'requiere_imei ' => 0,
				'imei_ingresado' => 0,
				'tipo_doc' => $tipodoc,
				'referencia' => $referencia,
				'correlativo' => $correlativo,
				'guia' => "",
				'caja' => $caja,
				'id_apertura' => $id_apertura,
				'hora_fin' => $hora,
			);


			/*editar encabezado*/
			$this->utils->update('ventas', $data, "id_venta=$id_venta");

			/*Cargo los detalles previos*/
			$detalles_previos = $this->ventas->get_detail_ci($id_venta);
			if ($detalles_previos != NULL) {
				foreach ($detalles_previos as $key) {

					if ($key->tipo_prod == 0) {
						$stock_data = $this->ventas->get_stock($key->id_producto, $key->id_color, $id_sucursalO);
						$newstock = ($stock_data->cantidad) + ($key->cantidad);
						$this->utils->update("stock", array('cantidad' => $newstock,), "id_stock=" . $stock_data->id_stock);
					}
				}
			}
			/*eliminar detalles previos*/
			$this->utils->delete("ventas_detalle", "id_venta=$id_venta");

			/*nuevos detalles*/
			if ($data_ingreso != NULL) {
				foreach ($data_ingreso as $fila) {
					$id_producto = $fila['id_producto'];
					$costo = $fila['costo'];
					$cantidad = $fila['cantidad'];
					$precio_sugerido = $fila['precio_sugerido'];
					$descuento = $fila['descuento'];
					$precio_final = $fila['precio_final'];
					$subtotal = $fila['subtotal'];
					$color = $fila['color'];
					$estado = $fila['est'];
					$tipo = $fila['tipo']; //"0:PRODUCTO,1:SERVICIO"
					($tipo == 0) ? $id_precio = $fila['id_precio'] : $id_precio = 0;

					$form_data = array(
						'id_venta' => $id_venta,
						'id_producto' => $id_producto,
						'id_color' => $color,
						'costo' => $costo,
						'precio' => $precio_sugerido,
						'precio_fin' => $precio_final,
						'descuento' => $descuento,
						'cantidad' => $cantidad,
						'subtotal' => $subtotal,
						'condicion' => $estado,
						'tipo_prod' => $tipo,
						'garantia' =>  0,
						'id_precio_producto' => $id_precio,
					);
					$id_detalle = $this->ventas->inAndCon('ventas_detalle', $form_data);
					$this->utils->update(
						"producto",
						array(
							'precio_sugerido' => $precio_sugerido,
							'costo_s_iva' => $costo,
							'costo_c_iva' => round($costo * 1.13),
						),
						"id_producto=$id_producto"
					);

					if ($tipo == 0 && $estado != "SERVICIO") {
						$stock_data = $this->ventas->get_stock($id_producto, $color, $id_sucursal);
						$newstock = ($stock_data->cantidad) - $cantidad;
						$this->utils->update("stock", array('cantidad' => $newstock,), "id_stock=" . $stock_data->id_stock);
					}
				}
			}

			$this->utils->commit();
			$xdatos["type"] = "success";
			$xdatos['title'] = 'Información';
			$xdatos["msg"] = "Venta guardada correctamente!";
			$xdatos["id_factura"] = $id_venta;
			$xdatos["proceso"] = "finalizar";

			echo json_encode($xdatos);
		}
	}

	/**
	 * finalize a presale or direct sale
	 *
	 * @return void
	 */
	function fin_fact($id = -1)
	{
		// check the method
		if ($this->input->method(TRUE) == "POST") {

			// obtain the id of the sale, in case there is one
			$id_venta = $this->input->post("id_venta");
			$fechahoy        = date('Y-m-d');
			if ($id_venta == -1) {

				// if there is no reference, direct sales are made
				$this->utils->begin();

				// get form data
				$fecha        = date('Y-m-d');
				$total        = $this->input->post("total");
				$id_cliente   = $this->input->post("client");
				$id_sucursal  = $this->input->post("id_sucursal");
				//$tipodoc      = $this->input->post("tipodoc");
				$tipodoc      = $this->input->post("tipo_doc_h");
				$id_usuario   = $this->session->id_usuario;
				$hora         = date("H:i:s");
				$fechahoy     = date('Y-m-d');
				//$tipo_pago 		= $this->input->post("tipo_pago");
				$tipo_pago 		= $this->input->post("tipo_pago_h");
				$efectivo = $this->input->post("efectivo");

				$data_ingreso = json_decode(
					$this->input->post("data_ingreso"),
					true
				);
				//en base al tipo pago 1 efectivo,2 tarjeta credito o débito, 3 credito
				$voucher = "";
				$credito = 0;
				$dias_credito = 0;
				$abono = 0;
				if ($tipo_pago == 0) {
					$tipo_pago = 1;
				}
				if ($tipo_pago == 2) { //si es pago con tarjeta traer el num voucher
					$voucher = $this->input->post("cambio");
				}
				if ($tipo_pago == 3) { //si es pago credito
					$dias_credito = $this->input->post("cambio");
					$credito = 1;
					$abono = $efectivo;
				}

				$row_ap = $this->ventas->get_caja_activa($id_sucursal, $fecha);
				$id_apertura = $row_ap->id_apertura;
				$caja = $row_ap->caja;

				$referencia = 0;
				switch ($tipodoc) {
					case 1:
						$correlativo = $this->ventas->get_correlative('tik', $id_sucursal);
						$this->ventas->update_correlative('tik', $correlativo, $id_sucursal);
						break;
					case 2:
						$correlativo = $this->ventas->get_correlative('cof', $id_sucursal);
						$this->ventas->update_correlative('cof', $correlativo, $id_sucursal);
						break;
					case 3:
						$correlativo = $this->ventas->get_correlative('ccf', $id_sucursal);
						$this->ventas->update_correlative('ccf', $correlativo, $id_sucursal);
						break;
				}

				$data = array(
					'fecha'                => $fechahoy,
					'hora'                 => $hora,
					'concepto'             => "FINALIZADA REF. TIPO PAGO:".$tipo_pago,
					'indicaciones'         => "FINALIZADA REF",
					'id_cliente'           => $id_cliente,
					'id_estado'            => 2,
					'id_sucursal_despacho' => $id_sucursal,
					'correlativo'          => $correlativo,
					'total'                => $total,
					'id_sucursal'          => $id_sucursal,
					'id_usuario'           => $id_usuario,
					'tipo_doc'             => $tipodoc,
					'referencia'           => 0,
					'caja'                 => $caja,
					'id_apertura'          => $id_apertura,
					'hora_fin'             => $hora,
					'tipo_pago'            => $tipo_pago,
					//add for chage tipo_pago 27 jul 2021
					'voucher_pago' => $voucher,
					'credito' => $credito,
					'dias_credito' => $dias_credito,
					'hora_fin' => $hora,
					'fecha' => $fechahoy, //add 13-01-2020
					'id_apertura' => $id_apertura,

				);

				$id_factura = $this->ventas->inAndCon('ventas', $data);
				//si es venta al credito validar que se guarde
				if ($tipo_pago == 3) { //si es pago credito
					$abono = $efectivo;
					$saldo = $total-$abono;
					$t1 = "cuentas_por_cobrar";
					$t2 = "cuentas_por_cobrar_abonos";
					if ($saldo == 0) {
						$estado_cxc = 1;
					} else {
						$estado_cxc = 0;
					}
					$arr_cxc = array(
						'id_venta' => $id_factura,
						'abono'	=> $abono,
						'saldo' => $saldo,
						'estado' => $estado_cxc,
					);
					$id_cxc = $this->ventas->inAndCon($t1, $arr_cxc);
				}
				$movimiento_header = [
					"tipo"        => "SALIDA",
					"proceso"     => "VENTA",
					"num_doc"     => "",
					"correlativo" => $data['correlativo'],
					"total"       => $data['total'],
					"id_despacho" => $data['id_sucursal'],
					"id_destino"  => $data['id_sucursal'],
					"id_proceso"  => $id_factura,
					"concepto"    => $data['concepto']
				];

				$id_movimiento_producto = $this->Movimiento_producto
					->insertar_movimiento_producto($movimiento_header);

				if ($id_factura != NULL) {
					if ($data_ingreso != NULL) {

						foreach ($data_ingreso as $fila) {

							$id_producto     = $fila['id_producto'];
							$costo           = $fila['costo'];
							$cantidad        = $fila['cantidad'];
							$precio_sugerido = $fila['precio_sugerido'];
							$descuento       = $fila['descuento'];
							$precio_final    = $fila['precio_final'];
							$subtotal        = $fila['subtotal'];
							$color           = $fila['color'];
							$tipo            = $fila['tipo']; //"0:PRODUCTO,1:SERVICIO"

							//$estado = $fila['est'];
							if($tipo==1){
								$id_precio = -1;
								$estado="SERVICIO";
							}else {
								$id_precio = $fila['id_precio'];
								$estado="PRODUCTO";
							}


							$form_data = array(
								'id_venta'    => $id_factura,
								'id_producto' => $id_producto,
								'id_color'    => $color,
								'costo'       => $costo,
								'precio'      => $precio_sugerido,
								'precio_fin'  => $precio_final,
								'descuento'   => $descuento,
								'cantidad'    => $cantidad,
								'subtotal'    => $subtotal,
								'condicion'   => $estado,
								'tipo_prod'   => $tipo,
								'id_precio_producto' => $id_precio,
								'garantia'    => 0,
							);

							$id_detalle = $this->ventas->inAndCon('ventas_detalle', $form_data);

							// insert product movement detail
							$movimiento_detalle = [
								'id_movimiento' => $id_movimiento_producto,
								'id_producto'   => $form_data['id_producto'],
								'id_color'      => $form_data['id_color'],
								'costo'         => $form_data['costo'],
								'precio'        => $form_data['precio'],
								'cantidad'      => $form_data['cantidad'],
							];

							$this->Movimiento_producto
								->insertar_movimiento_detalle($movimiento_detalle);

							// discount stock -----------------------------
							if ($tipo == 0) {
								$stock_data = $this->ventas->get_stock(
									$id_producto,
									$color,
									$id_sucursal
								);
								$newstock = ($stock_data->cantidad) - $cantidad;
								$this->utils->update(
									"stock",
									array('cantidad' => $newstock,),
									"id_stock=" . $stock_data->id_stock
								);
							}
							// --------------------------------------------

						}
					}

					$this->utils->commit();
					$xdatos["type"] = "success";
					$xdatos['title'] = 'Información';
					$xdatos["msg"] = "Registo ingresado correctamente!";
					$xdatos["id_factura"] = $id_factura;
					$xdatos["proceso"] = "facturar";
				} else {
					$this->utils->rollback();
					$xdatos["type"] = "error";
					$xdatos['title'] = 'Alerta';
					$xdatos["msg"] = "Error al ingresar el registro";
				}
			} //finaliza venta sin referencia como de facturar
			else { //en caso que si hay referencia es como finalizar !
				$this->utils->begin();
				$concepto = strtoupper($this->input->post("concepto"));
				$fecha = Y_m_d($this->input->post("fecha"));

				$total = $this->input->post("total");
				$id_cliente = $this->input->post("client");
				$data_ingreso = json_decode($this->input->post("data_ingreso"), true);
				//$tipodoc = $this->input->post("tipodoc");
				$tipodoc = $this->input->post("tipo_doc_h");
				$tipo_pago 		= $this->input->post("tipo_pago_h");
				$efectivo = $this->input->post("efectivo");
				$id_sucursal = $this->session->id_sucursal;
				$envio = $this->input->post("envio");
				$id_usuario = $this->session->id_usuario;
				$hora = date("H:i:s");

				$row = $this->ventas->get_one_row("ventas", array('id_venta' => $id_venta,));
				$id_sucursalO = $row->id_sucursal;
				$fecha_corr = $this->ventas->get_date_correlative($id_sucursal);
				$referencia =  $row->referencia;

				switch ($tipodoc) {
					case 1:
						$correlativo = $this->ventas->get_correlative('tik', $id_sucursal);
						$correlativo1 = $this->ventas->update_correlative('tik', $correlativo, $id_sucursal);
						break;
					case 2:
						$correlativo = $this->ventas->get_correlative('cof', $id_sucursal);
						$correlativo1 = $this->ventas->update_correlative('cof', $correlativo, $id_sucursal);
						break;
					case 3:
						$correlativo = $this->ventas->get_correlative('ccf', $id_sucursal);
						$correlativo1 = $this->ventas->update_correlative('ccf', $correlativo, $id_sucursal);
						break;
				}
				$row_ap = $this->ventas->get_caja_activa($id_sucursal, $fecha);
				$id_apertura = $row_ap->id_apertura;
				$caja = $row_ap->caja;

				//en base al tipo pago 1 efectivo,2 tarjeta credito o débito, 3 credito
				$voucher = "";
				$credito = 0;
				$dias_credito = 0;
				$abono = 0;
				if ($tipo_pago == 0) {
					$tipo_pago = 1;
				}
				if ($tipo_pago == 2) { //si es pago con tarjeta traer el num voucher
					$voucher = $this->input->post("cambio");
				}
				if ($tipo_pago == 3) { //si es pago credito
					$dias_credito = $this->input->post("cambio");
					$credito = 1;
					$abono = $efectivo;
				}

				$data = array(
					'fecha' => $fecha,
					//'hora' => $hora,
					'concepto' => "FINALIZADA REF",
					'indicaciones' => "FINALIZADA REF:" . $referencia,
					'id_cliente' => $id_cliente,
					'envio' => "",
					'id_estado' => 2,
					'id_sucursal_despacho' => $id_sucursal,
					'total' => $total,
					'id_sucursal' => $id_sucursal,
					'id_usuario' => $id_usuario,
					'requiere_imei ' => 0,
					'imei_ingresado' => 0,
					'tipo_doc' => $tipodoc,
					'tipo_pago'    => $tipo_pago,
					'correlativo' => $correlativo,
					'guia' => "",
					'caja' => $caja,
					'id_apertura' => $id_apertura,

					//add for chage tipo_pago 27 jul 2021
					'voucher_pago' => $voucher,
					'credito' => $credito,
					'dias_credito' => $dias_credito,
					'hora_fin' => $hora,
					'fecha' => $fechahoy, //add 13-01-2020

				);


				/*editar encabezado*/
				$this->utils->update('ventas', $data, "id_venta=$id_venta");
				//si es venta al credito validar que se guarde
				if ($tipo_pago == 3) { //si es pago credito
					$abono = $efectivo;
					$saldo = $total-$abono;
					$t1 = "cuentas_por_cobrar";
					$t2 = "cuentas_por_cobrar_abonos";
					if ($saldo == 0) {
						$estado_cxc = 1;
					} else {
						$estado_cxc = 0;
					}
					$arr_cxc = array(
						'id_venta' => $id_venta,
						'abono'	=> $abono,
						'saldo' => $saldo,
						'estado' => $estado_cxc,
					);
					$id_cxc = $this->ventas->inAndCon($t1, $arr_cxc);
				}




				$movimiento_header = [
					"tipo" => "SALIDA",
					"proceso"   => "VENTA",
					"num_doc"   => "",
					"correlativo" => $data['correlativo'],
					"total"  => $data['total'],
					"id_despacho" => $data['id_sucursal'],
					"id_destino" => $data['id_sucursal'],
					"id_proceso" => $id_venta,
					"concepto" => $data['concepto']
				  ];

				$id_movimiento_producto = $this->Movimiento_producto
				->insertar_movimiento_producto($movimiento_header);

				/*Cargo los detalles previos*/
				$detalles_previos = $this->ventas->get_detail_ci($id_venta);
				// if ($detalles_previos != NULL) {
				// 	foreach ($detalles_previos as $key) {

				// 		if ($key->tipo_prod == 0) {
				// 			$stock_data = $this->ventas->get_stock($key->id_producto, $key->id_color, $id_sucursalO);
				// 			$newstock = ($stock_data->cantidad) + ($key->cantidad);
				// 			$this->utils->update("stock", array('cantidad' => $newstock,), "id_stock=" . $stock_data->id_stock);
				// 		}
				// 	}
				// }
				/*eliminar detalles previos*/
				$this->utils->delete("ventas_detalle", "id_venta=$id_venta");

				/*nuevos detalles*/
				if ($data_ingreso != NULL) {
					foreach ($data_ingreso as $fila) {
						$id_producto = $fila['id_producto'];
						$costo = $fila['costo'];
						$cantidad = $fila['cantidad'];
						$precio_sugerido = $fila['precio_sugerido'];
						$descuento = $fila['descuento'];
						$precio_final = $fila['precio_final'];
						$subtotal = $fila['subtotal'];
						$color = $fila['color'];
						$estado = $fila['est'];
						$tipo = $fila['tipo']; //"0:PRODUCTO,1:SERVICIO"
						($tipo == 0) ? $id_precio = $fila['id_precio'] : $id_precio = 0;

						$form_data = array(
							'id_venta' => $id_venta,
							'id_producto' => $id_producto,
							'id_color' => $color,
							'costo' => $costo,
							'precio' => $precio_sugerido,
							'precio_fin' => $precio_final,
							'descuento' => $descuento,
							'cantidad' => $cantidad,
							'subtotal' => $subtotal,
							'condicion' => $estado,
							'tipo_prod' => $tipo,
							'garantia' => 0,
							'id_precio_producto' => $id_precio,
						);

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


						$id_detalle = $this->ventas->inAndCon('ventas_detalle', $form_data);
						$this->utils->update(
							"producto",
							array(
								'precio_sugerido' => $precio_sugerido,
								'costo_s_iva' => $costo,
								'costo_c_iva' => round($costo * 1.13),
							),
							"id_producto=$id_producto"
						);

						if ($tipo == 0 && $estado != "SERVICIO") {
							$stock_data = $this->ventas->get_stock($id_producto, $color, $id_sucursal);
							$newstock = ($stock_data->cantidad) - $cantidad;
							$this->utils->update("stock", array('cantidad' => $newstock,), "id_stock=" . $stock_data->id_stock);
						}
					}
				}

				$this->utils->commit();
				$xdatos["type"] = "success";
				$xdatos['title'] = 'Información';
				$xdatos["msg"] = "Venta guardada correctamente!";
				$xdatos["id_factura"] = $id_venta;
				$xdatos["proceso"] = "finalizar";
			}
			echo json_encode($xdatos);
		}
	}

	function up_data_client()
	{
		$this->load->library('user_agent');
		if ($this->input->method(TRUE) == "POST") {
			if ($this->agent->is_browser()) {
				$agent = $this->agent->browser() . ' ' . $this->agent->version();
				$opsys = $this->agent->platform();
			}
			$errors = false;
			$this->utils->begin();
			$id_venta = $this->input->post("id_vta");
			$id_cliente = $this->input->post("id_client");
			$clasifica = $this->input->post("clasifica");
			$nomcte = $this->input->post("nombre_cliente");
			$nomcomer = $this->input->post("nombre_cliente");
			$correlativo = $this->input->post("numero_doc");
			$tipo_pago = $this->input->post("tipo_pago");
			$efectivo = $this->input->post("efectivo");
			$direccion = "EL SALVADOR";
			$id_sucursal = $this->session->id_sucursal;
			$id_usuario = $this->session->id_usuario;
			$fechahoy = date('Y-m-d');
			$hora = date("H:i:s");
			//datos de apertura actual
			$row_ap = $this->ventas->get_caja_activa($id_sucursal, $fechahoy);
			$id_apertura = $row_ap->id_apertura;
			$caja = $row_ap->caja;
			//fin datos de apertura actual

			$rowvta = $this->ventas->get_one_row("ventas", array('id_venta' => $id_venta,));
			$row_confdir = $this->ventas->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));
			$tipodoc =	$rowvta->tipo_doc;
			/*
			$voucher = "";
			$credito = 0;
			$dias_credito = 0;
			$abono = 0;
			if ($tipo_pago == 0) {
				$tipo_pago = 1;
			}
			if ($tipo_pago == 2) { //si es pago con tarjeta traer el num voucher
				$voucher = $this->input->post("cambio");
			}
			if ($tipo_pago == 3) { //si es pago credito
				$dias_credito = $this->input->post("cambio");
				$credito = 1;
				$abono = $efectivo;
			}
			*/
			switch ($tipodoc) {
				case 1:
					$form_data = array(
						'nombre' => $nomcte,
						'nombre_comercial' => $nomcomer,
						'clasifica' => $clasifica,
						'activo' => 1,
					);
					$row_confpos = $this->ventas->get_one_row("config_pos", array('id_sucursal' => $id_sucursal, 'alias_tipodoc' => 'tik',));
					//$correlativo1 = $this->ventas->update_correlative('tik',$correlativo,$id_sucursal);
					break;
				case 2:

					$nit = $this->input->post("nit");
					$nrc = $this->input->post("nrc");
					$form_data = array(
						'nombre' => $nomcte,
						'nombre_comercial' => $nomcomer,
						'direccion' => $direccion,
						'clasifica' => $clasifica,
						'activo' => 1,
					);
					//$correlativo1 = $this->ventas->update_correlative('cof',$correlativo,$id_sucursal);
					break;
				case 3:
					$nit = $this->input->post("nit");
					$nrc = $this->input->post("nrc");
					$form_data = array(
						'nombre' => $nomcte,
						'nombre_comercial' => $nomcomer,
						'direccion' => $direccion,
						'clasifica' => $clasifica,
						'nit' => $nit,
						'nrc' => $nrc,
						'activo' => 1,
					);
					//$correlativo1 = $this->ventas->update_correlative('ccf',$correlativo,$id_sucursal);
					break;
			}
			/*
			if ($id_cliente < 0) {
				//$id_client=$id_cliente;
				if ($tipodoc > 1) {
					$id_cliente = $this->ventas->inAndCon("clientes", $form_data);
				}
				if ($id_cliente == NULL) {
					$errors = true;
				} else {

					$form_cte = array(
						'id_cliente' => $id_cliente,
						//'correlativo'	=>$correlativo,
						'id_estado' => 2,
						"concepto" => "VENTA DE PRODUCTOS Y SERVICIOS",
						'tipo_pago' => $tipo_pago,
						'voucher_pago' => $voucher,
						'credito' => $credito,
						'dias_credito' => $dias_credito,
						'hora_fin' => $hora,
						'fecha' => $fechahoy, //add 13-01-2020
						'id_apertura' => $id_apertura,
						'caja' => $caja,
					);
					$this->utils->update("ventas", $form_cte, "id_venta=$id_venta");
					if ($tipo_pago == 3) { //si es pago credito
						$abono = $efectivo;
						//$saldo=$rowvta->total-$abono;
						$saldo = $abono;
						$t1 = "cuentas_por_cobrar";
						$t2 = "cuentas_por_cobrar_abonos";
						if ($saldo == 0) {
							$estado_cxc = 1;
						} else {
							$estado_cxc = 0;
						}
						$arr_cxc = array(
							'id_venta' => $id_venta,
							'abono'	=> 0,
							'saldo' => $saldo,
							'estado' => $estado_cxc,
						);
						$id_cxc = $this->ventas->inAndCon($t1, $arr_cxc);

					}
				}
			} else { */
				if ($tipodoc > 1) {
					$this->utils->update(
						"clientes",
						$form_data,
						"id_cliente=$id_cliente"
					);
				}
				$form_cte = array(
					'id_cliente' => $id_cliente,
					'correlativo'	=> $correlativo,
					'id_estado'	=> 2, //cambiar estadoa Finalizado id:2, cambiar despues
					"concepto" => "VENTA DE PRODUCTOS Y SERVICIOS",


				/* tipo_pago ya no es del modal
				  'tipo_pago' => $tipo_pago,
					'voucher_pago' => $voucher,
					'credito' => $credito,
					'dias_credito' => $dias_credito,
					'hora_fin' => $hora,
					'fecha' => $fechahoy, //add 13-01-2020
					'id_apertura' => $id_apertura,
					*/
					'caja' => $caja,

				);
				$this->utils->update("ventas", $form_cte, "id_venta=$id_venta");
					/* el tipo pago ya no en modal
				if ($tipo_pago == 3) { //si es pago credito
					$abono = $efectivo;
					//$saldo=$rowvta->total-$abono;
					$saldo = $abono;
					$t1 = "cuentas_por_cobrar";
					$t2 = "cuentas_por_cobrar_abonos";
					if ($saldo == 0) {
						$estado_cxc = 1;
					} else {
						$estado_cxc = 0;
					}
					$arr_cxc = array(
						'id_venta' => $id_venta,
						'abono'	=> 0,
						'saldo' => $saldo,
						'estado' => $estado_cxc,
					);
					$id_cxc = $this->ventas->inAndCon($t1, $arr_cxc);
				}*/

				$id_client = $id_cliente;
			//}
			if ($errors == true) {
				// code...
				$this->utils->rollback();
				$xdatos["type"] = "error";
				$xdatos['title'] = 'Alerta';
				$xdatos["msg"] = "Error al ingresar el registro";
				$xdatos["agent"] = $agent;
			} else {
				$this->utils->commit();
				$facturar = $correlativo;

				$tot_letras = NumeroALetras::convertir($rowvta->total, 'Dolares', false, 'centavos');
				$total_letras = wordwrap(strtoupper($tot_letras), 40) . "\n";

				switch ($tipodoc) {
					case 1:
						$xdatos = $this->print_ticket($id_venta, $id_sucursal, $rowvta->correlativo, $rowvta->total, $rowvta);
						break;
					case 2:
						$xdatos = $this->print_cof($id_venta, $id_sucursal, $rowvta);
						break;
					case 3:
						$xdatos = $this->print_ccf($id_venta, $id_sucursal, $rowvta);
						break;
				}

				$xdatos["type"] = "success";
				$xdatos['title'] = 'Información';
				$xdatos["msg"] = "Venta guardada correctamente!";
				$xdatos["tipodoc"] = $tipodoc;
				$xdatos["id_client"] = $id_cliente;
				$xdatos["total_letras"] = $total_letras;
				$xdatos["opsys"] = $opsys;
				$xdatos["dir_print"] = $row_confdir->dir_print_script; //for Linux
				$xdatos["dir_print_pos"] = $row_confdir->shared_printer_pos; //for win

			}
			echo json_encode($xdatos);
		}
	}
	function devolver($id = -1)
	{
		if ($this->input->method(TRUE) == "GET") {
			//apertura de caja
			$id_usuario = $this->session->id_usuario;
			$fecha = date('Y-m-d');
			$id_sucursal = $this->session->id_sucursal;
			$row_ap = $this->ventas->get_caja_activa($id_sucursal, $fecha);
			$usuario_ap = NULL;
			if ($row_ap != NULL) {
				$id_apertura = $row_ap->id_apertura;
				$usuario_ap =	$this->ventas->get_one_row("usuario", array('id_usuario' => $row_ap->id_usuario,));
			}
			$row_clientes = $this->ventas->get_detail_rows("clientes", array('null' => -1,)); //
			//fin apertura caja

			$id = $this->uri->segment(3);
			$row = $this->ventas->get_one_row("ventas", array('id_venta' => $id,));
			$rowc = $this->ventas->get_one_row("clientes", array('id_cliente' => $row->id_cliente,));
			$rowpc = $this->ventas->get_porcent_client($rowc->clasifica);

			$detalles = $this->ventas->get_detail_ci($id);
			$detalleservicios = $this->ventas->get_detail_serv($id);
			$tipodoc =	$this->ventas->get_tipodoc();
			$detalles1 = array();
			if ($detalles != NULL) {
				foreach ($detalles as $detalle) {
					$id_producto = $detalle->id_producto;
					$precio = $detalle->precio;
					$detallesp = $this->precios_producto($id_producto, $precio);
					if ($detallesp != 0) {
						$stock_data = $this->ventas->get_stock($id_producto, $detalle->id_color, $row->id_sucursal);
						$detalle->precios = $detallesp["precios"];
						$detalle->stock = $stock_data->cantidad;
						$detalle->id_stock = $stock_data->id_stock;
						$detalle->id_color = $stock_data->id_color;

						$d = $this->ventas->get_reservado($id_producto, $id, $detalle->id_color);
						$detalle->reservado = $d->reservado;

						$row_dev = $this->ventas->get_dev_ante($id_producto, $id);
						$detalle->dev_ante = $row_dev->dev_ante;


						$estado = "<select class='est'>";
						if ($detalle->condicion == "NUEVO") {
							$estado .= "<option selected value='NUEVO'>NUEVO</option>";
							$estado .= "<option value='USADO'>USADO</option>";
						} else {
							$estado .= "<option value='NUEVO'>NUEVO</option>";
							$estado .= "<option selected value='USADO'>USADO</option>";
						}
						$estado .= "</select>";

						$detalle->estado = $estado;
					}
					array_push($detalles1, $detalle);
				}
			}
			$detalles2 = array();
			if ($detalleservicios != NULL) {

				foreach ($detalleservicios as $detalleserv) {
					$id_producto = $detalleserv->id_producto;
					$row_dev = $this->ventas->get_dev_ante($id_producto, $id);
					$detalleserv->dev_ante = $row_dev->dev_ante;
					if ($row_dev != NULL)
						$detalleserv->dev_ante = $row_dev->dev_ante;
					else {
						$detalleserv->dev_ante = 0;
					}
					array_push($detalles2, $detalleserv);
				}
			}


			if ($row && $id != "") {

				$id_usuario = $this->session->id_usuario;
				$fecha = date('Y-m-d');
				$data = array(
					"row" => $row,
					"detalles" => $detalles1,
					"detalleservicios" => $detalles2,
					'tipodoc' => $tipodoc,
					'rowpc' => $rowpc,
					"sucursal" => $this->ventas->get_detail_rows("sucursales", array('1' => 1,)),
					"id_sucursal" => $row->id_sucursal,
					"rowc" => $rowc,
					"id_usuario" => $id_usuario,
					"row_ap" => $row_ap,
					"usuario_ap" => $usuario_ap,
				);
				$extras = array(
					'css' => array(
						"css/scripts/ventas.css"
					),
					'js' => array(
						"js/scripts/ventas.js"
					),
				);
				layout("ventas/devolver", $data, $extras);
			} else {
				redirect('errorpage');
			}
		} else if ($this->input->method(TRUE) == "POST") {
			$this->utils->begin();
			$id_venta = $this->input->post("id_venta");
			$fecha_venta = Y_m_d($this->input->post("fecha"));
			$total_dev = $this->input->post("total");
			$id_cliente = $this->input->post("id_cliente");
			$data_ingreso = json_decode($this->input->post("data_ingreso"), true);

			$rowdoc = $this->ventas->get_tipodoc_alias("DEV");
			$id_sucursal = $this->session->id_sucursal;
			$correlativo = $this->ventas->get_correlative('dev', $id_sucursal);

			$totcant = count($data_ingreso);
			$id_usuario = $this->session->id_usuario;
			$hora = date("H:i:s");
			$fecha_dev = date('Y-m-d');
			//insertar EN la tabla devolucion  el encabezado
			$tabla = "devoluciones";
			$form_data = array(
				'id_venta' => $id_venta,
				'cant' => $totcant,
				'monto' => $total_dev,
				'fecha' => $fecha_dev,
				'hora' => $hora,

			);

			$row_ap = $this->ventas->get_caja_activa($id_sucursal, $fecha_dev);
			$id_apertura = $row_ap->id_apertura;
			$caja = $row_ap->caja;
			$correlativo1 = $this->ventas->update_correlative('dev', $correlativo, $id_sucursal);
			$id_dev = $this->ventas->inAndCon($tabla, $form_data);


			//se inserta tambien en la tabla ventas como tipo devolucion ID 4 DEV
			$row = $this->ventas->get_one_row("ventas", array('id_venta' => $id_venta,));
			$id_sucursal = $row->id_sucursal;
			if ($rowdoc != NULL)
				$tipodoc = $rowdoc->idtipodoc;

			$concepto = "DEVOLUCION";
			$data = array(
				'fecha' => $fecha_dev,
				'hora' => $hora,
				'concepto' => $concepto,
				'id_cliente' => $id_cliente,
				'id_estado' => 2,
				'id_sucursal_despacho' => $id_sucursal,
				'total' => $total_dev,
				'id_sucursal' => $id_sucursal,
				'id_usuario' => $id_usuario,
				'tipo_doc' => $tipodoc,
				'referencia' => 0,
				'correlativo' => $correlativo,
				'caja' => $caja,
				'id_apertura' => $id_apertura,
				'id_devolucion' => $id_dev,
			);
			$tabla1 = "ventas";
			$id_dev_vta = $this->ventas->inAndCon($tabla1, $data);

			$movimiento_header = [
				"tipo" => "ENTRADA",
				"proceso"   => "DEVOLUCION",
				"num_doc"   => "",
				"correlativo" => $data['correlativo'],
				"total"  => $data['total'],
				"id_despacho" => $data['id_sucursal'],
				"id_destino" => $data['id_sucursal'],
				"id_proceso" => $id_dev_vta,
				"concepto" => $data['concepto']
			];

			$id_movimiento_producto = $this->Movimiento_producto
				->insertar_movimiento_producto($movimiento_header);

			/*nuevos detalles*/
			if ($data_ingreso != NULL) {
				foreach ($data_ingreso as $fila) {
					$id_producto = $fila['id_producto'];
					$costo = $fila['costo'];
					$cantidad = $fila['cantidad'];
					$id_detalle = $fila['id_detalle'];
					$cant_dev = $fila['cant_dev'];
					$precio_final = $fila['precio_final'];
					$subtotal = $fila['subtotal'];
					$color = $fila['color'];
					$estado = $fila['est'];
					$tipo = $fila['tipo_prod']; //"0:PRODUCTO,1:SERVICIO"

					$form_data = array(
						'id_venta' => $id_dev_vta,
						'id_producto' => $id_producto,
						'id_color' => $color,
						'costo' => $costo,
						'precio' => $precio_final,
						'precio_fin' => $precio_final,
						'cantidad' => $cant_dev,
						'subtotal' => $subtotal,
						'condicion' => $estado,
						'tipo_prod' => $tipo,
					);
					//se inserta en la tabla venta_detalle c/u de los items
					$id_detalle = $this->ventas->inAndCon('ventas_detalle', $form_data);

					// insert product movement detail
					$movimiento_detalle = [
						'id_movimiento' => $id_movimiento_producto,
						'id_producto'  => $form_data['id_producto'],
						'id_color' => $form_data['id_color'],
						'costo'  => $form_data['costo'],
						'precio'  => $form_data['precio'],
						'cantidad'  => $form_data['cantidad'],
					];

					$this->Movimiento_producto
						->insertar_movimiento_detalle($movimiento_detalle);


					if ($tipo == 0) {
						$stock_data = $this->ventas->get_stock($id_producto, $color, $id_sucursal);
						$newstock = ($stock_data->cantidad) + $cant_dev;
						$this->utils->update("stock", array('cantidad' => $newstock,), "id_stock=" . $stock_data->id_stock);
					}

					$tabla2 = 'devoluciones_det';
					$form_data2 = array(
						'id_dev' => $id_dev,
						'id_venta' => $id_venta,
						'id_producto' => $id_producto,
						'cant' => $cant_dev,
						'monto' => $costo,
						'id_venta_detalle' => $id_detalle,
					);
					$insertar = $this->ventas->inAndCon($tabla2, $form_data2);
				}
			}

			$this->utils->commit();
			$xdatos["type"] = "success";
			$xdatos['title'] = 'Información';
			$xdatos["msg"] = "Venta guardada correctamente!";
			$xdatos["proceso"] = "finalizar";

			echo json_encode($xdatos);
		}
	}
	function imei($id = -1)
	{
		if ($this->input->method(TRUE) == "GET") {
			$id = $this->uri->segment(3);
			$row = $this->ventas->get_one_row("ventas", array('id_venta' => $id,));
			if ($row && $id != "") {
				$data = array(
					"row" => $row,
					"detalles" => $this->ventas->get_detail_ci($id),
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"js/scripts/ventas_imei.js"
					),
				);
				layout("ventas/cargaimei", $data, $extras);
			} else {
				redirect('errorpage');
			}
		} else if ($this->input->method(TRUE) == "POST") {
			$this->utils->begin();

			$errors = false;
			$array_error = array("Log");
			$data_ingreso = json_decode($this->input->post("data_ingreso"), true);
			$id_venta = $this->input->post("id_venta");
			foreach ($data_ingreso as $fila) {
				// code...
				$form_data = array(
					'id_producto' => $fila['id_producto'],
					'imei' => $fila['imei'],
					'id_detalle' => $fila['id_detalle'],
					'chain' => $fila['chain'],
					'id_venta' => $id_venta,
					'vendido' => 1,
				);

				$id_detalle = $this->ventas->inAndCon('ventas_imei', $form_data);

				if ($id_detalle == NULL) {
					// code...
					$errors = true;
				}
			}

			if ($errors == true) {
				// code...
				$this->utils->rollback();
				$xdatos["type"] = "error";
				$xdatos['title'] = 'Alerta';
				$xdatos["msg"] = "Error al ingresar el registro";
			} else {
				// code...
				$this->utils->update("ventas", array('imei_ingresado' => 1,), "id_venta=$id_venta");
				$this->utils->commit();
				$xdatos["type"] = "success";
				$xdatos['title'] = 'Información';
				$xdatos["msg"] = "Registo ingresado correctamente!";
			}

			echo json_encode($xdatos);
		}
	}
	function editarimei($id = -1)
	{
		if ($this->input->method(TRUE) == "GET") {
			$id = $this->uri->segment(3);
			$row = $this->ventas->get_one_row("ventas", array('id_venta' => $id,));

			$info = $this->ventas->get_imei_ci($id);
			$detalles = array();
			$c = 0;
			foreach ($info as $key) {
				// code...
				$detalles[$c] = array(
					'id_venta' => $key->id_venta,
					'id_producto' => $key->id_producto,
					'id_detalle' => $key->id_detalle,
					'nombre' => $key->nombre,
					'chain' => $key->chain,
					'data' => $this->ventas->get_imei_ci_det($key->chain),
				);
				$c++;
			}

			if ($row && $id != "") {
				$data = array(
					"row" => $row,
					"detalles" => $detalles,
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"js/scripts/ventas_imei.js"
					),
				);
				layout("ventas/editarimei", $data, $extras);
			} else {
				redirect('errorpage');
			}
		} else if ($this->input->method(TRUE) == "POST") {
			$this->utils->begin();

			$errors = false;
			$array_error = array("Log");
			$data_ingreso = json_decode($this->input->post("data_ingreso"), true);
			$id_venta = $this->input->post("id_venta");
			foreach ($data_ingreso as $fila) {
				// code...
				$form_data = array(
					'imei' => $fila['imei'],
				);

				$this->utils->update("ventas_imei", $form_data, "id_imei=$fila[id_imei]");
			}

			$this->utils->commit();
			$xdatos["type"] = "success";
			$xdatos['title'] = 'Información';
			$xdatos["msg"] = "Registo ingresado correctamente!";


			echo json_encode($xdatos);
		}
	}
	function delete()
	{
		if ($this->input->method(TRUE) == "POST") {
			$id_venta = $this->input->post("id");
			$this->utils->begin();
			$row = $this->ventas->get_one_row("ventas", array('id_venta' => $id_venta,));
			$id_sucursal = $row->id_sucursal;
			/*descargar los detalles previos*/
			$detalles_previos = $this->ventas->get_detail_rows("ventas_detalle", array('id_venta' => $id_venta,));
			foreach ($detalles_previos as $key) {
				// code...

				if ($key->tipo_prod == 0) {
					$stock_data = $this->ventas->get_stock($key->id_producto, $key->id_color, $id_sucursal);
					$newstock = ($stock_data->cantidad) + ($key->cantidad);
					$this->utils->update("stock", array('cantidad' => $newstock,), "id_stock=" . $stock_data->id_stock);
				}
			}
			/*eliminar detalles previos*/
			$this->utils->delete("ventas_detalle", "id_venta=$id_venta");
			$this->utils->delete("ventas", "id_venta=$id_venta");


			$this->utils->commit();
			$response["type"] = "success";
			$response["title"] = "Información";
			$response["msg"] = "Registro eliminado con éxito!";

			echo json_encode($response);
		}
	}
	function change()
	{
		if ($this->input->method(TRUE) == "POST") {
			$id_venta = $this->input->post("id");
			$id_estado = $this->input->post("id_estado");
			$this->utils->begin();
			$this->utils->update("ventas", array('id_estado' => $id_estado,), "id_venta=$id_venta");
			$this->utils->commit();
			$response["type"] = "success";
			$response["title"] = "Información";
			$response["msg"] = "Registro editado con éxito!";
			echo json_encode($response);
		}
	}
	function garantia($id = -1)
	{
		if ($this->input->method(TRUE) == "GET") {

			$id = $this->uri->segment(3);
			$this->load->library('GarantiaReport');
			$pdf = $this->garantiareport->getInstance('P', 'mm', 'Letter');
			$logo = base_url() . "assets/img/logo.png";
			$pdf->SetMargins(6, 10);
			$pdf->SetLeftMargin(5);
			$pdf->AliasNbPages();
			$pdf->SetAutoPageBreak(true, 15);
			$pdf->AliasNbPages();

			$vg = $this->ventas->get_venta($id);
			$id_sucursal = $vg->id_sucursal_despacho;
			$this->db->where("id_sucursal", $id_sucursal);
			$q = $this->db->get("sucursales");
			$dat = $q->row();
			$data = array("empresa" => $dat->nombre, "imagen" => $logo, 'fecha' => $this->input->post("fecha1"));
			$pdf->setear($data);
			$pdf->addPage();

			$l = array(
				's' => 10,
				'c' => 50,
				'v' => 50,
			);
			$array_data = array(
				array('', $l['s'], "C"),
				array("DATOS", $l['c'], "L"),
				array("DETALLE", $l['v'], "L"),
			);
			$pdf->SetFont('Arial', 'B', 10);
			$pdf->LineWrite($array_data);

			$pdf->SetFont('Arial', '', 10);
			$array_data = array(
				array('', $l['s'], "C"),
				array("Nombre", $l['c'], "L"),
				array($vg->nombre, $l['v'], "L"),
			);
			$pdf->LineWrite($array_data);

			$array_data = array(
				array('', $l['s'], "C"),
				array("Fecha de Compra", $l['c'], "L"),
				array(d_m_Y($vg->fecha), $l['v'], "L"),
			);
			$pdf->LineWrite($array_data);
			$pdf->Ln(5);

			$pdf->SetFont('Arial', 'B', 9);
			$l = array(
				's' => 10,
				'ma' => 30,
				'mo' => 30,
				'ime' => 65,
				'con' => 20,
				'tim' => 45
			);
			$array_data = array(
				array('', $l['s'], "C"),
				array('Marca', $l['ma'], "L"),
				array('Modelo', $l['mo'], "L"),
				array('Imei', $l['ime'], "C"),
				array('Condición', $l['con'], "C"),
				array('Tiempo de Garantia (Dias)', $l['tim'], "C"),

			);
			$pdf->LineWriteB($array_data);

			$pdf->SetFont('Arial', '', 9);

			$dat = $this->ventas->get_detail_ci($id);
			foreach ($dat as $key) {

				$im =  $this->ventas->get_imei_productos($key->id_detalle);
				$imei = "";

				if ($im) {
					// code...
					$arim = array();
					$p = 0;
					foreach ($im as $ke) {
						$arim[$p] = $ke->imei;
						$p++;
					}

					$imei = implode(", ", $arim);
				}
				$array_data = array(
					array('', $l['s'], "C"),
					array($key->cantidad . "x " . $key->marca, $l['ma'], "L"),
					array($key->modelo, $l['mo'], "L"),
					array($imei, $l['ime'], "C"),
					array($key->condicion, $l['con'], "C"),
					array($key->garantia, $l['tim'], "C"),

				);
				$pdf->LineWriteB($array_data);
			}
			$pdf->Ln(5);
			//function SetStyle($tag, $family, $style, $size, $color, $indent=-1)
			$pdf->SetStyle("p", "arial", "N", 9, "0,0,0", 0);
			$pdf->SetStyle("b", "arial", "BN", 9, "0,0,0");

			$dat = $this->ventas->get_one_row("report_parrafo", array('tipo' => "GarantiaE",));
			$pdf->WriteTag(0, 4, $dat->texto, 0, "J", 0, 0);
			$pdf->Ln(5);

			$this->db->where("tipo", "GarantiaEX");
			$this->db->where("id_sucursal", $id_sucursal);
			$this->db->order_by('orden', 'ASC');
			$query = $this->db->get("report_detail");

			$dat = $query->result();
			$l = array(
				's' => 9,
				'c' => 5,
				'v' => 192,
			);
			foreach ($dat as $key) {
				// code...
				$array_data = array(
					array('', $l['s'], "C"),
					array(("*"), $l['c'], "R"),
					array($key->texto, $l['v'], "L"),
				);
				$pdf->LineWrite($array_data);
			}

			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(205, 10, "Procedimiento a seguir en caso de garantía:", 0, 1, 'L');

			$this->db->where("tipo", "GarantiaEC");
			$this->db->where("id_sucursal", $id_sucursal);
			$this->db->order_by('orden', 'ASC');
			$query = $this->db->get("report_detail");

			$pdf->SetFont('Arial', '', 9);
			$dat = $query->result();
			$i = 1;
			foreach ($dat as $key) {
				// code...
				$array_data = array(
					array('', $l['s'], "C"),
					array(("$i."), $l['c'], "R"),
					array($key->texto, $l['v'], "L"),
				);
				$pdf->LineWrite($array_data);
				$i++;
			}

			$pdf->Output();
		} else {
			redirect('errorpage');
		}
	}
	public function detalle_servicio($id = 0)
	{
		if ($id == 0) {
			$id_producto = $this->input->post("id");
			$id_color = $this->input->post("id_s");
			$id_venta = $this->input->post("id_venta");
		}
		//SELECT `id_servicio`, `id_categoria`, `nombre`, `costo_s_iva`, `costo_c_iva`, `cesc`,
		//`precio_sugerido`, `precio_minimo`, `dias_garantia`, `activo`, `deleted` FROM `servicio` WHERE 1
		$prods = $this->ventas->get_row_servicios($id_producto);
		$xdatos["precio_sugerido"] = $prods->precio_sugerido;
		$xdatos["precio_minimo"] = $prods->precio_minimo;
		$xdatos["costo"] = number_format($prods->costo_s_iva, 2, ".", "");
		$xdatos["costo_iva"] = number_format($prods->costo_c_iva, 2, ".", "");
		echo json_encode($xdatos);
	}

	/**
	 * returns the information and the stock of a product in a branch
	 *
	 * @param int $id_producto the id of the product to search
	 * @param int $id_preventa id of the "preventa" to check stock reserve
	 *
	 * returns the detail of a product and its stock, subtracting the stock
	 * that is reserved for pre-sales
	 *
	 * @return object
	 */
	public function detalle_producto($id_producto = 0, $id_preventa = 0)
	{
		// identify "sucursal"
		$id_sucursal = $this->session->id_sucursal;

		// ?
		if ($id_producto == 0) {
			$id_producto	= $this->input->post("id");
			$clasifica		= $this->input->post("clasifica");
			$id_color 		= $this->input->post("id_s");
			$id_venta 		= $this->input->post("id_venta");
		}

		// will store the available prices of the product
		$lista = "";

		if ($id_color != -1)
			$stock_data = $this->ventas->get_stock($id_producto, $id_color, $id_sucursal);

		$prods 		= $this->ventas->get_producto($id_producto);
		$preciosS 	= $this->ventas->get_detail_rows("producto_precio", array('id_producto' => $id_producto, 'id_listaprecio' => $clasifica));
		$precios 	= $this->ventas->get_detail_rows("producto_precio", array('id_producto' => $id_producto));
		$colores 	= $this->ventas->get_detail_rows("producto_color", array('id_producto' => $id_producto,));

		$reservado = $this->ventas->get_reserved_stock($id_sucursal, $id_producto, $id_color, $id_venta);

		$color_select = "";
		if ($colores) {
			$color_select .= "<select class='form-control color' style='width:100%;'>";
			foreach ($colores as $key) {
				$color_select .= "<option value='" . $key->id_color . "'>" . $key->color . "</option>";
			}
			$color_select .= "/<select>";
		} else {
			$color_select .= "<select class='form-control color sel' style='width:100%;'>";
			$color_select .= "<option value='0'>SIN COLOR</option>";
			$color_select .= "/<select>";
		}

		$lista .= "<select class='form-control precios sel' style='width:100%;'>";
		$costo = 0;
		$costo_iva = 0;
		//echo $preciosS[0]->id_precio."#";
		foreach ($precios as $row_por) {
			$id_porcentaje = $row_por->id_precio;
			$costo = $row_por->costo;
			$costo_iva = $row_por->costo_iva;

			$precio = $row_por->porcentaje;
			if ($preciosS[0]->id_precio == $row_por->id_precio) {
				$lista .= "<option value='" . $precio . "' precio='" . $precio . "' id_precio='" . $row_por->id_precio . "' selected>" . number_format($precio, 2, ".", ",") . "</option>";
			} else {
				// code...
				$lista .= "<option value='" . $precio . "' precio='" . $precio . "' id_precio='" . $row_por->id_precio . "'>" . number_format($precio, 2, ".", ",") . "</option>";
			}
		}
		$lista .= "</select>";
		$xdatos["precio_sugerido"] = $prods->precio_sugerido;
		$xdatos["precio_ini"]      = $precio;
		$xdatos["precios"]         = $lista;
		$xdatos["reservado"]       = $reservado;
		$xdatos["stock"]           = $stock_data->cantidad - $reservado;
		$xdatos["id_s"]            = $stock_data->id_stock;
		$xdatos["costo"]           = number_format($costo, 2, ".", "");
		$xdatos["costo_iva"]       = number_format($costo_iva, 2, ".", "");
		echo json_encode($xdatos);
	}
	public function precios_producto($id = 0, $precioe = 0)
	{
		$id_sucursal = $this->session->id_sucursal;

		$precios = $this->ventas->get_precios_exis($id);

		// $precios=$this->ventas->get_detail_rows("producto_precio",array('id_producto' =>$id_producto,'id_listaprecio'=>$clasifica));
		$lista = "";
		$lista .= "<select class='form-control precios sel' style='width:100%;'>";
		$costo = 0;
		$costo_iva = 0;
		foreach ($precios as $row_por) {
			$id_porcentaje = $row_por->id_precio;
			$costo = $row_por->costo;
			$costo_iva = $row_por->costo_iva;
			//$precio = $row_por->total_iva;
			$precio = $row_por->precio_venta;
			$lista .= "<option value='" . $precio . "' precio='" . $precio . "'";
			if ($precio == $precioe) {
				$lista .= " selected ";
			}
			$lista .= ">$" . number_format($precio, 2, ".", ",") . "</option>";
		}
		$lista .= "</select>";
		$xdatos["precios"] = $lista;
		return $xdatos;
	}
	function get_productos()
	{
		$query = $this->input->post("query");
		$id_sucursal = $this->input->post("id_sucursal");
		$rows = $this->ventas->get_productos($query, $id_sucursal);
		$rows2 = $this->ventas->get_servicios($query, $id_sucursal);
		$output = array();
		if ($rows != NULL) {
			foreach ($rows as $row) {
				$output[] = array(

					'producto' => $row->id_producto . "|" . $row->nombre ." " . $row->color . "|" . $row->id_color,
				);
			}
		}
		if ($rows2 != NULL) {
			foreach ($rows2 as $row) {
				$output[] = array(

					'producto' => $row->id_servicio . "|" . $row->nombre . " (SERVICIO)" . "|" . "SERVICIO",
				);
			}
		}
		echo json_encode($output);
	}
	function get_clientes()
	{
		$query = $this->input->post("query");
		$rows = $this->ventas->get_clientes($query);
		$output = array();
		if ($rows != NULL) {
			foreach ($rows as $row) {
				$output[] = array(
					//'producto' => $row->id_producto."|".$row->nombre." ".$row->marca." ".$row->modelo,
					'cliente' => $row->id_cliente . "|" . $row->nombre . "|" . $row->clasifica,
				);
			}
		}
		echo json_encode($output);
	}
	function get_porcent_cliente()
	{
		$clasifica = $this->input->post("clasifica");
		$row = $this->ventas->get_one_row("clientes", array('activo' => 1, 'deleted' => 0, 'id_cliente' => $clasifica));
		$porcent_clasifica = 0;
		$mostrador =0; //si en tabla clientes es mostrador!!!
		if ($row != NULL) {
			$porcent_clasifica = $row->clasifica;
			$mostrador         =$row->mostrador;
		}
		$xdatos["porc_clasifica"] = $porcent_clasifica;
		$xdatos["mostrador"] = $mostrador;
		$xdatos["type"] = "success";
		$xdatos['title'] = 'Alerta';
		$xdatos["msg"] = "Cliente Seleccionado";
		echo json_encode($xdatos);
	}
	function facturar()
	{
		if ($this->input->method(TRUE) == "GET") {
			$tipodoc =	$this->ventas->get_tipodoc();
			$id_usuario = $this->session->id_usuario;
			$id_sucursal = $this->session->id_sucursal;
			$fecha = date('Y-m-d');
			$row_ap = $this->ventas->get_caja_activa($id_sucursal, $fecha);
			$usuario_ap = NULL;
			if ($row_ap != NULL) {
				$id_apertura = $row_ap->id_apertura;
				$usuario_ap =	$this->ventas->get_one_row("usuario", array('id_usuario' => $row_ap->id_usuario,));
			}
			$row_clientes = $this->ventas->get_detail_rows("clientes", array('null' => -1,)); //
			$data = array(
				"sucursal" => $this->ventas->get_detail_rows("sucursales", array('1' => 1,)),
				"id_sucursal" => $this->session->id_sucursal,
				"tipodoc" => $tipodoc,
				"row_ap" => $row_ap,
				"row_clientes" => $row_clientes,
				"id_usuario" => $id_usuario,
				"usuario_ap" => $usuario_ap,
			);

			$extras = array(
				'css' => array(
					"css/scripts/ventas.css"
				),
				'js' => array(
					"js/scripts/ventas.js"
				),
			);

			layout("ventas/facturar", $data, $extras);
		} else if ($this->input->method(TRUE) == "POST") {

			$this->utils->begin();
			$concepto = "VENTA";
			$fecha = Y_m_d($this->input->post("fecha"));
			$total = $this->input->post("total");

			$id_cliente = $this->input->post("client");
			$data_ingreso = json_decode($this->input->post("data_ingreso"), true);
			$id_sucursal = $this->input->post("id_sucursal");
			$tipodoc = $this->input->post("tipodoc");
			$id_usuario = $this->session->id_usuario;
			$hora = date("H:i:s");
			$fecha_corr = $this->ventas->get_date_correlative($id_sucursal);

			$row_ap = $this->ventas->get_caja_activa($id_sucursal, $fecha);
			$id_apertura = $row_ap->id_apertura;
			$caja = $row_ap->caja;
			/*
			if($fecha==$fecha_corr){
				$referencia = $this->ventas->get_correlative('refdia',$id_sucursal);
	       $this->utils->update("correlativo",array("refdia" =>$referencia, ),"id_sucursal=".$id_sucursal);
			}else{
					$referencia =1;
					$this->utils->update("correlativo",array('fecha' => $fecha,"refdia" =>$referencia, ),"id_sucursal=".$id_sucursal);
			}
			*/
			$referencia = 0;
			switch ($tipodoc) {
				case 1:
					$correlativo = $this->ventas->get_correlative('tik', $id_sucursal);
					break;
				case 2:
					$correlativo = $this->ventas->get_correlative('cof', $id_sucursal);
					break;
				case 3:
					$correlativo = $this->ventas->get_correlative('ccf', $id_sucursal);
					break;
			}

			$data = array(
				'fecha' => $fecha,
				'hora' => $hora,
				'concepto' => $concepto,
				'indicaciones' => "VENTA DE PRODUCTOS Y SERVICIOS",
				'id_cliente' => $id_cliente,
				'id_estado' => 1, //probar cambiar a estado 1 pendiente para ver lo de actualizarlo a la hora de activar form data_client finalizar!!!!!!!
				'id_sucursal_despacho' => $id_sucursal,
				'correlativo' => $correlativo,
				'total' => $total,
				'id_sucursal' => $id_sucursal,
				'id_usuario' => $id_usuario,
				'tipo_doc' => $tipodoc,
				'referencia' => 0,
				'requiere_imei ' => 0,
				'imei_ingresado' => 0,
				'guia' => "",
				'caja' => $caja,
				'id_apertura' => $id_apertura,
			);

			$id_factura = $this->ventas->inAndCon('ventas', $data);
			if ($id_factura != NULL) {
				if ($data_ingreso != NULL) {
					foreach ($data_ingreso as $fila) {
						$id_producto = $fila['id_producto'];
						$costo = $fila['costo'];
						$cantidad = $fila['cantidad'];
						$precio_sugerido = $fila['precio_sugerido'];
						$descuento = $fila['descuento'];
						$precio_final = $fila['precio_final'];
						$subtotal = $fila['subtotal'];
						$color = $fila['color'];
						$tipo = $fila['tipo']; //"0:PRODUCTO,1:SERVICIO"

						$estado = $fila['est'];

						$form_data = array(
							'id_venta' => $id_factura,
							'id_producto' => $id_producto,
							'id_color' => $color,
							'costo' => $costo,
							'precio' => $precio_sugerido,
							'precio_fin' => $precio_final,
							'descuento' => $descuento,
							'cantidad' => $cantidad,
							'subtotal' => $subtotal,
							'condicion' => $estado,
							'tipo_prod' => $tipo,
							'garantia' =>  0,
						);
						$id_detalle = $this->ventas->inAndCon('ventas_detalle', $form_data);
						if ($tipo == 0) {
							$stock_data = $this->ventas->get_stock($id_producto, $color, $id_sucursal);
							$newstock = ($stock_data->cantidad) - $cantidad;
							$this->utils->update("stock", array('cantidad' => $newstock,), "id_stock=" . $stock_data->id_stock);
						}

					}
				}

				$this->utils->commit();
				$xdatos = [
					"type"       => "success",
					'title'      => 'Información',
					"msg"        => "Registo ingresado correctamente!",
					"id_factura" => $id_factura,
					"proceso"    => "facturar"
				];
			} else {
				$this->utils->rollback();
				$xdatos = [
					"type"  => "error",
					"title" => 'Alerta',
					"msg"   => "Error al ingresar el registro"
				];
			}


			echo json_encode($xdatos);
		}
	}

	/**
	 * Show the interface to finalize "preventa"
	 *
	 * @return void
	 */
	function finalizaref()
	{
		// check the request method
		if ($this->input->method(TRUE) == "GET") {

			// Get the server time, user id and server id to search for
			// cash opening
			$fecha       = date('Y-m-d');
			$id_usuario  = $this->session->id_usuario;
			$id_sucursal = $this->session->id_sucursal;

			// identify document type
			$tipodoc     = $this->ventas->get_tipodoc();

			// search for cash opening
			$row_ap = $this->ventas->get_caja_activa($id_sucursal, $fecha);
			$usuario_ap = NULL;
			if ($row_ap != NULL) {
				$usuario_ap =	$this->ventas->get_one_row(
					"usuario",
					array('id_usuario' => $row_ap->id_usuario)
				);
			}
			//tipo_pago
			$row_tipopago = $this->ventas->get_detail_rows("tipo_pago", array('null' => -1,));
			// ?
			$row_clientes = $this->ventas->get_detail_rows(
				"clientes",array('deleted' => 0,'activo'=>1));

			// Prepare the information to be displayed in the view
			$data = array(
				"id_sucursal"  => $this->session->id_sucursal,
				"tipodoc"      => $tipodoc,
				"row_ap"       => $row_ap,
				"row_clientes" => $row_clientes,
				"id_usuario"   => $id_usuario,
				"usuario_ap"   => $usuario_ap,
				"tipo_pago"		 => $row_tipopago,
				"sucursal"     => $this->ventas->get_detail_rows(
					"sucursales",
					array('1' => 1,)
				),
			);

			// indicate js and css files to use
			$extras = array(
				'css' => array("css/scripts/ventas.css"),
				'js' => array("js/scripts/ventas.js"),
			);

			// show view
			layout("ventas/finalizaref", $data, $extras);
		}
	}

	/**
	 * load a sale by "referencia"
	 *
	 * @return object
	 */
	function cargar_venta($id = -1)
	{
		// check the request method
		if ($this->input->method(TRUE) == "POST") {

			// get parameters sent in the request
			$fecha      = date('Y-m-d');
			$referencia = $this->input->post("referencia");

			// get sale header
			$venta = $this->ventas->get_one_row(
				"ventas",
				array(
					"referencia" => $referencia,
					"fecha" => $fecha,
					"id_estado" => 1,
				)
			);

			if ($venta != NULL) {

				// get details of the sale
				$id_venta = $venta->id_venta;
				$detalles = $this->ventas->get_detail_ci($id_venta);

				// get customer information
				$rowc = $this->ventas->get_one_row(
					"clientes",
					array('id_cliente' => $venta->id_cliente,)
				);

				$detalles1 = array();
				if ($detalles != NULL) {

					foreach ($detalles as $detalle) {
						$id_producto = $detalle->id_producto;
						$precio = $detalle->precio;
						$qty_sold = $detalle->cantidad;


						// get product prices
						$precios = $this->ventas->get_detail_rows(
							"producto_precio",
							array('id_producto' => $id_producto)
						);

						// get product stock
						$stock_data = $this->ventas->get_stock(
							$id_producto,
							$detalle->id_color,
							$venta->id_sucursal
						);

						// create price select ----------------------------
						$lista = "
						<select
							class='form-control precios sel' style='width:100%;'>";

						foreach ($precios as $row_por) {
							$precio = $row_por->porcentaje;

							if ($detalle->id_precio_producto == $row_por->id_precio) {
								$lista .= "
								<option
									value='" . $precio . "'
									precio='" . $precio . "'
									id_precio='" . $row_por->id_precio . "'
									selected>"
									. number_format($precio, 2, ".", ",") .
									"</option>";
							} else {
								$lista .= "
								<option
									value='" . $precio . "'
									precio='" . $precio . "'
									id_precio='" . $row_por->id_precio .
									"'>" .
									number_format($precio, 2, ".", ",") .
									"</option>";
							}
						}
						$lista .= "</select>";
						// ------------------------------------------------

						// get reserve stock
						$reservado = $this->ventas->get_reserved_stock(
							$venta->id_sucursal,
							$id_producto,
							$detalle->id_color,
							$id_venta
						);

						// prepare product detail
						$detalle->precios   = $lista;
						$detalle->stock     = $stock_data->cantidad - $reservado;
						$detalle->id_s  = $stock_data->id_stock;
						$detalle->id_color  = $stock_data->id_color;

						// create status select ---------------------------
						$estado = "<select class='est'>";
						if ($detalle->condicion == "NUEVO") {
							$estado .= "<option selected value='NUEVO'>NUEVO</option>";
							$estado .= "<option value='USADO'>USADO</option>";
						} else {
							$estado .= "<option value='NUEVO'>NUEVO</option>";
							$estado .= "<option selected value='USADO'>USADO</option>";
						}
						$estado .= "</select>";
						// ------------------------------------------------

						$detalle->estado = $estado;

						array_push($detalles1, $detalle);
					}
				}

				$detalleservicios = $this->ventas->get_detail_serv($id_venta);
				$fecha_dmy = d_m_Y($venta->fecha);

				// order sale data
				$xdatos = [
					"id_cliente" => $venta->id_cliente,
					"venta"      => $venta,
					"id_venta"   => $id_venta,
					"fecha"      => $fecha_dmy,
					"total"      => $venta->total,
					"tipo_doc"   => $venta->tipo_doc,
					"detprod"    => $detalles1,
					"detserv"    => $detalleservicios,
					"type"       => "success",
					"title"      => "Información",
					"msg"        => "Venta cargada correctamente!",
					"proceso"    => "cargar"
				];
			} else {
				$xdatos = [
					"type"    => "error",
					'title'   => 'Información',
					"msg"     => "Referencia de Venta no Encontrada!",
					"proceso" => "finalizar",
				];
			}

			// response
			echo json_encode($xdatos);
		}
	}

	//cargar datos de  venta por referencia
	function cargar_ref($id = -1)
	{

		if ($this->input->method(TRUE) == "GET") {
			//$referencia= $this->uri->segment(3);
			$referencia = $this->input->get("referencia");
			$fecha = date('Y-m-d');

			$venta =	$this->ventas->get_one_row("ventas", array('referencia' => $referencia, 'fecha' => $fecha,));
			$row =	$this->ventas->get_one_row("ventas", array('referencia' => $referencia, 'fecha' => $fecha,));
			$id = $venta->id_venta;

			$id_usuario = $this->session->id_usuario;
			$fecha = date('Y-m-d');
			$id_sucursal = $this->session->id_sucursal;
			$row_ap = $this->ventas->get_caja_activa($id_sucursal, $fecha);
			$usuario_ap = NULL;
			if ($row_ap != NULL) {
				$id_apertura = $row_ap->id_apertura;
				$usuario_ap =	$this->ventas->get_one_row("usuario", array('id_usuario' => $row_ap->id_usuario,));
			}
			$row_clientes = $this->ventas->get_detail_rows("clientes", array('null' => -1,)); //
			//fin apertura caja


			$rowc = $this->ventas->get_one_row("clientes", array('id_cliente' => $row->id_cliente,));
			$rowpc = $this->ventas->get_porcent_client($rowc->clasifica);

			$detalles = $this->ventas->get_detail_ci($id);
			$detalleservicios = $this->ventas->get_detail_serv($id);
			$tipodoc =	$this->ventas->get_tipodoc();
			$detalles1 = array();
			if ($detalles != NULL) {
				foreach ($detalles as $detalle) {
					$id_producto = $detalle->id_producto;
					$precio = $detalle->precio;
					$detallesp = $this->precios_producto($id_producto, $precio);
					if ($detallesp != 0) {
						$stock_data = $this->ventas->get_stock($id_producto, $detalle->id_color, $row->id_sucursal);
						$detalle->precios = $detallesp["precios"];
						$detalle->stock = $stock_data->cantidad;
						$detalle->id_stock = $stock_data->id_stock;
						$detalle->id_color = $stock_data->id_color;

						$d = $this->ventas->get_reservado($id_producto, $id, $detalle->id_color);
						$detalle->reservado = $d->reservado;

						$estado = "<select class='est'>";
						if ($detalle->condicion == "NUEVO") {
							$estado .= "<option selected value='NUEVO'>NUEVO</option>";
							$estado .= "<option value='USADO'>USADO</option>";
						} else {
							$estado .= "<option value='NUEVO'>NUEVO</option>";
							$estado .= "<option selected value='USADO'>USADO</option>";
						}
						$estado .= "</select>";

						$detalle->estado = $estado;
					}
					array_push($detalles1, $detalle);
				}
			}
			if ($row && $id != "") {

				$id_usuario = $this->session->id_usuario;
				$fecha = date('Y-m-d');
				$data = array(
					"row" => $row,
					"detalles" => $detalles1,
					"detalleservicios" => $detalleservicios,
					'tipodoc' => $tipodoc,
					'rowpc' => $rowpc,
					"sucursal" => $this->ventas->get_detail_rows("sucursales", array('1' => 1,)),
					"id_sucursal" => $row->id_sucursal,
					"rowc" => $rowc,
					"row_clientes" => $row_clientes,
					"id_usuario" => $id_usuario,
					"row_ap" => $row_ap,
					"usuario_ap" => $usuario_ap,
				);
				$extras = array(
					'css' => array(
						"css/scripts/ventas.css"
					),
					'js' => array(
						"js/scripts/ventas.js"
					),
				);
				layout("ventas/finalizaref", $data, $extras);
			} else {
				redirect('errorpage');
			}
		} else if ($this->input->method(TRUE) == "POST") {
			$referencia = $this->input->post("referencia");
			$xdatos["type"] = "success";
			$xdatos['title'] = 'Información';
			$xdatos["msg"] = "Venta guardada correctamente!";
			$xdatos["proceso"] = "finalizar";

			echo json_encode($xdatos);
		}
	}
	//impresion documentos
	function print_ticket($id_venta, $id_sucursal, $correlativo, $total, $rowvta)
	{
		//encabezado
		$id_usuario = $rowvta->id_usuario;
		$row_hf = $this->ventas->get_one_row("config_pos", array('id_sucursal' => $id_sucursal, 'alias_tipodoc' => 'TIK',));
		$row_user = $this->ventas->get_one_row("usuario", array('id_usuario' => $id_usuario,));
		$hstring = "";
		// probar para set imagen to raste print
		// instalar php-imagick en server

		$img1=base_url(getLogo());
		$line1 = str_repeat("_", 42) . "\n";

		$hstring .= chr(27) . chr(33) . chr(16); //FONT double size
		$hstring .= chr(27) . chr(97) . chr(1); //Center

		if ($row_hf->header1 != '')
			$hstring .= chr(13) . $row_hf->header1 . "\n";
		$hstring .= chr(27) . chr(33) . chr(0); //FONT A normal size
		if ($row_hf->header2 != '')
			$hstring .= chr(13) . $row_hf->header2 . "\n";
		if ($row_hf->header3 != '')
			$hstring .= chr(13) . $row_hf->header3 . "\n";
		if ($row_hf->header4 != '')
			$hstring .= chr(13) . $row_hf->header4 . "\n";
		if ($row_hf->header5 != '')
			$hstring .= chr(13) . $row_hf->header5 . "\n";
		if ($row_hf->header6 != '')
			$hstring .= chr(13) . $row_hf->header6 . "\n";
		if ($row_hf->header7 != '')
			$hstring .= chr(13) . $row_hf->header7 . "\n";
		if ($row_hf->header8 != '')
			$hstring .= chr(13) . $row_hf->header8 . "\n";
		if ($row_hf->header9 != '')
			$hstring .= chr(13) . $row_hf->header9 . "\n";
		if ($row_hf->header10 != '')
			$hstring .= chr(13) . $row_hf->header10 . "\n";

		//pie
		if ($row_hf->footer1 != '')
			$pstring = chr(13) . $row_hf->footer1 . "\n";
		if ($row_hf->footer2 != '')
			$pstring .= chr(13) . $row_hf->footer2 . "\n";
		if ($row_hf->footer3 != '')
			$pstring .= chr(13) . $row_hf->footer3 . "\n";
		if ($row_hf->footer4 != '')
			$pstring .= chr(13) . $row_hf->footer4 . "\n";
		if ($row_hf->footer5 != '')
			$pstring .= chr(13) . $row_hf->footer5 . "\n";
		if ($row_hf->footer6 != '')
			$pstring .= chr(13) . $row_hf->footer6 . "\n";
		if ($row_hf->footer7 != '')
			$pstring .= chr(13) . $row_hf->footer7 . "\n";
		if ($row_hf->footer8 != '')
			$pstring .= chr(13) . $row_hf->footer8 . "\n";
		if ($row_hf->footer9 != '')
			$pstring .= chr(13) . $row_hf->footer9 . "\n";
		if ($row_hf->footer10 != '')
			$pstring .= chr(13) . $row_hf->footer10 . "\n";
		//detalles productos
		$det_ticket = "";
		$date1 = new DateTime($rowvta->fecha." ".$rowvta->hora);
    $hora= $date1->format("g"). ':' .$date1->format("i"). ' ' .$date1->format("A");
		$hstring .= chr(13) . " FECHA: " .	d_m_Y($rowvta->fecha) . " HORA:" . $hora . "\n";
		$hstring .= chr(13) . " CAJA #: " . $rowvta->caja . "\n";
		$hstring .= chr(13) . " CAJERO: " . $row_user->nombre . "\n";
		$tiq = str_pad($correlativo, 10, '0', STR_PAD_LEFT);
		$hstring .= chr(13) . " TICKET #: " . $tiq . "\n";
		$det_ticket .= chr(27) . chr(97) . chr(0); //Left
		$det_ticket .= chr(13) . $line1 . "\n"; // Print text Lin
		//$det_ticket.=chr(13)."\n"; // Print text
		//$det_ticket.= chr(27).chr(97).chr(0); //Center
		$th = chr(13) . " DESCRIPCION    CANT.    P.U      SUBTOTAL" . "\n";
		$det_ticket .= chr(13) . $th;
		$det_ticket .= chr(13) . $line1;
		$detalleproductos = $this->ventas->get_detail_ci($id_venta);
		$espacio = " ";
		$margen_izq1 = AlignMarginText::leftmargin($espacio, 2);
		$margen_izq2 = AlignMarginText::leftmargin($espacio, 3);

		if ($detalleproductos != NULL) {
			foreach ($detalleproductos as $detalle) {
				$id_producto = $detalle->id_producto;
				$descripcion = $detalle->nombre . " " . $detalle->color;
				$precio_fin = "$ " . $detalle->precio_fin;
				$cantidad = $detalle->cantidad;
				$subtotal = "$ " . $detalle->subtotal;
				$desc = AlignMarginText::onelineleft($descripcion, 32, 1, $espacio);
				$pre = AlignMarginText::rightaligner($precio_fin, $espacio, 12);
				$cant = AlignMarginText::rightaligner($cantidad, $espacio, 5);
				$subt = AlignMarginText::rightaligner($subtotal, $espacio, 12);
				$det_ticket .= $desc . "\n";
				$det_ticket .= $margen_izq2 . $cant . " X " . $margen_izq1 . $pre . $margen_izq1 . " = " . $subt . "\n";
			}
		}
		//detalles servicios
		$detalleservicios = $this->ventas->get_detail_serv($id_venta);
		if ($detalleservicios != NULL) {
			foreach ($detalleservicios as $detalle) {
				$id_producto = $detalle->id_producto;
				$descripcion = $detalle->nombre;
				$precio_fin = "$ " . $detalle->precio_fin;
				$cantidad = $detalle->cantidad;
				$subtotal = "$ " . $detalle->subtotal;
				$desc = AlignMarginText::onelineleft($descripcion, 32, 1, $espacio);
				//$espacio="#";
				$pre = AlignMarginText::rightaligner($precio_fin, $espacio, 12);
				$cant = AlignMarginText::rightaligner($cantidad, $espacio, 5);
				$subt = AlignMarginText::rightaligner($subtotal, $espacio, 12);
				$det_ticket .= $desc . "\n";
				$det_ticket .= $margen_izq2 . $cant . " X " . $margen_izq1 . $pre . $margen_izq1 . " = " . $subt . "\n";
			}
		}
		$det_ticket .= chr(13) . $line1;
		$det_ticket .= chr(27) . chr(33) . chr(0); //FONT A
		$det_ticket .= chr(27) . chr(97) . chr(1); //Center align
		$totales = chr(27) . chr(33) . chr(16); //FONT A
		$totales .= chr(27) . chr(97) . chr(2); //Right align
		$totals = "  TOTAL   $ " . $total . "   " . "\n";
		$lentot = strlen($totals);
		$totales .= $totals;
		$totales .= chr(27) . chr(33) . chr(0); //FONT A
		$l2 = str_repeat("_", $lentot) . "\n";
		$totales .= $l2;
		$xdatos["encabezado"] = $hstring;
		$xdatos["totales"] = $totales;
		$xdatos["cuerpo"] = $det_ticket;
		$xdatos["pie"] = $pstring;
		$xdatos["img"] = $img1;
		return $xdatos;
	}
	function print_cof($id_venta, $id_sucursal, $rowvta)
	{
		//Cliente
		$row_cte = $this->ventas->get_one_row("clientes", array('id_cliente' => $rowvta->id_cliente,));

		list($anio, $mes, $dia) = explode("-", $rowvta->fecha);
		//inicio header print_cof
		$det_factura = "";
		$hstring = "";
		$espacio = " ";
		for ($n = 0; $n < 8; $n++) {
			//$hstring.= chr(10); //Line Feed
			$hstring .= chr(13) . "\n"; // Print text
		}
		$nombre = wordwrap(strtoupper($row_cte->nombre), 60);
		$direccion = wordwrap(strtoupper($row_cte->direccion), 65);
		$sp = AlignMarginText::leftmargin($espacio, 51);
		$hstring .= $sp . $dia . "  -  " . $mes . "   -  " . $anio . "\n";
		$sp1 = AlignMarginText::leftmargin($espacio, 10);
		$hstring .= chr(13) . "\n\n"; // Print text
		$hstring .= chr(13) . $sp1 . $nombre . "\n";
		$hstring .= chr(13) . $sp1 . "  " . $direccion . "\n";
		for ($n = 0; $n < 3; $n++) {
			$hstring .= chr(13) . "\n"; // Print text
		}

		$detalleproductos = $this->ventas->get_detail_ci($id_venta);
		$margen_izq0 = AlignMarginText::leftmargin($espacio, 1);
		$margen_izq = AlignMarginText::leftmargin($espacio, 3);
		$lineas = 0;
		if ($detalleproductos != NULL) {
			foreach ($detalleproductos as $detalle) {
				$id_producto = $detalle->id_producto;
				$descripcion = $detalle->nombre . " " . $detalle->marca . " " . $detalle->modelo . " " . $detalle->color;
				$precio_fin = $detalle->precio_fin;
				$cantidad = $detalle->cantidad;
				$subtotal = $detalle->subtotal;
				$cant = AlignMarginText::rightaligner($cantidad, $espacio, 6);
				$desc = AlignMarginText::onelineleft($descripcion, 42, 2, $espacio);
				$pre = "$ " . AlignMarginText::rightaligner($precio_fin, $espacio, 10);
				$subt = "$ " . AlignMarginText::rightaligner($subtotal, $espacio, 12);
				$det_factura .= $cant . $desc . $margen_izq0 . $pre . $margen_izq . $subt . " \n";
				$lineas++;
			}
		}
		//detalles servicios
		$detalleservicios = $this->ventas->get_detail_serv($id_venta);

		if ($detalleservicios != NULL) {
			foreach ($detalleservicios as $detalle) {
				$id_producto = $detalle->id_producto;
				$descripcion = $detalle->nombre;
				$precio_fin = $detalle->precio_fin;
				$cantidad = $detalle->cantidad;
				$subtotal = $detalle->subtotal;
				$cant = AlignMarginText::rightaligner($cantidad, $espacio, 6);
				$desc = AlignMarginText::onelineleft($descripcion, 42, 2, $espacio);
				$pre = "$ " . AlignMarginText::rightaligner($precio_fin, $espacio, 10);
				$subt = "$ " . AlignMarginText::rightaligner($subtotal, $espacio, 12);
				$det_factura .= $cant . $desc . $margen_izq0 . $pre . $margen_izq . $subt . " \n";
				$lineas++;
			}
		}
		$nlineas = 20; //numero de lineas maxima para el formato
		$lin_add = 0;
		if ($lineas <= $nlineas) {
			$lin_add = $nlineas - $lineas + 1;
		}
		for ($n = 0; $n < $lin_add; $n++) {
			//$hstring.= chr(10); //Line Feed
			$det_factura .= chr(13) . "\n"; // Print text
		}
		//totales
		//	$hstring.=chr(13).$row_cte->nombre."\n";
		$tot_letras = NumeroALetras::convertir($rowvta->total, 'Dolares', false, 'ctvs');
		//$total_letras = wordwrap(strtoupper($tot_letras),30) . "\n";
		$ln_txt = 34;
		$total_let = AlignMarginText::wordwrap1(strtoupper($tot_letras), $ln_txt);
		$tmplinea = array();
		$ln = 0;
		foreach ($total_let as $total_txt1) {
			$ln = $ln + 1;
			$tmplinea[] = trim($total_txt1);
		}
		$totales = "";
		$long_lin_tot = 62;
		$margen_txt_totals = AlignMarginText::leftmargin($espacio, 4);
		$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot - $ln_txt);
		$total_fin = "$ " . AlignMarginText::rightaligner($rowvta->total, $espacio, 13);
		if ($ln == 1) {
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot - strlen($tmplinea[0]));
			$totales .= $margen_txt_totals . $tmplinea[0];
			$totales .= $margen_totals . $total_fin . "\n";
			$totales .= chr(13) . "\n"; // Print text
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot + 4);
			$totales .= $margen_totals . " $ " . $rowvta->total . "\n";
			$totales .= chr(13) . "\n"; // Print text
		}
		if ($ln == 2) {
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot - strlen($tmplinea[0]));
			$totales .= $margen_txt_totals . $tmplinea[0];
			$totales .= $margen_totals . $total_fin . "\n";
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot - strlen($tmplinea[1]));
			$totales .= $margen_txt_totals . $tmplinea[1] . "\n";
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot + 4);
			$totales .= $margen_totals . $total_fin . "\n";
			$totales .= chr(13) . "\n"; // Print text
		}
		if ($ln == 3 || $ln == 4) {
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot - strlen($tmplinea[0]));
			$totales .= $margen_txt_totals . $tmplinea[0];
			$totales .= $margen_totals . $total_fin . "\n";
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot - strlen($tmplinea[1]));
			$totales .= $margen_txt_totals . $tmplinea[1] . "\n";
			$totales .= $margen_txt_totals . $tmplinea[2];
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot - strlen($tmplinea[2]));
			$totales .= $margen_totals . $total_fin . "\n";
			$totales .= chr(13) . "\n"; // Print text
		}
		$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot + 4);
		$totales .= chr(13) . "\n"; // Print text
		$totales .= $margen_totals . $total_fin . "\n";
		$xdatos["encabezado"] = $hstring;
		$xdatos["cuerpo"] = $det_factura;
		$xdatos["totales"] = $totales;
		$xdatos["pie"] = ".";
		return $xdatos;
	}
	function print_ccf($id_venta, $id_sucursal, $rowvta)
	{
		//Cliente
		$row_cte = $this->ventas->get_one_row("clientes", array('id_cliente' => $rowvta->id_cliente,));

		list($anio, $mes, $dia) = explode("-", $rowvta->fecha);
		//inicio header print_cof
		$det_factura = "";
		$hstring = "";
		$espacio = " ";
		for ($n = 0; $n < 8; $n++) {
			//$hstring.= chr(10); //Line Feed
			$hstring .= chr(13) . "\n"; // Print text
		}
		$nombre = wordwrap(strtoupper($row_cte->nombre), 60);
		$direccion = wordwrap(strtoupper($row_cte->direccion), 65);
		$sp = AlignMarginText::leftmargin($espacio, 51);
		$hstring .= $sp . $dia . "  -  " . $mes . "   -  " . $anio . "\n";
		$sp1 = AlignMarginText::leftmargin($espacio, 10);
		$hstring .= chr(13) . "\n\n"; // Print text
		$hstring .= chr(13) . $sp1 . $nombre . "\n";
		$hstring .= chr(13) . $sp1 . "  " . $direccion . "\n";
		for ($n = 0; $n < 3; $n++) {
			$hstring .= chr(13) . "\n"; // Print text
		}

		$detalleproductos = $this->ventas->get_detail_ci($id_venta);
		$margen_izq0 = AlignMarginText::leftmargin($espacio, 1);
		$margen_izq = AlignMarginText::leftmargin($espacio, 3);
		$lineas = 0;
		if ($detalleproductos != NULL) {
			foreach ($detalleproductos as $detalle) {
				$id_producto = $detalle->id_producto;
				$descripcion = $detalle->nombre . " " . $detalle->marca . " " . $detalle->modelo . " " . $detalle->color;
				$precio_fin = $detalle->precio_fin;
				$cantidad = $detalle->cantidad;
				$subtotal = $detalle->subtotal;
				$cant = AlignMarginText::rightaligner($cantidad, $espacio, 6);
				$desc = AlignMarginText::onelineleft($descripcion, 42, 2, $espacio);
				$pre = "$ " . AlignMarginText::rightaligner($precio_fin, $espacio, 10);
				$subt = "$ " . AlignMarginText::rightaligner($subtotal, $espacio, 12);
				$det_factura .= $cant . $desc . $margen_izq0 . $pre . $margen_izq . $subt . " \n";
				$lineas++;
			}
		}
		//detalles servicios
		$detalleservicios = $this->ventas->get_detail_serv($id_venta);

		if ($detalleservicios != NULL) {
			foreach ($detalleservicios as $detalle) {
				$id_producto = $detalle->id_producto;
				$descripcion = $detalle->nombre;
				$precio_fin = $detalle->precio_fin;
				$cantidad = $detalle->cantidad;
				$subtotal = $detalle->subtotal;
				$cant = AlignMarginText::rightaligner($cantidad, $espacio, 6);
				$desc = AlignMarginText::onelineleft($descripcion, 42, 2, $espacio);
				$pre = "$ " . AlignMarginText::rightaligner($precio_fin, $espacio, 10);
				$subt = "$ " . AlignMarginText::rightaligner($subtotal, $espacio, 12);
				$det_factura .= $cant . $desc . $margen_izq0 . $pre . $margen_izq . $subt . " \n";
				$lineas++;
			}
		}
		$nlineas = 20; //numero de lineas maxima para el formato
		$lin_add = 0;
		if ($lineas <= $nlineas) {
			$lin_add = $nlineas - $lineas + 1;
		}
		for ($n = 0; $n < $lin_add; $n++) {
			//$hstring.= chr(10); //Line Feed
			$det_factura .= chr(13) . "\n"; // Print text
		}
		//totales
		//	$hstring.=chr(13).$row_cte->nombre."\n";
		$tot_letras = NumeroALetras::convertir($rowvta->total, 'Dolares', false, 'ctvs');
		//$total_letras = wordwrap(strtoupper($tot_letras),30) . "\n";
		$ln_txt = 34;
		$total_let = AlignMarginText::wordwrap1(strtoupper($tot_letras), $ln_txt);
		$tmplinea = array();
		$ln = 0;
		foreach ($total_let as $total_txt1) {
			$ln = $ln + 1;
			$tmplinea[] = trim($total_txt1);
		}
		$totales = "";
		$long_lin_tot = 62;
		$margen_txt_totals = AlignMarginText::leftmargin($espacio, 4);
		$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot - $ln_txt);
		$total_fin = "$ " . AlignMarginText::rightaligner($rowvta->total, $espacio, 13);
		if ($ln == 1) {
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot - strlen($tmplinea[0]));
			$totales .= $margen_txt_totals . $tmplinea[0];
			$totales .= $margen_totals . $total_fin . "\n";
			$totales .= chr(13) . "\n"; // Print text
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot + 4);
			$totales .= $margen_totals . " $ " . $rowvta->total . "\n";
			$totales .= chr(13) . "\n"; // Print text
		}
		if ($ln == 2) {
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot - strlen($tmplinea[0]));
			$totales .= $margen_txt_totals . $tmplinea[0];
			$totales .= $margen_totals . $total_fin . "\n";
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot - strlen($tmplinea[1]));
			$totales .= $margen_txt_totals . $tmplinea[1] . "\n";
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot + 4);
			$totales .= $margen_totals . $total_fin . "\n";
			$totales .= chr(13) . "\n"; // Print text
		}
		if ($ln == 3 || $ln == 4) {
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot - strlen($tmplinea[0]));
			$totales .= $margen_txt_totals . $tmplinea[0];
			$totales .= $margen_totals . $total_fin . "\n";
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot - strlen($tmplinea[1]));
			$totales .= $margen_txt_totals . $tmplinea[1] . "\n";
			$totales .= $margen_txt_totals . $tmplinea[2];
			$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot - strlen($tmplinea[2]));
			$totales .= $margen_totals . $total_fin . "\n";
			$totales .= chr(13) . "\n"; // Print text
		}
		$margen_totals = AlignMarginText::leftmargin($espacio, $long_lin_tot + 4);
		$totales .= chr(13) . "\n"; // Print text
		$totales .= $margen_totals . $total_fin . "\n";
		$xdatos["encabezado"] = $hstring;
		$xdatos["cuerpo"] = $det_factura;
		$xdatos["totales"] = $totales;
		$xdatos["pie"] = ".";
		return $xdatos;
	}

	function printdoc($id = -1)
	{
		if ($this->input->method(TRUE) == "POST") {
			if ($this->agent->is_browser()) {
				$agent = $this->agent->browser() . ' ' . $this->agent->version();
				$opsys = $this->agent->platform();
			}
			$id_venta = $this->input->post("id_venta");
			$rowvta = $this->ventas->get_one_row("ventas", array('id_venta' => $id_venta,));
			if ($rowvta != NULL) {
				$id_sucursal = $rowvta->id_sucursal;
				$row_confdir = $this->ventas->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));
				$tot_letras = NumeroALetras::convertir($rowvta->total, 'Dolares', false, 'centavos');
				$total_letras = wordwrap(strtoupper($tot_letras), 40) . "\n";
				$tipodoc = $rowvta->tipo_doc;
				switch ($tipodoc) {
					case 1:
						$xdatos = $this->print_ticket($id_venta, $id_sucursal, $rowvta->correlativo, $rowvta->total, $rowvta);
						break;
					case 2:
						$xdatos = $this->print_cof($id_venta, $id_sucursal, $rowvta);
						break;
					case 3:
						$xdatos = $this->print_ccf($id_venta, $id_sucursal, $rowvta);
						break;
				}
				$xdatos["type"] = "success";
				$xdatos['title'] = 'Información';
				$xdatos["msg"] = "Documento impreso correctamente!";

				$xdatos["tipodoc"] = $tipodoc;
				$xdatos["id_client"] = $rowvta->id_cliente;
				$xdatos["total_letras"] = $total_letras;
				$xdatos["opsys"] = $opsys;
				$xdatos["dir_print"] = $row_confdir->dir_print_script; //for Linux
				$xdatos["dir_print_pos"] = $row_confdir->shared_printer_pos; //for win


				echo json_encode($xdatos);
			}
		}
	}
	/* Mostrar modal de Creación de clientes */
	function new_data_client($id = -1)
	{

		if ($this->input->method(TRUE) == "GET") {
				$clasifica_cliente = $this->clientes->get_clasifica_cliente();
				$data = array(
					"clasifica_cliente"=>$clasifica_cliente,
				);
				$this->load->view("ventas/new_client_modal.php", $data);
			} else {
				redirect('errorpage');
			}

	}
	function save_data_client(){
			if ($this->input->method(TRUE) == "POST") {
				$errors = false;
				$this->utils->begin();
				$nomcte = strtoupper($this->input->post("nombre"));
				$nit = $this->input->post("nit");
				$dui = $this->input->post("dui");
				$nrc = $this->input->post("nrc");
				$clasifica= $this->input->post("clasifica");
				$form_data = array(
					'nombre' => $nomcte,
					'nombre_comercial' => $nomcte,
					'direccion' => "SAN MIGUEL, EL SALVADOR",
					'clasifica' => $clasifica,
					'dui'=>$dui,
					'nit'=>$nit,
					'nrc'=>$nrc,
					'departamento'=>13,
					'municipio'=>81,
					'activo' => 1,
				);
					$id_cliente = $this->ventas->inAndCon("clientes", $form_data);
					if ($id_cliente == NULL) {
						$errors = true;
					}
					if ($errors == true) {

						$this->utils->rollback();
						$xdatos["type"] = "error";
						$xdatos['title'] = 'Alerta';
						$xdatos["msg"] = "Error al ingresar el registro";
						$xdatos["id_cliente"] = -1;
						$xdatos["nomcte"] = " ";
					} else {
						$this->utils->commit();

						$xdatos["type"] = "success";
						$xdatos['title'] = 'Alerta';
						$xdatos["msg"] = "Exito al ingresar el registro";
						$xdatos["id_cliente"] = $id_cliente;
						$xdatos["nomcte"] = $nomcte;
					}
						echo json_encode($xdatos);
			}
	}
}

/* End of file Ventas.php */
