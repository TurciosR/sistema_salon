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

class Dashboard extends CI_Controller {


	public function __construct()
	{
		parent::__construct();
		//validar_session($this);
		$this->load->model("Dashboard_model");
		$this->load->model('UtilsModel',"utils");
		$this->load->Model("CorteModel","corte");
	}

	public function index()
	{
		if ($this->session->logged_in == "") {
			redirect('login', 'refresh');
		}
		//procedemos a obtener el total de caja
		$id_sucursal = $this->session->id_sucursal;
		$fechaHoy = date("Y-m-d");
		$id_estado=2;
		$row_ap = $this->corte->get_cajas_activa_sucursal($id_sucursal,$fechaHoy);
		if($row_ap!=NULL){
			$id_apertura=$row_ap->id_apertura;
			//procedemos a obtener todos los abonos realizados durante el dia
			$whereA=array(
			 "id_sucursal" => $id_sucursal,
			 "id_apertura" =>$id_apertura,
				"id_estado"=>$id_estado,
			);
			$whereA['v.credito']=1; //($hora_ap,$hora_actual,$valor,$where)
			$total_abonos_diarios= $this->corte->get_totales_abonos($fechaHoy,$whereA);
		}
		else {
			// code...
			$total_abonos_diarios=0;
		}
		//echo $total_abonos_diarios."#";
		$this->db->where("id_sucursal", $id_sucursal);
		$this->db->where("fecha", $fechaHoy);
		$this->db->select("SUM(monto_apertura) as monto_apertura");
		$this->db->from("apertura_caja");
		$queryCaja = $this->db->get();
		$arrCaja = $queryCaja->row();
		//echo $arrCaja->monto_apertura;
		//procedemos a obtener las entradas y las salidas de dinero
		$this->db->where("salida", "1");
		$this->db->where("anulado", "0");
		$this->db->where("id_sucursal", $id_sucursal);
		$this->db->where("fecha", $fechaHoy);
		$this->db->select("SUM(valor) as total_salida");
		$this->db->from("mov_caja");
		$querySalida = $this->db->get();
		$arrSalida = $querySalida->row();

		$this->db->where("entrada", "1");
		$this->db->where("anulado", "0");
		$this->db->where("id_sucursal", $id_sucursal);
		$this->db->where("fecha", $fechaHoy);
		$this->db->select("SUM(valor) as total_entrada");
		$this->db->from("mov_caja");
		$queryEntrada = $this->db->get();
		$arrEntrada = $queryEntrada->row();

		$this->db->where("ventas.fecha", $fechaHoy);
		$this->db->where("ventas.id_estado", "2");

		// corregido para agregar todos los tipo de documentos: ticket,consumidor y c. fiscal
		$this->db->where("ventas.tipo_doc BETWEEN '1' AND '3' ",NULL,FALSE);
		$this->db->where("ventas.credito", "0");
		$this->db->where("ventas.id_sucursal", $id_sucursal);
		$this->db->select("SUM(ventas.total) as total_ventas");
		$this->db->from("ventas");
		$queryVenta = $this->db->get();
		$arrVenta = $queryVenta->row();

		//devoluciones
		$this->db->where("ventas.fecha", $fechaHoy);
		//$this->db->where("ventas.id_estado", "2");
		$this->db->where("ventas.id_sucursal", $id_sucursal);
		$this->db->select("SUM(devoluciones.monto) as total_dev");
		$this->db->join("ventas", "ventas.id_venta=devoluciones.id_venta");
		$this->db->from("devoluciones");
		$queryDev = $this->db->get();
		$arrDev = $queryDev->row();

		$montoApertura = ($arrCaja->monto_apertura==NULL)?0:$arrCaja->monto_apertura;
		//echo $montoApertura."+".$arrEntrada->total_entrada."+".$arrVenta->total_ventas."-".$arrSalida->total_salida."-",$arrDev->total_dev;
		$totalCaja = $montoApertura + $arrEntrada->total_entrada + $arrVenta->total_ventas + $total_abonos_diarios - $arrSalida->total_salida - $arrDev->total_dev;
		//echo $totalCaja."##";
		$this->db->where("ventas.fecha", $fechaHoy);
		$this->db->where("ventas.credito", "0");
		$this->db->select("estado.id_estado, estado.descripcion, SUM(ventas.total) as total_ventas");
		$this->db->from("estado");
		$this->db->join("ventas","ventas ON ventas.id_estado=estado.id_estado","left");
		$this->db->group_by("estado.id_estado");
		$query = $this->db->get();

		//prodecemos a generar las ventas por sucursal
		$arrDatosCompletos = [];
		$j =1;
		foreach ($query->result() as $arrQuery){
			// code...
			$idEstado = $arrQuery->id_estado;
			$arrSucursalDatos = [];
			$this->db->select("sucursales.nombre, sucursales.id_sucursal");
			$this->db->from("sucursales");
			$queryS = $this->db->get();
			foreach ($queryS->result() as $arrSuc) {
				// code...
				//echo $j;
				$id_sucursalD = $arrSuc->id_sucursal;
				//echo $id_sucursalD;
				$this->db->where("ventas.fecha", $fechaHoy);
				$this->db->where("ventas.id_estado", $idEstado);
				// corregido para agregar todos los tipo de documentos: ticket,consumidor y c. fiscal
				$this->db->where("ventas.tipo_doc BETWEEN '1' AND '3' ",NULL,FALSE);
				$this->db->where("ventas.credito", "0");
				$this->db->where("ventas.id_sucursal_despacho", $id_sucursalD);
				$this->db->select("SUM(ventas.total) as total_ventas");
				$this->db->from("ventas");
				$queryD = $this->db->get();
				$arrSucD = $queryD->row();
				$arrSucursalDatos[] = array("sucursal" => $arrSuc->nombre, "total"=>($arrSucD->total_ventas==NULL)? 0:$arrSucD->total_ventas);
			}
			$arrDatosCompletos[] = array($arrSucursalDatos);
			$j++;
		}

		//procedemos a obtener los movimientos
		$this->db->where("id_sucursal", $id_sucursal);
		$this->db->where("fecha", $fechaHoy);
		$this->db->where("anulado", "0");
		$this->db->select("concepto, IF(salida=1, 'salida', 'entrada') as tipo, nombre_recibe, valor");
		$this->db->from("mov_caja");
		$queryM = $this->db->get();

		//var_dump($arrDatosCompletos);
		$id_usuario = $this->session->id_usuario;
		$usuario_ap =	$this->utils->get_one_row("usuario", array('id_usuario' => $id_usuario,));

		$role_user=	$this->utils->get_one_row("roles", array('id_rol' => $usuario_ap->id_rol,));
		$rol_usuario="";
		if($role_user!=NULL){
			$rol_usuario=strtoupper($role_user->nombre);
		}
		$visual = array(
			1 => array('color' => "lazur-bg", 'icon' => 'mdi mdi-package-down mdi-48px'),
			2 => array('color' => "yellow-bg", 'icon' => 'mdi mdi-file-check-outline mdi-48px'),
			3 => array('color' => "lazur-bg", 'icon' => 'mdi mdi-alert-circle-check-outline mdi-48px'),
			4 => array('color' => "navy-bg", 'icon' => 'mdi mdi-car-hatchback mdi-48px'),
			5 => array('color' => "yellow-bg", 'icon' => 'mdi mdi-emoticon-outline mdi-48px'),
			6 => array('color' => "red-bg", 'icon' => 'mdi mdi-cancel mdi-48px'),
		);
		$view_data = array(
			'data' => $query->result(),
			"datosSuc" => $arrDatosCompletos,
			"totalCaja" => $totalCaja,
			'visual' => $visual,
			"usuario_ap"=>$usuario_ap,
			"rol_usuario"=>$rol_usuario,
			"datosMovimientos"=>($queryM->num_rows()>0)?$queryM->result():0,
		);
		layout('dashboard',$view_data,"");
	}

