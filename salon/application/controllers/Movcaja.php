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
include APPPATH . 'libraries/AlignMarginText.php';

class Movcaja extends CI_Controller {

	/*
	Enviroment variables
	*/
	private $table = "mov_caja";
	private $pk = "id_mov";

	function __construct()
	{
		parent::__construct();
		$this->load->Model("MovcajaModel","movcaja");
		//$this->load->Model("CajaModel","caja");
		$this->load->model('UtilsModel',"utils");
		$this->load->library('user_agent');
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
			"titulo"=> "Moviemtos de caja",
			"icono"=> "mdi mdi-format-list-bulleted",
			"buttons" => array(
				0 => array(
					"icon"=> "mdi mdi-plus",
					'url' => 'movcaja/agregarvale',
					'txt' => 'Agregar vale',
					'modal' => true,
				),
				1 => array(
					"icon"=> "mdi mdi-plus",
					'url' => 'movcaja/agregar_ing',
					'txt' => 'Agregar ingreso',
					'modal' => true,
				),
			),
			"inputs" => array(
				0 => array(
					"name"=> "fecha1",
					"type"=> "text",
					"value"=> date('01-m-Y'),
					"classes" =>"form-control datepicker",
					'txt' => 'Fecha 1',
					'placeholder' => 'Ingresar fecha final',
					'modal' => false,
					'extra'=>'required data-parsley-trigger="change"',
				),
				1 => array(
					"name"=> "fecha2",
					"type"=> "text",
					"value"=>	date('d-m-Y'),
					"classes" =>"form-control datepicker",
					'txt' => 'Fecha 2',
					'placeholder' => 'Ingresar fecha final',
					'modal' => false,
					'extra'=>'required data-parsley-trigger="change"',
				),

				2 => array(
					"name"=> "btn_consulta",
					"type"=> "button",
					"value"=>	'Buscar',
					"classes" =>"form-control btn btn-primary btn_consultar",
					'txt' => 'Buscar',
					'placeholder' => '',
					'modal' => false,
					'extra'=>'',
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
				"No."=>3,
				"Concepto"=>15,
				"Fecha"=>5,
				"Monto"=>5,
				"Recibe"=>10,
				"Tipo Movimiento"=>5,
				"Estado"=>5,
				"Acciones"=>5,
			),
		);

		$extras = array(
			'css' => array(
			),
			'js' => array(
				"js/scripts/movcaja.js",
			),
		);

		layout("template/admin",$data,$extras);
	}

