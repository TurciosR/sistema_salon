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

class Reportes extends CI_Controller {

	/*
	Enviroment variables
	*/
	private $table = "colores";
	private $pk = "id_color";

	function __construct()
	{
		parent::__construct();
		$this->load->Model("ColoresModel","colores");
		$this->load->helper("upload_file");
		$this->load->model('UtilsModel',"utils");
		$this->load->model("ReportesModel","reportes");
		$this->load->model("InventarioModel","inventario");
		$this->load->model("ProductosModel","productos");
	}

	function agregar(){

		if($this->input->method(TRUE) == "GET"){
		$generarReportes = $this->reportes->get_reportes();
			$id_sucursal = 1;
			$stock = $this->productos->get_stock_r($id_sucursal);
		//var_dump($generarReportes);
			$data = array(
				"productos"=>$stock,
		"reportes"=> $generarReportes,
				"sucursal"=>$this->inventario->get_detail_rows("sucursales",array('1' => 1, )),
				"id_sucursal" => $this->session->id_sucursal,
			);
			$extras = array(
				'css' => array(
				),
				'js' => array(
					"js/scripts/reportes.js",
				),
			);
			layout("reports/generar_reportes",$data,$extras);
		}
	}
	function reporte_existencias(){

		if($this->input->method(TRUE) == "GET"){
		$generarReportes = $this->reportes->get_reportes();
			$id_sucursal = 1;
			$stock = $this->productos->get_stock_r($id_sucursal);
		//var_dump($generarReportes);
			$data = array(
				"productos"=>$stock,
		"reportes"=> $generarReportes,
				"sucursal"=>$this->inventario->get_detail_rows("sucursales",array('1' => 1, )),
				"id_sucursal" => $this->session->id_sucursal,
			);
			$extras = array(
				'css' => array(
				),
				'js' => array(
					"js/scripts/reportes.js",
				),
			);
			layout("reports/reporte_existencias",$data,$extras);
		}
	}
	function get_stock_sucursal(){
		$id_sucursal = $this->input->post("id");
		$stock = $this->productos->get_stock_r($id_sucursal);
		$option ="<option value=''>Seleccione...</option>";
		foreach ($stock as $arrP) {
			$option .= "<option value='".$arrP->id_producto."' color='".$arrP->id_color."'>$arrP->codigo_barra $arrP->nombre  $arrP->color</option>";
		}
		echo $option;
	}
	function generar(){
	if($this->input->method(TRUE) == "GET"){

	$id = $this->uri->segment(3);
	//echo $id;
	$tipoReporte = $this->uri->segment(4);
	$fechaI = $this->uri->segment(5);
	$fechaF = $this->uri->segment(6);
		$sucursal = $this->uri->segment(7);//sucursal
		//procedemos a obtener los datos de la sucursal
		$arrSucursal = $this->reportes->get_row_sucursal($sucursal);
	$this->load->library('Report');
	//procedemos a obtener el tipo de reporte
	$obtenerTipo = $this->reportes->get_tipo_reporte($id);
	//var_dump($obtenerTipo);
	$pdf = $this->report->getInstance('P','mm', 'Letter');
	$logo = getLogo();
	$pdf->SetMargins(6, 10);
	$pdf->SetLeftMargin(5);
	$pdf->AliasNbPages();
	$pdf->SetAutoPageBreak(true, 15);
	$pdf->AliasNbPages();
	$data = array("empresa" => "Jah","imagen" => $logo, 'fecha' =>"14-10-1998", 'titulo' => $obtenerTipo->nombre);
	$pdf->setear($data);
	$pdf->addPage();
	$pdf->SetFont('Arial','B',10);

		$l = array(
			's' => 10,
			'con' =>180,
		);
		$array_data = array(
			array('',$l['s'],"C"),
			array($arrSucursal->nombre." ".$arrSucursal->direccion,$l['con'],"C"),
		);
		$pdf->LineWrite($array_data);
		$pdf->LN(5);

	if ($tipoReporte==0) {
		// general...
		if($obtenerTipo->parametro=="report_utilidades"){
		$l = array(
			's' => 10,
			'con' =>130,
			'tot' => 60
		);
		$array_data = array(
			array('',$l['s'],"C"),
			array('Concepto',$l['con'],"C"),
			array('Total',$l['tot'],"C"),
		);
		$pdf->LineWriteB($array_data);

		$data = $this->reportes->get_totales(Y_m_d($fechaI), Y_m_d($fechaF), $sucursal);
		//var_dump($data);
		$ventasTotales = $data->total-$data->descuento;
		$ventasNetas = $ventasTotales - $data->costo;
		$pdf->SetFont('Arial','',10);
		$array_data = array(
		array('',$l['s'],"C"),
		array("Ventas Totales",$l['con'],"L"),
		array("$".number_format(($ventasTotales), 2, '.', ''),$l['tot'],"R")
		);
		$pdf->LineWriteB($array_data);

		$array_data = array(
		array('',$l['s'],"C"),
		array("(-)Costo de Ventas",$l['con'],"L"),
		array("$".number_format($data->costo, 2, '.', ''),$l['tot'],"R")
		);
		$pdf->LineWriteB($array_data);

		$pdf->SetFont('Arial','B',10);
		$array_data = array(
		array('',$l['s'],"C"),
		array("(=)Ventas Netas",$l['con'],"L"),
		array("$".number_format($ventasNetas, 2, '.', ''),$l['tot'],"R")
		);
		$pdf->LineWriteB($array_data);
		}//fin de reporte utilidades
	}
	elseif ($tipoReporte==1) {
		// especifico...
		if($obtenerTipo->parametro=="report_utilidades"){
		$totalAcum = 0;
				$costoAcum=0;
				$cantidadAcum=0;
		$l = array(
			's' => 10,
			'nom' =>110,
			'can' => 15,
			'cos' => 30,
			'tot' => 30
		);
		$array_data = array(
			array('',$l['s'],"C"),
			array('Nombre',$l['nom'],"C"),
			array('Cant.',$l['can'],"C"),
			array('Costo',$l['cos'],"C"),
			array('Subtotal',$l['tot'],"C"),
		);
		$pdf->LineWriteB($array_data);

		$data = $this->reportes->get_ventas_rango(Y_m_d($fechaI), Y_m_d($fechaF), $sucursal);
		//var_dump($data);
		$pdf->SetFont('Arial','',10);
				if ($data==0) {
					$array_data = array(
					array('',$l['s'],"C"),
					array('sin resultados...',$l['nom'],"L"),
					array('',$l['can'],"R"),
					array('',$l['cos'],"R"),
					array('',$l['tot'],"R"),
					);
					$pdf->LineWriteB($array_data);
				}
				else {
					foreach ($data as $arrData) {
						$totalAcum += $arrData['subtotal'];
						$costoAcum += $arrData['costo'];
						$cantidadAcum += $arrData['cantidad'];
						$array_data = array(
						array('',$l['s'],"C"),
						array($arrData['nombre'],$l['nom'],"L"),
						array($arrData['cantidad'],$l['can'],"R"),
						array("$".number_format($arrData['costo'], 2, '.', ''),$l['cos'],"R"),
						array("$".number_format($arrData['subtotal'], 2, '.', ''),$l['tot'],"R"),
						);
						$pdf->LineWriteB($array_data);
					}

					$pdf->SetFont('Arial','B',10);
					$array_data = array(
					array('',$l['s'],"C"),
					array("Total",$l['nom'],"L"),
					array($cantidadAcum,$l['can'],"R"),
					array("$".number_format(($costoAcum), 2, '.', ''),$l['cos'],"R"),
					array("$".number_format(($totalAcum), 2, '.', ''),$l['tot'],"R")
					);
					$pdf->LineWriteB($array_data);
				}
		}//fin de reporte utilidades
	}

	}//emergencias reportadas
	$pdf->Output();
	//echo $id."#";
	}
	function generarExist(){
	if($this->input->method(TRUE) == "GET"){

		$id = $this->uri->segment(3);
		$sucursal = $this->uri->segment(4);//sucursal
		//procedemos a obtener los datos de la sucursal
		$arrSucursal = $this->reportes->get_row_sucursal($sucursal);
		$this->load->library('Report');
		$pdf = $this->report->getInstance('P','mm', 'Letter');
		$logo = getLogo();
		$pdf->SetMargins(6, 10);
		$pdf->SetLeftMargin(5);
		$pdf->AliasNbPages();
		$pdf->SetAutoPageBreak(true, 15);
		$pdf->AliasNbPages();
		$data = array("empresa" => "Jah","imagen" => $logo, 'fecha' =>"14-10-1998", 'titulo' => "Reporte de Existencias");
		$pdf->setear($data);
		$pdf->addPage();
		$pdf->SetFont('Arial','B',10);

		$l = array(
			's' => 10,
			'con' =>180,
		);
		$array_data = array(
			array('',$l['s'],"C"),
			array($arrSucursal->nombre." ".$arrSucursal->direccion,$l['con'],"C"),
		);
		$pdf->LineWrite($array_data);
		$pdf->LN(5);

				$totalAcum = 0;
				$costoAcum=0;
				$cantidadAcum=0;
				$l = array(
					's' => 10,
					'cod' => 40,
					'nom' =>130,
					'can' => 15
				);
				$array_data = array(
					array('',$l['s'],"C"),
					array('Codigo',$l['cod'],"C"),
					array('Nombre',$l['nom'],"C"),
					array('Cant.',$l['can'],"C"),
				);
				$pdf->LineWriteB($array_data);

				$data = $this->reportes->get_existencias($sucursal);
				//var_dump($data);
				$pdf->SetFont('Arial','',10);
				if ($data==0) {
					$array_data = array(
					array('',$l['s'],"C"),
					array('',$l['cod'],"R"),
					array('sin resultados...',$l['nom'],"L"),
					array('',$l['can'],"R"),
					);
					$pdf->LineWriteB($array_data);
				}
				else {
					foreach ($data as $arrData) {
						$cantidadAcum += $arrData->cantidad;
						$array_data = array(
						array('',$l['s'],"C"),
						array($arrData->codigo_barra,$l['cod'],"L"),
						array($arrData->cadena,$l['nom'],"L"),
						array($arrData->cantidad,$l['can'],"R"),
						);
						$pdf->LineWriteB($array_data);
					}
				}

		}//emergencias reportadas
		$pdf->Output();
		//echo $id."#";
	}

