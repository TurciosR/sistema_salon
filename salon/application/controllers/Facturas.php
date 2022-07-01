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

include APPPATH . 'libraries/NumeroALetras.php';
//include APPPATH . 'libraries/AlignMarginText.php';

class Facturas extends CI_Controller {
	/*
	Global table name
	*/
	private $table = "stock";
	private $pk = "id_producto";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->model("FacturasModel","facturas");
		$this->load->model("Clientes_model", "clientes");
		$this->load->helper('print_helper');
	}
	/*********************************************************/
	/*********************************************************/
	/************************CARGAS***************************/
	/*********************************************************/
	/*********************************************************/
	public function index()
	{
		$this->agregar();
	}

	function get_data_cliente($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$giro = $this->clientes->get_giro();
			$row = $this->facturas->get_one_row("ventas",array('id_venta' => $id, ));
			$rowcte = $this->facturas->get_one_row("clientes",array('id_cliente' => $row->id_cliente, ));
			//$id_cliente=$row->id_cliente;

			if($row && $id!=""){
				$data = array(
					"row"=>$row,
					"rowcte"=>$rowcte,
					"giro"=>$giro,
				);
				$this->load->view("facturas/data_client.php",$data);
			}else{
				redirect('errorpage');
			}
		}
	}
	function agregar(){
		if($this->input->method(TRUE) == "GET"){
       $tipodoc =	$this->facturas->get_tipodoc();
			$data = array(
				"sucursal"=>$this->facturas->get_detail_rows("sucursales",array('1' => 1, )),
				"id_sucursal" => $this->session->id_sucursal,
				"tipodoc" => $tipodoc,
			);

			$extras = array(
				'css' => array(
						"css/scripts/ventas.css"
				),
				'js' => array(
					"js/scripts/facturas.js"
				),
			);

			layout("facturas/facturar",$data,$extras);
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->load->model("ProductosModel","productos");
			$this->utils->begin();
			$concepto = strtoupper($this->input->post("concepto"));
			$fecha = Y_m_d($this->input->post("fecha"));
			$total = $this->input->post("total");
			$id_cliente = $this->input->post("id_cliente");
			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$id_sucursal = $this->input->post("id_sucursal");
			$tipodoc= $this->input->post("tipodoc");
			$id_usuario = $this->session->id_usuario;
			$hora = date("H:i:s");

			switch ($tipodoc ) {
				 case 1 :
					$correlativo = $this->facturas->get_correlative('tik',$id_sucursal);
					break;
				 case 2 :
						$correlativo = $this->facturas->get_correlative('cof',$id_sucursal);
						break;
				 case 3 :
						$correlativo = $this->facturas->get_correlative('ccf',$id_sucursal);
						break;
		 }

			$data = array(
				'fecha' => $fecha,
				'hora' => $hora,
				'concepto' => $concepto,
				'indicaciones ' => "VENTA DE PRODUCTOS Y SERVICIOS",
				'id_cliente' => $id_cliente,
				'id_estado' => 2,
				'id_sucursal_despacho' => $id_sucursal,
				'correlativo' => $correlativo,
				'total' => $total,
				'id_sucursal' => $id_sucursal,
				'id_usuario' => $id_usuario,
				'tipo_doc' => $tipodoc,
				'requiere_imei ' => 0,
				'imei_ingresado' => 0,
				'guia' => "",
			);

			$imei_required = false;

			$id_factura = $this->facturas->inAndCon('ventas',$data);
			if($id_factura!=NULL){
			if($data_ingreso!=NULL){
				foreach ($data_ingreso as $fila)
				{
					$id_producto = $fila['id_producto'];
					$costo = $fila['costo'];
					$cantidad = $fila['cantidad'];
					$precio_sugerido = $fila['precio_sugerido'];
					$descuento = $fila['descuento'];
		      $precio_final = $fila['precio_final'];
					$subtotal = $fila['subtotal'];
					$color = $fila['color'];
					$tipo = $fila['tipo'];//"0:PRODUCTO,1:SERVICIO"

					$estado = $fila['est'];

					$form_data = array(
						'id_venta' => $id_factura,
						'id_producto' => $id_producto,
						'id_color' => $color,
						'costo' => $costo,
						'precio' => $precio_sugerido,
						'precio_fin'=>$precio_final,
						'descuento' => $descuento,
						'cantidad' => $cantidad,
						'subtotal' => $subtotal,
						'condicion' => $estado,
						'tipo_prod' => $tipo,
						'garantia' =>  $this->facturas->getGarantia($id_producto,$estado),
					);
					$id_detalle = $this->facturas->inAndCon('ventas_detalle',$form_data);
					$stock_data = $this->facturas->get_stock($id_producto,$color,$id_sucursal);
					$newstock = ($stock_data->cantidad)-$cantidad;
					$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);
					if ($this->facturas->has_imei_required($id_producto)) {
						// code...
						$imei_required=true;
					}
				}
			}
				if ($imei_required) {
					// code...
					$this->utils->update("ventas",array('requiere_imei' => 1, ),"id_venta=$id_factura");
				}
				$this->utils->commit();
				$xdatos["type"]="success";
				$xdatos['title']='Información';
				$xdatos["msg"]="Registo ingresado correctamente!";
				$xdatos["id_factura"]=$id_factura;
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
			$row = $this->facturas->get_one_row("ventas", array('id_venta' => $id,));
			$rowc = $this->facturas->get_one_row("clientes", array('id_cliente' => $row->id_cliente,));
			$rowpc=$this->facturas->get_porcent_client($rowc->clasifica);

			//$porcent_clasifica=$rowpc->porcentaje;
			$detalles = $this->facturas->get_detail_ci($id);
			$detalleservicios = $this->facturas->get_detail_serv($id);

			$detalles1 = array();
			if($detalles !=NULL){
			foreach ($detalles as $detalle)
			{
				$id_producto = $detalle->id_producto;
				$precio = $detalle->precio;
				$detallesp = $this->precios_producto($id_producto, $precio);
				if($detallesp != 0)
				{
					$stock_data = $this->facturas->get_stock($id_producto,$detalle->id_color,$row->id_sucursal);
					$detalle->precios = $detallesp["precios"];
					$detalle->stock = $stock_data->cantidad;
					$detalle->id_stock = $stock_data->id_stock;
					$detalle->id_color = $stock_data->id_color;

					$d = $this->facturas->get_reservado($id_producto,$id,$detalle->id_color);
					$detalle->reservado = $d->reservado;

					$estado = "<select class='est'>";
					if ($detalle->condicion=="NUEVO") {
						$estado .= "<option selected value='NUEVO'>NUEVO</option>";
						$estado .= "<option value='USADO'>USADO</option>";
					}
					else {
						$estado .= "<option value='NUEVO'>NUEVO</option>";
						$estado .= "<option selected value='USADO'>USADO</option>";
					}
					$estado .= "</select>";

					$detalle->estado = $estado;
				}
				array_push($detalles1,$detalle);
			}
		}
			if($row && $id!=""){
				$data = array(
					"row"=>$row,
					"detalles"=>$detalles1,
					"detalleservicios"=>$detalleservicios,
					'rowpc'=>$rowpc,
					"sucursal"=>$this->facturas->get_detail_rows("sucursales",array('1' => 1, )),
					"id_sucursal" => $row->id_sucursal,
					"rowc" => $rowc,
				);
				$extras = array(
					'css' => array(
							"css/scripts/ventas.css"
					),
					'js' => array(
						"js/scripts/facturas.js"
					),
				);
				layout("facturas/editar",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->utils->begin();
			$id_factura = $this->input->post("id_venta");
			$concepto = strtoupper($this->input->post("concepto"));
			$fecha = Y_m_d($this->input->post("fecha"));
			$instrucciones = $this->input->post("instrucciones");
			$total = $this->input->post("total");
			$id_cliente = $this->input->post("id_cliente");
			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$id_sucursal = $this->input->post("sucursal");
			$envio = $this->input->post("envio");
			$id_usuario = $this->session->id_usuario;
			$hora = date("H:i:s");

			$row = $this->facturas->get_one_row("ventas", array('id_venta' => $id_factura,));
			$id_sucursalO = $row->id_sucursal;

			if ($id_sucursalO==$id_sucursal) {
				// code...
				$data = array(
					'fecha' => $fecha,
					'hora' => $hora,
					'concepto' => $concepto,
					'indicaciones ' => $instrucciones,
					'id_cliente' => $id_cliente,
					'envio' => $envio,
					'id_estado' => 1,
					'id_sucursal_despacho' => $id_sucursal,
					'total' => $total,
					'id_sucursal' => $id_sucursal,
					'id_usuario' => $id_usuario,
					'requiere_imei ' => 0,
					'imei_ingresado' => 0,
					'guia' => "",
				);
			}
			else {
				// code...
				//$correlativo = $this->facturas->get_max_correlative('ven',$id_sucursal);
				$data = array(
					'fecha' => $fecha,
					'hora' => $hora,
					'concepto' => $concepto,
					'indicaciones ' => $instrucciones,
					'id_cliente' => $id_cliente,
					'envio' => $envio,
					'id_estado' => 1,
					'id_sucursal_despacho' => $id_sucursal,
					'correlativo' => $correlativo,
					'total' => $total,
					'id_sucursal' => $id_sucursal,
					'id_usuario' => $id_usuario,
					'requiere_imei ' => 0,
					'imei_ingresado' => 0,
			//		'correlativo' => $correlativo,
				);
			}
			$imei_required = false;
			/*editar encabezado*/
			$this->utils->update('ventas',$data,"id_venta=$id_factura");

			/*Cargo los detalles previos*/
			$detalles_previos = $this->facturas->get_detail_ci($id_factura);
			if($detalles_previos!=NULL){
				foreach ($detalles_previos as $key) {
					// code...
					$stock_data = $this->facturas->get_stock($key->id_producto,$key->id_color,$id_sucursalO);
					$newstock = ($stock_data->cantidad)+($key->cantidad);
					$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);
				}
			}
			/*eliminar detalles previos*/
			$this->utils->delete("ventas_detalle","id_venta=$id_factura");

			/*nuevos detalles*/
			if($data_ingreso!=NULL){
				foreach ($data_ingreso as $fila)
				{
					$id_producto = $fila['id_producto'];
					$costo = $fila['costo'];
					$cantidad = $fila['cantidad'];
					$precio_sugerido = $fila['precio_sugerido'];
					$descuento = $fila['descuento'];
					$precio_final = $fila['precio_final'];
					$subtotal = $fila['subtotal'];
					$color = $fila['color'];
					$estado = $fila['est'];
					$tipo = $fila['tipo'];//"0:PRODUCTO,1:SERVICIO"

					$form_data = array(
						'id_venta' => $id_factura,
						'id_producto' => $id_producto,
						'id_color' => $color,
						'costo' => $costo,
						'precio' => $precio_sugerido,
						'precio_fin'=>$precio_final,
						'descuento' => $descuento,
						'cantidad' => $cantidad,
						'subtotal' => $subtotal,
						'condicion' => $estado,
						'tipo_prod' => $tipo,
						'garantia' =>  $this->facturas->getGarantia($id_producto,$estado),
					);
					$id_detalle = $this->facturas->inAndCon('ventas_detalle',$form_data);
					$this->utils->update(
						"producto",
						array(
							'precio_sugerido' => $precio_sugerido,
							'costo_s_iva' => $costo,
							'costo_c_iva' => round($costo*1.13),
						),
						"id_producto=$id_producto"
					);
					$stock_data = $this->facturas->get_stock($id_producto,$color,$id_sucursal);
					$newstock = ($stock_data->cantidad)-$cantidad;
					if($tipo==0 && $estado!="SERVICIO"){
						$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);
					}
					if ($this->facturas->has_imei_required($id_producto)) {
						// code...
						$imei_required=true;
					}
				}
			}
			if ($imei_required) {
				// code...
				$this->utils->update("ventas",array('requiere_imei' => 1, ),"id_venta=$id_factura");
			}
			$this->utils->commit();
			$xdatos["type"]="success";
			$xdatos['title']='Información';
			$xdatos["msg"]="Registo ingresado correctamente!";

			echo json_encode($xdatos);
		}
	}

	function finalizar($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->facturas->get_one_row("ventas", array('id_venta' => $id,));
			$rowc = $this->facturas->get_one_row("clientes", array('id_cliente' => $row->id_cliente,));
			$rowpc=$this->facturas->get_porcent_client($rowc->clasifica);

			$detalles = $this->facturas->get_detail_ci($id);
			$detalleservicios = $this->facturas->get_detail_serv($id);
			$tipodoc =	$this->facturas->get_tipodoc();
			$detalles1 = array();
			if($detalles !=NULL){
			foreach ($detalles as $detalle){
				$id_producto = $detalle->id_producto;
				$precio = $detalle->precio;
				$detallesp = $this->precios_producto($id_producto, $precio);
				if($detallesp != 0)
				{
					$stock_data = $this->facturas->get_stock($id_producto,$detalle->id_color,$row->id_sucursal);
					$detalle->precios = $detallesp["precios"];
					$detalle->stock = $stock_data->cantidad;
					$detalle->id_stock = $stock_data->id_stock;
					$detalle->id_color = $stock_data->id_color;

					$d = $this->facturas->get_reservado($id_producto,$id,$detalle->id_color);
					$detalle->reservado = $d->reservado;

					$estado = "<select class='est'>";
					if ($detalle->condicion=="NUEVO") {
						$estado .= "<option selected value='NUEVO'>NUEVO</option>";
						$estado .= "<option value='USADO'>USADO</option>";
					}
					else {
						$estado .= "<option value='NUEVO'>NUEVO</option>";
						$estado .= "<option selected value='USADO'>USADO</option>";
					}
					$estado .= "</select>";

					$detalle->estado = $estado;
				}
				array_push($detalles1,$detalle);
			}
		}
			if($row && $id!=""){
				$data = array(
					"row"=>$row,
					"detalles"=>$detalles1,
					"detalleservicios"=>$detalleservicios,
					'tipodoc'=>$tipodoc,
					'rowpc'=>$rowpc,
					"sucursal"=>$this->facturas->get_detail_rows("sucursales",array('1' => 1, )),
					"id_sucursal" => $row->id_sucursal,
					"rowc" => $rowc,
				);
				$extras = array(
					'css' => array(
							"css/scripts/ventas.css"
					),
					'js' => array(
						"js/scripts/facturas.js"
					),
				);
				layout("facturas/finalizar",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->utils->begin();
			$id_factura = $this->input->post("id_venta");
			$concepto = strtoupper($this->input->post("concepto"));
			$fecha = Y_m_d($this->input->post("fecha"));

			$total = $this->input->post("total");
			$id_cliente = $this->input->post("id_cliente");
			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$tipodoc= $this->input->post("tipodoc");
			$id_sucursal = $this->session->id_sucursal;
			$envio = $this->input->post("envio");
			$id_usuario = $this->session->id_usuario;
			$hora = date("H:i:s");

			$row = $this->ventas->get_one_row("ventas", array('id_venta' => $id_factura,));
			$id_sucursalO = $row->id_sucursal;

			switch ($tipodoc ) {
				 case 1 :
					$correlativo = $this->facturas->get_correlative('tik',$id_sucursal);
					break;
				 case 2 :
						$correlativo = $this->facturas->get_correlative('cof',$id_sucursal);
						break;
				 case 3 :
						$correlativo = $this->facturas->get_correlative('ccf',$id_sucursal);
						break;
		 }
			if ($id_sucursalO==$id_sucursal) {
				// code...
				$data = array(
					'fecha' => $fecha,
					'hora' => $hora,
					'concepto' => $concepto,
				//	'indicaciones ' => $instrucciones,
					'id_cliente' => $id_cliente,
					'envio' => $envio,
					'id_estado' => 1,
					'id_sucursal_despacho' => $id_sucursal,
					'total' => $total,
					'id_sucursal' => $id_sucursal,
					'id_usuario' => $id_usuario,
					'requiere_imei ' => 0,
					'imei_ingresado' => 0,
					'tipo_doc' => $tipodoc,
					'correlativo' => $correlativo,
					'guia' => "",
				);
			}
			else {
				// code...

				//$correlativo = $this->facturas->get_max_correlative('ven',$id_sucursal);
				$data = array(
					'fecha' => $fecha,
					'hora' => $hora,
					'concepto' => $concepto,
				//	'indicaciones ' => $instrucciones,
					'id_cliente' => $id_cliente,
					'envio' => $envio,
					'id_estado' => 1,
					'id_sucursal_despacho' => $id_sucursal,
					'correlativo' => $correlativo,
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
			$this->utils->update('ventas',$data,"id_venta=$id_factura");

			/*Cargo los detalles previos*/
			$detalles_previos = $this->facturas->get_detail_ci($id_factura);
			if($detalles_previos!=NULL){
				foreach ($detalles_previos as $key) {
					$stock_data = $this->facturas->get_stock($key->id_producto,$key->id_color,$id_sucursalO);
					$newstock = ($stock_data->cantidad)+($key->cantidad);
					$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);
				}
			}
			/*eliminar detalles previos*/
			$this->utils->delete("ventas_detalle","id_venta=$id_factura");

			/*nuevos detalles*/
			if($data_ingreso!=NULL){
				foreach ($data_ingreso as $fila)
				{
					$id_producto = $fila['id_producto'];
					$costo = $fila['costo'];
					$cantidad = $fila['cantidad'];
					$precio_sugerido = $fila['precio_sugerido'];
					$descuento = $fila['descuento'];
					$precio_final = $fila['precio_final'];
					$subtotal = $fila['subtotal'];
					$color = $fila['color'];
					$estado = $fila['est'];
					$tipo = $fila['tipo'];//"0:PRODUCTO,1:SERVICIO"

					$form_data = array(
						'id_venta' => $id_factura,
						'id_producto' => $id_producto,
						'id_color' => $color,
						'costo' => $costo,
						'precio' => $precio_sugerido,
						'precio_fin'=>$precio_final,
						'descuento' => $descuento,
						'cantidad' => $cantidad,
						'subtotal' => $subtotal,
						'condicion' => $estado,
						'tipo_prod' => $tipo,
						'garantia' =>  $this->facturas->getGarantia($id_producto,$estado),
					);
					$id_detalle = $this->facturas->inAndCon('ventas_detalle',$form_data);
					$this->utils->update(
						"producto",
						array(
							'precio_sugerido' => $precio_sugerido,
							'costo_s_iva' => $costo,
							'costo_c_iva' => round($costo*1.13),
						),
						"id_producto=$id_producto"
					);
					$stock_data = $this->facturas->get_stock($id_producto,$color,$id_sucursal);
					$newstock = ($stock_data->cantidad)-$cantidad;
					if($tipo==0 && $estado!="SERVICIO"){
						$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);
					}
					if ($this->facturas->has_imei_required($id_producto)) {
						// code...
						$imei_required=true;
					}
				}
			}
			if ($imei_required) {
				// code...
				$this->utils->update("ventas",array('requiere_imei' => 1, ),"id_venta=$id_factura");
			}
			$this->utils->commit();
			$xdatos["type"]="success";
			$xdatos['title']='Información';
			$xdatos["msg"]="Venta guardada correctamente!";

			echo json_encode($xdatos);
		}
	}
	function up_data_client(){

	if($this->input->method(TRUE) == "POST"){
			$this->load->library('user_agent');
		if ($this->agent->is_browser())
		  {
	        $agent = $this->agent->browser().' '.$this->agent->version();
					$opsys = $this->agent->platform();
		  }
			$errors =false;
			$this->utils->begin();
			$id_factura = $this->input->post("id_vta");
			$id_cliente = $this->input->post("id_client");
			$nomcte= $this->input->post("nombre_cliente");
			$nomcomer= $this->input->post("nombre_comercial");
			$correlativo= $this->input->post("numero_doc");
			$id_sucursal=$this->session->id_sucursal;
			$hora = date("H:i:s");
			$rowvta = $this->facturas->get_one_row("ventas", array('id_venta' => $id_factura,));
			$row_confdir=$this->facturas->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));
			$tipodoc=	$rowvta->tipo_doc;
			switch ($tipodoc) {
				 case 1 :
				 $form_data = array(
					 'nombre' => $nomcte,
					 'nombre_comercial' => $nomcomer,
				 );
				 $row_confpos=$this->facturas->get_one_row("config_pos", array('id_sucursal' => $id_sucursal,'alias_tipodoc'=>'tik',));
					$correlativo1 = $this->facturas->update_correlative('tik',$correlativo,$id_sucursal);
					break;
				 case 2 :
						 $direccion= $this->input->post("direccion");
						 $dui= $this->input->post("dui");
						 $nit= $this->input->post("nit");
						 $nrc= $this->input->post("nrc");
						 $giro= $this->input->post("giro");
						 $form_data = array(
								'nombre' => $nomcte,
								'nombre_comercial' => $nomcomer,
								'direccion' => $direccion,
								'dui' => $dui,
								'nit' => $nit,
								'nrc'=>$nrc,
								'giro' => $giro,
								'activo' => 1,
							);
								$correlativo1 = $this->facturas->update_correlative('cof',$correlativo,$id_sucursal);
							break;
				 case 3 :
				 $direccion= $this->input->post("direccion");
				 $dui= $this->input->post("dui");
				 $nit= $this->input->post("nit");
				 $nrc= $this->input->post("nrc");
				 $giro= $this->input->post("giro");
				 $form_data = array(
 					'nombre' => $nomcte,
 					'nombre_comercial' => $nomcomer,
 					'direccion' => $direccion,
 					'dui' => $dui,
 					'nit' => $nit,
 					'nrc'=>$nrc,
 					'giro' => $giro,
					'activo' => 1,
 					);
					$correlativo1 = $this->facturas->update_correlative('ccf',$correlativo,$id_sucursal);
					break;
		 }

				if($id_cliente<0){
						$id_client=$id_cliente;
						if($tipodoc>1){
							$id_client=$this->facturas->inAndCon("clientes",$form_data);
							if ($id_client==NULL) {
								$id_client=$id_cliente;
								$errors = true;
							}else {
								$form_cte = array(
									'id_cliente' => $id_client,
									'correlativo'	=>$correlativo,
									'id_estado' =>2,
								);
								$this->utils->update("ventas",$form_cte,"id_venta=$id_factura");
								$id_cliente=$id_client;
							}
					}
				}
				else {
					if($tipodoc>1){
						$id_client=$id_cliente;
						$this->utils->update(
							"clientes",$form_data,"id_cliente=$id_cliente"
						);
						$form_cte = array(
							'id_cliente' => $id_cliente,
							'correlativo'	=>$correlativo,
						  'id_estado'	=>2, //cambiar estadoa Finalizado id:2, cambiar despues
						);
							$this->utils->update("ventas",$form_cte,"id_venta=$id_factura");
					}
				}
				if ($errors==true) {
					// code...
					$this->utils->rollback();
					$xdatos["type"]="error";
					$xdatos['title']='Alerta';
					$xdatos["msg"]="Error al ingresar el registro";
					$xdatos["agent"]=$agent;
				}else{
					$this->utils->commit();
					$facturar=$correlativo;

			 		$total_letras = NumeroALetras::convertir($rowvta->total, 'Dolares',false, 'centavos');

					switch ($tipodoc) {
						 case 1 :
							//$facturar=$this->print_ticket($id_factura,$id_sucursal);
							$facturar=print_ticket($id_factura,$id_sucursal,"facturas");
							$xdatos["header"]=$row_confpos;
							break;
						 case 2 :
						 //$facturar=$this->print_cof($id_factura,$id_sucursal);
						 $facturar=print_cof($id_factura,$id_sucursal,"facturas");
							$xdatos["header"]=$form_data;
							break;
						 case 3 :
						 $facturar=print_ccf($id_factura,$id_sucursal,"facturas");
							//$facturar=$this->print_ccf($id_factura,$id_sucursal);
							$xdatos["header"]=$form_data;
							break;
				 }
				 $xdatos["type"]="success";
				 $xdatos['title']='Información';
				 $xdatos["msg"]="Venta guardada correctamente!";
				 $xdatos["tipodoc"]=$tipodoc;
				 $xdatos["id_client"]=$id_client;
				 $xdatos["opsys"]=$opsys;
				 $xdatos["dir_print"] =$row_confdir->dir_print_script;
				 $xdatos["total_letras"]=$total_letras;

				 $xdatos["headerfact"]=$rowvta;
				 $xdatos["facturar"]=$facturar;
				}
			echo json_encode($xdatos);

		}

	}
	function imei($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->facturas->get_one_row("ventas", array('id_venta' => $id,));
			if($row && $id!=""){
				$data = array(
					"row"=>$row,
					"detalles"=>$this->facturas->get_detail_ci($id),
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/facturas_imei.js"
					),
				);
				layout("facturas/cargaimei",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->utils->begin();
			$errors = false;
			$array_error= array("Log");
			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$id_factura = $this->input->post("id_venta");
			foreach ($data_ingreso as $fila) {
				$form_data = array(
					'id_producto' => $fila['id_producto'],
					'imei' => $fila['imei'],
					'id_detalle' => $fila['id_detalle'],
					'chain' => $fila['chain'],
					'id_venta' => $id_factura,
					'vendido' => 1,
				);
				$id_detalle = $this->facturas->inAndCon('facturas_imei',$form_data);
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
				// code...
				$this->utils->update("ventas",array('imei_ingresado' => 1, ),"id_venta=$id_factura");
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
			$row = $this->facturas->get_one_row("ventas", array('id_venta' => $id,));

			$info = $this->facturas->get_imei_ci($id);
			$detalles = array();
			$c=0;
			foreach ($info as $key) {
				// code...
				$detalles[$c]=array(
					'id_venta' => $key->id_factura,
					'id_producto' => $key->id_producto,
					'id_detalle' => $key->id_detalle,
					'nombre' => $key->nombre,
					'chain' => $key->chain,
					'data' => $this->facturas->get_imei_ci_det($key->chain),
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
						"js/scripts/facturas_imei.js"
					),
				);
				layout("facturas/editarimei",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->utils->begin();

			$errors = false;
			$array_error= array("Log");
			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$id_factura = $this->input->post("id_venta");
			foreach ($data_ingreso as $fila) {
				$form_data = array(
					'imei' => $fila['imei'],
				);
				$this->utils->update("facturas_imei",$form_data,"id_imei=$fila[id_imei]");
			}
			$this->utils->commit();
			$xdatos["type"]="success";
			$xdatos['title']='Información';
			$xdatos["msg"]="Registo ingresado correctamente!";
			echo json_encode($xdatos);
		}
	}
	function delete(){
		if($this->input->method(TRUE) == "POST"){
			$id_factura = $this->input->post("id");
			$this->utils->begin();
			$row = $this->facturas->get_one_row("ventas", array('id_venta' => $id_factura,));
			$id_sucursal = $row->id_sucursal;
			/*descargar los detalles previos*/
			$detalles_previos = $this->facturas->get_detail_rows("ventas_detalle", array('id_venta' => $id_factura, ));
			foreach ($detalles_previos as $key) {
				// code...
				$stock_data = $this->facturas->get_stock($key->id_producto,$key->id_color,$id_sucursal);
				$newstock = ($stock_data->cantidad)+($key->cantidad);
				$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);
			}
			/*eliminar detalles previos*/
			$this->utils->delete("ventas_detalle","id_venta=$id_factura");
			$this->utils->delete("ventas","id_venta=$id_factura");


			$this->utils->commit();
			$response["type"] = "success";
			$response["title"] = "Información";
			$response["msg"] = "Registro eliminado con éxito!";

			echo json_encode($response);
		}
	}
	function change(){
		if($this->input->method(TRUE) == "POST"){
			$id_factura = $this->input->post("id");
			$id_estado = $this->input->post("id_estado");
			$this->utils->begin();
			$this->utils->update("ventas",array('id_estado' => $id_estado, ),"id_venta=$id_factura");
			$this->utils->commit();
			$response["type"] = "success";
			$response["title"] = "Información";
			$response["msg"] = "Registro editado con éxito!";
			echo json_encode($response);
		}
	}
	function garantia($id=-1){
		if($this->input->method(TRUE) == "GET"){

			$id = $this->uri->segment(3);
			$this->load->library('GarantiaReport');
			$pdf = $this->garantiareport->getInstance('P','mm', 'Letter');
			$logo = base_url().getLogo();
			$pdf->SetMargins(6, 10);
			$pdf->SetLeftMargin(5);
			$pdf->AliasNbPages();
			$pdf->SetAutoPageBreak(true, 15);
			$pdf->AliasNbPages();

			$vg = $this->facturas->get_facturas($id);
			$id_sucursal = $vg->id_sucursal_despacho;
			$this->db->where("id_sucursal",$id_sucursal);
			$q = $this->db->get("sucursales");
			$dat = $q->row();
			$data = array("empresa" => $dat->nombre,"imagen" => $logo, 'fecha' => $this->input->post("fecha1"));
			$pdf->setear($data);
			$pdf->addPage();

			$l = array(
				's' =>10,
				'c' => 50,
				'v' => 50,
			);
			$array_data = array(
				array('',$l['s'],"C"),
				array("DATOS",$l['c'],"L"),
				array("DETALLE",$l['v'],"L"),
			);
			$pdf->SetFont('Arial','B',10);
			$pdf->LineWrite($array_data);

			$pdf->SetFont('Arial','',10);
			$array_data = array(
				array('',$l['s'],"C"),
				array("Nombre",$l['c'],"L"),
				array($vg->nombre,$l['v'],"L"),
			);
			$pdf->LineWrite($array_data);

			$array_data = array(
				array('',$l['s'],"C"),
				array("Fecha de Compra",$l['c'],"L"),
				array(d_m_Y($vg->fecha),$l['v'],"L"),
			);
			$pdf->LineWrite($array_data);
			$pdf->Ln(5);

			$pdf->SetFont('Arial','B',9);
			$l = array(
				's' =>10,
				'ma' => 30,
				'mo' => 30,
				'ime' => 65,
				'con' => 20,
				'tim' =>45
			);
			$array_data = array(
				array('',$l['s'],"C"),
				array('Marca',$l['ma'],"L"),
				array('Modelo',$l['mo'],"L"),
				array('Imei',$l['ime'],"C"),
				array('Condición',$l['con'],"C"),
				array('Tiempo de Garantia (Dias)',$l['tim'],"C"),

			);
			$pdf->LineWriteB($array_data);

			$pdf->SetFont('Arial','',9);

			$dat = $this->facturas->get_detail_ci($id);
			foreach ($dat as $key) {

				$im =  $this->facturas->get_imei_productos($key->id_detalle);
				$imei="";

				if ($im) {
					// code...
					$arim = array();
					$p=0;
					foreach ($im as $ke) {
						$arim[$p]=$ke->imei;
						$p++;
					}

					$imei = implode(", ",$arim);
				}
				$array_data = array(
					array('',$l['s'],"C"),
					array($key->cantidad."x ".$key->marca,$l['ma'],"L"),
					array($key->modelo,$l['mo'],"L"),
					array($imei,$l['ime'],"C"),
					array($key->condicion,$l['con'],"C"),
					array($key->garantia,$l['tim'],"C"),

				);
				$pdf->LineWriteB($array_data);
			}
			$pdf->Ln(5);
			//function SetStyle($tag, $family, $style, $size, $color, $indent=-1)
			$pdf->SetStyle("p","arial","N",9,"0,0,0",0);
			$pdf->SetStyle("b","arial","BN",9,"0,0,0");

			$dat = $this->facturas->get_one_row("report_parrafo",array('tipo' => "GarantiaE",));
			$pdf->WriteTag(0,4,$dat->texto,0,"J",0,0);
			$pdf->Ln(5);

			$this->db->where("tipo","GarantiaEX");
			$this->db->where("id_sucursal",$id_sucursal);
			$this->db->order_by('orden', 'ASC');
			$query = $this->db->get("report_detail");

			$dat = $query->result();
			$l = array(
				's' =>9,
				'c' => 5,
				'v' => 192,
			);
			foreach ($dat as $key) {
				// code...
				$array_data = array(
					array('',$l['s'],"C"),
					array(("*"),$l['c'],"R"),
					array($key->texto,$l['v'],"L"),
				);
				$pdf->LineWrite($array_data);
			}

			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(205, 10,"Procedimiento a seguir en caso de garantía:", 0, 1, 'L');

			$this->db->where("tipo","GarantiaEC");
			$this->db->where("id_sucursal",$id_sucursal);
			$this->db->order_by('orden', 'ASC');
			$query = $this->db->get("report_detail");

			$pdf->SetFont('Arial','',9);
			$dat = $query->result();
			$i=1;
			foreach ($dat as $key) {
				// code...
				$array_data = array(
					array('',$l['s'],"C"),
					array(("$i."),$l['c'],"R"),
					array($key->texto,$l['v'],"L"),
				);
				$pdf->LineWrite($array_data);
				$i++;
			}

			$pdf->Output();
		}
		else {
			redirect('errorpage');
		}
	}
	public function detalle_servicio($id=0){
		if($id == 0)
		{
			$id_producto = $this->input->post("id");
			$id_color = $this->input->post("id_s");
			$id_factura = $this->input->post("id_venta");
		}
		//SELECT `id_servicio`, `id_categoria`, `nombre`, `costo_s_iva`, `costo_c_iva`, `cesc`,
		//`precio_sugerido`, `precio_minimo`, `dias_garantia`, `activo`, `deleted` FROM `servicio` WHERE 1
		$prods = $this->facturas->get_row_servicios($id_producto);
		$xdatos["precio_sugerido"]=$prods->precio_sugerido;
		$xdatos["precio_minimo"] =$prods->precio_minimo;
		$xdatos["costo"] = number_format($prods->costo_s_iva,2,".","");
		$xdatos["costo_iva"] = number_format($prods->costo_c_iva,2,".","");
			echo json_encode($xdatos);
	}
	public function detalle_producto($id=0){
		$id_sucursal = $this->session->id_sucursal;
		if($id == 0)
		{
			$id_producto = $this->input->post("id");
			$id_color = $this->input->post("id_s");
			$id_factura= $this->input->post("id_venta");
		}
		$lista = "";
		$stock_data = $this->facturas->get_stock($id_producto,$id_color,$id_sucursal);
		$prods = $this->facturas->get_producto($id_producto);
		$precios = $this->facturas->get_precios_exis($id_producto);

		$colores = $this->facturas->get_detail_rows("producto_color", array('id_producto' => $id_producto,));

		$d = $this->facturas->get_reservado($id_producto,$id_factura,$id_color);

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

		$lista .= "<select class='form-control precios sel' style='width:100%;'>";
		$costo = 0;
		$costo_iva = 0;
		foreach ($precios as $row_por)
		{
			$id_porcentaje = $row_por->id_precio;
			$costo = $row_por->costo;
			$costo_iva = $row_por->costo_iva;
		//	$precio = $row_por->total_iva;
			$precio = $row_por->porcentaje;
			$lista .= "<option value='".$precio."' precio='".$precio."'>$".number_format($precio,2,".",",")."</option>";
		}
		$lista .= "</select>";
		$xdatos["precio_sugerido"]=$prods->precio_sugerido;
		$xdatos["precios"] = $lista;
		$xdatos["stock"] = $stock_data->cantidad+$d->reservado;
		$xdatos["id_s"] = $stock_data->id_stock;
		$xdatos["marca"] = $prods->marca;
		$xdatos["modelo"] = $prods->modelo;
		$xdatos["costo"] = number_format($costo,2,".","");
		$xdatos["costo_iva"] = number_format($costo_iva,2,".","");
		echo json_encode($xdatos);
	}
	public function precios_producto($id=0,$precioe=0){
		$id_sucursal = $this->session->id_sucursal;

		$precios = $this->facturas->get_precios_exis($id);
		$lista= "";
		$lista .= "<select class='form-control precios sel' style='width:100%;'>";
		$costo = 0;
		$costo_iva = 0;
		foreach ($precios as $row_por)
		{
			$id_porcentaje = $row_por->id_precio;
			$costo = $row_por->costo;
			$costo_iva = $row_por->costo_iva;
			//$precio = $row_por->total_iva;
			$precio = $row_por->precio_facturas;
			$lista .= "<option value='".$precio."' precio='".$precio."'";
			if($precio == $precioe)
			{
				$lista.= " selected ";
			}
			$lista.= ">$".number_format($precio,2,".",",")."</option>";
		}
		$lista .= "</select>";
		$xdatos["precios"] = $lista;
		return $xdatos;
	}
	function get_productos(){
		$query = $this->input->post("query");
		$id_sucursal = $this->input->post("id_sucursal");
		$rows = $this->facturas->get_productos($query,$id_sucursal);
		$rows2 = $this->facturas->get_servicios($query,$id_sucursal);
		$output = array();
		if($rows!=NULL) {
			foreach ($rows as $row) {
				$output[] = array(

					'producto' => $row->id_producto."|".$row->modelo." ".$row->color."|".$row->id_color,
				);
			}
		}
		if($rows2!=NULL) {
			foreach ($rows2 as $row) {
			$output[]= array(

					'producto' => $row->id_servicio."|".$row->nombre." (SERVICIO)"."|"."SERVICIO",
				);
			}
		}
		echo json_encode($output);
	}
	function get_clientes(){
		$query = $this->input->post("query");
		$rows = $this->facturas->get_clientes($query);
		$output = array();
		if($rows!=NULL) {
			foreach ($rows as $row) {
				$output[] = array(
					//'producto' => $row->id_producto."|".$row->nombre." ".$row->marca." ".$row->modelo,
					'cliente' => $row->id_cliente."|".$row->nombre."|".$row->clasifica,
				);
			}
		}
		echo json_encode($output);
	}
	function get_porcent_cliente(){
		$clasifica = $this->input->post("clasifica");
		$row = $this->facturas->get_porcent_client($clasifica);
		if($row!=NULL) {
			$porcent_clasifica=$row->porcentaje;
		}else {
				$porcent_clasifica=0;
		}
			$xdatos["porc_clasifica"]=$porcent_clasifica;
			$xdatos["type"]="success";
			$xdatos['title']='Alerta';
			$xdatos["msg"]="Cliente Seleccionado";
			echo json_encode($xdatos);
	}

	//crear formato impresion ticket
	/*
	function print_ticket($id_factura,$id_sucursal){
		$this->load->library('user_agent');
		if ($this->agent->is_browser()){
					$opsys = $this->agent->platform();
		}

		$info_factura="";
		//header print_ticket
		$row_confpos=$this->facturas->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));
		$info_factura.="DESCRIPCION  CANT.  P. UNIT    SUBTOT.\n|";
		//encabezado de la facturas
		$rowvta = $this->facturas->get_one_row("ventas", array('id_venta' => $id_factura,));
		//convertir(1988208.99,'dolares',false,'Centavos')


		//detalles productos
		$detalleproductos = $this->facturas->get_detail_ci($id_factura);
		$espacio="&nbsp;";
		$espacio=" ";
			$margen_izq=AlignMarginText::leftmargin($espacio,2);
		if($detalleproductos !=NULL){

			foreach ($detalleproductos as $detalle){
				$id_producto = $detalle->id_producto;
				$descripcion=$detalle->marca." ".$detalle->modelo." ".$detalle->color;
				$precio_fin = $detalle->precio_fin;
				$cantidad = $detalle->cantidad;
				$subtotal = $detalle->subtotal;
				//AlignMarginText::onelineleft(texto,longitud,margin_izq,caracter_espacios);
				//AlignMarginText::rightaligner($input,$caracter = " ",$width)
				$desc = AlignMarginText::onelineleft($descripcion,20,10,$espacio);
				$pre=AlignMarginText::rightaligner($precio_fin,$espacio,12);
				$cant=AlignMarginText::rightaligner($cantidad,$espacio,12);
				$subt=AlignMarginText::rightaligner($subtotal,$espacio,12);
				$info_factura.=$desc.$margen_izq.$cantidad.$margen_izq.$pre.$margen_izq.$subtotal." \n";
			}
		}
		//detalles servicios
		$detalleservicios = $this->facturas->get_detail_serv($id_factura);
		if($detalleservicios !=NULL){
			foreach ($detalleservicios as $detalle){
				$id_producto = $detalle->id_producto;
				$descripcion=$detalle->nombre;
				$precio_fin = $detalle->precio_fin;
				$cantidad = $detalle->cantidad;
				$subtotal = $detalle->subtotal;
				$desc = AlignMarginText::onelineleft($descripcion,20,10,$espacio);
				$pre=AlignMarginText::rightaligner($precio_fin,$espacio,12);
				$cant=AlignMarginText::rightaligner($cantidad,$espacio,12);
				$subt=AlignMarginText::rightaligner($subtotal,$espacio,12);
				$info_factura.=$desc.$margen_izq.$cantidad.$margen_izq.$pre.$margen_izq.$subtotal." \n";
			}
		}
		return $info_factura;
	}
	function print_cof($id_factura,$id_sucursal){

		$info_factura="";
		//header print_cof
		$row_confpos=$this->facturas->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));
		$info_factura.="DESCRIPCION  CANT.  P. UNIT    SUBTOT.\n|";
		//encabezado de la facturas
		$rowvta = $this->facturas->get_one_row("ventas", array('id_venta' => $id_factura,));
		//detalles productos
		$detalleproductos = $this->facturas->get_detail_ci($id_factura);
		$espacio="&nbsp;";
		$espacio=" ";
		$margen_izq=AlignMarginText::leftmargin($espacio,2);
		if($detalleproductos !=NULL){

			foreach ($detalleproductos as $detalle){
				$id_producto = $detalle->id_producto;
				$descripcion=$detalle->marca." ".$detalle->modelo." ".$detalle->color;
				$precio_fin = $detalle->precio_fin;
				$cantidad = $detalle->cantidad;
				$subtotal = $detalle->subtotal;
				//AlignMarginText::onelineleft(texto,longitud,margin_izq,caracter_espacios);
				//AlignMarginText::rightaligner($input,$caracter = " ",$width)
				$desc = AlignMarginText::onelineleft($descripcion,20,10,$espacio);
				$pre=AlignMarginText::rightaligner($precio_fin,$espacio,12);
				$cant=AlignMarginText::rightaligner($cantidad,$espacio,12);
				$subt=AlignMarginText::rightaligner($subtotal,$espacio,12);
				$info_factura.=$desc.$margen_izq.$cantidad.$margen_izq.$pre.$margen_izq.$subtotal." \n";
			}
		}
		//detalles servicios
		$detalleservicios = $this->facturas->get_detail_serv($id_factura);
		if($detalleservicios !=NULL){
			foreach ($detalleservicios as $detalle){
				$id_producto = $detalle->id_producto;
				$descripcion=$detalle->nombre;
				$precio_fin = $detalle->precio_fin;
				$cantidad = $detalle->cantidad;
				$subtotal = $detalle->subtotal;
				$desc = AlignMarginText::onelineleft($descripcion,20,10,$espacio);
				$pre=AlignMarginText::rightaligner($precio_fin,$espacio,12);
				$cant=AlignMarginText::rightaligner($cantidad,$espacio,12);
				$subt=AlignMarginText::rightaligner($subtotal,$espacio,12);
				$info_factura.=$desc.$margen_izq.$cantidad.$margen_izq.$pre.$margen_izq.$subtotal." \n";
			}
		}
		return $info_factura;
	}
	function print_ccf($id_factura,$id_sucursal){

		$info_factura="";
		//header print_cof
		$row_confpos=$this->facturas->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));
		$info_factura.="DESCRIPCION  CANT.  P. UNIT    SUBTOT.\n|";
		//encabezado de la facturas
		$rowvta = $this->facturas->get_one_row("ventas", array('id_venta' => $id_factura,));
		//detalles productos
		$detalleproductos = $this->facturas->get_detail_ci($id_factura);
		$espacio="&nbsp;";
		$espacio=" ";
		$margen_izq=AlignMarginText::leftmargin($espacio,2);
		if($detalleproductos !=NULL){

			foreach ($detalleproductos as $detalle){
				$id_producto = $detalle->id_producto;
				$descripcion=$detalle->marca." ".$detalle->modelo." ".$detalle->color;
				$precio_fin = $detalle->precio_fin;
				$cantidad = $detalle->cantidad;
				$subtotal = $detalle->subtotal;
				//AlignMarginText::onelineleft(texto,longitud,margin_izq,caracter_espacios);
				//AlignMarginText::rightaligner($input,$caracter = " ",$width)
				$desc = AlignMarginText::onelineleft($descripcion,20,10,$espacio);
				$pre=AlignMarginText::rightaligner($precio_fin,$espacio,12);
				$cant=AlignMarginText::rightaligner($cantidad,$espacio,12);
				$subt=AlignMarginText::rightaligner($subtotal,$espacio,12);
				$info_factura.=$desc.$margen_izq.$cantidad.$margen_izq.$pre.$margen_izq.$subtotal." \n";
			}
		}
		//detalles servicios
		$detalleservicios = $this->facturas->get_detail_serv($id_factura);
		if($detalleservicios !=NULL){
			foreach ($detalleservicios as $detalle){
				$id_producto = $detalle->id_producto;
				$descripcion=$detalle->nombre;
				$precio_fin = $detalle->precio_fin;
				$cantidad = $detalle->cantidad;
				$subtotal = $detalle->subtotal;
				$desc = AlignMarginText::onelineleft($descripcion,20,10,$espacio);
				$pre=AlignMarginText::rightaligner($precio_fin,$espacio,12);
				$cant=AlignMarginText::rightaligner($cantidad,$espacio,12);
				$subt=AlignMarginText::rightaligner($subtotal,$espacio,12);
				$info_factura.=$desc.$margen_izq.$cantidad.$margen_izq.$pre.$margen_izq.$subtotal." \n";
			}
		}
		return $info_factura;
	}
	*/
}

/* End of file Facturas.php */
