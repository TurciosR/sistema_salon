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

class cuentas_cobrar extends CI_Controller {

	/*
	Enviroment variables
	*/
	private $table = "categoria";
	private $pk = "id_categoria";

	function __construct()
	{
		parent::__construct();
		$this->load->Model("Cuentas_cobrarModel","cobrar");
		$this->load->helper("upload_file");
		$this->load->model('UtilsModel',"utils");
	}

	public function index()
	{
		$data = array(
			"titulo"=> "Cuentas por Cobrar
				<div class='row'>
				<div class='col-md-12 text-left small'>
					<i class='mdi mdi-help-circle'></i>
					Haga Click en una fila de la Tabla para realizar los ABONOS
				</div>
			</div>",
			"icono"=> "mdi mdi-format-list-bulleted",
			"buttons" => array(
				//0 => array(
					//"icon"=> "mdi mdi-plus",
					//'url' => 'cuentas_cobrar/agregar',
					//'txt' => 'Agregar Categoria',
					//'modal' => false,
				//),
			),
			"table"=>array(
				// "ID"=>1,
				"Nombre"=>4,
				"Tipo Doc"=>4,
				"Serie"=>4,
				"Total"=>4,
				"Abono"=>4,
				"Saldo"=>4,
				"Estado"=>2,
				// "Acciones"=>1,
			),
		);

		$extras = array(
			'css' => array(
			),
			'js' => array(
				"js/scripts/cuentas_cobrar.js",
			),
		);

		layout("template/admin",$data,$extras);
	}

	function get_data(){

		$valid_columns = array(
			0 => 'c.nombre',
			1 => 'v.serie',
		);

		// Create query based on mariadb tables required
		$query_val  = $this->cobrar->create_dt_query();

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
			$data = array();
			foreach ($response as $rows) {
				$menudrop  = "<div class='btn-group'><button data-toggle='dropdown'";
				$menudrop .= " class='btn btn-success dropdown-toggle' aria-expanded='false'>";
				$menudrop .= "<i class='mdi mdi-menu' aria-haspopup='false'></i> Menú</button>";
				$menudrop .= "<ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";
				$filename = base_url("categorias/editar/");
				$menudrop .= "<li><a role='button' href='" . $filename.$rows->id_cuentas;
				$menudrop .= "' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";
				$menudrop .= "<li><a  class='delete_row'  id=" . $rows->id_cuentas;
				$menudrop .= " ><i class='mdi mdi-trash-can-outline'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";

				if (isset($rows->estado)) {
					if ($rows->estado == 'pendiente') {
						$estado = "<span class='badge badge-danger'>" . strtoupper($rows->estado) . "</span>";
					} else {
						$estado = "<span class='badge badge-success'>" . strtoupper($rows->estado) . "</span>";
					}
				}
				$data[] = array(
					"<input type='hidden' id='cc' class='cc' value='".$rows->id_cuentas."'>".$rows->nombre,
					$rows->tipo_doc,
					$rows->serie,
					$rows->total,
					$rows->abono,
					$rows->saldo,
					$estado,
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
			);
			$output = array(
				"draw"            => $draw,
				"recordsTotal"    => 0,
				"recordsFiltered" => 0,
				"data"            => $data
			);
		}
		echo json_encode($output);
	}

	function realizar_abono()
	{
		if ($this->input->method(TRUE) == "GET") {
			$id			= $this->uri->segment(3);
			$cuentaRow	 = $this->cobrar->get_row($id);
			$detalleAbonos = $this->cobrar->get_row_abonos($id);
			$data		  = array(
				"cuenta"		=> $cuentaRow,
				"detalleAbonos" => $detalleAbonos,
			);
			$extras = array(
				'css' => array(
				),
				'js'  => array(
					"js/scripts/cuentas_cobrar.js",
				),
			);
			layout("cuentas_cobrar/realizar_abono", $data, $extras);
		}
		else if ($this->input->method(TRUE) == "POST") {
			$saldo = $this->input->post("saldo");
			$abono = $this->input->post("abonos");
			$monto = $this->input->post("monto");
			$id_cuentas = $this->input->post("id_cuentas");

			//procedemos a realizar el calculo de el abono
			$abonoTotal = $abono + $monto;
			$saldoTotal = $saldo - $monto;

			$data = array(
				"id_cuentas_por_cobrar" => $id_cuentas,
				"abono"				 => $monto,
				"fecha"				 => date("Y-m-d"),
				"hora"				  => date("H:i:s"),
			);
			$insert = $this->utils->insert("cuentas_por_cobrar_abonos",$data);
			if ($insert) {
				$where = " id_cuentas='".$id_cuentas."'";
				if ($saldoTotal==0) {
					$data = array(
						"abono"  => $abonoTotal,
						"saldo"  => $saldoTotal,
						"estado" => "1",
					);
				} else {
					$data = array(
						"abono" => $abonoTotal,
						"saldo" => $saldoTotal,
					);
				}

				$update = $this->utils->update("cuentas_por_cobrar",$data, $where);

				$this->utils->commit();
				$xdatos["type"]="success";
				$xdatos['title']='Exito';
				$xdatos["msg"]="Registo ingresado correctamente!";
			} else {
				$this->utils->rollback();
				$xdatos["type"]="error";
				$xdatos['title']='Alerta';
				$xdatos["msg"]="Error al ingresar el registro";
			}
			echo json_encode($xdatos);
		}
	}

