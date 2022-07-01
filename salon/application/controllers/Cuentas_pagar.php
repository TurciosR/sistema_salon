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

class cuentas_pagar extends CI_Controller
{

	/**
	 * Cuentas_pagar Controller
	 * 
	 * This display module Cuentas_pagar
	 * 
	 * @package		OpenPyme2
	 * @subpackage	Controllers
	 * @category	Controllers
	 * @author		OpenPyme Dev Team
	 * @link		https://docs.apps-oss.com/cuentas_pagar_controller
	 */

	/*
	Enviroment variables
	*/
	private $table = "cuentas_por_pagar";
	private $pk = "id_cuentas_por_pagar";

	function __construct()
	{
		parent::__construct();
		$this->load->Model("Cuentas_pagarModel", "pagar");
		$this->load->helper("upload_file");
		$this->load->model('UtilsModel', "utils");
	}

	/**
	 * Displays the admin page of the Cuentas por pagar module
	 * 
	 * @return void
	 */
	public function index()
	{
		// Preparamos la data para mostrar la vista
		$data = array(
			"titulo"	=> " Cuentas por Pagar
				<div class='row'>
				<div class='col-md-6 text-left small'>
					<i class='mdi mdi-help-circle'></i>
					Haga Click en una fila de la Tabla para realizar los ABONOS
				</div>
				<div class='col-md-6 text-right'>Total: $" . $this->pagar->obtener_total() . " </div>
			</div>",
			"icono"		=> "mdi mdi-format-list-bulleted",
			"table"		=> array(
				"Proveedor"	=> 4,
				"Total"		=> 4,
				"Abono"		=> 4,
				"Saldo"		=> 4
			),
		);
		$extras = array(
			'css' => array(),
			'js' => array("js/scripts/cuentas_pagar.js"),
		);

		layout("template/admin", $data, $extras);
	}

