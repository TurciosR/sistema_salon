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

class Corte extends CI_Controller {

	/*
	Enviroment variables
	*/
	private $table = "controlcaja";
	private $pk = "id_corte";

	function __construct()
	{
		parent::__construct();
		$this->load->Model("CorteModel","corte");
		$this->load->model('UtilsModel',"utils");
		$this->load->library('user_agent');
	}

	public function index()
	{
		//Sucursales todas para Admin, sino solo la que corresponde
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
		//Datos extra que verifican el estado de Apertura

		$fecha = date('Y-m-d');
		$hora_actual= date("H:i:s");
		$row_ap = $this->corte->get_cajas_activa_sucursal($id_sucursal,$fecha);
		 $usuario_ap=NULL;
		 $detalle_ap =NULL;
		 $detalle_ap2 =	 NULL;
		 	$usuario_det = NULL;
		if($row_ap!=NULL){
			$id_apertura=$row_ap->id_apertura;
			$usuario_ap =	$this->corte->get_one_row("usuario", array('id_usuario' => $row_ap->id_usuario,));
			$detalle_ap =	$this->corte->get_one_row("detalle_apertura", array('id_apertura' => $id_apertura,'vigente'=>1,'id_usuario' => $row_ap->id_usuario,));
			if($detalle_ap==NULL){
				$detalle_ap2 =	$this->corte->get_one_row("detalle_apertura", array('id_apertura' => $id_apertura,'vigente'=>1,));
				$usuario_det =	$this->corte->get_one_row("usuario", array('id_usuario' => $detalle_ap2->id_usuario,));
			}else{
				$usuario_det =	$this->corte->get_one_row("usuario", array('id_usuario' => $detalle_ap->id_usuario,));
			}
		}
		//fin datos apertura
		/*
		"buttons" => array(
			0 => array(
				"icon"=> "mdi mdi-plus",
				'url' => 'corte/corte_caja_diario',
				'txt' => 'Agregar Corte',
				'modal' => false,
				),
				1 => array(
					"icon"=> "mdi mdi-plus",
					'url' => 'corte/cierre_turno',
					'txt' => 'Cerrar turno',
					'modal' => true,
				),
			),
		*/
		$data = array(
			"titulo"=> "Corte",
			"icono"=> "mdi mdi-format-list-bulleted",

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
						"value"=> date('d-m-Y'),
						"classes" =>"form-control datepicker",
						'txt' => 'Fecha 2',
						'placeholder' => 'Ingresar fecha final',
						'modal' => false,
						'extra'=>'required data-parsley-trigger="change"',
					),

					2 => array(
						"name"=> "btn_consulta",
						"type"=> "button",
						"value"=> 'Buscar',
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
				"No."=>4,
				"Fecha"=>4,
				"Hora"=>4,
				"Empleado"=>10,
				"Apertura"=>4,
				"Tipo corte"=>4,
				"Total $"=>5,
				"Diferencia $"=>5,
				"Turno"=>4,
				"Acciones"=>4,
			),
			"row_ap"=>$row_ap,
			"usuario_ap"=>$usuario_ap,
			"id_usuario"=>$id_usuario,
			"usuario_det"=>$usuario_det,
			"detalle_ap"=>$detalle_ap,
			"detalle_ap2"=>$detalle_ap2,
		);

		$extras = array(
			'css' => array(
			),
			'js' => array(
                "js/scripts/corte.js",
			),
		);