	function agregar(){

		if($this->input->method(TRUE) == "GET"){
			$data = array(
			);
			$extras = array(
				'css' => array(
				),
				'js' => array(
					"js/scripts/categorias.js",
				),
			);
			layout("productos/agregar_categoria",$data,$extras);
		}
		else if($this->input->method(TRUE) == "POST"){

			$descripcion = strtoupper($this->input->post("descripcion"));
			$nombre = strtoupper($this->input->post("nombre"));
			$path = "assets/img/productos/";

			if ($_FILES["foto"]["name"] != "") {
				$imagen = upload_image("foto",$path);
				$url=$path.$imagen;
			}
			else $url = "";

			$data = array(
				"descripcion"=>$descripcion,
				"nombre"=>$nombre,
				"imagen"=>$url,
				"activo"=>1,
			);
			$response = insert_row($this->table,$data);
			echo json_encode($response);
		}
	}

	function editar($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->categorias->get_row_info($id);
			$state = $row->activo;
			if($state==1){
					$txt = "Desactivar";
					$show_text = "<span class='badge badge-success font-bold'>Activo<span>";
					$icon = "mdi mdi-toggle-switch-off";
			}
			else{
					$txt = "Activar";
					$show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$icon = "mdi mdi-toggle-switch";
			}
			if($row && $id!=""){
				$data = array(
					"row"=>$row,
					"txt"=>$txt,
					"show_text"=>$show_text,
					"icon"=>$icon,
					"id"=>$id,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/categorias.js"
					),
				);
				layout("productos/editar_categoria",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$descripcion = strtoupper($this->input->post("descripcion"));
			$nombre = strtoupper($this->input->post("nombre"));
			$id_categoria = strtoupper($this->input->post("id_categoria"));
			$row = $this->categorias->get_row_info($id_categoria);
			$where = $this->pk."='".$id_categoria."'";

			$path = "assets/img/productos/";
			if ($_FILES["foto"]["name"] != "") {
				$imagen = upload_image("foto",$path);
				$url=$path.$imagen;
			}
			else{
				$url = $row->imagen;
			}

			$data = array(
				"descripcion"=>$descripcion,
				"nombre"=>$nombre,
				"imagen"=>$url,
			);
			$response = edit_row($this->table,$data,$where);
			echo json_encode($response);
		}
	}

	function delete(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$id_cuenta = $this->input->post("id_cuenta");
			$monto = $this->input->post("monto");

			$abono = $this->input->post("abono");//abono total
			$saldo = $this->input->post("saldo");//saldo total

			$where = " id_abono='".$id."'";
	  $response = $this->utils->delete('cuentas_por_cobrar_abonos',$where);
			//procedemos a restaurar el valor de cuentas por cobrar

			$abonoTotal = $abono - $monto;
			$saldoTotal = $saldo + $monto;
			$where = " id_cuentas='".$id_cuenta."'";
			$data = array(
				"abono"=>$abonoTotal,
				"saldo"=>$saldoTotal,
				"estado"=>"0",
			);
			$update = $this->utils->update("cuentas_por_cobrar",$data, $where);
			if($update){
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
	function update(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$id_cuenta = $this->input->post("id_cuenta");
			$monto = $this->input->post("monto");

			$montoNuevo = $this->input->post("montoNuevo");
			$abono = $this->input->post("abono");//abono total
			$saldo = $this->input->post("saldo");//saldo total

			//$where = " id_abono='".$id."'";
	  //$response = $this->utils->delete('cuentas_por_cobrar_abonos',$where);
			//procedemos a restaurar el valor de cuentas por cobrar
			$validacion = $monto - $montoNuevo;
			//echo $abono." - ".$montoNuevo;
			if ($validacion<0) {
				// se debe sumar al abono...
				$abono +=abs($validacion);
				$saldo -=abs($validacion);
			}
			else{
				$abono -=abs($validacion);
				$saldo +=abs($validacion);
			}

			$where = " id_abono='".$id."'";
			$data = array(
				"abono"=>$montoNuevo,
			);
			$update = $this->utils->update("cuentas_por_cobrar_abonos",$data, $where);

			$where = " id_cuentas='".$id_cuenta."'";
			if ($saldo==0) {
				// code...
				$data = array(
					"abono"=>$abono,
					"saldo"=>$saldo,
					"estado"=>"1",
				);
			}
			else {
				// code...
				$data = array(
					"abono"=>$abono,
					"saldo"=>$saldo,
				);
			}
			$update = $this->utils->update("cuentas_por_cobrar",$data, $where);
			if($update){
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

	function state_change(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$active = $this->categorias->get_state($id);
			$response = change_state($this->table,$this->pk,$id,$active);
			echo json_encode($response);
		}
	}

}

/* End of file Productos.php */