	function get_data()
	{

		$valid_columns = array(
			0 => 'p.nombre',
		);
		// Create query based on mariadb tables required
		$query_val  = $this->pagar->create_dt_query();

		/* You can pass where and join clauses as necessary or include it on model
		 * function as necessary. If no join includ it set to NULL.
		 */
		$options_dt = array(
				'valid_columns' => $valid_columns,
		);
		$options_dt = array_merge($query_val, $options_dt);
		$response   = generate_dt("UtilsModel", $options_dt, FALSE);
		$draw       = intval($this->input->post("draw"));

		if ($response != 0) {
			/**
			 * Group search results by providers
			 */
			$response = $this->agrupar_por_proveedor($response);

			$data = array();
			foreach ($response as $rows) {
				$menudrop  = "<div class='btn-group'><button data-toggle='dropdown'";
				$menudrop .= " class='btn btn-success dropdown-toggle' aria-expanded='false'>";
				$menudrop .= "<i class='mdi mdi-menu' aria-haspopup='false'></i> Menú</button>";
				$menudrop .= "<ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";
				$filename  = base_url("categorias/editar/");
				$menudrop .= "<li><a role='button' href='" . $filename . $rows['id_cuentas'];
				$menudrop .= "' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";
				$menudrop .= "<li><a  class='delete_row'  id=" . $rows['id_cuentas'];
				$menudrop .= " ><i class='mdi mdi-trash-can-outline'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";

				$data[] = array(
					"<input type='hidden' id='cc' class='cc' value='" . $rows['id_cuentas'] . "'>
					 <input type='hidden' id='id_p' class='id_p' value='" . $rows['id_proveedor'] . "'>
					" . $rows['proveedor'],
					$rows['total'],
					$rows['abono'],
					$rows['saldo'],
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
			$data = array(
				"",
				"No se encontraron registros",
				"",
				"",
			);
			$output = array(
				"draw"            => 1,
				"recordsTotal"    => 0,
				"recordsFiltered" => 0,
				"data"            => [$data],
			);
		}
		echo json_encode($output);
	}

	/**
	 * diplay "realizar abono" module
	 * 
	 */
	function realizar_abono()
	{

		// Show interface to pay
		if ($this->input->method(TRUE) == "GET") {

			$id 			= $this->uri->segment(3);
			$cuentaRow 		= $this->pagar->get_row($id);
			$detalleAbonos	= $this->pagar->get_row_abonos($id);

			$data = array(
				"cuenta" => $cuentaRow,
				"detalleAbonos" => $detalleAbonos,
			);

			$extras = array('js' => array("js/scripts/cuentas_pagar.js"));

			layout("cuentas_pagar/realizar_abono", $data, $extras);

		} else if ($this->input->method(TRUE) == "POST") {

			$saldo = $this->input->post("saldo");
			$abono = $this->input->post("abonos");
			$monto = $this->input->post("monto");
			$id_cuentas = $this->input->post("id_cuentas");

			//procedemos a realizar el calculo de el abono
			$abonoTotal = $abono + $monto;
			$saldoTotal = $saldo - $monto;

			$data = array(
				"id_cuentas_por_pagar"	=> $id_cuentas,
				"abono" 				=> $monto,
				"fecha" 				=> date("Y-m-d"),
				"hora" 					=> date("H:i:s"),
			);

			$insert = $this->utils->insert("cuentas_por_pagar_abonos", $data);

			if ($insert) {

				$where = " id_cuentas='" . $id_cuentas . "'";

				if ($saldoTotal == 0) {
					// code...
					$data = array(
						"abono" => $abonoTotal,
						"saldo" => $saldoTotal,
						"estado" => "1",
					);
				} else {
					// code...
					$data = array(
						"abono" => $abonoTotal,
						"saldo" => $saldoTotal,
					);
				}
				$update = $this->utils->update("cuentas_por_pagar", $data, $where);

				$this->utils->commit();
				$xdatos["type"] = "success";
				$xdatos['title'] = 'Exito';
				$xdatos["msg"] = "Registo ingresado correctamente!";
			} else {
				$this->utils->rollback();
				$xdatos["type"] = "error";
				$xdatos['title'] = 'Alerta';
				$xdatos["msg"] = "Error al ingresar el registro";
			}
			echo json_encode($xdatos);
		}
	}

	function agregar()
	{

		if ($this->input->method(TRUE) == "GET") {
			$data = array();
			$extras = array(
				'css' => array(),
				'js' => array(
					"js/scripts/categorias.js",
				),
			);
			layout("productos/agregar_categoria", $data, $extras);
		} else if ($this->input->method(TRUE) == "POST") {

			$descripcion = strtoupper($this->input->post("descripcion"));
			$nombre = strtoupper($this->input->post("nombre"));
			$path = "assets/img/productos/";

			if ($_FILES["foto"]["name"] != "") {
				$imagen = upload_image("foto", $path);
				$url = $path . $imagen;
			} else $url = "";

			$data = array(
				"descripcion" => $descripcion,
				"nombre" => $nombre,
				"imagen" => $url,
				"activo" => 1,
			);
			$response = insert_row($this->table, $data);
			echo json_encode($response);
		}
	}

	function editar($id = -1)
	{
		if ($this->input->method(TRUE) == "GET") {
			$id = $this->uri->segment(3);
			$row = $this->categorias->get_row_info($id);
			$state = $row->activo;
			if ($state == 1) {
				$txt = "Desactivar";
				$show_text = "<span class='badge badge-success font-bold'>Activo<span>";
				$icon = "mdi mdi-toggle-switch-off";
			} else {
				$txt = "Activar";
				$show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
				$icon = "mdi mdi-toggle-switch";
			}
			if ($row && $id != "") {
				$data = array(
					"row" => $row,
					"txt" => $txt,
					"show_text" => $show_text,
					"icon" => $icon,
					"id" => $id,
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"js/scripts/categorias.js"
					),
				);
				layout("productos/editar_categoria", $data, $extras);
			} else {
				redirect('errorpage');
			}
		} else if ($this->input->method(TRUE) == "POST") {
			$descripcion = strtoupper($this->input->post("descripcion"));
			$nombre = strtoupper($this->input->post("nombre"));
			$id_categoria = strtoupper($this->input->post("id_categoria"));
			$row = $this->categorias->get_row_info($id_categoria);
			$where = $this->pk . "='" . $id_categoria . "'";

			$path = "assets/img/productos/";
			if ($_FILES["foto"]["name"] != "") {
				$imagen = upload_image("foto", $path);
				$url = $path . $imagen;
			} else {
				$url = $row->imagen;
			}

			$data = array(
				"descripcion" => $descripcion,
				"nombre" => $nombre,
				"imagen" => $url,
			);
			$response = edit_row($this->table, $data, $where);
			echo json_encode($response);
		}
	}

	function delete()
	{
		if ($this->input->method(TRUE) == "POST") {
			$id = $this->input->post("id");
			$id_cuenta = $this->input->post("id_cuenta");
			$monto = $this->input->post("monto");

			$abono = $this->input->post("abono"); //abono total
			$saldo = $this->input->post("saldo"); //saldo total

			$where = " id_abono='" . $id . "'";
			$response = $this->utils->delete('cuentas_por_pagar_abonos', $where);
			//procedemos a restaurar el valor de cuentas por cobrar

			$abonoTotal = $abono - $monto;
			$saldoTotal = $saldo + $monto;
			$where = " id_cuentas='" . $id_cuenta . "'";
			$data = array(
				"abono" => $abonoTotal,
				"saldo" => $saldoTotal,
			);
			$update = $this->utils->update("cuentas_por_pagar", $data, $where);
			if ($update) {
				$this->utils->commit();
				$xdatos["type"] = "success";
				$xdatos['title'] = 'Exito';
				$xdatos["msg"] = "Registo ingresado correctamente!";
			} else {
				$this->utils->rollback();
				$xdatos["type"] = "error";
				$xdatos['title'] = 'Alerta';
				$xdatos["msg"] = "Error al ingresar el registro";
			}
			echo json_encode($xdatos);
		}
	}
	function update()
	{
		if ($this->input->method(TRUE) == "POST") {
			$id = $this->input->post("id");
			$id_cuenta = $this->input->post("id_cuenta");
			$monto = $this->input->post("monto");

			$montoNuevo = $this->input->post("montoNuevo");
			$abono = $this->input->post("abono"); //abono total
			$saldo = $this->input->post("saldo"); //saldo total

			//$where = " id_abono='".$id."'";
			//$response = $this->utils->delete('cuentas_por_cobrar_abonos',$where);
			//procedemos a restaurar el valor de cuentas por cobrar
			$validacion = $monto - $montoNuevo;
			//echo $abono." - ".$montoNuevo;
			if ($validacion < 0) {
				// se debe sumar al abono...
				$abono += abs($validacion);
				$saldo -= abs($validacion);
			} else {
				$abono -= abs($validacion);
				$saldo += abs($validacion);
			}

			$where = " id_abono='" . $id . "'";
			$data = array(
				"abono" => $montoNuevo,
			);
			$update = $this->utils->update("cuentas_por_pagar_abonos", $data, $where);

			$where = " id_cuentas='" . $id_cuenta . "'";
			if ($saldo == 0) {
				// code...
				$data = array(
					"abono" => $abono,
					"saldo" => $saldo,
					"estado" => "1",
				);
			} else {
				// code...
				$data = array(
					"abono" => $abono,
					"saldo" => $saldo,
				);
			}
			$update = $this->utils->update("cuentas_por_pagar", $data, $where);
			if ($update) {
				$this->utils->commit();
				$xdatos["type"] = "success";
				$xdatos['title'] = 'Exito';
				$xdatos["msg"] = "Registo ingresado correctamente!";
			} else {
				$this->utils->rollback();
				$xdatos["type"] = "error";
				$xdatos['title'] = 'Alerta';
				$xdatos["msg"] = "Error al ingresar el registro";
			}
			echo json_encode($xdatos);
		}
	}

	function state_change()
	{
		if ($this->input->method(TRUE) == "POST") {
			$id = $this->input->post("id");
			$active = $this->categorias->get_state($id);
			$response = change_state($this->table, $this->pk, $id, $active);
			echo json_encode($response);
		}
	}

	/**
	 * display "cuentas por pagar" grouped by "proveedor"
	 */
	public function cuentas_proveedor()
	{

		// get proveedor id
		$id_proveedor = $this->uri->segment(3);

		// Preparamos la data para mostrar la vista
		$data = array(
			"titulo"	=> " Cuentas por pagar a " . $this->pagar->nombre_proveedor($id_proveedor) . "
				<div class='row'>
				<div class='col-md-12 text-right'>Total: $" . $this->pagar->obtener_total($id_proveedor) . " </div>
			</div>",
			"icono"		=> "mdi mdi-format-list-bulleted",
			"table"		=> array(
				"ID" 		=> 4,
				"Fecha" 	=> 4,
				"Documento"	=> 4,
				"Número Doc" => 4,
				"Total" 	=> 4,
				"Saldo"		=> 4,
				"Fecha vence" => 4,
				"Estado"	=> 4,
				"Acción"	=> 4,

			),
			"hidden_id" => $id_proveedor
		);

		$extras = array('js' => array("js/scripts/cuentas_pagar_proveedor.js"));

		layout("template/admin", $data, $extras);
	}

	/**
	 * generate the data for the table "cuentas por pagar proveedor"
	 * 
	 * use the library Customized SSP Class For Datatables Library to fill 
	 * a datatable.
	 * 
	 * for more information see https://github.com/emran/ssp
	 */
	public function get_data_proveedores()
	{

		// get proveedor id
		$id_proveedor = $this->uri->segment(3);

		// DB table to use
		$table = 'cuentas_por_pagar';

		// Table's primary key
		$primaryKey = 'id_cuentas';


		$joinQuery = "FROM $table AS cpp INNER JOIN compra as c ON 
			cpp.id_compra = c.id_compra";

		$extraCondition = "c.id_proveedor = $id_proveedor";

		$columns = array(
			array('db' => 'cpp.id_cuentas', 'dt' => 0, "field" => "id_cuentas"),
			array(
				'db' => 'c.fecha',       	'dt' => 1, "field" => "fecha",
				"formatter" => function ($d) {
					return date("d-m-Y", strtotime($d));
				}
			),
			array('db' => 'c.alias_tipodoc', 'dt' => 2, "as" => "documento", 
			"field" => "documento"),
			array('db' => 'c.numero_doc',   'dt' => 3, "field" => "numero_doc"),
			array('db' => 'cpp.total',   	'dt' => 4, "field" => "total"),
			array('db' => 'cpp.saldo',   	'dt' => 5, "field" => "saldo"),
			array(
				'db' => 'c.dias_credito', 'dt' => 6, "as" => "fecha_vence",
				"field" => "fecha_vence",
				'formatter' => function ($d, $row) {
					return date("d-m-Y", strtotime($row['fecha'] 
						. " + " . $d . " days"));
				},
			),
			array(
				'db' => 'cpp.saldo',   	'dt' => 7, "as" => "estado",
				"field" => "estado",
				"formatter" => function ($d) {
					return ($d <= 0.00)
						? '<span class="label label-success">FINALIZADA</span>'
						: '<span class="label label-danger">PENDIENTE</span>';
				}
			),
			array(
				'db' => 'cpp.id_cuentas', 'dt' => 8, "as" => "accion",
				"field" => "accion",
				"formatter" => function ($d) {
					return '<a href="' . base_url() 
						. 'cuentas_pagar/realizar_abono/' . $d 
						. '" class="btn btn-sm btn-primary m-t-n-xs">ABONAR</a>';
				}
			)
		);
		// SQL server connection informa;tion
		$sql_details = array(
			'user' => $_ENV['DB_USER'],
			'pass' => $_ENV['DB_PASSWORD'],
			'db'   => $_ENV['DB_NAME'],
			'host' => $_ENV['DB_HOST']
		);


		/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		   * If you just want to use the basic configuration for DataTables with PHP
		   * server-side, there is no need to edit below this line.
		*/

		echo json_encode(
			SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraCondition)
		);
	}

