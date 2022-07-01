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

class Carrier extends CI_Controller {
	/*
	Global table name
	*/
	private $table = "stock";
	private $pk = "id_producto";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->model("CarrierModel","carrier");
	}
	/*********************************************************/
	/*********************************************************/
	/************************CARGAS***************************/
	/*********************************************************/
	/*********************************************************/
	public function index()
	{
		$data = array(
			"titulo"=> "Carriers",
			"icono"=> "mdi mdi-cart",
			"buttons" => array(
				0 => array(
					"icon"=> "mdi mdi-plus",
					'url' => 'carrier/agregar',
					'txt' => ' Nuevo Carrier',
					'modal' => false,
				),
			),
			"selects" => array(
				0 => array(
					"name" => "sucursales",
					"data" => $this->carrier->get_detail_rows("sucursales",array('1' => 1, )),
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
				"ID"=>5,
				"Nombre"=>35,
				"Telefono"=>10,
				"Contacto"=>15,
				"Acciones"=>10,
			),
		);
		$extras = array(
			'css' => array(
			),
			'js' => array(
				"js/scripts/carrier.js"
			),
		);
		layout("template/admin",$data,$extras);
	}

	function agregar(){
		if($this->input->method(TRUE) == "GET"){

			$data = array(
				"sucursal"=>$this->carrier->get_detail_rows("sucursales",array('1' => 1, )),
				"id_sucursal" => $this->session->id_sucursal,
			);

			$extras = array(
				'css' => array(
				),
				'js' => array(
					"js/scripts/carrier.js"
				),
			);

			layout("carrier/agregar_carrier",$data,$extras);
		}
		else if($this->input->method(TRUE) == "POST"){
			$this->utils->begin();
			$nombre = $this->input->post("nombre");
			$telefono = $this->input->post("telefono");
			$contacto = $this->input->post("contacto");
			$sucursal = $this->input->post("sucursal");
			$json_arr = json_decode($this->input->post("json_arr"), true);

			$data = array(
				'nombre' => $nombre,
				'telefono' => $telefono,
				'contacto' => $contacto,
				'id_sucursal' => $sucursal,
			);

			$insert = $this->utils->insert('carrier',$data);
			if($insert){
				$id_carrier = $this->utils->insert_id();
				foreach ($json_arr as $fila)
				{
					$tipo = $fila['tipo'];
					$descripcion = $fila['descripcion'];
					$monto = $fila['monto'];

					$form_data = array(
						'id_carrier' => $id_carrier,
						'tipo' => $tipo,
						'descripcion' => $descripcion,
						'monto' => $monto,
					);
					$insert_detalle = $this->utils->insert('carrier_detalle', $form_data);

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

	function get_data(){
		$draw = intval($this->input->post("draw"));
		$start = intval($this->input->post("start"));
		$length = intval($this->input->post("length"));
		$id_sucursal = $this->input->post("id_sucursal");

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
      0 => 'nombre',
			1 => 'telefono',
			2 => 'contacto',
		);
		if (!isset($valid_columns[$col])) {
			$order = null;
		} else {
			$order = $valid_columns[$col];
		}

		$row = $this->carrier->get_collection($order, $search, $valid_columns, $length, $start, $dir, $id_sucursal);
		//print_r($row);
		if ($row != 0) {
			$data = array();
			foreach ($row as $rows) {

				$menudrop = "<div class='btn-group'>
				<button data-toggle='dropdown' class='btn btn-success dropdown-toggle' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";


				// $menudrop .= "<li><a  data-toggle='modal' data-target='#viewModal' data-refresh='true'  role='button' class='detail' data-id=".$rows->id_carrier."><i class='mdi mdi-eye-check' ></i> Detalles</a></li>";
				$menudrop .= "<li><a  role='button' class='status_change' data-id=".$rows->id_carrier."><i class='mdi mdi-state-machine' ></i> Cambiar estado</a></li>";
				// $menudrop .= "<li><a href=".base_url("ventas/garantia/").$rows->id_venta." target='_blank'><i class='mdi mdi-certificate-outline' ></i> Garantia</a></li>";
				$menudrop .= "</ul></div>";


				$data[] = array(
					"<input type='hidden' id='estado' class='estado' value='".$rows->estado."'>".$rows->id_carrier,
					"<input type='hidden' id='id_carrierx' class='id_carrierx' value='".$rows->id_carrier."'>".$rows->nombre,
					$rows->telefono,
					$rows->contacto,
					$menudrop,
				);
			}
			$total = $this->carrier->total_rows();
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $total,
				"recordsFiltered" => $total,
				"data" => $data
			);
		} else {
			$data[] = array(
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
		exit();
	}

	function delete(){
		if($this->input->method(TRUE) == "POST"){
			$id_venta = $this->input->post("id");
			$this->utils->begin();
			$row = $this->ventas->get_one_row("ventas", array('id_venta' => $id_venta,));
			$id_sucursal = $row->id_sucursal;
			/*descargar los detalles previos*/
			$detalles_previos = $this->ventas->get_detail_rows("ventas_detalle", array('id_venta' => $id_venta, ));
			foreach ($detalles_previos as $key) {
				// code...
				$stock_data = $this->ventas->get_stock($key->id_producto,$id_sucursal);
				$newstock = ($stock_data->cantidad)+($key->cantidad);
				$this->utils->update("stock",array('cantidad' => $newstock, ),"id_producto=$key->id_producto AND id_sucursal=$id_sucursal");
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
			$id_carrier = $this->input->post("id_carrier");
			$estado = $this->input->post("estado");
			$this->utils->begin();
			$this->utils->update("carrier",array('estado' => $estado, ),"id_carrier=".$id_carrier);
			$this->utils->commit();
			$response["type"] = "success";
			$response["title"] = "Información";
			$response["msg"] = "Registro editado con éxito!";
			echo json_encode($response);
		}
	}


}

/* End of file Ventas.php */