	public function estado($id=-1)
	{
		if($this->input->method(TRUE) == "GET"){
				$id = $this->uri->segment(3);
				$this->db->select('estado.descripcion,estado.id_estado');
				$this->db->from("estado");
				$this->db->where("id_estado",$id);
				$query = $this->db->get();
				$rows=0;

				if ($query->num_rows()>0) {
					// code...
					$rows = $query->row();
				}
				else {
					$rows=0;
				}

				if($rows && $id!=""){
						$data = array(
								"rows"=>$rows,
						);
						layout('dash/estado.php',$data,"");
				}else{
						redirect('errorpage');
				}
		}
	}
	public function search()
	{
		$this->load->view("dash/searchimei");
	}

	public function imei()
	{
		// code...
		$val = $this->input->post("imei");
		if (trim($val)=="") {
			// code...
			$xdatos['typeinfo']="Error";
			$xdatos['msg']="El IMEI no puede estar vacio";

		}
		else {

			$this->db->select("ventas_detalle.id_venta,clientes.nombre,ventas_detalle.id_detalle,ventas.fecha, producto.nombre,producto.marca,producto.modelo,ventas_detalle.condicion,ventas_detalle.garantia");
			$this->db->from("ventas_imei");
			$this->db->join("ventas_detalle","ventas_detalle.id_detalle = ventas_imei.id_detalle");
			$this->db->join("ventas","ventas.id_venta=ventas_imei.id_venta");
			$this->db->join("producto","producto.id_producto=ventas_imei.id_producto");
			$this->db->join("clientes","clientes.id_cliente = ventas.id_cliente","left");
			$this->db->where("ventas_imei.imei",$val);
			$query = $this->db->get();

			if ($query->num_rows()>0) {
				// code...
				$data = $query->row();
				$garantiamaxima = date("Y-m-d",strtotime( $data->fecha."+ ".$data->garantia." days"));
				$data->vencimiento = d_m_Y($garantiamaxima);
				if (strtotime($garantiamaxima) >=strtotime(date("Y-m-d"))) {
					// code...
					$data->garantia_vigente = "true";
				}
				else {
					// code...
					$data->garantia_vigente = "false";
				}
				$data->fecha = d_m_Y($data->fecha);

				$xdatos['typeinfo']="Success";
				$xdatos['msg']="IMEI encontrado";
				$xdatos['data']=$data;
				$xdatos['d1']=strtotime($garantiamaxima);
				$xdatos['d2']=strtotime(date("Y-m-d"));

			}
			else {
				// code...
				$xdatos['typeinfo']="Error";
				$xdatos['msg']="El IMEI no esta registrado";
			}
		}
		echo json_encode($xdatos);
	}
	function getGrafica(){

		$a = date("Y");
		$m = date("m");
		//$a=2019;
		$ult = cal_days_in_month(CAL_GREGORIAN, $m, $a);
		$start = "$a-$m-01";
		$end = "$a-$m-$ult";

		$id_sucursal = $this->session->id_sucursal;

		$this->db->select("COUNT(ventas.id_estado) as cantidad, estado.descripcion ");
		$this->db->from("ventas");
		$this->db->join('estado', 'estado.id_estado=ventas.id_estado');
		$this->db->where("fecha BETWEEN '$start' AND '$end'");
		$this->db->where("ventas.id_sucursal",$id_sucursal);
		$this->db->group_by("ventas.id_estado");
		$query = $this->db->get();
		$data = array();

		if($query->num_rows()>0){

			 $d = $query->result();

			 foreach ($d as $key) {
			 	// code...
				$data[] = array(
					"total" => $key->cantidad,
					"mes" => $key->descripcion,
				);
			 }
		}else{
			$data[] = array(
				"total" => 0,
				"mes" => "No hay datos",
			);
		}
		echo json_encode($data);
	}