	function get_data()
	{
		$fecha1        = Y_m_d($this->input->post("fecha1"));
		$fecha2        = Y_m_d($this->input->post("fecha2"));
		$valid_columns = array(
			0 => 'mc.id_mov',
			1 => 'mc.concepto',
			2 => 'mc.fecha',
			3 => 'mc.valor',
			4 => 'mc.nombre_recibe',
			5 => 'mc.id_tipo',
			6 => 'mc.anulado',
		);
		// Create query based on mariadb tables required
		$query_val  = $this->movcaja->create_dt_query();
		$where  = array(
			"mc.id_sucursal" => $this->input->post("id_sucursal"),
			"mc.fecha BETWEEN '$fecha1' AND '$fecha2' " => NULL,
		);
		/* You can pass where and join clauses as necessary or include it on model
		 * function as necessary. If no join includ it set to NULL.
		 */
		$options_dt = array(
				'valid_columns' => $valid_columns,
				'where'         => $where,
		);
		$options_dt = array_merge($query_val, $options_dt);
		$row        = generate_dt("UtilsModel", $options_dt, FALSE);
		$draw       = intval($this->input->post("draw"));

		if ($row != 0) {
			$data = array();
			foreach ($row as $rows) {
				$menudrop = "<div class='btn-group'>
				<button data-toggle='dropdown' class='btn btn-success dropdown-toggle' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";
				//$filename = base_url("movcaja/editar/");

				$tipo = $rows->id_tipo;
				$state = $rows->anulado;
				if($tipo==0){
					$txt = "Entrada";
					$show_text = "<span class='badge badge-info font-bold'>".$txt."<span>";
					$icon = "mdi mdi-toggle-switch-off";
				}
					if($tipo=="1"){
					$txt = "Salida";
					$show_text = "<span class='badge badge-warning font-bold'>".	$txt."<span>";
					$icon = "mdi mdi-toggle-switch";
				}
				if($state==0){
					$txt = "Activo";
					$show_text_st = "<span class='badge badge-success font-bold'>".$txt."<span>";

				}
					else{
					$txt = "Anulado";
					$show_text_st = "<span class='badge badge-danger font-bold'>".	$txt."<span>";

				}
				$menudrop .= "<li><a data-toggle='modal' data-target='#viewModal' data-refresh='true'role='button' class='modal_edit' data-id=".$rows->id_mov." data-type=".$tipo."><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";
				$menudrop .= "<li><a class='state_change' data-state='Anular' id=" . $rows->id_mov . " ><i class='$icon'></i> Anular</a></li>";
				$menudrop .= "</ul></div>";

				$data[] = array(
					$rows->id_mov,
					$rows->concepto,
					$rows->fecha,
					$rows->valor,
					$rows->nombre_recibe,
					$show_text,
					$show_text_st,
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

	function agregarvale(){
			$this->load->library('user_agent');
		//El boton que activa el modal se define en la vista admin pero en js se activa el click para cargar dicho modal
		if($this->input->method(TRUE) == "GET"){
				$data = array();
				$this->load->view("movcaja/agregar_vale",$data);
		}
		else if($this->input->method(TRUE) == "POST"){
			$valor= $this->input->post("monto2");
			$recibe = strtoupper($this->input->post("recibe2"));
			$concepto =strtoupper($this->input->post("concepto2"));
 			$hora = date("H:i:s");
			$fecha = date('Y-m-d');
			$id_sucursal=$this->session->id_sucursal;
			$id_usuario=$this->session->id_usuario;
			$row_ap = $this->movcaja->get_caja_activa($id_usuario,$fecha);

			if($row_ap!=NULL){
				if($row_ap->vigente==1){
						$turno = $row_ap->turno;
						$caja=$row_ap->caja;
						$id_apertura=$row_ap->id_apertura;

				$data = array(
					"concepto"=>$concepto,
					"valor"=>$valor,
							"fecha"=>$fecha,
							"hora"=>$hora,
							"nombre_recibe"=>$recibe,
							"id_tipo"=>1,
							"entrada"=>0,
							"salida"=>1,
							"id_sucursal"=>$id_sucursal,
					"id_empleado"=>$id_usuario,
							'turno' => $turno,
							'caja'=>$caja,
							'id_apertura'=>$id_apertura,
					);
				}
					$id_mov = $this->movcaja->inAndCon($this->table,$data);
			}else{
				$id_mov = NULL;
			}


							if ($this->agent->is_browser()){
										$agent = $this->agent->browser().' '.$this->agent->version();
										$opsys = $this->agent->platform();
								}
						if($id_mov!=NULL){
							$row_confpos=$this->movcaja->get_one_row("config_pos", array('id_sucursal' => $id_sucursal,'alias_tipodoc'=>'VALE',));
							$row_confdir=$this->movcaja->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));
							$xdatos=$this->print_vale($id_mov,$id_sucursal);
							$xdatos["type"]="success";
							$xdatos['title']='Información';
							$xdatos["msg"]="¡Registo ingresado correctamente!";
							$xdatos["opsys"]=$opsys;
							$xdatos["dir_print"] =$row_confdir->dir_print_script; //for Linux
							$xdatos["dir_print_pos"] =$row_confdir->shared_printer_pos; //for win

						}
					else{
						$xdatos["type"]="error";
						$xdatos['title']='Alerta';
						$xdatos["msg"]="Error No ingresado";
					}
				echo json_encode($xdatos);

		}
	}
	function print_vale($id_mov,$id_sucursal){
		$info_vale="";
		//encabezado
		$row_hf=$this->movcaja->get_one_row("config_pos", array('id_sucursal' => $id_sucursal,'alias_tipodoc'=>'VALE',));
			$hstring="";
			if($row_hf->header1!='')
				$hstring.=chr(27).chr(33).chr(16); //FONT double size
				$hstring.=chr(13).$row_hf->header1."\n";
				$hstring.=chr(27).chr(33).chr(0); //FONT A normal size
			if($row_hf->header2!='')
				$hstring.=chr(13).$row_hf->header2."\n";
			if($row_hf->header3!='')
				$hstring.=chr(13).$row_hf->header3."\n";
			if($row_hf->header4!='')
				$hstring.=chr(13).$row_hf->header4."\n";
			if($row_hf->header5!='')
				$hstring.=chr(13).$row_hf->header5."\n";
			if ($row_hf->header6!='')
				$hstring.=chr(13).$row_hf->header6."\n";
			if($row_hf->header7!='')
				$hstring.=chr(13).$row_hf->header7."\n";
			if($row_hf->header8!='')
				$hstring.=chr(13).$row_hf->header8."\n";
			if($row_hf->header9!='')
				$hstring.=chr(13).$row_hf->header9."\n";
			if($row_hf->header10!='')
				$hstring.=chr(13).$row_hf->header10."\n";

			//pie
					$pstring="";
			if($row_hf->footer1!='')
				$pstring=chr(13).$row_hf->footer1."\n";
			if($row_hf->footer2!='')
				$pstring.=chr(13).$row_hf->footer2."\n";
			if($row_hf->footer3!='')
				$pstring.=chr(13).$row_hf->footer3."\n";
			if($row_hf->footer4!='')
				$pstring.=chr(13).$row_hf->footer4."\n";
			if($row_hf->footer5!='')
				$pstring.=chr(13).$row_hf->footer5."\n";
			if($row_hf->footer6!='')
				$pstring.=chr(13).$row_hf->footer6."\n";
			if($row_hf->footer7!='')
				$pstring.=chr(13).$row_hf->footer7."\n";
			if($row_hf->footer8!='')
				$pstring.=chr(13).$row_hf->footer8."\n";
			if($row_hf->footer9!='')
				$pstring.=chr(13).$row_hf->footer9."\n";
			if($row_hf->footer10!='')
				$pstring.=chr(13).$row_hf->footer10."\n";
		//detalle
		$detalle = $this->movcaja->get_one_row("mov_caja", array('id_mov' => $id_mov,));
		$espacio=" ";
		$margen_izq1=AlignMarginText::leftmargin($espacio,2);
		$margen_izq2=AlignMarginText::leftmargin($espacio,3);
		if($detalle !=NULL){

				$descripcion="CONCEPTO :".$detalle->concepto;
				$fecha = d_m_Y($detalle->fecha);
				$hora =$detalle->hora;
				$desc = AlignMarginText::wordwrap2($descripcion,40) . "\n";
	 			$valor=sprintf("%.2f", $detalle->valor);
				$val=AlignMarginText::rightaligner($valor,$espacio,12);
				$id_mov=str_pad($detalle->id_mov, 12, '0', STR_PAD_LEFT);
				if($detalle->id_tipo==0){
					$info_vale.="VALE INGRESO# :".$id_mov."\n";
				}
				else {
					$info_vale.="VALE EGRESO# :".$id_mov."\n";
				}


				$info_vale.="CAJA :".$detalle->caja."\n";
				$info_vale.="FECHA: ".$fecha.$margen_izq2."HORA: ".$hora."\n";

				$info_vale.="MONTO $ :".$val."\n";
				$info_vale.= chr(27).chr(97).chr(0); //Left align
				$info_vale.=$desc."\n\n\n";
				$info_vale.= chr(27).chr(97).chr(1); //Left align
				$info_vale.="RECIBE:".$detalle->nombre_recibe."\n";
				$xdatos["encabezado"]=$hstring;
				$xdatos["cuerpo"]=$info_vale;
				$xdatos["pie"]=$pstring;
		}
		//detalles
			return $xdatos;
		}
	function editar($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->movcaja->get_row_info($id);
			if($row && $id!=""){
				$data = array(
					"row"=>$row,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/movcaja.js"
					),
				);
				$this->load->view("movcaja/editar",$data);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$id_mov = $this->input->post("id_mov");
			$valor= $this->input->post("monto2");
			$recibe = strtoupper($this->input->post("recibe2"));
			$concepto =strtoupper($this->input->post("concepto2"));
			$hora = date("H:i:s");
			$fecha = date('Y-m-d');
			$id_sucursal=$this->session->id_sucursal;
			$id_usuario=$this->session->id_usuario;
			$row_ap = $this->movcaja->get_caja_activa($id_usuario,$fecha);
			$row_mov=$this->movcaja->get_movcaja_id($id_mov);
			if($row_ap!=NULL){
					$this->utils->begin();
				if($row_ap->vigente==1 && $row_mov->fecha==$fecha){
					$data = array(
						"concepto"=>$concepto,
						"valor"=>$valor,
						"hora"=>$hora,
						"nombre_recibe"=>$recibe,
						"id_empleado"=>$id_usuario,
					);
					$where="id_mov='".$id_mov."'";

					$insert = $this->utils->update($this->table,$data,$where);
				}
				else{
					$insert =false;
				}

			if($insert){

					if ($this->agent->is_browser()){
								$agent = $this->agent->browser().' '.$this->agent->version();
								$opsys = $this->agent->platform();
						}
					$row_confpos=$this->movcaja->get_one_row("config_pos", array('id_sucursal' => $id_sucursal,'alias_tipodoc'=>'VALE',));
					$row_confdir=$this->movcaja->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));
					$xdatos=$this->print_vale($id_mov,$id_sucursal);
					$xdatos["type"]="success";
					$xdatos['title']='Información';
					$xdatos["msg"]="¡Registo ingresado correctamente!";
					$xdatos["opsys"]=$opsys;
					$xdatos["dir_print"] =$row_confdir->dir_print_script; //for Linux
					$xdatos["dir_print_pos"] =$row_confdir->shared_printer_pos; //for win

			}
			else {
					$this->utils->rollback();
					$xdatos["type"]="error";
					$xdatos['title']='Alerta';
					$xdatos["msg"]="Error al editar el registro,\n Verificar si esta abierta la Caja
					\n	o el movimiento es de esta fecha";
			}
		}	else {

					$xdatos["type"]="error";
					$xdatos['title']='Alerta';
					$xdatos["msg"]="Error al editar el registro";
			}

			echo json_encode($xdatos);
		}


	}
	function state_change(){
		if($this->input->method(TRUE) == "POST"){
			$id_mov = $this->input->post("id");
			$anular =1;
			$where="id_mov='".$id_mov."'";
			$data = array(
					"anulado"=>1,
			);

			$response =$this->utils->update($this->table,$data,$where);
				if($response){
					$xdatos["type"]="success";
					$xdatos['title']='Información';
					$xdatos["msg"]="¡Registo anulado correctamente!";
				}
				else {

						$xdatos["type"]="error";
						$xdatos['title']='Alerta';
						$xdatos["msg"]="Registo no pudo ser anulado";
				}
			echo json_encode($xdatos);
		}
	}
	function delete(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$response = safe_delete($this->table,$this->pk,$id);
			echo json_encode($response);
		}
	}
	function agregar_ing(){
			$this->load->library('user_agent');
		//El boton que activa el modal se define en la vista admin pero en js se activa el click para cargar dicho modal
		if($this->input->method(TRUE) == "GET"){
				$data = array();
				$this->load->view("movcaja/agregar_ing",$data);
		}
		else if($this->input->method(TRUE) == "POST"){
			$valor= $this->input->post("monto2");
			$concepto =strtoupper($this->input->post("concepto2"));
			$hora = date("H:i:s");
			$fecha = date('Y-m-d');
			$id_sucursal=$this->session->id_sucursal;
			$id_usuario=$this->session->id_usuario;
			$row_ap = $this->movcaja->get_caja_activa($id_usuario,$fecha);
			$row_user= $this->movcaja->get_row_info_user($id_usuario);

			if($row_ap!=NULL){
				if($row_ap->vigente==1){
						$turno = $row_ap->turno;
						$caja=$row_ap->caja;
						$id_apertura=$row_ap->id_apertura;

						$data = array(
							"concepto"=>$concepto,
							"valor"=>$valor,
							"fecha"=>$fecha,
							"hora"=>$hora,
							"id_tipo"=>0,
							"entrada"=>1,
							"salida"=>0,
							"id_sucursal"=>$id_sucursal,
							"id_empleado"=>$id_usuario,
							'turno' => $turno,
							'nombre_recibe'=> $row_user->nombre,
							'caja'=>$caja,
							'id_apertura'=>$id_apertura,
						);
				}
					$id_mov = $this->movcaja->inAndCon($this->table,$data);
			}else{
				$id_mov = NULL;
			}



						if($id_mov!=NULL){
							if ($this->agent->is_browser()){
										$agent = $this->agent->browser().' '.$this->agent->version();
										$opsys = $this->agent->platform();
								}
							$row_confpos=$this->movcaja->get_one_row("config_pos", array('id_sucursal' => $id_sucursal,'alias_tipodoc'=>'VALE',));
							$row_confdir=$this->movcaja->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));
							$xdatos=$this->print_vale($id_mov,$id_sucursal);
							$xdatos["type"]="success";
							$xdatos['title']='Información';
							$xdatos["msg"]="¡Registo ingresado correctamente!";
							$xdatos["opsys"]=$opsys;
							$xdatos["dir_print"] =$row_confdir->dir_print_script; //for Linux
							$xdatos["dir_print_pos"] =$row_confdir->shared_printer_pos; //for win

						}
					else{
						$xdatos["type"]="error";
						$xdatos['title']='Alerta';
						$xdatos["msg"]="Error No ingresado";
					}
					echo json_encode($xdatos);

		}
	}
	function editar_ing($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->movcaja->get_row_info($id);
			if($row && $id!=""){
				$data = array(
					"row"=>$row,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
							"js/scripts/movcaja.js"
					),
				);
				$this->load->view("movcaja/editar_ing",$data);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$id_mov = $this->input->post("id_mov");
			$valor= $this->input->post("monto2");

			$concepto =strtoupper($this->input->post("concepto2"));
			$hora = date("H:i:s");
			$fecha = date('Y-m-d');
			$id_sucursal=$this->session->id_sucursal;
			$id_usuario=$this->session->id_usuario;
			$row_ap = $this->movcaja->get_caja_activa($id_usuario,$fecha);
			$row_mov=$this->movcaja->get_movcaja_id($id_mov);
			if($row_ap!=NULL){
					$this->utils->begin();
				if($row_ap->vigente==1 && $row_mov->fecha==$fecha){
					$data = array(
						"concepto"=>$concepto,
						"valor"=>$valor,
						"hora"=>$hora,
						"id_empleado"=>$id_usuario,
					);
					$where="id_mov='".$id_mov."'";

					$insert = $this->utils->update($this->table,$data,$where);
				}
				else{
					$insert =false;
				}

			if($insert){
					$this->utils->commit();

					if ($this->agent->is_browser()){
								$agent = $this->agent->browser().' '.$this->agent->version();
								$opsys = $this->agent->platform();
						}
					$row_confpos=$this->movcaja->get_one_row("config_pos", array('id_sucursal' => $id_sucursal,'alias_tipodoc'=>'VALE',));
					$row_confdir=$this->movcaja->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));
					$xdatos=$this->print_vale($id_mov,$id_sucursal);
					$xdatos["type"]="success";
					$xdatos['title']='Información';
					$xdatos["msg"]="¡Registo ingresado correctamente!";
					$xdatos["opsys"]=$opsys;
					$xdatos["dir_print"] =$row_confdir->dir_print_script; //for Linux
					$xdatos["dir_print_pos"] =$row_confdir->shared_printer_pos; //for win
			}
			else {
					$this->utils->rollback();
					$xdatos["type"]="error";
					$xdatos['title']='Alerta';
					$xdatos["msg"]="Error al editar el registro,\n Verificar si esta abierta la Caja
					\n	o el movimiento es de esta fecha";
			}
		}	else {

					$xdatos["type"]="error";
					$xdatos['title']='Alerta';
					$xdatos["msg"]="Error al editar el registro";
			}

				echo json_encode($xdatos);
		}


	}

}

/* End of file Productos.php */