	/**
	 * Generated kardex report
	 *
	 * generates a report with all the movements of a product
	 *
	 * @param string $fecha_inicial
	 * @param string $fecha_final
	 * @param int $id_sucursal
	 * @param int $id_producto
	 * @param int $id_color
	 *
	 * @return void
	 *
	 */
	public function generar_kardex(){
		if ($this->input->method(TRUE) == "GET") {

			// Get parameters
			$fecha_inicial	= $this->uri->segment(3);
			$fecha_final 	= $this->uri->segment(4);
			$id_sucursal 	= $this->uri->segment(5);
			$id_producto 	= $this->uri->segment(6);
			$id_color 		= $this->uri->segment(7);

			$encabezado = $this->productos->get_stock_data($id_producto, $id_color);
			$encabezado =$encabezado->nombre." ".$encabezado->color;

			// Get product data
			$kardex = $this->reportes->obtener_kardex(
				$fecha_inicial,
				$fecha_final,
				$id_sucursal,
				$id_producto,
				$id_color
			);

			$this->load->library('Report');

			$pdf = $this->report->getInstance('L','mm', 'Letter');
			$logo = getLogo();
			$pdf->SetMargins(6, 10);
			$pdf->SetLeftMargin(5);
			$pdf->AliasNbPages();
			$pdf->SetAutoPageBreak(true, 15);
			$pdf->AliasNbPages();
			$data = array("empresa" => "Jah","imagen" => $logo, 'fecha' =>"14-10-1998", 'titulo' => $encabezado." - Kardex");
			$pdf->setear($data);
			$pdf->addPage();
			$pdf->SetFont('Arial','B',10);



			$l = array(
				's' => 10,
				'fec' =>20,
				'doc' =>15,
				'tip' =>15,
				'pro' =>25,
				'ent' => 58,
				'sal' => 58,
				'exi' => 58
			);

			if ($kardex) {
				$array_data = array(
				array('',$l['s'],"C"),
				array('FECHA',$l['fec'],"C"),
				array('DOC',$l['doc'],"C"),
				array('TIPO',$l['tip'],"C"),
				array('PROCESO',$l['pro'],"C"),
				array('ENTRADAS',$l['ent'],"C"),
				array('SALIDAS',$l['sal'],"C"),
				array('EXISTENCIAS',$l['exi'],"C"),
				);
				$pdf->LineWriteB($array_data);
				$pdf->SetFont('Arial','',7);

				$l = array(
					's' => 10,
					'fec' =>20,
					'doc' => 15,
					'tip' =>15,
					'pro' =>25,
					'can' => 20,
					'cos' => 20,
					'tot' => 18,
					'rel' => 60
				);
				$pdf->SetFont('Arial','B',6);
				$array_data = array(
				array('',$l['s'],"C"),
				array("",$l['fec'],"L"),
				array("",$l['doc'],"C"),
				array("",$l['tip'],"C"),
				array("",$l['pro'],"C"),

				array("UNIDAD",$l['can'],"C"),
				array("COSTO",$l['cos'],"C"),
				array("SUBTOTAL",$l['tot'],"C"),
				array("UNIDAD",$l['can'],"C"),
				array("COSTO",$l['cos'],"C"),
				array("SUBTOTAL",$l['tot'],"C"),
				array("UNIDAD",$l['can'],"C"),
				array("COSTO",$l['cos'],"C"),
				array("SUBTOTAL",$l['tot'],"C"),
				//array("",$l['rel'],"R"),
				);
				$pdf->LineWriteB($array_data);
				$existencias =0;
				$costoK=0;
				$subtotalK = 0;
				$pdf->SetFont('Arial','',7);

				foreach ($kardex as $arrKardex) {
					$l = array(
						's' => 10,
						'fec' =>20,
						'doc' =>15,
						'tip' =>15,
						'pro' =>25,
						'can' => 20,
						'cos' => 20,
						'tot' => 18,
						'rel' => 58
					);

					$subtotalInd = $arrKardex['costo'] * $arrKardex['cantidad'];
					$subtotalK = $arrKardex['costo'] * $arrKardex['stock_actual'];

					if ($arrKardex['tipo'] == "ENTRADA") {
						$array_data = array(
							array('',$l['s'],"C"),
							array($arrKardex['fecha'],$l['fec'],"L"),
							array($arrKardex['numero_documento'],$l['doc'],"C"),
							array(strtoupper($arrKardex['tipo']),$l['tip'],"C"),
							array($arrKardex['proceso'],$l['pro'],"C"),
							array($arrKardex['cantidad'],$l['can'],"R"),
							array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
							array("$".number_format($subtotalInd, 2),$l['tot'],"R"),
							array("",$l['rel'],"R"),
							array($arrKardex['stock_actual'],$l['can'],"R"),
							array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
							array("$".number_format($subtotalK, 2),$l['tot'],"R"),
							//array("",$l['rel'],"R"),
						);

					} else{
						$array_data = array(
							array('',$l['s'],"C"),
							array($arrKardex['fecha'],$l['fec'],"L"),
							array($arrKardex['numero_documento'],$l['doc'],"C"),
							array(strtoupper($arrKardex['tipo']),$l['tip'],"C"),
							array($arrKardex['proceso'],$l['pro'],"C"),
							array("",$l['rel'],"R"),
							array($arrKardex['cantidad'],$l['can'],"R"),
							array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
							array("$".number_format($subtotalInd, 2),$l['tot'],"R"),
							array($arrKardex['stock_actual'],$l['can'],"R"),
							array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
							array("$".number_format($subtotalK, 2),$l['tot'],"R"),
							//array("",$l['rel'],"R"),
						);
					}

					$pdf->LineWriteB($array_data);
				}
			}else{
				$array_data = array(
					array('',10,"C"),
					array("No hay registros en este periodo de tiempo", 245
					,"L"),
				);
				$pdf->LineWriteB($array_data);
			}
			$pdf->Output();
		}
	}

