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

class Traslado extends CI_Controller {
	/*
	Global table name
	*/
	private $table = "stock";
	private $pk = "id_producto";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->model("TrasladoModel","traslados");
		$this->load->model("SucursalesModel","sucursales");
		$this->load->model("VentasModel", "ventas");
	}

	public function index()
	{
		$id_usuario   = $this->session->id_usuario;
		$id_sucursal  = $this->session->id_sucursal;
		// Get total branches (sucursales)
		$total_suc    = $this->sucursales->total_rows();
		$usuario_tipo = $this->traslados
						->get_one_row(
							"usuario",
							array('id_usuario' => $id_usuario)
						);
		if ($usuario_tipo != NULL) {
			if ($usuario_tipo->admin == 1 || $usuario_tipo->super_admin == 1) {
				$sucursales = $this->traslados
								->get_detail_rows(
									"sucursales",
									array('1' => 1)
								);
			} else {
				$sucursales = $this->traslados
								->get_detail_rows(
									"sucursales",
									array('id_sucursal' => $id_sucursal)
								);
			}
		} else {
			$sucursales = $this->traslados
							->get_detail_rows(
								"sucursales",
								array('1' => 1)
							);
		}

		$data = array(
			"titulo"  => "Traslados",
			"icono"   => "mdi mdi-cart",
			"buttons" => array(
				0     => array(
					"icon"  => 'mdi mdi-plus',
					'url'   => 'traslado/agregar',
					'txt'   => 'Nuevo traslado',
					'modal' => false
				),
			),
			"sucursales" => $total_suc,
			"selects" => array(
				0     => array(
					"name" => 'sucursales',
					"data" => $sucursales,
					"id"   => 'id_sucursal',
					"text" => array(
						"nombre",
						"direccion",
					),
					"separator" => " ",
					"selected"  => $id_sucursal,
				),
			),
			"table" => array(
				"ID"       => 5,
				"Fecha"    => 10,
				"Origen"   => 10,
				"Destino"  => 10,
				"Total"    => 10,
				"Detalle"  => 30,
				"Acciones" => 10,
			),
		);
		$extras   = array(
			'css' => array(
			),
			'js'  => array(
				"js/scripts/traslados.js"
			),
		);
		layout("template/admin", $data, $extras);
	}

	function get_data()
	{
		$valid_columns = array(
			0 => 'v.id_traslado',
			1 => 'v.fecha',
			2 => 'CONCAT(s1.nombre," ",s1.direccion)',
			3 => 'CONCAT(s2.nombre," ",s2.direccion)',
		);
		// Create query based on mariadb tables required
		$query_val  = $this->traslados->create_dt_query();
		$where  = array(
			'v.id_sucursal' => $this->input->post("id_sucursal"),
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
				//procedemos a obtener el detalle del traslado
				$detalleT = $this->traslados->get_detalle_traslado($rows->id_traslado);

				$menudrop = "<div class='btn-group'>
				<button data-toggle='dropdown' class='btn btn-success dropdown-toggle' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menú</button>
				<ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";

				$menudrop .= "<li><a  data-toggle='modal' data-target='#viewModal' data-refresh='true'  role='button' class='detail' data-id=".$rows->id_traslado."><i class='mdi mdi-eye-check' ></i> Detalles</a></li>";
				$menudrop .= "</ul></div>";

				//procedemos a validar si se mostraran los precios
				if($this->session->admin==1 || $this->session->super_admin==1){
					$validarCostos = "";
				}
				else {
					$validarCostos = "hidden";
				}

				$data[] = array(
					$rows->id_traslado,
					$rows->fecha,
					$rows->suc1,
					$rows->suc2,
					"<div $validarCostos>".$rows->total."</div>",
					$detalleT->detalle_t,
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

	function detalle($id=-1)
	{
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$rows = $this->traslados->get_detail_ci($id);
			//procedemos a validar si se mostraran los precios
			if($this->session->admin==1 || $this->session->super_admin==1){
				$validarCostos = "";
			}
			else {
				$validarCostos = "hidden";
			}
			if ($rows && $id != "") {
				$data = array(
					"rows"=>$rows,
					"process" => "traslado",
					"ocultar" => $validarCostos
				);
				$this->load->view("inventario/ver_detalle.php",$data);
			} else {
				redirect('errorpage');
			}
		}
	}

	function change_state($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->traslados->get_one_row("ventas",array('id_venta' => $id, ));
			$rows = $this->traslados->get_detail_rows("estado",array('1' => 1, ));
			if ($rows && $id != "") {
				$data = array(
					"row"=>$row,
					"rows"=>$rows,
				);
				$this->load->view("ventas/change_state.php",$data);
			} else {
				redirect('errorpage');
			}
		}
	}

	function agregar()
	{
		if ($this->input->method(TRUE) == "GET") {
			$id_usuario   = $this->session->id_usuario;
			$id_sucursal  = $this->session->id_sucursal;
			$usuario_tipo = $this->traslados
							->get_one_row(
								"usuario",
								array('id_usuario' => $id_usuario,)
							);
			if ($usuario_tipo != NULL) {
				if ($usuario_tipo->admin == 1 || $usuario_tipo->super_admin == 1) {
					$sucursales = $this->traslados
									->get_detail_rows(
										"sucursales",
										array('1' => 1)
									);
					$sucursal   = $this->traslados
									->get_detail_rows(
										"sucursales",
										array('1' => 1)
									);
				} else {
					$sucursales = $this->traslados
									->get_detail_rows(
										"sucursales",
										array('id_sucursal' => $id_sucursal)
									);
					$sucursal   = $this->traslados
									->get_detail_rows(
										"sucursales",
										array(
											'1' => 1,
											'id_sucursal !=' => $id_sucursal
										)
									);
				}
			} else {
				$sucursales = $this->traslados
								->get_detail_rows("sucursales", array('1' => 1));
			}

			$data = array(
				"sucursal_envio" => $sucursales,
				"sucursal"       => $sucursal,
				"id_sucursal"    => $this->session->id_sucursal
			);

			$extras = array(
				'css' => array(
				),
				'js'  => array(
					"js/scripts/traslados.js"
				),
			);

			layout("traslado/guardar", $data, $extras);
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->load->model("ProductosModel","productos");
			$this->utils->begin();
			$concepto            = strtoupper($this->input->post("concepto"));
			$fecha               = Y_m_d($this->input->post("fecha"));
			$instrucciones       = $this->input->post("instrucciones");
			$total               = $this->input->post("total");
			$data_ingreso        = json_decode($this->input->post("data_ingreso"),true);
			$id_sucursal         = $this->input->post("sucursal");
			$id_sucursal_destino = $this->input->post("sucursal_destino");
			$id_usuario          = $this->session->id_usuario;
			$hora                = date("H:i:s");
			$correlativo         = $this->traslados->get_max_correlative('tr',$id_sucursal);

			$data = array(
				'fecha'                => $fecha,
				'hora'                 => $hora,
				'concepto'             => $concepto,
				'indicaciones '        => $instrucciones,
				'id_sucursal_despacho' => $id_sucursal,
				'id_sucursal_destino'  => $id_sucursal_destino,
				'correlativo'          => $correlativo,
				'total'                => $total,
				'id_sucursal'          => $id_sucursal,
				'id_usuario'           => $id_usuario,
				'requiere_imei '       => 0,
				'imei_ingresado'       => 0,
				'guia'                 => ""
			);

			$imei_required = FALSE;
			$id_venta      = $this->traslados->inAndCon('traslado',$data);
			if ($id_venta != NULL) {

				foreach ($data_ingreso as $fila)
				{
					$id_producto     = $fila['id_producto'];
					$costo           = $fila['costo'];
					$cantidad        = $fila['cantidad'];
					$precio_sugerido = $fila['precio_sugerido'];
					$subtotal        = $fila['subtotal'];
					$color           = $fila['color'];
					$estado          = $fila['est'];

					//Descarga del origen
					$form_data = array(
						'id_traslado' => $id_venta,
						'id_producto' => $id_producto,
						'id_color'    => $color,
						'costo'       => $costo,
						'precio'      => $precio_sugerido,
						'cantidad'    => $cantidad,
						'subtotal'    => $subtotal,
						'condicion'   => $estado,
						'garantia'    => $this->traslados->getGarantia($id_producto,$estado),
						'carga' => 0
					);
					$id_detalle = $this->traslados->inAndCon('traslado_detalle',$form_data);
					/*
					$stock_data = $this->traslados->get_stock($id_producto,$color,$id_sucursal);
					$newstock = ($stock_data->cantidad)-$cantidad;
					$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);

					//Carga en el destino
					$stock_data = $this->traslados->get_stock($id_producto,$color,$id_sucursal_destino);
					$newstock = ($stock_data->cantidad)+$cantidad;
					$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);

					*/
					if ($this->traslados->has_imei_required($id_producto)) {
						$imei_required = TRUE;
					}
				}

				if ($imei_required) {
					$this->utils->update(
						"ventas",
						array('requiere_imei' => 1, ),
						"id_venta=$id_venta"
					);
				}
				$this->utils->commit();
				$xdatos["type"]  = "success";
				$xdatos['title'] = "Información";
				$xdatos["msg"]   = "Registo ingresado correctamente!";
			} else {
				$this->utils->rollback();
				$xdatos["type"]  = "error";
				$xdatos['title'] = "Alerta";
				$xdatos["msg"]   = "Error al ingresar el registro";
			}

			echo json_encode($xdatos);
		}
	}

	function imei($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->traslados->get_one_row("ventas", array('id_venta' => $id,));
			if ($row && $id != "") {
				$data = array(
					"row"=>$row,
					"detalles"=>$this->traslados->get_detail_ci($id),
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/ventas_imei.js"
					),
				);
				layout("ventas/cargaimei",$data,$extras);
			} else {
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->utils->begin();

			$errors = false;
			$array_error= array("Log");
			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$id_venta = $this->input->post("id_venta");
			foreach ($data_ingreso as $fila) {
				$form_data = array(
					'id_producto' => $fila['id_producto'],
					'imei' => $fila['imei'],
					'id_detalle' => $fila['id_detalle'],
					'chain' => $fila['chain'],
					'id_venta' => $id_venta,
					'vendido' => 1,
				);

				$id_detalle = $this->traslados->inAndCon('ventas_imei',$form_data);

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
				$this->utils->update("ventas",array('imei_ingresado' => 1, ),"id_venta=$id_venta");
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
			$row = $this->traslados->get_one_row("ventas", array('id_venta' => $id,));

			$info = $this->traslados->get_imei_ci($id);
			$detalles = array();
			$c=0;
			foreach ($info as $key) {
				$detalles[$c]=array(
					'id_venta' => $key->id_venta,
					'id_producto' => $key->id_producto,
					'id_detalle' => $key->id_detalle,
					'nombre' => $key->nombre,
					'chain' => $key->chain,
					'data' => $this->traslados->get_imei_ci_det($key->chain),
				);
				$c++;
			}

			if ($row && $id != "") {
				$data = array(
					"row"=>$row,
					"detalles"=>$detalles,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/ventas_imei.js"
					),
				);
				layout("ventas/editarimei",$data,$extras);
			} else {
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->utils->begin();

			$errors = false;
			$array_error= array("Log");
			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$id_venta = $this->input->post("id_venta");
			foreach ($data_ingreso as $fila) {
				$form_data = array(
					'imei' => $fila['imei'],
				);

				$this->utils->update("ventas_imei",$form_data,"id_imei=$fila[id_imei]");
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
			$id_venta = $this->input->post("id");
			$this->utils->begin();
			$row = $this->traslados->get_one_row("ventas", array('id_venta' => $id_venta,));
			$id_sucursal = $row->id_sucursal;
			/*descargar los detalles previos*/
			$detalles_previos = $this->traslados->get_detail_rows("ventas_detalle", array('id_venta' => $id_venta, ));
			foreach ($detalles_previos as $key) {
				$stock_data = $this->traslados->get_stock($key->id_producto,$key->id_color,$id_sucursal);
				$newstock = ($stock_data->cantidad)+($key->cantidad);
				$this->utils->update("stock",array('cantidad' => $newstock, ),"id_stock=".$stock_data->id_stock);
			}
			/*eliminar detalles previos*/
			$this->utils->delete("ventas_detalle","id_venta=$id_venta");
			$this->utils->delete("ventas","id_venta=$id_venta");


			$this->utils->commit();
			$response["type"] = "success";
			$response["title"] = "Información";
			$response["msg"] = "Registro eliminado con éxito!";

			echo json_encode($response);
		}
	}
	function change(){
		if($this->input->method(TRUE) == "POST"){
			$id_venta = $this->input->post("id");
			$id_estado = $this->input->post("id_estado");
			$this->utils->begin();
			$this->utils->update("ventas",array('id_estado' => $id_estado, ),"id_venta=$id_venta");
			$this->utils->commit();
			$response["type"] = "success";
			$response["title"] = "Información";
			$response["msg"] = "Registro editado con éxito!";
			echo json_encode($response);
		}
	}

	/**
	 * 
	 */
	public function detalle_producto($id=0)
	{
		//$id_sucursal = $this->session->id_sucursal;
		if($id == 0)
		{
			$id_producto = $this->input->post("id");
			$id_color    = $this->input->post("id_s");
			$id_venta    = $this->input->post("id_venta");
			$id_sucursal = $this->input->post("id_sucursal");
		}
		$lista = "";
		$stock_data = $this->traslados->get_stock($id_producto,$id_color,$id_sucursal);
		$prods = $this->traslados->get_producto($id_producto);
		$precios = $this->traslados->get_precios_exis($id_producto);
		$colores = $this->traslados->get_detail_rows("producto_color", array('id_producto' => $id_producto,));

		
		$reservado = $this->ventas->get_reserved_stock($id_sucursal, $id_producto, $id_color);

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
		$xdatos["id_sucursal"] = $id_sucursal;
		$xdatos["marca"] = $prods->marca;
		$xdatos["modelo"] = $prods->modelo;
		$xdatos["costo"] = number_format($costo,2,".","");
		$xdatos["costo_iva"] = number_format($costo_iva,2,".","");
		$xdatos["ocultar"] = $validarCostos;
		echo json_encode($xdatos);
	}
	public function precios_producto($id=0,$precioe=0)
	{
		$id_sucursal = $this->session->id_sucursal;

		$precios = $this->traslados->get_precios_exis($id);
		$lista= "";
		$lista .= "<select class='form-control precios sel' style='width:100%;'>";
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
		$lista .= "</select>";
		$xdatos["precios"] = $lista;
		return $xdatos;
	}


	function get_productos(){
		$query = $this->input->post("query");
		$id_sucursal = $this->input->post("id_sucursal");
		$rows = $this->traslados->get_productos($query,$id_sucursal);
		$output = array();
		if($rows!=NULL) {
			foreach ($rows as $row) {
				$output[] = array(
					//'producto' => $row->id_producto."|".$row->nombre." ".$row->marca." ".$row->modelo,
					'producto' => $row->id_producto."|".$row->modelo." ".$row->color."|".$row->id_color."|".$row->nombre."|".$row->modelo."|".$row->marca."|".$row->codigo_barra."|".$row->color,
				);
			}
		}
		echo json_encode($output);
	}
	function get_clientes(){
		$query = $this->input->post("query");
		$rows = $this->traslados->get_clientes($query);
		$output = array();
		if($rows!=NULL) {
			foreach ($rows as $row) {
				$output[] = array(
					//'producto' => $row->id_producto."|".$row->nombre." ".$row->marca." ".$row->modelo,
					'cliente' => $row->id_cliente."|".$row->nombre,
				);
			}
		}
		echo json_encode($output);
	}

}

/* End of file Ventas.php */
