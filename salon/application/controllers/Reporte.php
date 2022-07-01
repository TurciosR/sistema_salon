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

class Reporte extends CI_Controller {
	/*
	Global table name
	*/
	private $table = "stock";
	private $pk = "id_stock";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->model("InventarioModel","inventario");
		$this->load->helper("upload_file");
	}
	public function Resumen()
	{
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$this->load->library('ReporteResumen');
			$pdf = $this->reporteresumen->getInstance('L','mm', 'Letter');
			$logo = base_url().getLogo();
			$pdf->SetMargins(10, 10);
			$pdf->SetLeftMargin(4);
			$pdf->AliasNbPages();
			$pdf->SetAutoPageBreak(true, 15);
			$pdf->AliasNbPages();
			$pdf->AddFont("latin","","latin.php");
			$pdf->SetFont('Latin','',10);
			$data = array("empresa" => "raul","imagen" => $logo);
			$pdf->setear($data);

			$pdf->addPage();

			$this->db->select("clientes.nombre as name,ventas.fecha,clientes.direccion, producto.nombre,producto.marca,producto.modelo,ventas_detalle.precio,ventas.guia");
			$this->db->from("ventas_detalle");
			$this->db->join("ventas","ventas.id_venta = ventas_detalle.id_venta");
			$this->db->join("clientes","clientes.id_cliente = ventas.id_cliente","left");
			$this->db->join("producto","producto.id_producto = ventas_detalle.id_producto");
			$this->db->where('ventas.id_estado ', 3);
			$this->db->where('ventas.id_sucursal', $id);
			$query = $this->db->get();
			if ($query->num_rows() > 0) {
				$data = $query->result();

				foreach ($data as $key) {
					/*dato, tamaño , aliniacion*/
					$array_data = array(
						array($key->name,64,"L"),
						array(d_m_Y($key->fecha),30,"C"),
						array($key->direccion,64,"L"),
						array($key->nombre,30,"L"),
						array($key->marca,20,"L"),
						array($key->modelo,21,"L"),
						array(number_format($key->precio,2),21,"R"),
						array($key->guia,21,"L"),
					);
					$pdf->LineWrite($array_data);
				}
			}

			$pdf -> Ln(10);
			$pdf->Cell(18,5,"Nombre:",0,0,'L',0);
			$pdf->Cell(115,5,"","B",1,'L',0);
			$pdf -> Ln(10);
			$pdf->Cell(18,5,"Fecha:",0,0,'L',0);
			$pdf->Cell(115,5,"","B",1,'L',0);
			$pdf -> Ln(10);
			$pdf->Cell(18,5,"Firma:",0,0,'L',0);
			$pdf->Cell(115,5,"","B",1,'L',0);
			$pdf -> Ln(10);
			$pdf->Cell(55,5,"Cantidad de Paquete Recibidos:",0,0,'L',0);
			$pdf->Cell(78,5,"","B",1,'L',0);

			$pdf->Output();
		} else {
			redirect('errorpage');
		}
	}
}
/* end of file Reporte.php */
