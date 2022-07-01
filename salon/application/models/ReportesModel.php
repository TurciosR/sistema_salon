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

class ReportesModel extends CI_Model {

	function get_categorias(){
		$this->db->where("desactivado", "0");
		$row = $this->db->get("categoria");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return 0;
		}
	}
	function get_tipo_reporte($id){
		$this->db->where("id_reporte", $id);
		$row = $this->db->get("reportes");
		if ($row->num_rows() > 0) {
			return $row->row();
		} else {
			return 0;
		}
	}
  function get_totales($fechaI, $fechaF, $sucursal){
    //echo $fechaI;
    $this->db->select("SUM(vd.subtotal) as total, SUM(vd.descuento) as descuento, SUM(costo) as costo");
    $this->db->where("ventas.fecha BETWEEN '".$fechaI."' AND '".$fechaF."'");
    $this->db->where("ventas.id_estado", "2");
		$this->db->where("ventas.id_sucursal", $sucursal);
    $this->db->join("ventas", "ventas.id_venta = vd.id_venta");
    $row = $this->db->get("ventas_detalle as vd");
    if ($row->num_rows() > 0) {
      return $row->row();
    } else {
      return 0;
    }
  }
  function get_ventas_rango($fechaI, $fechaF, $sucursal){
    //echo $fechaI;
		/*
    $this->db->select("v.*, sucursales.nombre, sucursales.direccion");
    $this->db->where("v.fecha BETWEEN '".$fechaI."' AND '".$fechaF."'");
    $this->db->where("v.id_estado", "2");
    $this->db->join("sucursales", "sucursales.id_sucursal=v.id_sucursal");
    $row = $this->db->get("ventas as v");
    if ($row->num_rows() > 0) {
      return $row->result();
    } else {
      return 0;
    }
		*/
		$arrProductos =[];
		$sql = $this->db->query("SELECT vd.*, p.nombre,  c.color FROM ventas_detalle as vd
											LEFT JOIN ventas as v ON v.id_venta = vd.id_venta
											LEFT JOIN producto as p ON p.id_producto = vd.id_producto
											LEFT JOIN producto_color as c ON c.id_color = vd.id_color
											WHERE v.id_estado = 2 AND v.fecha BETWEEN '".$fechaI."' AND '".$fechaF."' AND v.id_sucursal='$sucursal'
					  				  GROUP BY vd.id_producto, vd.id_color, vd.condicion ORDER BY SUM(subtotal)");
		if ($sql->num_rows()>0) {
			// code...
			foreach ($sql->result() as $row) {
				// code...
				//echo $row->id_producto." - ".$row->id_color." - ".$row->condicion." - ".$row->contar_producto." # ";
				$this->db->select("SUM(costo) as costo_p, SUM(subtotal) as subtotal_p, SUM(cantidad) as cantidad_p");
				$this->db->where("v.fecha BETWEEN '".$fechaI."' AND '".$fechaF."'");
				$this->db->where("v.id_estado", "2");
				$this->db->where("v.id_sucursal", $sucursal);
				$this->db->where("vd.id_producto", $row->id_producto);
				$this->db->where("vd.id_color", $row->id_color);
				$this->db->where("vd.condicion", $row->condicion);
				$this->db->order_by("SUM(subtotal)", "ASC");
				$this->db->join("ventas as v", "v.id_venta = vd.id_venta");
				$arr = $this->db->get("ventas_detalle as vd");
				if ($arr->num_rows() > 0) {
					$arregloD = $arr->row();
					//echo $arregloD->costo_p."#".$arregloD->subtotal_p."#";
					$arrProductos[] = array("nombre"=>$row->nombre." ".$row->color, "cantidad"=>$arregloD->cantidad_p, "color"=>$row->color, "condicion"=>$row->condicion, "costo"=>$arregloD->costo_p, "subtotal"=>$arregloD->subtotal_p);
				} else {
					//return 0;
				}
			}
			return $arrProductos;
		}
		else {
			// code...
			return 0;
		}
		//var_dump($arrProductos);
		/*
    if ($row->num_rows() > 0) {
      return $row->result();
    } else {
      return 0;
    }
		*/
  }

	function get_reportes(){
		$this->db->select("id_reporte, nombre, parametro");
		$this->db->from("reportes");
		$this->db->where("visible", "1");
		$row = $this->db->get();
		if ($row->num_rows() > 0) {
      return $row->result();
		} else {
			return 0;
		}
	}
	function get_row_sucursal($id){
		$this->db->where("id_sucursal", $id);
		$row = $this->db->get("sucursales");
		if ($row->num_rows() > 0) {
			return $row->row();
		} else {
			return 0;
		}
	}
	function get_kardex($id, $color, $sucursal, $fechaI, $fechaF){
		$fechaI = Y_m_d($fechaI);
		$fechaF = Y_m_d($fechaF);
		$sql = $this->db->query("(SELECT p.id_producto, p.nombre, c.color,
		c.id_color, 0 as stock_anterior, cantidad, 'carga' as movimiento, id_sucursal,
		'suma' as tipo, icd.costo, icd.subtotal, ic.fecha, ic.hora, ic.correlativo
		FROM `inventario_carga_detalle` as icd
		INNER JOIN inventario_carga as ic ON ic.id_carga = icd.id_carga
		INNER JOIN producto as p ON p.id_producto = icd.id_producto
		INNER JOIN producto_color as c ON c.id_color = icd.id_color
		WHERE p.id_producto=$id AND c.id_color=$color AND ic.id_sucursal=$sucursal
		AND ic.fecha <= '$fechaF'
		ORDER BY ic.fecha ASC)
		UNION
		(SELECT p.id_producto, p.nombre, c.color, c.id_color, 0 as stock_anterior,
		cantidad, 'descarga' as movimiento, id_sucursal, 'suma' as tipo, idd.costo, idd.subtotal ,
		id.fecha, id.hora, id.correlativo
		FROM `inventario_descarga_detalle`  as idd
		INNER JOIN inventario_descarga as id ON id.id_descarga = idd.id_descarga
		INNER JOIN producto as p ON p.id_producto = idd.id_producto
		INNER JOIN producto_color as c ON c.id_color = idd.id_color
		WHERE p.id_producto=$id AND c.id_color=$color AND id.id_sucursal=$sucursal
		AND id.fecha <= '$fechaF'
		ORDER BY id.fecha ASC)
		UNION
		(SELECT p.id_producto, p.nombre, c.color, c.id_color, stock_anterior, cantidad, 'ajuste' as movimiento, id_sucursal,
		IF((stock_anterior-cantidad)<0, 'suma', 'resta') as tipo, iad.costo, iad.subtotal,
		ia.fecha, ia.hora, ia.correlativo
		FROM `inventario_ajuste_detalle` as iad
		INNER JOIN inventario_ajuste as ia ON ia.id_ajuste = iad.id_ajuste
		INNER JOIN producto as p ON p.id_producto = iad.id_producto
		INNER JOIN producto_color as c ON c.id_color = iad.id_color
		WHERE p.id_producto=$id AND c.id_color=$color AND ia.id_sucursal=$sucursal
		AND ia.fecha <= '$fechaF'
		ORDER BY ia.fecha ASC)
		UNION
		(SELECT p.id_producto, p.nombre, c.color, c.id_color, 0 as stock_anterior,
		cantidad, 'traslado' as movimiento, id_sucursal_destino as id_sucursal, IF(t.id_sucursal_despacho=$sucursal,
		'resta', 'suma') as tipo, td.costo, td.subtotal, t.fecha, t.hora, t.correlativo
		FROM `traslado_detalle` as td
		INNER JOIN traslado as t ON t.id_traslado = td.id_traslado
		INNER JOIN producto as p ON p.id_producto = td.id_producto
		INNER JOIN producto_color as c ON c.id_color = td.id_color
		WHERE p.id_producto=$id AND c.id_color=$color AND (t.id_sucursal_despacho=$sucursal OR t.id_sucursal_destino=$sucursal)
		AND t.fecha <= '$fechaF' AND t.estado=1
		ORDER BY t.fecha ASC)
		UNION
		(SELECT p.id_producto, p.nombre,  c.color, c.id_color, 0 as stock_anterior,
		cantidad, 'ventas' as movimiento, v.id_sucursal, 'resta' as tipo, vd.costo, vd.subtotal, v.fecha, v.hora, v.correlativo
		FROM `ventas_detalle` as vd
		INNER JOIN ventas as v ON v.id_venta = vd.id_venta
		INNER JOIN producto as p ON p.id_producto = vd.id_producto
		INNER JOIN producto_color as c ON c.id_color = vd.id_color
		WHERE p.id_producto=$id AND c.id_color=$color AND v.id_sucursal=$sucursal AND (v.tipo_doc=1 OR v.tipo_doc=0)
		AND v.fecha <= '$fechaF'
		ORDER BY v.fecha ASC)
		UNION
		(SELECT p.id_producto, p.nombre,  c.color, c.id_color, 0 as stock_anterior,
		cantidad, 'devoluciones' as movimiento, v.id_sucursal, 'resta' as tipo, dd.monto, 0 as subtotal, d.fecha, d.hora, '-' as correlativo
		FROM `devoluciones_det` as dd
		INNER JOIN devoluciones as d ON d.id_dev = dd.id_dev
		INNER JOIN ventas_detalle as vd ON vd.id_detalle = dd.id_venta_detalle
		INNER JOIN ventas as v ON v.id_venta = dd.id_venta
		INNER JOIN producto as p ON p.id_producto = vd.id_producto
		INNER JOIN producto_color as c ON c.id_color = vd.id_color
		WHERE p.id_producto=$id AND c.id_color=$color AND v.id_sucursal=$sucursal
		AND d.fecha <= '$fechaF'
		ORDER BY d.fecha ASC)
		");
		if ($sql->num_rows()>0) {
			// code...
			//return $sql->result();
			//procedemos a definir un arreglo donde se llenaran todos los datos
			$arregloDatos = [];
			foreach ($sql->result() as $arrSql) {
				// code...
				$arregloDatos[] = array("id_producto"=>$arrSql->id_producto, "nombre"=>$arrSql->nombre,
				 "color"=>$arrSql->color,"id_color"=>$arrSql->id_color, "stock_anterior"=>$arrSql->stock_anterior,
				 "cantidad"=>$arrSql->cantidad, "movimiento"=>$arrSql->movimiento,
				 "id_sucursal"=>$arrSql->id_sucursal,  "tipo"=>$arrSql->tipo, "costo"=>$arrSql->costo,
				 "subtotal"=>$arrSql->subtotal, "fecha"=>$arrSql->fecha." ".$arrSql->hora, "fechaEval"=>$arrSql->fecha, "correlativo"=>$arrSql->correlativo);
			}
			//var_dump($arregloDatos);
			//print_r($arregloDatos);
			usort($arregloDatos, function ($a, $b) {
			    return strcmp($a["fecha"], $b["fecha"]);
			});
			//echo "Arreglo DESPUÉS de ordenar: ";
			//print_r($arregloDatos);
			return $arregloDatos;
		}
		else {
			// code...
			return 0;
		}
	}
	function get_existencias($sucursal){
		$this->db->select("p.codigo_barra, CONCAT(p.nombre, ' ', pc.color) as cadena, s.cantidad");
		$this->db->from("stock as s");
		$this->db->join("producto as p", "p.id_producto=s.id_producto");
		$this->db->join("producto_color as pc", " pc.id_color=s.id_color");
		$this->db->where("cantidad>", "0");
		$this->db->where("id_sucursal", $sucursal);
		$row = $this->db->get();
		if ($row->num_rows() > 0) {
      return $row->result();
		} else {
			return 0;
		}
	}

	/**
	 * Get kardex info
	 *
	 * Obtains the information of the movements of a product
	 *
	 * @param string $fecha_inicial
	 * @param string $fecha_final
	 * @param int $id_sucursal
	 * @param int $id_producto
	 * @param int $id_color
	 *
	 * @return void
	 */
	function obtener_kardex($fecha_inicial, $fecha_final, $id_sucursal, $id_producto, $id_color){

		// format date
		$fecha_inicial 	= Y_m_d($fecha_inicial);
		$fecha_final 	= Y_m_d($fecha_final);
		return $this->db->select("m.id_movimiento, m.fecha, m.numero_documento, m.correlativo, m.proceso, m.tipo, md.cantidad, md.costo, md.stock_anterior, md.stock_actual")
		->join("movimiento_producto AS m", "m.id_movimiento = md.id_movimiento")
		->where("m.fecha BETWEEN '$fecha_inicial' AND '$fecha_final'", null, false)
		->where(['id_sucursal' => $id_sucursal, 'id_producto' => $id_producto, 'id_color' => $id_color])
		->get("movimiento_producto_detalle AS md")->result_array();

	}
}


/* End of file ReporteModel.php */
