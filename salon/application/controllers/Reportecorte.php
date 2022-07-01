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

class Reportecorte extends CI_Controller {
	/*
	Global table name
	*/
	private $table = "stock";
	private $pk = "id_stock";
	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->model("VentasModel","ventas");
		$this->load->model("Clientes_model", "clientes");
		$this->load->library('user_agent');
		$this->load->model("InventarioModel","inventario");
	//	$this->load->helper('print_helper');
	}

	function ticketauditoria(){

		if($this->input->method(TRUE) == "GET"){
			$data = array(
				"sucursal"=>$this->inventario->get_detail_rows("sucursales",array('1' => 1, )),
				"id_sucursal" => $this->session->id_sucursal,
			);
			$extras = array(
				'css' => array(
				),
				'js' => array(

				),
			);
			layout("reports/generar_ticket",$data,$extras);
		}
	}

	function tikets()
	{
		if($this->input->method(TRUE) == "GET"){
			$this->load->library('Reportez');
			$pdf = $this->reportez->getInstance('P','mm');
			$pdf->SetMargins(10,5);
			$pdf->SetTopMargin(2);
			$pdf->SetLeftMargin(2);
			$pdf->AliasNbPages();
			$pdf->SetAutoPageBreak(false);
			$pdf->AddFont("courier new","","courier.php");
			$pdf->SetFont('courier new','',8);

			$id_sucursal=$id = $this->uri->segment(3);
			$fini= Y_m_d($this->uri->segment(4));
			$ffin= Y_m_d($this->uri->segment(5));
			//obtenemos las ventas porfecha
			/*$this->db->where("fecha>=",$fini);
			$this->db->where("fecha<=",$ffin);
			$e = array('2','3' );
			$this->db->where_in("id_estado",$e);
			$this->db->order_by('fecha ASC, correlativo ASC');
			$this->db->where("id_sucursal","$id_sucursal");
			$detail = $this->db->get("ventas");*/

			$detail = $this->db->query("SELECT ventas.*, CAST(correlativo as int) as cr FROM ventas WHERE id_sucursal=$id_sucursal AND id_estado IN(2,3) AND tipo_doc=1  AND fecha BETWEEN '$fini' AND '$ffin' ORDER BY fecha ASC, cr ASC");
			if ($detail->num_rows() > 0) {
				$detalles = $detail->result();
				foreach ($detalles as $detv)
				{
					$rowvta = $this->ventas->get_one_row("ventas", array('id_venta' =>$detv->id_venta,));

					$total_gravado=0;
					$total_exento=0;
					$total_pago=0;
					$row_hf=$this->ventas->get_one_row("config_pos", array('id_sucursal' => $id_sucursal,'alias_tipodoc'=>'TIK',));
					$row_user=$this->ventas->get_one_row("usuario", array('id_usuario' => $detv->id_usuario,));
					$nm=0;
					$headers=array();
					if($row_hf->header1!=''){
						$headers[] = $row_hf->header1;
					}
					if($row_hf->header2!=''){
						$headers[] = $row_hf->header2;
					}
					if($row_hf->header3!=''){
						$headers[] = $row_hf->header3;
					}
					if($row_hf->header4!=''){
						$headers[] = $row_hf->header4;
					}
					if($row_hf->header5!=''){
						$headers[] = $row_hf->header5;
					}
					if($row_hf->header6!=''){
						$headers[] = $row_hf->header6;
					}
					if($row_hf->header7!=''){
						$headers[] = $row_hf->header7;
					}
					if($row_hf->header8!=''){
						$headers[] = $row_hf->header8;
					}
					if($row_hf->header9!=''){
						$headers[] = $row_hf->header9;
					}
					if($row_hf->header10!=''){
						$headers[] = $row_hf->header10;
					}

					$l = array(
						's' =>25,
						'c' => 11,
						'v' => 20,
						'z' => 20,
					);

					$detalleproductos = $this->ventas->get_detail_ci($detv->id_venta);
					$detalleservicios = $this->ventas->get_detail_serv($detv->id_venta);

					if($detalleproductos)
					{
						foreach ($detalleproductos as $detalle)
						{
							$descripcion=$detalle->nombre." ".$detalle->marca." ".$detalle->modelo." ".$detalle->color;
							$array_data = array(
								array($descripcion,$l['s'],"L"),
							);
							$a = $pdf->LineWriteC($array_data);
							$nm = $nm + ($a*5);
						}
					}
					if($detalleservicios)
					{
						foreach ($detalleservicios as $detalle)
						{
							$descripcion=$detalle->nombre;
							$array_data = array(
								array($descripcion,$l['s'],"L"),
							);
							$a = $pdf->LineWriteC($array_data);
							$nm = $nm + ($a*5);
						}
					}

					$pdf->AddPage('P', array(80, 80+$nm));

					foreach($headers as $value)
					{
						if(trim($value)!="")
						{
							$pdf->Cell(76,4,utf8_decode($value),0,1,'C');
						}
					}

					$pdf->Ln(4);
					$pdf->Cell(76,4,utf8_decode("FECHA: ".	d_m_Y($rowvta->fecha)." HORA:".$rowvta->hora),0,1,'C');
					$pdf->Cell(76,4,utf8_decode("CAJA #: ".$rowvta->caja),0,1,'C');
					$pdf->Cell(76,4,utf8_decode("CAJERO: ".$row_user->nombre),0,1,'C');
					$tiq=str_pad($rowvta->correlativo, 10, '0', STR_PAD_LEFT);
					$pdf->Cell(76,4,utf8_decode("TICKET #: ".$tiq),0,1,'C');


					$array_data = array(
						array('DESCRIPCION',$l['s'],"L"),
						array("CANT",$l['c'],"L"),
						array("P.U",$l['v'],"L"),
						array("SUBTOTAL",$l['z'],"L"),
					);
					$pdf->LineWrite($array_data);
					$pdf->Line(1, $pdf->GetY(), 79, $pdf->GetY());

					if ($detalleproductos)
					{
						foreach ($detalleproductos as $detalle)
						{
							$id_producto = $detalle->id_producto;
							$descripcion=$detalle->nombre." ".$detalle->marca." ".$detalle->modelo." ".$detalle->color;
							$precio_fin ="$ ". $detalle->precio_fin;
							$cantidad = $detalle->cantidad;
							$subtotal = "$ ".$detalle->subtotal;

							$array_data = array(
								array($descripcion,$l['s'],"L"),
								array($cantidad,$l['c'],"R"),
								array($precio_fin,$l['v'],"R"),
								array($subtotal,$l['z'],"R"),
							);
							$pdf->LineWrite($array_data);
						}
					}

					if($detalleservicios)
					{
						foreach ($detalleservicios as $detalle)
						{
							$id_producto = $detalle->id_producto;
							$descripcion=$detalle->nombre;
							$precio_fin ="$ ".$detalle->precio_fin;
							$cantidad = $detalle->cantidad;
							$subtotal = "$ ".$detalle->subtotal;

							$array_data = array(
								array($descripcion,$l['s'],"L"),
								array($cantidad,$l['c'],"R"),
								array($precio_fin,$l['v'],"R"),
								array($subtotal,$l['z'],"R"),
							);
							$pdf->LineWrite($array_data);
						}
					}
					$pdf->Line(1, $pdf->GetY(), 79, $pdf->GetY());
					$pdf->Cell(40,4,utf8_decode("TOTAL"),0,0,'L');
					$pdf->Cell(36,4,utf8_decode("$    ".number_format((round($rowvta->total,2)),2)),0,1,'R');


				}
			} else {
				$pdf->AddPage('P', array(80, 80));
				$pdf->Cell(40,5,utf8_decode("NO HAY DATOS"),0,0,'L');
			}

			$pdf->Output();
		}

	}

}
 ?>
