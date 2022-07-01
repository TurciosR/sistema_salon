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

class Movimiento_producto_model extends CI_Model {

    /**
     * Register the header of a product movement
     * 
     * receives an array with the data from the product move header, 
     * structured as follows.
	 * 
	 * The type of movement should only be 'ENTRADA' OR 'SALIDA'
     * 
     *  $data = [
     *      'tipo'			=> 'ENTRADA',
     *      'proceso'		=> 'CARGA DE INVENTARIO',
     *      'num_doc'		=> 'F123',
     *      'correlativo'	=> '123',
	 * 		'total'			=> 20,
	 * 		'id_despacho'	=> 1,
	 * 		'id_destino'	=> 2,
	 * 		'id_proceso'	=> 23,
	 * 		'concepto'		=> 'por carga de inventario'
     *  ]
     * 
	 * @param array $data 
	 * 
	 * @return int $id_movimiento
     */
    public function insertar_movimiento_producto($data){

		// define time zone
		date_default_timezone_set('America/El_Salvador');

		$insert = [
			'fecha' 				=> date('Y-m-d'),
			'tipo'					=> $data['tipo'],
			'proceso'				=> $data['proceso'],
			'numero_documento'		=> $data['num_doc'],
			'correlativo'			=> $data['correlativo'],
			'hora'					=> date('H:i:s'),
			'total'					=> $data['total'],
			'id_sucursal_despacho'	=> $data['id_despacho'],
			'id_sucursal_destino'	=> $data['id_destino'],
			'id_proceso'			=> $data['id_proceso'],
			'id_usuario'			=> $this->session->id_usuario,
			'concepto'				=> $data['concepto']
		];

		$this->db->insert('movimiento_producto', $insert);
		return $this->db->insert_id();
    }

	/**
	 * Register the detail of a product movement
     * 
     * receives an array with the data from the product move detail, 
     * structured as follows.
	 * 
	 * 	$data = [
	 * 		'id_movimiento' => 1,
	 * 		'id_producto' 	=> 5,
	 * 		'id_color'		=> 4,
	 * 		'costo'			=> 2,
	 * 		'precio'		=> 10,
	 * 		'cantidad'		=> 20,
	 * 	]
	 * 
	 */
	public function insertar_movimiento_detalle($data){

		$header = $this->obtener_tipo_movimiento($data['id_movimiento']);
		$tipo = $header->tipo;

		$stock_anterior = $this->obtener_stock_anterior(
			$data['id_producto'], 
			$data['id_color'],
			$data['id_movimiento'],
			$tipo
		);

		$stock_actual = 0;

		if ($tipo == "ENTRADA") {
			$stock_actual = $stock_anterior + $data['cantidad'];
		}else{
			$stock_actual = $stock_anterior - $data['cantidad'];
		}

		$insert = [
			'id_movimiento' => $data['id_movimiento'],
			'id_sucursal'	=> $tipo == "ENTRADA"
				? $header->id_sucursal_destino
				: $header->id_sucursal_despacho,
			'id_producto'	=> $data['id_producto'],
			'id_color'		=> $data['id_color'],
			'costo'			=> $data['costo'],
			'precio'		=> $data['precio'],
			'cantidad'		=> $data['cantidad'],
			'stock_anterior'=> $stock_anterior,
			'stock_actual' 	=> $stock_actual
		];

		$this->db->insert("movimiento_producto_detalle", $insert);

	}

	/**
	 * Get the previous stock of a product
	 * 
	 * @param int $id_producto
	 * @param int $id_color
	 * 
	 * @return int $stock
	 */
	private function obtener_stock_anterior($id_producto, $id_color, $id_movimiento, $tipo){

		// Obtenemos la los id de las sucursales afectadas en el movimiento
		$sucursales = $this->db->select("id_sucursal_despacho, id_sucursal_destino")
		->where("id_movimiento", $id_movimiento)->get("movimiento_producto")->row();

		if ($tipo == "ENTRADA") {
			$id_sucursal = $sucursales->id_sucursal_destino;
		}else{
			$id_sucursal = $sucursales->id_sucursal_despacho;
		}

		// Obtenemos el stock actual
		$this->db->select('stock_actual')
		->from("movimiento_producto_detalle AS mpd")
		->where([
			'mpd.id_sucursal' => $id_sucursal,
			'mpd.id_producto' => $id_producto,
			'mpd.id_color' => $id_color]
		)
		->order_by("mpd.id_detalle", "DESC");


		$resultado = $this->db->get()->row();

		if ($resultado) {
			return $resultado->stock_actual;
		}

		return 0;

	}

	/**
	 * Get the type of movement
	 * 
	 * @param int $id_movimiento
	 * 
	 * @return object $tipo
	 */
	private function obtener_tipo_movimiento($id_movimiento){

		$resultado = $this->db->select("tipo, id_sucursal_despacho, id_sucursal_destino")->from("movimiento_producto")
		->where("id_movimiento", $id_movimiento)->get()->row();

		if ($resultado) {
			return $resultado;
		}

		return '';
	}
}


/* End of file Movimiento_producto_model.php and path /application/models/movimiento_producto_model.php */

