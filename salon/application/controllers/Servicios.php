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

class Servicios extends CI_Controller {
	/*
	Global table name
	*/
	private $table = "servicio";
	private $pk = "id_servicio";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->model("ServiciosModel","servicios");
		$this->load->model("InventarioModel","inventario");
		$this->load->helper("upload_file");
	}

	public function index()
	{
		$data = array(
			"titulo"=> "Servicios",
			"icono"=> "mdi mdi-archive",
			"buttons" => array(
				0 => array(
					"icon"=> "mdi mdi-plus",
					'url' => 'servicios/agregar',
					'txt' => ' Agregar servicio',
					'modal' => false,
				),
			),
			"table"=>array(
				"Descripción"=>20,
				//"Costo"=>20,
				"Precio"=>20,
				"Estado"=>10,
				"Acciones"=>10,
			),
		);
		$extras = array(
			'css' => array(
			),
			'js' => array(
				"js/scripts/servicios.js"
			),
		);
		layout("template/admin",$data,$extras);
	}

	function get_data(){
		$valid_columns = array(
			0 => 'servicio.id_servicio',
			1 => 'servicio.nombre',
			2 => 'servicio.costo_s_iva',
			3 => 'servicio.precio_sugerido',
		);
		// Create query based on mariadb tables required
		$query_val  = $this->servicios->create_dt_query();

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

				$menudrop = "<div class='btn-group'>
				<button data-toggle='dropdown' class='btn btn-success dropdown-toggle' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menú</button>
				<ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";
				$filename = base_url("servicios/editar/");
				$menudrop .= "<li><a role='button' href='" . $filename.$rows->id_servicio. "' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";


				$state = $rows->activo;
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
				$menudrop .= "<li><a  class='state_change' data-state='$txt'  id=" . $rows->id_servicio . " ><i class='$icon'></i> $txt</a></li>";

				$menudrop .= "<li><a  class='delete_row'  id=" . $rows->id_servicio . " ><i class='mdi mdi-trash-can-outline'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";


				$data[] = array(
					$rows->nombre,
					//$rows->costo_s_iva,
					$rows->precio_sugerido,
					$show_text,
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
				"No se encontraron registros",
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

	function agregar(){
		if($this->input->method(TRUE) == "GET"){

			$data = array(
				"titulo"=>"Servicios",

			);
			$extras = array(
				'css' => array(
				),
				'js' => array(
					"js/scripts/servicios.js"
				),
			);
			layout("servicios/agregar_servicio",$data,$extras);
		}
		else if($this->input->method(TRUE) == "POST"){
			$servicio = strtoupper($this->input->post("servicio"));
			$nombre = strtoupper($this->input->post("nombre"));
			$categoria = strtoupper($this->input->post("categoria"));

			$costo_s_iva = $this->input->post("ultcosto");
			$costo_c_iva = $this->input->post("costo_c_iva");
			$precio_sugerido = $this->input->post("precio_sug");
			$precio_minimo = $this->input->post("precio_min");
			$dias_garantia = 0;
			$preciosg = $this->input->post("preciosg");

			$data = array(
				//"id_categoria"=>$categoria,
				"nombre"=>$nombre,
				"costo_s_iva"=>$costo_s_iva,
				"costo_c_iva"=>$costo_s_iva,
				"precio_sugerido"=>$precio_sugerido,
				"precio_minimo"=>$precio_sugerido,
				"dias_garantia"=>0,
				"activo"=>1,
				"deleted"=> 0,
			);

			$this->utils->begin();
      $row_existe = $this->utils->get_one_row("servicio", array('nombre'=> $nombre,));
 		 if($row_existe==NULL){

			$servicio = $this->servicios->insertar_servicio($data);
			if($servicio!=NULL){
				$this->utils->commit();
				$xdatos["type"]="success";
				$xdatos['title']='Información';
				$xdatos["msg"]="Registro ingresado correctamente!";
			}
			else {
				$this->utils->rollback();
				$xdatos["type"]="error";
				$xdatos['title']='Alerta';
				$xdatos["msg"]="Error al ingresar el registro";
			}



		}
  	else {
      $this->utils->rollback();
      $xdatos["type"]="error";
      $xdatos['title']='Alerta';
      $xdatos["msg"]="Ya  existe un registro con ese mismo nombre!!!";
    }
    	echo json_encode($xdatos);
	}
}
	function editar($id=-1){

		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->servicios->get_row_info($id);
			if($row && $id!=""){

				$data = array(
					"row"=>$row,
				);
				$extras = array(
					'css' => array(
						"libs/jquery_image_multiple/image-uploader.min.css"
					),
					'js' => array(
						"libs/jquery_image_multiple/image-uploader.min.js",
						"js/scripts/servicios.js"
					),
				);
				layout("servicios/editar_servicio",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			//$servicio = strtoupper($this->input->post("servicio"));
			$id_servicio =$this->input->post("id_servicio");
			$nombre = strtoupper($this->input->post("nombre"));

			$precio_sugerido = $this->input->post("precio_sug");

			$data = array(
				//"id_categoria"=>$categoria,
				"nombre"=>$nombre,
				"costo_s_iva"=>$precio_sugerido,
				"costo_c_iva"=>$precio_sugerido,
				"precio_sugerido"=>$precio_sugerido,
				"precio_minimo"=>$precio_sugerido,
				//"dias_garantia"=>$dias_garantia,
				"activo"=>1,
				"deleted"=> 0,
			);
			$where = "id_servicio='".$id_servicio."'";
			$this->utils->begin();
			$update = $this->utils->update($this->table,$data,$where);
			if($update){
				$this->utils->commit();
				$xdatos["type"]="success";
				$xdatos['title']='Información';
				$xdatos["msg"]="Registro actualizado correctamente!";
			}
			else {
				$this->utils->rollback();
				$xdatos["type"]="error";
				$xdatos['title']='Alerta';
				$xdatos["msg"]="Error al actualizar el registro";
			}


			echo json_encode($xdatos);
		}
	}
	public function costos()
	{
		$icesc = $this->input->post("cesc");
		$costo = $this->input->post("costo");
		$precio_min = $this->input->post("precio_min");
		$precio_sug = $this->input->post("precio_sug");
		$lista = "";
		$precios = $this->servicios->get_precios();
		$impuestos = $this->servicios->get_impuestos();
		$imp_iva=$impuestos->iva/100;
		$imp_cesc=$impuestos->cesc/100;
		$iva = round($costo *$imp_iva,2);
		if($icesc == "true")
		{
			$cesc = round($costo * $imp_cesc,2);
		}
		else {
			$cesc = round($costo * 0.00,2);
		}
		$lista = "";
		$ctotal = $costo+$iva+$cesc;

			$detalle = 'DETALLE DE SERVICIO';
			//$resultado = round($costo * ($porcentaje / 100) , 2);
			$gana1 = round($precio_min - $ctotal,2) ;
			if($gana1<0)
				$gana1 = 0.00;
			$gana2 = round( $precio_sug - $ctotal , 2);
			if($gana2<0)
				$gana2 = 0.00;
			$lista .= "<tr>";

			$lista .= "<td style='text-align: right' class='td_desc'><input type='text' style='width:350px;' class='form-control desc_td' id='desc_td' name='desc_td' value='".$detalle."' readonly></td>";
			$lista .= "<td style='text-align: right' class='td_costo'><input type='hidden' class='form-control costo_td' id='costo_td' name='costo_td' value='".$costo."'>$ ".number_format($costo,2)."</td>";
			$lista .= "<td style='text-align: right' class='td_costo_iva'><input type='hidden' class='form-control costo_td_iva' id='costo_td_iva' name='costo_td_iva' value='".$iva."'>$ ".number_format($iva,2)."</td>";
			$lista .= "<td style='text-align: right' class='td_precio'><input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='".$cesc."'>$ ".number_format($cesc, 2)."</td>";
			$lista .= "<td style='text-align: right' class='td_precio_iva'><input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='".$ctotal."'>$ ".number_format($ctotal,2)."</td>";
			$lista .= "<td style='text-align: right' class='td_porcentaje'><input type='hidden' class='form-control ganancia_min_td' id='ganancia_min_td' name='ganancia_min_td' value='".$gana1."'>$ ".number_format($gana1,2)."</td>";
			$lista .= "<td style='text-align: right' class='td_ganancia'><input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='".$gana2."'>$ ".number_format($gana2, 2)."</td>";
			$lista .= "</tr>";

		echo $lista;
	}
	public function precios()
	{
		$icesc = $this->input->post("cesc");
		$costo = $this->input->post("costo");
		$lista = "";
		$precios = $this->servicios->get_precios();

		$iva = round($costo * 0.13,2);
		if($icesc == "true")
		{
			$cesc = round($costo * 0.05,2);
		}
		else {
			$cesc = round($costo * 0.00,2);
		}
		$lista = "";
		$ctotal = $costo+$iva+$cesc;
		foreach ($precios as $row_por)
		{

			$id_porcentaje = $row_por->id_porcentaje;
			$porcentaje = $row_por->porcentaje;
			$detalle = $row_por->descripcion;

			$resultado = round($costo * ($porcentaje / 100) , 2);
			$resultado1 = $costo + $resultado;
			$resultado2 = round($resultado1 * 1.13, 2);
			$gana = $resultado2 - $ctotal;
			$lista .= "<tr>";

			$lista .= "<td style='text-align: right' class='td_desc'><input type='text' style='width:350px;' class='form-control desc_td' id='desc_td' name='desc_td' value='".$detalle."' readonly></td>";
			$lista .= "<td style='text-align: right' class='td_costo'><input type='hidden' class='form-control costo_td' id='costo_td' name='costo_td' value='".$costo."'>$ ".number_format($costo,2)."</td>";
			$lista .= "<td style='text-align: right' class='td_costo_iva'><input type='hidden' class='form-control costo_td_iva' id='costo_td_iva' name='costo_td_iva' value='".$iva."'>$ ".number_format($iva,2)."</td>";
			$lista .= "<td style='text-align: right' class='td_precio'><input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='".$cesc."'>$ ".number_format($cesc, 2)."</td>";
			$lista .= "<td style='text-align: right' class='td_precio_iva'><input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='".$ctotal."'>$ ".number_format($ctotal,2)."</td>";
			$lista .= "<td style='text-align: right' class='td_porcentaje'><input type='hidden' class='form-control ganancia_min_td' id='ganancia_min_td' name='ganancia_min_td' value='".$resultado2."'>$ ".number_format($resultado2,2)."</td>";
			$lista .= "<td style='text-align: right' class='td_ganancia'><input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='".$gana."'>$ ".number_format($gana, 2)."</td>";
			$lista .= "<td style='text-align: right'><button type='button' id='delete' class='btn btn-success delete'><i class='mdi mdi-delete'></i></button></td>";
			$lista .= "</tr>";
		}
		echo $lista;
	}
	function proveedores(){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->servicios->get_row_info($id);
			if($row && $id!=""){
				$proveedores = $this->servicios->get_proveedores($id);
				$data = array(
					"row"=>$row,
					"proveedores"=>$proveedores,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/servicios.js"
					),
				);
				layout("servicios/proveedores",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}else if($this->input->method(TRUE) == "POST"){
			$servicio = $this->input->post("servicio");
			$proveedores = $this->input->post("proveedores");

			$this->utils->begin();
			if(isset($proveedores)) {
				foreach ($proveedores as $rec) {

					if ($rec["id_pp"] == 0) {
						$form_detalle = array(
							"servicio" => $servicio,
							"id_proveedor" => $rec["id_proveedor"],
						);
						$this->utils->insert("servicio_proveedor", $form_detalle);
					} else {
						$form_detalle = array(
							"servicio" => $servicio,
							"id_proveedor" => $rec["id_proveedor"],
						);
						$wherer = " id_pp='" . $rec["id_pp"] . "'";
						$this->utils->update("servicio_proveedor", $form_detalle, $wherer);
					}
				}
			}

			$this->utils->commit();
			$xdatos["type"]="success";
			$xdatos['title']='Exito';
			$xdatos["msg"]="Registo guardado correctamente!";

			echo json_encode($xdatos);
		}
	}

function get_proveedor_autocomplete(){
	$query = $this->input->post("query");
	$rows = $this->servicios->get_proveedor_autocomplete($query);
	$output = array();
	if($rows!=NULL) {
		foreach ($rows as $row) {
			$output[] = array(
				'proveedor' => $row->id_proveedor . " | " . $row->nombre
			);
		}
	}
	echo json_encode($output);
}

function eliminar_proveedor(){
	if($this->input->method(TRUE) == "POST"){
		$id = $this->input->post("id");
		$where = " id_pp ='".$id."'";
		$this->utils->begin();
		$delete = $this->utils->delete("servicio_proveedor",$where);
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

function get_images(){
	if($this->input->method(TRUE) == "POST"){
		$id = $this->input->post("id");
		$servicios = $this->servicios->get_images($id);
		$new = [];
		foreach ($servicios as $row){
			array_push($new, array('id'=>$row->id_imagen, 'imagen'=>base_url($row->url)));
		}
		echo json_encode($new);
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
		$active = $this->servicios->get_state($id);
		$response = change_state($this->table,$this->pk,$id,$active);
		echo json_encode($response);
	}
}

function eliminar_color(){
	if($this->input->method(TRUE) == "POST"){
		$id = $this->input->post("id");
		$color = $this->input->post("color");
		$where = " servicio='".$id."' AND color ='".$color."'";
		$this->utils->begin();
		$delete = $this->utils->delete("servicio_color",$where);
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
function get_idColor(){
	if($this->input->method(TRUE) == "POST"){
		$servicio = $this->input->post("id");
		$color = $this->input->post("color");

		$row = $this->servicios->get_idColor($servicio,$color);
		if($row!=NULL){
			$id_color=$row->id_color;
		}
		else{
			$id_color=-1;
			$data["type"] = "Error";
			$data["title"] = "Alerta!";
			$data["msg"] = "Registro no pudo ser eliminado, tiene stock!";

			echo json_encode($data);

			}
		//ver si tiene stock
		if($id_color>0){
			$row2 = $this->servicios->get_color_stock($servicio,$id_color);
			if($row2!=NULL){
				$id_stock=$row2->id_stock;

				$cantidad=$row2->cantidad;
				$data["type"] = "Error";
				$data["title"] = "Alerta!";
				$data["msg"] = "Registro no pudo ser eliminado, tiene stock!";

				echo json_encode($data);
			}
			else{

				 $this->borrar_color($servicio,$id_color);
			}
		}
	}
}
	function borrar_color($servicio,$id_color){
			$where = " servicio='".$servicio."' AND id_color ='".$id_color."'";
			$this->utils->begin();
			$delete = $this->utils->delete("servicio_color",$where);
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
	function get_Color($servicio,$color){
		$row = $this->servicios->get_idColor($servicio,$color);
		if($row==NULL){
			return -1;
		}else{
			$id_color=$row->id_color;
			return $id_color;
		}


	}
}
/* End of file servicios.php */
