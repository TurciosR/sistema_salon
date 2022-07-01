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

class Stock extends CI_Controller {
	/*
	Global table name
	*/
	private $table = "stock";
	private $pk = "id_stock";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->model("ProductosModel","productos");
		$this->load->helper("upload_file");
		$this->load->model("VentasModel", "ventas");
	}

	public function index()
	{

		$this->load->model("InventarioModel","inventario");
		$data = array(
			"titulo"=> "Stock",
			"icono"=> "mdi mdi-archive",
			"buttons" => array(
				0 => array(
					"icon"=> "mdi mdi-plus",
					'url' => 'productos',
					'txt' => ' Productos',
					'modal' => false,
				),
			),
			"selects" => array(
				0 => array(
					"name" => "sucursales",
					"data" => $this->inventario->get_detail_rows("sucursales",array('1' => 1, )),
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
				"Barcode"      =>10,
				"Categoría"    =>20,
				"Descripción"  =>20,			
				"Color"        =>10,
				"Stock"        =>10,
				"Disponible"   =>10,
				"Reservado"    =>10,
				"Detalles"     =>10,
			),
		);
		$extras = array(
			'css' => array(
			),
			'js' => array(
				"js/scripts/stock.js"
			),
		);
		layout("template/admin",$data,$extras);
	}

	function get_data_stock()
	{
		$id_sucursal = $this->input->post("id_sucursal");
		$valid_columns = array(
			0 => 'producto.id_producto',
			1 => 'producto.nombre',
			2 => 'producto.codigo_barra',
			3 => 'categoria.nombre',

		);
		// Create query based on mariadb tables required
		$query_val  = $this->productos->create_dt_query_stock();
		$where  = array(
			'stock.id_sucursal' => $this->input->post("id_sucursal"),
		);
		/* You can pass where and join clauses as necessary or include it on model
		 * function as necessary. If no join includ it set to NULL.
		 */
		$options_dt = array(
				'valid_columns' => $valid_columns,
				'where'         => $where
		);
		$options_dt = array_merge($query_val, $options_dt);
		$row        = generate_dt("UtilsModel", $options_dt, FALSE);
		$draw       = intval($this->input->post("draw"));
		if ($row != 0) {
			$data = array();
			foreach ($row as $rows) {

				$state = $rows->activo;
				if($state==1){
					$show_text = "<span class='badge badge-success font-bold'>Activo<span>";
				}
				else{
					$show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
				}

				$menudrop = "<div class='btn-group'>
				<button data-toggle='dropdown' class='btn btn-success dropdown-toggle' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menú</button>
				<ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";
				$menudrop .= "<li><a  data-toggle='modal' data-target='#viewModal' data-refresh='true'  role='button' class='detail' data-id=".$rows->id_stock."><i class='mdi mdi-eye-check' ></i> Detalles</a></li>";

				$menudrop .= "</ul></div>";

				$this->db->select("producto_color.color");
				$this->db->from("stock");
				$this->db->join("producto_color","producto_color.id_color=stock.id_color","left");
				$this->db->where("id_stock",$rows->id_stock);
				$query = $this->db->get();

				$reservado = $this->ventas->get_reserved_stock($id_sucursal, $rows->id_producto, $rows->id_color);

				$dato = $query->row();

				$data[] = array(
					$rows->codigo_barra,
					$rows->categoria,
					$rows->nombre,
					$dato->color,
					$rows->stock,
					$rows->stock - $reservado,
					$reservado,
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
				"",
				"No se encontraron registros",
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

	function detalle($id=-1){
		$this->load->model("InventarioModel","inventario");
			$id = $this->uri->segment(3);


		if($this->input->method(TRUE) == "GET"){


			$sd = $this->inventario->get_one_row("stock",array('id_stock' => $id,));
			if($id!="" && $sd){
				$id_producto=$sd->id_producto;
				$row = $this->productos->get_row_info($id_producto);
				if($row){
						$reservado			 = $this->ventas->get_reserved_stock($sd->id_sucursal, $id_producto, $sd->id_color);
						$categorias       = $this->productos->get_categorias();
						$precios          = $this->productos->get_precios_exis($id_producto);
						$colores          = $this->productos->get_colores_exis($id_producto);
						$config_impuestos = $this->utils->get_one_row("configuracion",array('1' => 1));
					$imagenes 				= $this->utils->get_detail_rows("producto_imagen",array('id_producto' => $id_producto));
						$stock=$sd->cantidad-$reservado;
						$data_b=0;
						$data = array(
							"exis_p"  => $data_b,
							"precios" => $precios,
							"row"     => $row,
							"categorias"       => $categorias,
							"colores"          => $colores,
							"config_impuestos" => $config_impuestos,
							"imagen"					 => $imagenes,
							"stock"							=> $sd,
							"reservado"	=> $reservado,
						);
						$this->load->view("productos/ver_detalle.php",$data);
			 }
			}else{
				redirect('errorpage');
			}

		}
	}

}

/* End of file Productos.php */