		layout("corte/admin",$data,$extras);
	}

	function get_data(){
		$draw = intval($this->input->post("draw"));
		$start = intval($this->input->post("start"));
		$length = intval($this->input->post("length"));
		$id_sucursal = $this->input->post("id_sucursal");
		$f1 = Y_m_d($this->input->post("fecha1"));
		$f2 = Y_m_d($this->input->post("fecha2"));
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
		//id_corte, fecha_corte, hora_corte, id_empleado, id_apertura, tipo_corte, cashfinal, diferencia, turno
		$valid_columns = array(
			0 => 'c.id_corte',
			1 => 'c.fecha_corte',
			2 => 'c.hora_corte',
			3 => 'c.id_empleado',
			4 => 'c.id_apertura',
			5 => 'c.tipo_corte',
			6 => 'c.cashfinal',
			7 => 'c.diferencia',
			8 => 'c.turno',

		);
		if (!isset($valid_columns[$col])) {
			$order = null;
		} else {
			$order = $valid_columns[$col];
		}
		$row = $this->corte->get_collection($order, $search, $valid_columns, $length, $start, $dir, $id_sucursal,$f1,$f2);
		//print_r($row);
		if ($row != 0) {
			$data = array();
			foreach ($row as $rows) {
				$menudrop = "<div class='btn-group'>
				<button data-toggle='dropdown' class='btn btn-success dropdown-toggle' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menú</button>
				<ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";
				$filename = base_url("corte/imprimir/");
				//$menudrop.= "<li><a role='button' href='" . $filename.$rows->id_corte. "' ><i class='mdi mdi-printer' ></i> Imprimir</a></li>";
				$menudrop .= "<li><a  data-toggle='modal' data-target='#viewModal' data-refresh='true'  role='button' class='detail' data-id=".$rows->id_corte."><i class='mdi mdi-eye-check' ></i> Detalles</a></li>";

				$menudrop.= "</ul></div>";
				$data[] = array(
					$rows->id_corte,
					$rows->fecha_corte,
					$rows->hora_corte,
					$rows->id_empleado,
					$rows->id_apertura,
					$rows->tipo_corte,
					$rows->cashfinal,
					$rows->diferencia,
					$rows->turno,
					$menudrop,
				);
			}
			$total = count($row);
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

	function corte_caja_diario(){
		//Falta agregar ventas en efectivo o tarjeta en el form facturacion, asi como
		//impresion de ticket de corte,  enform de facturas revision de F3 factura para cambiar codigo
		// o descripcion, evitar esc en modals, otros!!!
		if($this->input->method(TRUE) == "GET"){
				//Datos extra que verifican el estado de Apertura
				$id_usuario = $this->session->id_usuario;
				$id_sucursal = $this->session->id_sucursal;
				$fecha = date('Y-m-d');
				$hora_actual= date("H:i:s");
				//$row_ap = $this->corte->get_caja_activa($id_usuario,$fecha);

				$row_ap = $this->corte->get_cajas_activa_sucursal($id_sucursal,$fecha);


				$monto_apertura=0;
				$usuario =	$this->corte->get_one_row("usuario", array('id_usuario' => $id_usuario,));
				if($row_ap!=NULL){
					$usuario_ap =	$this->corte->get_one_row("usuario", array('id_usuario' => $row_ap->id_usuario,));
					$fecha_apertura =$row_ap->fecha;
					 $hora_ap =$row_ap->hora;
					 $id_apertura=$row_ap->id_apertura;
					 $monto_apertura=$row_ap->monto_apertura;

					$id_estado=2; //venta finalizada
					//ejecutar solicitud de min y macx correlativos para enviarlos a la vista de corte , de ticket. ccf, y cof
					$ticket_row=$this->corte->get_consolidate($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,1);
					$cof_row=$this->corte->get_consolidate($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,2);
					$ccf_row=$this->corte->get_consolidate($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,3);
			    $count_row_tik=$this->corte->get_total_num_docs($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,1);
					$count_row_cof=$this->corte->get_total_num_docs($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,2);
		 			$count_row_ccf=$this->corte->get_total_num_docs($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,3);
					$total_row_tik=$this->corte->get_total_dinero_corte($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,1,"total");
					$total_row_cof=$this->corte->get_total_dinero_corte($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,2,"total");
					$total_row_ccf=$this->corte->get_total_dinero_corte($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,3,"total");
					$total_row_dev=$this->corte->get_total_dinero_corte($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,4,"total");
					$total_row_ret=$this->corte->get_total_dinero_corte($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,3,"retencion");
					//echo "id sucursal: ".$id_sucursal.", fecha apertura: ".$fecha_apertura.", hora ap: ".$hora_ap.", hora actual: ".$hora_actual.", id apertura: ".$id_apertura;
					$row_entrada_caja=$this->corte->get_total_mov_caja($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,"entrada");
					$row_salida_caja=$this->corte->get_total_mov_caja($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,"salida");

					//rangos por efectivo y por tarjeta, adelante puede ser por credito
					$valor="total";
					$where_val=array(
					 "id_sucursal" => $id_sucursal,
					 "id_apertura" =>$id_apertura,
						"id_estado"=>$id_estado,
						"fecha"=>$fecha_apertura,
					);
					$where_val["tipo_doc"] = 1;
					$where_val['tipo_pago']=1;
					$count_rango_efectivo_tik= $this->corte->get_total_docs_tp($hora_ap,$hora_actual,$valor,$where_val);
					$ticket_rowmm_efectivo=$this->corte->get_max_min_corr($hora_ap,$hora_actual,$valor,$where_val);
					$where_val['tipo_pago']=2;
					$count_rango_tarjeta_tik= $this->corte->get_total_docs_tp($hora_ap,$hora_actual,$valor,$where_val);
					$ticket_rowmm_tarjeta=$this->corte->get_max_min_corr($hora_ap,$hora_actual,$valor,$where_val);
					$where_val["tipo_doc"] = 2;
					$where_val['tipo_pago']=1;
					$count_rango_efectivo_cof= $this->corte->get_total_docs_tp($hora_ap,$hora_actual,$valor,$where_val);
					$cof_rowmm_efectivo=$this->corte->get_max_min_corr($hora_ap,$hora_actual,$valor,$where_val);
					$where_val['tipo_pago']=2;
					$count_rango_tarjeta_cof= $this->corte->get_total_docs_tp($hora_ap,$hora_actual,$valor,$where_val);
					$cof_rowmm_tarjeta=$this->corte->get_max_min_corr($hora_ap,$hora_actual,$valor,$where_val);
					$where_val["tipo_doc"] = 3;
					$where_val['tipo_pago']=1;
					$count_rango_efectivo_ccf= $this->corte->get_total_docs_tp($hora_ap,$hora_actual,$valor,$where_val);
					$ccf_rowmm_efectivo=$this->corte->get_max_min_corr($hora_ap,$hora_actual,$valor,$where_val);
					$where_val['tipo_pago']=2;
					$count_rango_tarjeta_ccf= $this->corte->get_total_docs_tp($hora_ap,$hora_actual,$valor,$where_val);
					$ccf_rowmm_tarjeta=$this->corte->get_max_min_corr($hora_ap,$hora_actual,$valor,$where_val);
					//total efectivo desde metodo modificado en model
					$where_array=array(
					 "id_sucursal" => $id_sucursal,
					 "id_apertura" =>$id_apertura,
						"id_estado"=>$id_estado,
					);

					$where_array["tipo_doc"] = 1;
					$where_array['tipo_pago']=1; //($hora_ap,$hora_actual,$valor,$where)
					$total_efectivo_tik= $this->corte->get_totales_dinero_corte($hora_ap,$hora_actual,"total",$where_array);
					$where_array['tipo_pago']=2;
					$total_tarjeta_tik= $this->corte->get_totales_dinero_corte($hora_ap,$hora_actual,"total",$where_array);
					$where_array["tipo_doc"] = 2;
					$where_array['tipo_pago']=1;
					$total_efectivo_cof= $this->corte->get_totales_dinero_corte($hora_ap,$hora_actual,"total",$where_array);
					$where_array['tipo_pago']=2;
					$total_tarjeta_cof= $this->corte->get_totales_dinero_corte($hora_ap,$hora_actual,"total",$where_array);

					$where_array["tipo_doc"] = 3;
					$where_array['tipo_pago']=1;
					$total_efectivo_ccf= $this->corte->get_totales_dinero_corte($hora_ap,$hora_actual,"total",$where_array);
					$where_array['tipo_pago']=2;
					$total_tarjeta_ccf= $this->corte->get_totales_dinero_corte($hora_ap,$hora_actual,"total",$where_array);

					$total_efectivo_fin=$total_efectivo_tik+$total_efectivo_cof+$total_efectivo_ccf+$monto_apertura;
					$total_tarjeta_fin=$total_tarjeta_tik+$total_tarjeta_cof+$total_tarjeta_ccf;

					$total_corte=$monto_apertura;
					$total_entrada_caja=0;
					$total_salida_caja=0;
					$total_mov_caja=0;

					//procedemos a obtener todos los abonos realizados durante el dia
					$whereA=array(
					 "id_sucursal" => $id_sucursal,
					 "id_apertura" =>$id_apertura,
						"id_estado"=>$id_estado,
					);
					$whereA['v.credito']=1; //($hora_ap,$hora_actual,$valor,$where)
					$total_abonos_diarios= $this->corte->get_totales_abonos($fecha,$whereA);
					//echo $total_abonos_diarios;
				if($total_row_tik!=NULL){
					$total_corte+=$total_row_tik->total;
				}
				if($total_row_cof!=NULL){
					$total_corte+=$total_row_cof->total;
				}
				if($total_row_ccf!=NULL){
					$total_corte+=$total_row_ccf->total;
				}
				if($total_row_ret!=NULL){
					$total_corte=$total_corte-$total_row_ret->retencion;
					$total_efectivo_fin=$total_efectivo_fin-$total_row_ret->retencion;
				}
				if($row_entrada_caja!=NULL){
					$total_entrada_caja=$row_entrada_caja->valor;
					$total_mov_caja+=$row_entrada_caja->valor;
				}
				if($row_salida_caja!=NULL){
					$total_salida_caja=$row_salida_caja->valor;
					$total_mov_caja+=$row_salida_caja->valor;
				}
				if($total_row_dev!=NULL){
					$total_corte=$total_corte-$total_row_dev->total;

				}
				//detalle devoluciones
				$array_parameter=array(
				 "id_sucursal" => $id_sucursal,
				 "id_apertura" =>$id_apertura,
				 	"tipo_doc"=>4,
					"id_estado"=>$id_estado,
					"fecha"=>$fecha_apertura,
				);
				$row_devs = $this->corte->get_detail_rows("ventas", $array_parameter);
				$detalles1 = array();
				if($row_devs!=NULL){
						foreach ($row_devs as $detalle){
							$id_devolucion = $detalle->id_devolucion;
							$row_data_dev = $this->corte->get_data_dev($id_devolucion);
							if($row_data_dev!=NULL){
								$detalle->doc_afecta =	$row_data_dev->doc_afecta;
								$detalle->tipo_doc =	$row_data_dev->tipo_doc;
								$detalle->nombredoc =	$row_data_dev->nombredoc;
								$detalle->corr_afecta =	$row_data_dev->corr_afecta;
							}
							array_push($detalles1,$detalle);
						}
				}

				$data = array(
					"usuario"=>$usuario,
					"id_sucursal" => $id_sucursal,
					"ap_row" =>$row_ap,
					"usuario_ap"=>$usuario_ap,
					 "ticket_row" =>$ticket_row,
					 "cof_row" =>$cof_row,
					 "ccf_row" =>$ccf_row,
					 "count_row_tik"=>$count_row_tik,
					 "count_row_cof"=>$count_row_cof,
					 "count_row_ccf"=>$count_row_ccf,
					 "total_row_tik"=>$total_row_tik,
					 "total_row_cof"=>$total_row_cof,
					 "total_row_ccf"=>$total_row_ccf,
					 "total_row_ret"=>$total_row_ret,
					 "total_row_dev"=>$total_row_dev,
					 "total_corte"=>$total_corte,
					 "total_entrada_caja"=>$total_entrada_caja,
					 "total_salida_caja"=>$total_salida_caja,
					 "total_efectivo_tik"=>$total_efectivo_tik,
					 "total_efectivo_cof"=>$total_efectivo_cof,
					 "total_efectivo_ccf"=>$total_efectivo_ccf,
					 "total_efectivo_fin"=>$total_efectivo_fin,
					 "total_tarjeta_tik"=>$total_tarjeta_tik,
					 "total_tarjeta_cof"=>$total_tarjeta_cof,
					 "total_tarjeta_ccf"=>$total_tarjeta_ccf,
					 "total_tarjeta_fin"=>$total_tarjeta_fin,
					 "count_rango_efectivo_tik"=>$count_rango_efectivo_tik,
					 "count_rango_tarjeta_tik"=>$count_rango_tarjeta_tik,
					 "count_rango_efectivo_cof"=>$count_rango_efectivo_cof,
					 "count_rango_tarjeta_cof"=>$count_rango_tarjeta_cof,
					 "count_rango_efectivo_ccf"=>$count_rango_efectivo_ccf,
					 "count_rango_tarjeta_ccf"=>$count_rango_tarjeta_ccf,
					 "ticket_rowmm_efectivo"=>$ticket_rowmm_efectivo,
					 "ticket_rowmm_tarjeta"=>$ticket_rowmm_tarjeta,
					 "cof_rowmm_efectivo"=>$cof_rowmm_efectivo,
					 "cof_rowmm_tarjeta"=>$cof_rowmm_tarjeta,
					 "ccf_rowmm_efectivo"=>$ccf_rowmm_efectivo,
				   "ccf_rowmm_tarjeta"=>$ccf_rowmm_tarjeta,
					 "rows_devs"=>$detalles1,
					 "hay_apertura"=>1,
					 "total_abonos_cuentas_cobrar"=>$total_abonos_diarios,
				);
			}else{
					$data = array(
						"usuario"=>$usuario,
						"id_sucursal" => $id_sucursal,
						"hay_apertura"=>0,
					);
				}
				$extras = array(
					'css' => array(
					),
					'js' => array(
	                    "js/scripts/corte.js",
					),
				);
				layout("corte/corte_caja_diario",$data,$extras);
		}
		else if($this->input->method(TRUE) == "POST"){
			if ($this->agent->is_browser())
				{
						$agent = $this->agent->browser().' '.$this->agent->version();
						$opsys = $this->agent->platform();
				}

			//datos POST para corte o cierre
			$id_usuario_corte=$this->input->post("id_usuario");
			$tipo_corte=$this->input->post("tipo_corte");
			$fecha = Y_m_d($this->input->post("fecha"));
			$id_apertura=$this->input->post("id_apertura");
			$turno = $this->input->post("turno");
			$caja=$this->input->post("caja");
			$tik_min=$this->input->post("t_min");
			$tik_max=$this->input->post("t_max");
			$tik_count=$this->input->post("t_count");
			$tik_total=$this->input->post("t_total");
			$cof_min=$this->input->post("cof_min");
			$cof_max=$this->input->post("cof_max");
			$cof_count=$this->input->post("cof_count");
			$cof_total=$this->input->post("cof_total");
			$ccf_min=$this->input->post("ccf_min");
			$ccf_max=$this->input->post("ccf_max");
			$ccf_count=$this->input->post("ccf_count");
			$ccf_total=$this->input->post("ccf_total");
			$monto_apertura=$this->input->post("monto_apertura");
			$total_retencion=$this->input->post("total_retencion");
			$total_dev=$this->input->post("total_dev");
			$total_efectivo_fin=$this->input->post("total_efectivo_fin");
			$total_entrada_caja=$this->input->post("total_entrada_caja");
			$total_salida_caja=$this->input->post("total_salida_caja");
			$total_efectivo=$this->input->post("total_efectivo");
			$total_fin=$this->input->post("total_fin");
			$diferencia_val=$this->input->post("diferencia_val");
			$observaciones=$this->input->post("observaciones");

			$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
			$id_sucursal=$this->session->id_sucursal;
			$id_usuario=$this->session->id_usuario;
			$hora_actual = date("H:i:s");
			$fecha_actual = date("Y-m-d");
			//config pos
			$row_confdir=$this->corte->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));

			//correlativo de caja disponible
			$where_arr= array('id_caja' => $caja);
			$correlativo_dispo = $this->corte->get_one_value("caja",$where_arr,"correlativo_dispo");
			$nn_tik = $correlativo_dispo + 1;

			$tabla = "controlcaja";
			$form_data = array(
				'fecha' => $fecha_actual,
				'fecha_corte' => $fecha_actual,
				'caja' => $caja,
				'turno' => $turno,
				//'cajero' => $id_usuario_corte,
				'tinicio' => $tik_min, //datos ticket
				'tfinal' => $tik_max, //datos ticket
				'totalnot' => $tik_count, //datos ticket
				'totalt' => $tik_total,//datos ticket
				'tgravado' => $tik_total,//datos ticket todos son gravados
				'finicio' => $cof_min,//datos cof
				'ffinal' => $cof_max,//datos cof
				'totalnof' => $cof_count,//datos cof
				'totalf' => $cof_total,//datos cof
				'fgravado' =>  $cof_total,//datos cof
				'cfinicio' => $ccf_min,//datos ccf
				'cffinal' => $ccf_max,
				'totalnocf' => $ccf_count,
				'totalcf' => $ccf_total,//datos ccf
	      'cfgravado' =>  $ccf_total,//datos ccf
				'vales'=> $total_salida_caja,
				'ingresos'=> $total_entrada_caja,
				'hora_corte' => $hora_actual,
				'id_empleado' => $id_usuario,
				'id_sucursal' => $id_sucursal,
				'id_apertura' => $id_apertura,
				'diferencia' => $diferencia_val,
				'totalgral' => $total_fin,
				'cashfinal' => $total_efectivo,
				'cashinicial' => $monto_apertura,
				'tipo_corte' => $tipo_corte,
				//'vtaefectivo' => $total_contado, //pendiente e importante facturar tipo pago
				//'tarjetas' => $total_tarjeta, //pendiente e importante facturar tipo pago
				'vales' => $total_salida_caja,
				'ingresos' => $total_entrada_caja,
				'totalnodev' => $total_dev, //averiguar si es nota devolucion o se imprime devolucion en tiquet
				//	'rinicio' => $res_min,
				//	'rfinal' => $res_max,
				// 'totalnor' => $t_res,
				//	'monto_ch' => $monto_ch,
				'retencion' => $total_retencion, //falta validar si son clientes grandes contribuyentes y facturas>100 aplicar 1% retencion
				'observaciones' => $observaciones,
			);
			$id_cortex="";
			$where_arr= array('id_apertura' => $id_apertura,'tipo_corte' =>'Z');
			$cuentax = $this->corte->get_totalrow_params($tabla,$where_arr);
			if($cuentax == 0){
				if($tipo_corte == "C"){
					$insertar = $this->utils->insert($tabla,$form_data);
					if($insertar){
						$id_cortex=$this->utils->insert_id();
						if($data_ingreso!=NULL){
							foreach ($data_ingreso as $fila){
								$tipo_doc=$fila["tipo_doc"];
								$correlativo=$fila["correlativo"];
								$nombre_doc=$fila["nombre_doc"];
								$correlativo_afecta=$fila["correlativo_afecta"];
								$subtotal_dev=$fila["subtotal_dev"];
								$table_dev = "devoluciones_corte";
								$form_dev = array(
									'id_corte' => $id_cortex,
									'n_devolucion' => $correlativo,
									't_devolucion' =>$subtotal_dev,
									'afecta' => $correlativo_afecta,
									'tipo' => $tipo_doc,
								);
								$id_dev = $this->corte->inAndCon($table_dev,$form_dev);
							}
						}
					}
				}
				if($tipo_corte == "X"){
					$extra = array('tiket' => $nn_tik ,);
					$resultx = array_merge($form_data, $extra);
					$insertar = $this->utils->insert($tabla,$resultx);
					$id_cortex=$this->utils->insert_id();
					if($insertar){
						//
						$t = "caja";
						$ff = array('correlativo_dispo' => $nn_tik,);
						$wp = "id_caja='".$caja."'";
						$upd =$this->utils->update($t,$ff,$wp);

						if($data_ingreso!=NULL){
							foreach ($data_ingreso as $fila){
								$tipo_doc=$fila["tipo_doc"];
								$correlativo=$fila["correlativo"];
								$nombre_doc=$fila["nombre_doc"];
								$correlativo_afecta=$fila["correlativo_afecta"];
								$subtotal_dev=$fila["subtotal_dev"];
								$table_dev = "devoluciones_corte";
								$form_dev = array(
									'id_corte' => $id_cortex,
									'n_devolucion' => $correlativo,
									't_devolucion' =>$subtotal_dev,
									'afecta' => $correlativo_afecta,
									'tipo' => $tipo_doc,
								);
								$id_dev = $this->corte->inAndCon($table_dev,$form_dev);
							}
						}

					}
				}
				if($tipo_corte == "Z"){
					$extra = array('tiket' => $nn_tik ,);
					$resultx = array_merge($form_data, $extra);
					$table_apertura = "apertura_caja";
					$form_up = array(
						'vigente' => 0,
						'monto_vendido' => $total_efectivo,
					);
					$where_apertura = "id_apertura='".$id_apertura."'";
					$up_apertura = $this->utils->update($table_apertura, $form_up, $where_apertura);
					if($up_apertura){
						$tab = "detalle_apertura";
						$form_d = array(
							'vigente' => 0 , );
							$ww = "id_apertura='".$id_apertura."' AND turno='".$turno."'";
							$up_turno = $this->utils->update($tab,$form_d, $ww);

							$insertar = $this->utils->insert($tabla, $resultx);
							$id_cortex = $this->utils->insert_id();
							if($insertar){
								$t = "caja";
								$ff = array('correlativo_dispo' => $nn_tik,);
								$wp = "id_caja='".$caja."'";
								$upd =$this->utils->update($t,$ff,$wp);
								if($data_ingreso!=NULL){
									foreach ($data_ingreso as $fila){
										$tipo_doc=$fila["tipo_doc"];
										$correlativo=$fila["correlativo"];
										$nombre_doc=$fila["nombre_doc"];
										$correlativo_afecta=$fila["correlativo_afecta"];
										$subtotal_dev=$fila["subtotal_dev"];
										$table_dev = "devoluciones_corte";
										$form_dev = array(
											'id_corte' => $id_cortex,
											'n_devolucion' => $correlativo,
											't_devolucion' =>$subtotal_dev,
											'afecta' => $correlativo_afecta,
											'tipo' => $tipo_doc,
										);
										$id_dev = $this->corte->inAndCon($table_dev,$form_dev);
									}
								}//if($data_ingreso!=NULL){
							}//if($insertar){

					}
				}//	if($tipo_corte == "Z"){
				if($insertar){
					$xdatos= $this->print_corte($id_cortex,$id_sucursal);
					$xdatos['type']='success';
					$xdatos['title']='Alerta';
					$xdatos['msg']='Corte guardado correctamente !'.$correlativo_dispo;
					$xdatos['proceso']='insert';
					$xdatos['id_corte']=$id_cortex;
					$xdatos["opsys"]=$opsys;
					$xdatos["dir_print"] =$row_confdir->dir_print_script; //for Linux
					$xdatos["dir_print_pos"] =$row_confdir->shared_printer_pos; //for win


				}
				else{
					$xdatos['type']='error';
					$xdatos['title']='Alerta';
					$xdatos['msg']='Error al guardar el corte !';
				}
			} //if($cuentax == 0){
			else{
					$xdatos['type']='error';
					$xdatos['title']='Alerta';
					$xdatos['msg']='Ya existe un corte con esta apertura!';
				}
				echo json_encode($xdatos);
			}//fin else if POST
		}

	function cierre_turno(){
					if($this->input->method(TRUE) == "GET"){
						if ($this->agent->is_browser())
							{
									$agent = $this->agent->browser().' '.$this->agent->version();
									$opsys = $this->agent->platform();
							}
						$id_usuario = $this->session->id_usuario;
						$id_sucursal = $this->session->id_sucursal;
						$fecha = date('Y-m-d');
						$hora_actual= date("H:i:s");
						$row_ap = $this->corte->get_caja_activa($id_usuario,$fecha);

						$monto_apertura=0;
						$usuario =	$this->corte->get_one_row("usuario", array('id_usuario' => $id_usuario,));
						if($row_ap!=NULL){
							$fecha_apertura =$row_ap->fecha;
							 $hora_ap =$row_ap->hora;
							 $id_apertura=$row_ap->id_apertura;
							 $monto_apertura=$row_ap->monto_apertura;

							$id_estado=2; //venta finalizada
							//ejecutar solicitud de min y macx correlativos para enviarlos a la vista de corte , de ticket. ccf, y cof
							$ticket_row=$this->corte->get_consolidate($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,1);
							$cof_row=$this->corte->get_consolidate($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,2);
							$ccf_row=$this->corte->get_consolidate($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,3);
					    $count_row_tik=$this->corte->get_total_num_docs($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,1);
							$count_row_cof=$this->corte->get_total_num_docs($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,2);
				 			$count_row_ccf=$this->corte->get_total_num_docs($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,3);
							$total_row_tik=$this->corte->get_total_dinero_corte($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,1,"total");
							$total_row_cof=$this->corte->get_total_dinero_corte($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,2,"total");
							$total_row_ccf=$this->corte->get_total_dinero_corte($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,3,"total");
							$total_row_dev=$this->corte->get_total_dinero_corte($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,4,"total");
							$total_row_ret=$this->corte->get_total_dinero_corte($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,$id_estado,3,"retencion");
							$row_entrada_caja=$this->corte->get_total_mov_caja($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,"entrada");
							$row_salida_caja=$this->corte->get_total_mov_caja($id_sucursal,$fecha_apertura,$hora_ap,$hora_actual,$id_apertura,"salida");
							//rangos por efectivo y por tarjeta, adelante puede ser por credito
							$valor="total";
							$where_array=array(
							 "id_sucursal" => $id_sucursal,
							 "id_apertura" =>$id_apertura,
								"id_estado"=>$id_estado,
								"fecha"=>$fecha_apertura,
							);
							$where_val["tipo_doc"] = 1;
							$where_val['tipo_pago']=1;
							$count_rango_efectivo_tik= $this->corte->get_total_docs_tp($hora_ap,$hora_actual,$valor,$where_val);
							$ticket_rowmm_efectivo=$this->corte->get_max_min_corr($hora_ap,$hora_actual,$valor,$where_val);
							$where_val['tipo_pago']=2;
							$count_rango_tarjeta_tik= $this->corte->get_total_docs_tp($hora_ap,$hora_actual,$valor,$where_val);
							$ticket_rowmm_tarjeta=$this->corte->get_max_min_corr($hora_ap,$hora_actual,$valor,$where_val);
							$where_val["tipo_doc"] = 2;
							$where_val['tipo_pago']=1;
							$count_rango_efectivo_cof= $this->corte->get_total_docs_tp($hora_ap,$hora_actual,$valor,$where_val);
							$cof_rowmm_efectivo=$this->corte->get_max_min_corr($hora_ap,$hora_actual,$valor,$where_val);
							$where_val['tipo_pago']=2;
							$count_rango_tarjeta_cof= $this->corte->get_total_docs_tp($hora_ap,$hora_actual,$valor,$where_val);
							$cof_rowmm_tarjeta=$this->corte->get_max_min_corr($hora_ap,$hora_actual,$valor,$where_val);
							$where_val["tipo_doc"] = 3;
							$where_val['tipo_pago']=1;
							$count_rango_efectivo_ccf= $this->corte->get_total_docs_tp($hora_ap,$hora_actual,$valor,$where_val);
							$ccf_rowmm_efectivo=$this->corte->get_max_min_corr($hora_ap,$hora_actual,$valor,$where_val);
							$where_val['tipo_pago']=2;
							$count_rango_tarjeta_ccf= $this->corte->get_total_docs_tp($hora_ap,$hora_actual,$valor,$where_val);
							$ccf_rowmm_tarjeta=$this->corte->get_max_min_corr($hora_ap,$hora_actual,$valor,$where_val);
							//total efectivo desde metodo modificado en model
							$where_array=array(
							 "id_sucursal" => $id_sucursal,
							 "id_apertura" =>$id_apertura,
								"id_estado"=>$id_estado,
							);
							$where_array["tipo_doc"] = 1;
							$where_array['tipo_pago']=1; //($hora_ap,$hora_actual,$valor,$where)
							$total_efectivo_tik= $this->corte->get_totales_dinero_corte($hora_ap,$hora_actual,"total",$where_array);
							$where_array['tipo_pago']=2;
							$total_tarjeta_tik= $this->corte->get_totales_dinero_corte($hora_ap,$hora_actual,"total",$where_array);
							$where_array["tipo_doc"] = 2;
							$where_array['tipo_pago']=1;
							$total_efectivo_cof= $this->corte->get_totales_dinero_corte($hora_ap,$hora_actual,"total",$where_array);
							$where_array['tipo_pago']=2;
							$total_tarjeta_cof= $this->corte->get_totales_dinero_corte($hora_ap,$hora_actual,"total",$where_array);

							$where_array["tipo_doc"] = 3;
							$where_array['tipo_pago']=1;
							$total_efectivo_ccf= $this->corte->get_totales_dinero_corte($hora_ap,$hora_actual,"total",$where_array);
							$where_array['tipo_pago']=2;
							$total_tarjeta_ccf= $this->corte->get_totales_dinero_corte($hora_ap,$hora_actual,"total",$where_array);

							$total_efectivo_fin=$total_efectivo_tik+$total_efectivo_cof+$total_efectivo_ccf+$monto_apertura;
							$total_tarjeta_fin=$total_tarjeta_tik+$total_tarjeta_cof+$total_tarjeta_ccf;

							$total_corte=$monto_apertura;
							$total_entrada_caja=0;
							$total_salida_caja=0;
							$total_mov_caja=0;
						if($total_row_tik!=NULL){
							$total_corte+=$total_row_tik->total;
						}
						if($total_row_cof!=NULL){
							$total_corte+=$total_row_cof->total;
						}
						if($total_row_ccf!=NULL){
							$total_corte+=$total_row_ccf->total;
						}
						if($total_row_ret!=NULL){
							$total_corte=$total_corte-$total_row_ret->retencion;
						}
						if($row_entrada_caja!=NULL){
							$total_entrada_caja=$row_entrada_caja->valor;
							$total_mov_caja+=$row_entrada_caja->valor;
						}
						if($row_salida_caja!=NULL){
							$total_salida_caja=$row_salida_caja->valor;
							$total_mov_caja+=$row_salida_caja->valor;
						}
						if($total_row_dev!=NULL){
							$total_corte=$total_corte-$total_row_dev->total;
						}
						//detalle devoluciones
						$array_parameter=array(
						 "id_sucursal" => $id_sucursal,
						 "id_apertura" =>$id_apertura,
							"tipo_doc"=>4,
							"id_estado"=>$id_estado,
							"fecha"=>$fecha_apertura,
						);
						$row_devs = $this->corte->get_detail_rows("ventas", $array_parameter);
						$detalles1 = array();
						if($row_devs!=NULL){
								foreach ($row_devs as $detalle){
									$id_devolucion = $detalle->id_devolucion;
									$row_data_dev = $this->corte->get_data_dev($id_devolucion);
									if($row_data_dev!=NULL){
										$detalle->doc_afecta =	$row_data_dev->doc_afecta;
										$detalle->tipo_doc =	$row_data_dev->tipo_doc;
										$detalle->nombredoc =	$row_data_dev->nombredoc;
										$detalle->corr_afecta =	$row_data_dev->corr_afecta;
									}
									array_push($detalles1,$detalle);
								}
						}

						$data = array(
							"usuario"=>$usuario,
							"id_sucursal" => $id_sucursal,
							"ap_row" =>$row_ap,
							 "ticket_row" =>$ticket_row,
							 "cof_row" =>$cof_row,
							 "ccf_row" =>$ccf_row,
							 "count_row_tik"=>$count_row_tik,
							 "count_row_cof"=>$count_row_cof,
							 "count_row_ccf"=>$count_row_ccf,
							 "total_row_tik"=>$total_row_tik,
							 "total_row_cof"=>$total_row_cof,
							 "total_row_ccf"=>$total_row_ccf,
							 "total_row_ret"=>$total_row_ret,
							 "total_row_dev"=>$total_row_dev,
							 "total_corte"=>$total_corte,
							 "total_entrada_caja"=>$total_entrada_caja,
							 "total_salida_caja"=>$total_salida_caja,
							 "total_efectivo_tik"=>$total_efectivo_tik,
							 "total_efectivo_cof"=>$total_efectivo_cof,
							 "total_efectivo_ccf"=>$total_efectivo_ccf,
							 "total_efectivo_fin"=>$total_efectivo_fin,
							 "total_tarjeta_tik"=>$total_tarjeta_tik,
							 "total_tarjeta_cof"=>$total_tarjeta_cof,
							 "total_tarjeta_ccf"=>$total_tarjeta_ccf,
							 "total_tarjeta_fin"=>$total_tarjeta_fin,
							 "count_rango_efectivo_tik"=>$count_rango_efectivo_tik,
							 "count_rango_tarjeta_tik"=>$count_rango_tarjeta_tik,
							 "count_rango_efectivo_cof"=>$count_rango_efectivo_cof,
							 "count_rango_tarjeta_cof"=>$count_rango_tarjeta_cof,
							 "count_rango_efectivo_ccf"=>$count_rango_efectivo_ccf,
							 "count_rango_tarjeta_ccf"=>$count_rango_tarjeta_ccf,
							 "ticket_rowmm_efectivo"=>$ticket_rowmm_efectivo,
							 "ticket_rowmm_tarjeta"=>$ticket_rowmm_tarjeta,
							 "cof_rowmm_efectivo"=>$cof_rowmm_efectivo,
							 "cof_rowmm_tarjeta"=>$cof_rowmm_tarjeta,
							 "ccf_rowmm_efectivo"=>$ccf_rowmm_efectivo,
							 "ccf_rowmm_tarjeta"=>$ccf_rowmm_tarjeta,
							 "rows_devs"=>$detalles1,
							 "hay_apertura"=>1,
						);
					}else{
							$data = array(
								"usuario"=>$usuario,
								"id_sucursal" => $id_sucursal,
								"hay_apertura"=>0,
							);
						}
						$extras = array(
							'css' => array(
							),
							'js' => array(
			                    "js/scripts/corte.js",
							),
						);
							$this->load->view("corte/cierre_turno",$data,$extras);
					}
					else if($this->input->method(TRUE) == "POST"){
						if ($this->agent->is_browser())
							{
									$agent = $this->agent->browser().' '.$this->agent->version();
									$opsys = $this->agent->platform();
							}
							//datos POST para corte o cierre
							$id_usuario_corte=$this->input->post("id_usuario");
							$tipo_corte=$this->input->post("tipo_corte");
							$fecha = Y_m_d($this->input->post("fecha"));
							$id_apertura=$this->input->post("id_apertura");
							$turno = $this->input->post("turno");
							$caja=$this->input->post("caja");
							$tik_min=$this->input->post("t_min");
							$tik_max=$this->input->post("t_max");
							$tik_count=$this->input->post("t_count");
							$tik_total=$this->input->post("t_total");
							$cof_min=$this->input->post("cof_min");
							$cof_max=$this->input->post("cof_max");
							$cof_count=$this->input->post("cof_count");
							$cof_total=$this->input->post("cof_total");
							$ccf_min=$this->input->post("ccf_min");
							$ccf_max=$this->input->post("ccf_max");
							$ccf_count=$this->input->post("ccf_count");
							$ccf_total=$this->input->post("ccf_total");
							$monto_apertura=$this->input->post("monto_apertura");
							$total_retencion=$this->input->post("total_retencion");
							$total_dev=$this->input->post("total_dev");
							$total_efectivo_fin=$this->input->post("total_efectivo_fin");
							$total_entrada_caja=$this->input->post("total_entrada_caja");
							$total_salida_caja=$this->input->post("total_salida_caja");
							$total_efectivo=$this->input->post("total_efectivo");
							$total_fin=$this->input->post("total_fin");
							$diferencia_val=$this->input->post("diferencia_val");
							$observaciones=$this->input->post("observaciones");

						//$data_ingreso = json_decode($this->input->post("data_ingreso"),true);
						$id_sucursal=$this->session->id_sucursal;
						$id_usuario=$this->session->id_usuario;
						$hora_actual = date("H:i:s");
						$fecha_actual = date("Y-m-d");
						//correlativo de caja disponible
						$where_arr= array('id_caja' => $caja);
						$correlativo_dispo = $this->corte->get_one_value("caja",$where_arr,"correlativo_dispo");
						$nn_tik = $correlativo_dispo + 1;
						//datos apertura caja
						$row_ap = $this->corte->get_caja_activa($id_usuario,$fecha_actual);

						$monto_apertura=0;
						 $usuario =	$this->corte->get_one_row("usuario", array('id_usuario' => $id_usuario,));
						if($row_ap!=NULL){
							$fecha_apertura =$row_ap->fecha;
							 $hora_ap =$row_ap->hora;
							 $id_apertura=$row_ap->id_apertura;
							 $monto_apertura=$row_ap->monto_apertura;
						 }
						//fin datos apertura
						$tabla = "controlcaja";
						$form_data = array(
							'fecha' => $fecha_actual,
							'fecha_corte' => $fecha_actual,
							'caja' => $caja,
							'turno' => $turno,
							//'cajero' => $id_usuario_corte,
							'tinicio' => $tik_min, //datos ticket
							'tfinal' => $tik_max, //datos ticket
							'totalnot' => $tik_count, //datos ticket
							'totalt' => $tik_total,//datos ticket
							'tgravado' => $tik_total,//datos ticket todos son gravados
							'finicio' => $cof_min,//datos cof
							'ffinal' => $cof_max,//datos cof
							'totalnof' => $cof_count,//datos cof
							'totalf' => $cof_total,//datos cof
							'fgravado' =>  $cof_total,//datos cof
							'cfinicio' => $ccf_min,//datos ccf
							'cffinal' => $ccf_max,
							'totalnocf' => $ccf_count,
							'totalcf' => $ccf_total,//datos ccf
							'cfgravado' =>  $ccf_total,//datos ccf
							'vales'=> $total_salida_caja,
							'ingresos'=> $total_entrada_caja,
							'hora_corte' => $hora_actual,
							'id_empleado' => $id_usuario,
							'id_sucursal' => $id_sucursal,
							'id_apertura' => $id_apertura,
							'diferencia' => $diferencia_val,
							'totalgral' => $total_fin,
							'cashfinal' => $total_efectivo,
							'cashinicial' => $monto_apertura,
							'tipo_corte' =>"C",
							//'vtaefectivo' => $total_contado, //pendiente e importante facturar tipo pago
							//'tarjetas' => $total_tarjeta, //pendiente e importante facturar tipo pago
							'vales' => $total_salida_caja,
							'ingresos' => $total_entrada_caja,
							'totalnodev' => $total_dev, //averiguar si es nota devolucion o se imprime devolucion en tiquet
							//	'rinicio' => $res_min,
							//	'rfinal' => $res_max,
							// 'totalnor' => $t_res,
							//	'monto_ch' => $monto_ch,
							'retencion' => $total_retencion, //falta validar si son clientes grandes contribuyentes y facturas>100 aplicar 1% retencion
							'observaciones' => $observaciones,
						);
						$id_cortex="";
						$where_arr= array('id_apertura' => $id_apertura,'tipo_corte' =>'Z');
						$cuentax = $this->corte->get_totalrow_params($tabla,$where_arr);
						$id_estado=2; //venta finalizada
						$array_parameter=array(
						 "id_sucursal" => $id_sucursal,
						 "id_apertura" =>$id_apertura,
							"tipo_doc"=>4,
							"id_estado"=>$id_estado,
							"fecha"=>$fecha_apertura,
						);
						$tipo_corte = "C";
						if($cuentax == 0){
								if($tipo_corte == "C"){
										$insert = $this->utils->insert($tabla,$form_data);
										$id_corte=$this->utils->insert_id();
										if($insert){
												//detalle turnos
											$row_turno =	$this->corte->get_turno_desc("detalle_apertura", array('id_apertura' => $id_apertura,));
									    $tuno = $row_turno->turno;
									    $id_usuario = $row_turno->id_usuario;
											$row_turno2 =	$this->corte->get_turno_desc("detalle_apertura", array('id_apertura' => $id_apertura,'vigente'=>1,));
											$id_detalle = $row_turno->id_detalle;
											$n_tuno = $tuno + 1;
									    $tabla = "detalle_apertura";
									    $form_data = array(
									        'vigente' => 0
									        );

									    $where_up = "id_detalle='".$id_detalle."'";
									    $update = $this->utils->update($tabla, $form_data, $where_up);
											////
											if($update){
													$tabla1 = "detalle_apertura";
													$form_data1 = array(
															'id_apertura' => $id_apertura,
															'turno' => $n_tuno,
															'fecha' => $fecha_actual,
															'hora' => $hora_actual,
															'vigente' => 1,

															);
													$insert2 =  $this->utils->insert($tabla1, $form_data1);
													if($insert2)
													{
															$tabla1 = "apertura_caja";
															$form_data1 = array(
																	'turno' => $n_tuno,
																	'turno_vigente' => 1,
																	);
															$where_up = "id_apertura='".$id_apertura."'";
															$update1 =  $this->utils->update($tabla1, $form_data1, $where_up);
													}
											}
												$table_apertura = "apertura_caja";
											$where_apertura = "id_apertura='".$id_apertura."'";
											$form_up = array(
												'monto_vendido' => $total_efectivo,
											);
											$up_apertura =  $this->utils->update($table_apertura, $form_up, $where_apertura);

											////

													$id_cortex=$this->utils->insert_id();
													$row_devs = $this->corte->get_detail_rows("ventas", $array_parameter);
													if($row_devs!=NULL){
															foreach ($row_devs as $detalle){
																$id_devolucion = $detalle->id_devolucion;
																$correlativo=$detalle->correlativo;
																$total_dev=$detalle->total;
																$row_data_dev = $this->corte->get_data_dev($id_devolucion);
																if($row_data_dev!=NULL){
																		$detalle->doc_afecta =	$row_data_dev->doc_afecta;
																		$detalle->tipo_doc =	$row_data_dev->tipo_doc;
																		$detalle->nombredoc =	$row_data_dev->nombredoc;
																		$detalle->corr_afecta =	$row_data_dev->corr_afecta;

																		$table_dev = "devoluciones_corte";
																		$form_dev = array(
																			'id_corte' => $id_cortex,
																			'n_devolucion' => $correlativo,
																			't_devolucion' =>	$total_dev,
																			'afecta' =>	$row_data_dev->corr_afecta,
																			'tipo' => 	$row_data_dev->tipo_doc,
																		);
																		$id_dev = $this->corte->inAndCon($table_dev,$form_dev);
																}
														}
											  }

										//}

										$row_confdir=$this->corte->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));
										$xdatos=$this->print_corte($id_corte,$id_sucursal);

											$xdatos["type"]="success";
											$xdatos['title']='Información';
											$xdatos["msg"]="turno cerrado correctamente!";

											$xdatos["opsys"]=$opsys;
											$xdatos["dir_print"] =$row_confdir->dir_print_script; //for Linux
											$xdatos["dir_print_pos"] =$row_confdir->shared_printer_pos; //for win
											$xdatos['id_corte']=$id_corte;
											$xdatos["proceso"]="insert";
										}
										else{
											$xdatos['type']='error';
											$xdatos['title']='Alerta';
											$xdatos['msg']='Error al guardar el turno !';
										}
										echo json_encode($xdatos);
								}
						}


					}

	}//cierre_turno();
	function print_corte($id_corte,$id_sucursal){
		//encabezado
		$row_hf=$this->corte->get_one_row("config_pos", array('id_sucursal' => $id_sucursal,'alias_tipodoc'=>'CORT',));
		$row_dte=$this->corte->get_one_row("sucursales", array('id_sucursal' => $id_sucursal,));
		$where_arr=array(
		 "id_sucursal" => $id_sucursal,
		 "id_corte" =>$id_corte,
		);
		$detcorte = $this->corte->get_one_row("controlcaja",$where_arr);
		$hstring="";
		$line1=str_repeat("_",42)."\n";

		$hstring.=chr(27).chr(33).chr(16); //FONT double size
		$hstring.= chr(27).chr(97).chr(1); //Center
		if($row_hf->header1!='')

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

		$hstring.=chr(13)."NIT: ".$row_dte->nit."\n";
		$hstring.=chr(13)."NRC: ".$row_dte->nit."\n";

		//pie
			$pstring="";
		if($row_hf->footer1!='')
			$pstring.=chr(13).$row_hf->footer1."\n";
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
		//detalles productos
		$det_corte="";
		$espacio=" ";
		$esp_init0=AlignMarginText::leftmargin($espacio,2);
		$margen_izq2=AlignMarginText::leftmargin($espacio,3);
		$sp1=AlignMarginText::leftmargin($espacio,1);
		//detalle corte
		$hora= $detcorte->hora_corte;
		$fecha= d_m_Y($detcorte->fecha_corte);
		$tipo= $detcorte->tipo_corte;
		$caja= $detcorte->caja;
		$tinicio= $detcorte->tinicio;
		$tfinal= $detcorte->tfinal;
		$finicio= $detcorte->finicio;
		$ffinal= $detcorte->ffinal;
		$cfinicio= $detcorte->cfinicio;
		$cffinal= $detcorte->cffinal;
		$cashini= $detcorte->cashinicial;
		$vtaefectivo= $detcorte->vtaefectivo;
		$ingresos= $detcorte->ingresos;
		$vales= $detcorte->vales;
		$totalgral= $detcorte->totalgral;
		$cashfinal= $detcorte->cashfinal;
		$diferencia= $detcorte->diferencia;
		$totalnot= $detcorte->totalnot;
		$totalnof= $detcorte->totalnof;
		$totalnocf= $detcorte->totalnocf;
		$monto_ch = $detcorte->monto_ch;
		$caja = $detcorte->caja;
		$tike =  $detcorte->tiket;
		$turno = $detcorte->turno;
		$retencion=   sprintf('%.2f',$detcorte->retencion);

		$texento= sprintf('%.2f', $detcorte->texento);
		$tgravado= sprintf('%.2f', $detcorte->tgravado);
		$totalt=  sprintf('%.2f', $detcorte->totalt);
		$fexento= sprintf('%.2f', $detcorte->fexento);
		$fgravado=sprintf('%.2f',  $detcorte->fgravado);
		$totalf= sprintf('%.2f', $detcorte->totalf);
		$cfexento= sprintf('%.2f', $detcorte->cfexento);
		$cfgravado=sprintf('%.2f',  $detcorte->cfgravado);
		$totalcf=sprintf('%.2f',  $detcorte->totalcf);
		$vtatotales=$totalt+$totalf+$totalcf;
		$vtatotales_print=sprintf('%.2f', $vtatotales);
		$vtaefectivo= sprintf('%.2f', $vtaefectivo);
		$cashini= sprintf('%.2f', $cashini);
		$ingresos= sprintf('%.2f', $ingresos);
		$vales=sprintf('%.2f', $vales);
		$cashfinal= sprintf('%.2f', $cashfinal);
		$diferencia= sprintf('%.2f', $diferencia);


		$dats_caja =	$this->corte->get_one_row("caja", array('id_caja' => $caja,));
		$fehca = d_m_Y($dats_caja->fecha);
		$resolucion = $dats_caja->resolucion;
		$serie = $dats_caja->serie;
		$desde = $dats_caja->desde;
		$hasta = $dats_caja->hasta;
		//$tinicio=  $this->utils->zfill($tinicio,$n);
		//$tfinal=  $this->utils->zfill($tfinal, 10);
		if($tipo=="C"){
			$desc_tipo='CORTE DE CAJA';
		}
		else{
			$desc_tipo="CORTE ".$tipo;
		}
			$det_corte.=chr(13).$desc_tipo."\n";
			$det_corte.=chr(13)."CORTE # ".$detcorte->id_corte."\n";
			if($tipo=="C"){
		$n=10;
		$subtotal=$cashini+$vtatotales+$ingresos;
		$totalcaja=$subtotal-$vales;
		$subtotal=sprintf('%.2f', $subtotal);
		$totalcaja=sprintf('%.2f', $totalcaja);
		$det_corte.=$line1;
		$det_corte.=$esp_init0."TIQUETES:     ".str_pad($tinicio,$n," ",STR_PAD_LEFT)."   ".str_pad($tfinal,7," ",STR_PAD_LEFT)."\n";
		$det_corte.=$esp_init0."FACTURAS:     ".str_pad($finicio,$n," ",STR_PAD_LEFT)."   ".str_pad($ffinal,7," ",STR_PAD_LEFT)."\n";
		$det_corte.=$esp_init0."FISCALES:     ".str_pad($cfinicio,$n," ",STR_PAD_LEFT)."   ".str_pad($cffinal,7," ",STR_PAD_LEFT)."\n";
		$det_corte.=$line1;
		$det_corte.="\n";
		$cashini=AlignMarginText::rightaligner($cashini,$espacio,12);
		$det_corte.=$esp_init0."SALDO INICIAL $:      ".$sp1.$cashini."\n";
	  $ingresos=AlignMarginText::rightaligner($ingresos,$espacio,12);
		$det_corte.=$esp_init0."(+)INGRESOS $:        ".$sp1.$ingresos."\n";
		$vtatotales_print=AlignMarginText::rightaligner($vtatotales_print,$espacio,12);
		$det_corte.=$esp_init0."(+) VENTA $:          ".$sp1.$vtatotales_print."\n";
		$det_corte.=$line1;
		$subtotal=AlignMarginText::rightaligner($subtotal,$espacio,12);
		$det_corte.=$esp_init0."SUBTOTAL $:           ".$sp1.$subtotal."\n";
		$vales=AlignMarginText::rightaligner($vales,$espacio,12);
		$det_corte.=$esp_init0."(-) VALES $:          ".$sp1.$vales."\n";
		$det_corte.=$line1;
		$totalcaja=AlignMarginText::rightaligner($totalcaja,$espacio,12);
		$det_corte.=$esp_init0."TOTAL CAJA $:         ".$sp1.$totalcaja."\n";
		$det_corte.="\n";
		$retencion=AlignMarginText::rightaligner(number_format($retencion,2,".",""),$espacio,12);
		$det_corte.=$esp_init0."(-) RETENCION $:      ".$sp1.$retencion."\n";
			$array_parameter=array(
			 "id_corte" => $id_corte,
			);
			$row_devs = $this->corte->get_detail_rows("devoluciones_corte", $array_parameter);
			$total_devoluciones=0;
			if($row_devs!=NULL){
					foreach ($row_devs as $detalle){
						$total_dev = AlignMarginText::rightaligner(number_format($detalle->t_devolucion,2,".",""),$espacio,12);
						$det_corte.=$esp_init0."(-)DEVOLUCIONES $:    ".$sp1.$total_dev."\n";
						$total_devoluciones+=$total_dev;
				}
			}

			$det_corte.=$line1;
			$cashfinal=AlignMarginText::rightaligner($cashfinal,$espacio,12);
			$det_corte.=$esp_init0."EFECTIVO $:           ".$sp1.$cashfinal."\n";
			$diferencia=AlignMarginText::rightaligner($diferencia,$espacio,12);
			$det_corte.=$esp_init0."DIFERENCIA $:         ".$sp1.$diferencia."\n";


					$det_corte.=chr(13).$line1;
					$det_corte.= chr(27).chr(33).chr(0); //FONT A
					$det_corte.= chr(27).chr(97).chr(1); //Center align
					$totales= chr(27).chr(33).chr(16); //FONT A
					$totales.= chr(27).chr(97).chr(2); //Right align
					$totals="  TOTAL   $ ".$totalgral."\n";
					$lentot=strlen($totals);
					$totales.=$totals;
					$totales.= chr(27).chr(33).chr(0); //FONT A
					$l2=str_repeat("_",$lentot)."\n";
					$totales.=$l2;
					$xdatos["encabezado"]=$hstring;
					$xdatos["totales"]=$totales;
					$xdatos["cuerpo"]=$det_corte;
					$xdatos["pie"]=$pstring;
					return $xdatos;
					}

					$esp_init=$esp_init0;
		if($tipo=="X" || $tipo=="Z"){
				$det_corte="";
				$det_corte.=chr(13).$desc_tipo."\n";
				$det_corte.=$esp_init."CORTE # ".$tike."\n\n";
				$subtotal=$cashini+$vtaefectivo+$ingresos;
				$totalcaja=$subtotal-$vales;
				$tot_exent=$texento+$fexento+$cfexento;
				$tot_grav=$tgravado+$fgravado+$cfgravado;
				$tot_fin=$totalt+$totalf+$totalcf;
				$tot_exent=sprintf('%.2f', $tot_exent);
				$tot_grav=sprintf('%.2f', $tot_grav);
				$tot_fin=sprintf('%.2f', $tot_fin);
				$subtotal=sprintf('%.2f', $subtotal);
				$totalcaja=sprintf('%.2f', $totalcaja);
				$esp_init1=AlignMarginText::leftmargin($espacio,12);
				$det_corte.=$esp_init1."EXEN.    GRAV.   TOTAL"."\n";
				$det_corte.=$line1;
				$n=8;
				$sp1=AlignMarginText::leftmargin($espacio,1);
				$texento=AlignMarginText::rightaligner($texento,$espacio,$n);
				$tgravado=AlignMarginText::rightaligner($tgravado,$espacio,$n);
				$totalt=AlignMarginText::rightaligner($totalt,$espacio,$n);
				$det_corte.=$sp1."TIQUETES:".$sp1.$texento.$sp1.$tgravado.$sp1.$totalt."\n";
				$fexento=AlignMarginText::rightaligner($fexento,$espacio,$n);
				$fgravado=AlignMarginText::rightaligner($fgravado,$espacio,$n);
				$totalf=AlignMarginText::rightaligner($totalf,$espacio,$n);
				$det_corte.=$sp1."FACTURAS:".$sp1.$fexento."".$sp1.$fgravado."".$sp1.$totalf."\n";
				$cfexento=AlignMarginText::rightaligner($cfexento,$espacio,$n);
				$cfgravado=AlignMarginText::rightaligner($cfgravado,$espacio,$n);
				$totalcf=AlignMarginText::rightaligner($totalcf,$espacio,$n);
				$det_corte.=$sp1."FISCALES:".$sp1.$cfexento."".$sp1.$cfgravado."".$sp1.$totalcf."\n";
				$det_corte.=$line1;
				$tot_exent=AlignMarginText::rightaligner($tot_exent,$espacio,$n);
				$tot_grav=AlignMarginText::rightaligner($tot_grav,$espacio,$n);
				$tot_fin=AlignMarginText::rightaligner($tot_fin,$espacio,$n);

				$det_corte.=$sp1."TOTAL $ :".$sp1.$tot_exent.$sp1.$tot_grav.$sp1.$tot_fin."\n";
				$det_corte.="\n";

				$det_corte.=$esp_init1."   INICIO   FINAL   TOTAL"."\n";
				$det_corte.=$line1;
				$n=7;
				$total_docs=$totalnot+$totalnof+$totalnocf;
				$tinicio=AlignMarginText::rightaligner($tinicio,$espacio,$n);
				$tfinal=AlignMarginText::rightaligner($tfinal,$espacio,$n);
				$totalnot=AlignMarginText::rightaligner($totalnot,$espacio,$n);
				$det_corte.=$sp1."TIQUETES: ".$sp1.$tinicio.$sp1.$tfinal.$sp1.$totalnot."\n";
				$finicio=AlignMarginText::rightaligner($finicio,$espacio,$n);
				$ffinal=AlignMarginText::rightaligner($ffinal,$espacio,$n);
				$totalnof=AlignMarginText::rightaligner($totalnof,$espacio,$n);
				$det_corte.=$sp1."FACTURAS: ".$sp1.$finicio.$sp1.$ffinal.$sp1.$totalnof."\n";
				$cfinicio=AlignMarginText::rightaligner($cfinicio,$espacio,$n);
				$cffinal=AlignMarginText::rightaligner($cffinal,$espacio,$n);
				$totalnocf=AlignMarginText::rightaligner($totalnocf,$espacio,$n);
				$det_corte.=$sp1."FISCALES: ".$sp1.$cfinicio.$sp1.$cffinal.$sp1.$totalnocf."\n";
				$det_corte.=$line1;
				$total_docs=AlignMarginText::rightaligner($total_docs,$espacio,24);
				$det_corte.=$sp1."TOTAL:".$sp1.$total_docs."\n";
				$det_corte.="\n";
				$xdatos["encabezado"]=$hstring;
				$xdatos["totales"]=$total_docs;
				$xdatos["cuerpo"]=$det_corte;
				$xdatos["pie"]=$pstring;
				return $xdatos;
			}
		}
	function apertura_turno(){
		if($this->input->method(TRUE) == "POST"){
			$fecha = date("Y-m-d");
			$hora_actual = date('H:i:s');
			$id_apertura = $this->input->post("id_apertura");
			$id_detalle = $this->input->post("id_detalle");
			$id_usuario=$this->session->id_usuario;


			$detalle_ap =	$this->corte->get_one_row("detalle_apertura", array('id_detalle' => $id_detalle,));

			if($detalle_ap!=NULL)
			{
					$tabla = "detalle_apertura";
					$form_data = array(
							'id_usuario' => $id_usuario,
							);

					$where_d = "id_detalle=".$id_detalle;
					$update_d= $this->utils->update($tabla, $form_data, $where_d);

					if($update_d)
					{
							$xdatos['type']='success';
							$xdatos['title']='Exito';
							$xdatos['msg']='Turno agregado correctamente!';
							$xdatos['process']='insert';
					}
					else
					{
						$xdatos['title']='Alerta';
							$xdatos['type']='error';
							$xdatos['msg']='Fallo al agregar el turno!';
					}
			}
			else
			{
				$xdatos['title']='Alerta';
					$xdatos['type']='Error';
					$xdatos['msg']='No existe un turno para asignar!';
			}
			echo json_encode($xdatos);
		}
	}
	function detalle($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			if ($this->agent->is_browser())
				{
						$agent = $this->agent->browser().' '.$this->agent->version();
						$opsys = $this->agent->platform();
				}
			$id_usuario = $this->session->id_usuario;
			$id_sucursal = $this->session->id_sucursal;

			$rowcorte=$this->corte->get_corte_id($id,$id_sucursal);
			if($rowcorte!=NULL){

			$row_ap = $this->corte->get_caja_corte($id_usuario,$rowcorte->fecha,$rowcorte->caja,$rowcorte->turno);

 			$monto_apertura = $row_ap->monto_apertura;
			$total_tmp = $monto_apertura+$rowcorte->totalt+$rowcorte->totalf+$rowcorte->totalcf+$rowcorte->ingresos-+$rowcorte->vales;

			$data = array(
				"id" =>$id,
				"ap_row" =>$row_ap,
				"rowcorte"=>$rowcorte,
				"total_tmp"=>$total_tmp,
			);
		}else{
				$data = array(
						"id" =>$id,
						"ap_row" =>-1,
					"rowcorte"=>-1,

				);
			}
			$extras = array(
				'css' => array(
				),
				'js' => array(
										"js/scripts/corte.js",
				),
			);
				$this->load->view("corte/ver_detalle.php",$data,$extras);
		}
	}
	//reimprimit corte
	function printdoc($id=-1){
		if($this->input->method(TRUE) == "POST"){
			if ($this->agent->is_browser())
				{
						$agent = $this->agent->browser().' '.$this->agent->version();
						$opsys = $this->agent->platform();
				}
			$id_corte = $this->input->post("id_corte");
			$rowcorte = $this->corte->get_one_row("controlcaja", array('id_corte' => $id_corte,));
			if($rowcorte!=NULL){
				$id_sucursal=$rowcorte->id_sucursal;
				$row_confdir=$this->corte->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));


				$xdatos["type"]="success";
				$xdatos['title']='Información';
				$xdatos["msg"]="Documento impreso correctamente!";
				$xdatos=$this->print_corte($id_corte,$id_sucursal);

					$xdatos["opsys"]=$opsys;
					$xdatos["dir_print"] =$row_confdir->dir_print_script; //for Linux
					$xdatos["dir_print_pos"] =$row_confdir->shared_printer_pos; //for win
					$xdatos['id_corte']=$id_corte;
					$xdatos["proceso"]="insert";

				echo json_encode($xdatos);
			}

		}
	}
}
/* End of file Productos.php */