	/**
	 * Group search results by providers
	 */
	private function agrupar_por_proveedor($rows)
	{

		$new_row = array();

		foreach ($rows as $row) {
			$new_row[$row->id_proveedor] = [
				'id_cuentas'	=> $row->id_cuentas,
				'id_proveedor' 	=> $row->id_proveedor,
				'id_compra'		=> $row->id_compra,
				'total' 		=> isset($new_row[$row->id_proveedor]['total']) 
					? $new_row[$row->id_proveedor]['total'] + $row->total
				 	: $row->total,
				'abono' 		=> isset($new_row[$row->id_proveedor]['abono'])
					? $new_row[$row->id_proveedor]['abono'] + $row->abono
					: $row->abono,
				'saldo' 		=> isset($new_row[$row->id_proveedor]['saldo'])
					? ($new_row[$row->id_proveedor]['saldo'] + $row->saldo )
					: ($row->saldo),
				'estado' 		=> $row->estado,
				'proveedor' 	=> $row->proveedor
			];
		}

		foreach ($new_row as $key => $item) {
			$new_row[$key]['total'] = number_format($new_row[$key]['total'], 4,".", "");
			$new_row[$key]['abono'] = number_format($new_row[$key]['abono'], 4,".", "");
			$new_row[$key]['saldo'] = number_format($new_row[$key]['saldo'], 4,".", "");
		}

		return $new_row;
	}
}

/* End of file Productos.php */