	function generar_kardex_old(){
	if($this->input->method(TRUE) == "GET"){
	$fechaI = $this->uri->segment(3);
	$fechaF = $this->uri->segment(4);
		$sucursal = $this->uri->segment(5);//sucursal
		$id = $this->uri->segment(6);//id producto
		$color = $this->uri->segment(7);//id color
		//procedemos a obtener los datos del producto
		$datosP = $this->productos->get_stock_data($id, $color);
		$encabezadoP = $datosP->nombre." ".$datosP->color;
		//procedemos a obtener los datos de la sucursal
		$arrSucursal = $this->reportes->get_row_sucursal($sucursal);
	$this->load->library('Report');
	//procedemos a obtener el tipo de reporte
	//var_dump($obtenerTipo);
	$pdf = $this->report->getInstance('L','mm', 'Letter');
	$logo = getLogo();
	$pdf->SetMargins(6, 10);
	$pdf->SetLeftMargin(5);
	$pdf->AliasNbPages();
	$pdf->SetAutoPageBreak(true, 15);
	$pdf->AliasNbPages();
	$data = array("empresa" => "Jah","imagen" => $logo, 'fecha' =>"14-10-1998", 'titulo' => $encabezadoP." - Kardex");
	$pdf->setear($data);
	$pdf->addPage();
	$pdf->SetFont('Arial','B',10);

		$l = array(
			's' => 10,
			'con' =>250,
		);
		$array_data = array(
			array('',$l['s'],"C"),
			array($arrSucursal->nombre." ".$arrSucursal->direccion,$l['con'],"C"),
		);
		$pdf->LineWrite($array_data);
		$pdf->LN(5);

		//procedemos a generar el kardex del producto
		$kardex = $this->reportes->get_kardex($id, $color, $sucursal, $fechaI, $fechaF);
		//var_dump($kardex);
		$pdf->SetFont('Arial','B',7);
		if ($kardex==0) {
			$l = array(
				's' => 10,
				'fec' =>30,
				'pro' =>55,
				'tip' =>30,
				'ent' => 45,
				'sal' => 45,
				'exi' => 45
			);
			$array_data = array(
			array('',$l['s'],"C"),
			array('',$l['fec'],"R"),
			array('sin resultados...',$l['pro'],"L"),
			array('',$l['ent'],"R"),
			array('',$l['sal'],"R"),
			array('',$l['exi'],"R"),
			);
		}
		else {
			$l = array(
				's' => 10,
				'fec' =>30,
				'pro' =>15,
				'tip' =>25,
				'ent' => 60,
				'sal' => 60,
				'exi' => 60
			);
			$array_data = array(
			array('',$l['s'],"C"),
			array('FECHA',$l['fec'],"C"),
			array('DOC',$l['pro'],"C"),
			array('TIPO',$l['tip'],"C"),
			array('ENTRADAS',$l['ent'],"C"),
			array('SALIDAS',$l['sal'],"C"),
			array('EXISTENCIAS',$l['exi'],"C"),
			);
			$pdf->LineWriteB($array_data);
			$pdf->SetFont('Arial','',7);

			//$existencias
			//var_dump($kardex);
			$l = array(
				's' => 10,
				'fec' =>30,
				'pro' =>15,
				'tip' =>25,
				'can' => 20,
				'cos' => 20,
				'tot' => 20,
				'rel' => 60
			);
			$pdf->SetFont('Arial','B',6);
			$array_data = array(
			array('',$l['s'],"C"),
			array("",$l['fec'],"L"),
			array("",$l['pro'],"C"),
			array("",$l['tip'],"C"),
			array("UNIDAD",$l['can'],"C"),
			array("COSTO",$l['cos'],"C"),
			array("SUBTOTAL",$l['tot'],"C"),
			array("UNIDAD",$l['can'],"C"),
			array("COSTO",$l['cos'],"C"),
			array("SUBTOTAL",$l['tot'],"C"),
			array("UNIDAD",$l['can'],"C"),
			array("COSTO",$l['cos'],"C"),
			array("SUBTOTAL",$l['tot'],"C"),
			//array("",$l['rel'],"R"),
			);
			$pdf->LineWriteB($array_data);
			$existencias =0;
			$costoK=0;
			$subtotalK = 0;
			$pdf->SetFont('Arial','',7);
			foreach ($kardex as $arrKardex) {
				$l = array(
					's' => 10,
					'fec' =>30,
					'pro' =>15,
					'tip' =>25,
					'can' => 20,
					'cos' => 20,
					'tot' => 20,
					'rel' => 60
				);

					if ($arrKardex['movimiento']=="carga") {
						$existencias += $arrKardex['cantidad'];
						$subtotalK += $arrKardex['cantidad']*$arrKardex['costo'];
						$subtotalInd = $arrKardex['cantidad']*$arrKardex['costo'];

						//echo $$arrKardex['fechaEval'].">=".$fechaI."&&".$arrKardex['fechaEval']."<=".$fechaF;
						if ($arrKardex['fechaEval']>=Y_m_d($fechaI)&&$arrKardex['fechaEval']<=Y_m_d($fechaF)) {
							$array_data = array(
							array('',$l['s'],"C"),
							array($arrKardex['fecha'],$l['fec'],"L"),
							array($arrKardex['correlativo'],$l['pro'],"C"),
							array(strtoupper($arrKardex['movimiento']),$l['tip'],"C"),
							array($arrKardex['cantidad'],$l['can'],"R"),
							array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
							array("$".number_format($subtotalInd, 2),$l['tot'],"R"),
							array("",$l['rel'],"R"),
							array($existencias,$l['can'],"R"),
							array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
							array("$".number_format($subtotalK, 2),$l['tot'],"R"),
							//array("",$l['rel'],"R"),
							);
							$pdf->LineWriteB($array_data);
						}
					}
					else if ($arrKardex['movimiento']=="descarga") {
						$existencias -= $arrKardex['cantidad'];
						$subtotalK -= $arrKardex['cantidad']*$arrKardex['costo'];
						$subtotalInd = $arrKardex['cantidad']*$arrKardex['costo'];
						//echo $$arrKardex['fechaEval'].">=".$fechaI."&&".$arrKardex['fechaEval']."<=".$fechaF;
						if ($arrKardex['fechaEval']>=Y_m_d($fechaI)&&$arrKardex['fechaEval']<=Y_m_d($fechaF)) {
							$array_data = array(
							array('',$l['s'],"C"),
							array($arrKardex['fecha'],$l['fec'],"L"),
							array($arrKardex['correlativo'],$l['pro'],"C"),
							array(strtoupper($arrKardex['movimiento']),$l['tip'],"C"),
							array("",$l['rel'],"R"),
							array($arrKardex['cantidad'],$l['can'],"R"),
							array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
							array("$".number_format($subtotalInd, 2),$l['tot'],"R"),
							array($existencias,$l['can'],"R"),
							array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
							array("$".number_format($subtotalK, 2),$l['tot'],"R"),
							//array("",$l['rel'],"R"),
							);
							$pdf->LineWriteB($array_data);
						}
					}
					else if ($arrKardex['movimiento']=="ajuste") {
						if ($arrKardex['tipo']=="resta") {
							$existencias -= $arrKardex['cantidad'];
							$subtotalK -= $arrKardex['cantidad']*$arrKardex['costo'];
							$subtotalInd = $arrKardex['cantidad']*$arrKardex['costo'];
							//echo $$arrKardex['fechaEval'].">=".$fechaI."&&".$arrKardex['fechaEval']."<=".$fechaF;
							if ($arrKardex['fechaEval']>=Y_m_d($fechaI)&&$arrKardex['fechaEval']<=Y_m_d($fechaF)) {
								$array_data = array(
								array('',$l['s'],"C"),
								array($arrKardex['fecha'],$l['fec'],"L"),
								array($arrKardex['correlativo'],$l['pro'],"C"),
								array(strtoupper($arrKardex['movimiento']),$l['tip'],"C"),
								array("",$l['rel'],"R"),
								array($arrKardex['cantidad'],$l['can'],"R"),
								array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
								array("$".number_format($subtotalInd, 2),$l['tot'],"R"),
								array($arrKardex['cantidad'],$l['can'],"R"),
								array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
								array("$".number_format($subtotalInd, 2),$l['tot'],"R"),
								//array("",$l['rel'],"R"),
								);
								$pdf->LineWriteB($array_data);
							}
						}
						else {
							$existencias += $arrKardex['cantidad'];
							$subtotalK += $arrKardex['cantidad']*$arrKardex['costo'];
							$subtotalInd = $arrKardex['cantidad']*$arrKardex['costo'];
							//echo $$arrKardex['fechaEval'].">=".$fechaI."&&".$arrKardex['fechaEval']."<=".$fechaF;
							if ($arrKardex['fechaEval']>=Y_m_d($fechaI)&&$arrKardex['fechaEval']<=Y_m_d($fechaF)) {
								$array_data = array(
								array('',$l['s'],"C"),
								array($arrKardex['fecha'],$l['fec'],"L"),
								array($arrKardex['correlativo'],$l['pro'],"C"),
								array(strtoupper($arrKardex['movimiento']),$l['tip'],"C"),
								array($arrKardex['cantidad'],$l['can'],"R"),
								array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
								array("$".number_format($subtotalInd, 2),$l['tot'],"R"),
								array("",$l['rel'],"R"),
								array($arrKardex['cantidad'],$l['can'],"R"),
								array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
								array("$".number_format($subtotalInd, 2),$l['tot'],"R"),
								//array("",$l['rel'],"R"),
								);
								$pdf->LineWriteB($array_data);
							}
						}

					}
					else if ($arrKardex['movimiento']=="traslado") {
						if ($arrKardex['tipo']=="resta") {
							$existencias -= $arrKardex['cantidad'];
							$subtotalK -= $arrKardex['cantidad']*$arrKardex['costo'];
							$subtotalInd = $arrKardex['cantidad']*$arrKardex['costo'];
							//echo $$arrKardex['fechaEval'].">=".$fechaI."&&".$arrKardex['fechaEval']."<=".$fechaF;
							if ($arrKardex['fechaEval']>=Y_m_d($fechaI)&&$arrKardex['fechaEval']<=Y_m_d($fechaF)) {
								$array_data = array(
								array('',$l['s'],"C"),
								array($arrKardex['fecha'],$l['fec'],"L"),
								array($arrKardex['correlativo'],$l['pro'],"C"),
								array(strtoupper($arrKardex['movimiento']),$l['tip'],"C"),
								array("",$l['rel'],"R"),
								array($arrKardex['cantidad'],$l['can'],"R"),
								array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
								array("$".number_format($subtotalInd, 2),$l['tot'],"R"),
								array($existencias,$l['can'],"R"),
								array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
								array("$".number_format($subtotalK, 2),$l['tot'],"R"),
								//array("",$l['rel'],"R"),
								);
								$pdf->LineWriteB($array_data);
							}
						}
						else {
							$existencias += $arrKardex['cantidad'];
							$subtotalK += $arrKardex['cantidad']*$arrKardex['costo'];
							$subtotalInd = $arrKardex['cantidad']*$arrKardex['costo'];
							//echo $$arrKardex['fechaEval'].">=".$fechaI."&&".$arrKardex['fechaEval']."<=".$fechaF;
							if ($arrKardex['fechaEval']>=Y_m_d($fechaI)&&$arrKardex['fechaEval']<=Y_m_d($fechaF)) {
								$array_data = array(
								array('',$l['s'],"C"),
								array($arrKardex['fecha'],$l['fec'],"L"),
								array($arrKardex['correlativo'],$l['pro'],"C"),
								array(strtoupper($arrKardex['movimiento']),$l['tip'],"C"),
								array($arrKardex['cantidad'],$l['can'],"R"),
								array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
								array("$".number_format($subtotalInd, 2),$l['tot'],"R"),
								array("",$l['rel'],"R"),
								array($existencias,$l['can'],"R"),
								array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
								array("$".number_format($subtotalK, 2),$l['tot'],"R"),
								//array("",$l['rel'],"R"),
								);
								$pdf->LineWriteB($array_data);
							}
						}
					}
					else if ($arrKardex['movimiento']=="ventas") {
						if ($arrKardex['tipo']=="resta") {
							$existencias -= $arrKardex['cantidad'];
							$subtotalK -= $arrKardex['cantidad']*$arrKardex['costo'];
							$subtotalInd = $arrKardex['cantidad']*$arrKardex['costo'];
							//echo $$arrKardex['fechaEval'].">=".$fechaI."&&".$arrKardex['fechaEval']."<=".$fechaF;
							if ($arrKardex['fechaEval']>=Y_m_d($fechaI)&&$arrKardex['fechaEval']<=Y_m_d($fechaF)) {
								$array_data = array(
								array('',$l['s'],"C"),
								array($arrKardex['fecha'],$l['fec'],"L"),
								array($arrKardex['correlativo'],$l['pro'],"C"),
								array(strtoupper($arrKardex['movimiento']),$l['tip'],"C"),
								array("",$l['rel'],"R"),
								array($arrKardex['cantidad'],$l['can'],"R"),
								array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
								array("$".number_format($subtotalInd, 2),$l['tot'],"R"),
								array($existencias,$l['can'],"R"),
								array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
								array("$".number_format($subtotalK, 2),$l['tot'],"R"),
								//array("",$l['rel'],"R"),
								);
								$pdf->LineWriteB($array_data);
							}
						}
						else {
							$existencias += $arrKardex['cantidad'];
							$subtotalK += $arrKardex['cantidad']*$arrKardex['costo'];
							$subtotalInd = $arrKardex['cantidad']*$arrKardex['costo'];
							//echo $$arrKardex['fechaEval'].">=".$fechaI."&&".$arrKardex['fechaEval']."<=".$fechaF;
							if ($arrKardex['fechaEval']>=Y_m_d($fechaI)&&$arrKardex['fechaEval']<=Y_m_d($fechaF)) {
								$array_data = array(
								array('',$l['s'],"C"),
								array($arrKardex['fecha'],$l['fec'],"L"),
								array($arrKardex['correlativo'],$l['pro'],"C"),
								array(strtoupper($arrKardex['movimiento']),$l['tip'],"C"),
								array($arrKardex['cantidad'],$l['can'],"R"),
								array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
								array("$".number_format($subtotalInd, 2),$l['tot'],"R"),
								array("",$l['rel'],"R"),
								array($existencias,$l['can'],"R"),
								array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
								array("$".number_format($subtotalK, 2),$l['tot'],"R"),
								//array("",$l['rel'],"R"),
								);
								$pdf->LineWriteB($array_data);
							}
						}
					}
					else if ($arrKardex['movimiento']=="devoluciones") {
						$existencias += $arrKardex['cantidad'];
						$subtotalK += $arrKardex['cantidad']*$arrKardex['costo'];
						$subtotalInd = $arrKardex['cantidad']*$arrKardex['costo'];
						//echo $$arrKardex['fechaEval'].">=".$fechaI."&&".$arrKardex['fechaEval']."<=".$fechaF;
						if ($arrKardex['fechaEval']>=Y_m_d($fechaI)&&$arrKardex['fechaEval']<=Y_m_d($fechaF)) {
							$array_data = array(
							array('',$l['s'],"C"),
							array($arrKardex['fecha'],$l['fec'],"L"),
							array($arrKardex['correlativo'],$l['pro'],"C"),
							array(strtoupper($arrKardex['movimiento']),$l['tip'],"C"),
							array($arrKardex['cantidad'],$l['can'],"R"),
							array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
							array("$".number_format($subtotalInd, 2),$l['tot'],"R"),
							array("",$l['rel'],"R"),
							array($existencias,$l['can'],"R"),
							array("$".number_format($arrKardex['costo'], 2),$l['cos'],"R"),
							array("$".number_format($subtotalK, 2),$l['tot'],"R"),
							//array("",$l['rel'],"R"),
							);
							$pdf->LineWriteB($array_data);
						}
					}
			}
		}
	}
	$pdf->Output();
	//echo $id."#";
	}
}
/* End of file Productos.php */