	function getGraficaEstado(){

		$id  = $this->input->post("tipo");
		$inicio = date("Y-m-d",strtotime(date("Y-m-d")."- 12 month"));

		$data = array();

		for($i=0; $i<13; $i++)
		{
			$a = explode("-",$inicio)[0];
			$m = explode("-",$inicio)[1];
			$ult = cal_days_in_month(CAL_GREGORIAN, $m, $a);
			$ini = "$a-$m-01";
			$fin = "$a-$m-$ult";
			$this->db->select("SUM(ventas.total) as total");
			$this->db->from("ventas");
			$this->db->where("fecha BETWEEN '$ini' AND '$fin'");
			$this->db->where("ventas.id_estado",$id);
			$query = $this->db->get();
			$row = $query->row();
				$data[] = array(
					"total" => number_format($row->total,2,".",""),
					"mes" => "$m-$a",
					);
			$inicio = date("Y-m-d",strtotime($ini."+ 1 month"));
		}

		echo json_encode($data);

	}
	function grafica_permiso_admin(){
		$inicio = restar_meses(date("Y-m-d"),6);
		for($i=0; $i<6; $i++)
		{
			$a = explode("-",$inicio)[0];
			$m = explode("-",$inicio)[1];
			$ult = cal_days_in_month(CAL_GREGORIAN, $m, $a);
			$start = "$a-$m-01";
			$end = "$a-$m-$ult";
			$row = $this->Dashboard_model->grafica_permiso($start,$end);
			$total = $row->total;
			$data[] = array(
				"total" => $total,
				"mes" => nombre_mes($m),
			);
			$inicio = sumar_meses($start,1);
		}
		echo json_encode($data);
	}


	function grafica_vacacion_admin(){
		$inicio = restar_meses(date("Y-m-d"),6);
		for($i=0; $i<6; $i++)
		{
			$a = explode("-",$inicio)[0];
			$m = explode("-",$inicio)[1];
			$ult = cal_days_in_month(CAL_GREGORIAN, $m, $a);
			$start = "$a-$m-01";
			$end = "$a-$m-$ult";
			$row = $this->Dashboard_model->grafica_vacacion($start,$end);
			$total = $row->total;
			$data[] = array(
				"total" => $total,
				"mes" => nombre_mes($m),
			);
			$inicio = sumar_meses($start,1);
		}
		echo json_encode($data);
	}

}
