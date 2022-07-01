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

/**
 * Productos Controller
 *
 * Display the Products Module
 *
 * @package		OpenPyme2
 * @subpackage	Controllers
 * @category	Controllers
 * @author		OpenPyme Dev Team
 * @link		sftp://docs.apps-oss.com/classes/Productos.html
 */
class Productos extends CI_Controller {
	/*
	Global table name
	*/
	private $table = "producto";
	private $pk    = "id_producto";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->model("ProductosModel","productos");
		$this->load->model("ColoresModel","colores");
		$this->load->model("InventarioModel","inventario");
		$this->load->helper("upload_file");
		$this->load->model("VentasModel", "ventas");
	}

	/**
	 * Displays the main page (admin) of the Products module
	 *
	 * @return void
	 */
	public function index()
	{
		$data = array(
			"titulo"=> "Productos",
			"icono"=> "mdi mdi-archive",
			"buttons" => array(
				0 => array(
					"icon"  => "mdi mdi-plus",
					'url'   => 'productos/agregar',
					'txt'   => ' Agregar Producto',
					'modal' => false,
				),
			),
			"table"=>array(
				"Barcode"     => 12,
				"Categoría"   => 15,
				"Descripción" => 25,

				"Estado"      => 10,
				"Acciones"    => 10,
			),
		);
		$extras = array(
			'css' => array(
			),
			'js'  => array(
				"js/scripts/productos.js"
			),
		);
		layout("template/admin",$data,$extras);
	}

	/**
	 * Get product detail
	 *
	 * @return void
	 */
	public function detalle(){

		$id  = $this->uri->segment(3);

		$row = $this->productos->get_row_info($id);

		if ($row && $id != "") {
			$categorias       = $this->productos->get_categorias();
			$precios          = $this->productos->get_precios_exis($id);
			$colores          = $this->productos->get_colores_exis($id);
			$config_impuestos = $this->utils->get_one_row("configuracion",array('1' => 1));
			$marcas           = $this->productos->get_marcas();
			$modelos          = $this->productos->get_modelos($row->id_marca);
			$imagenes         = $this->utils->get_detail_rows("producto_imagen",array('id_producto' => $id));
			$data = array(
				"row"              => $row,
				"categorias"       => $categorias,
				"precios"          => $precios,
				"colores"          => $colores,
				"config_impuestos" => $config_impuestos,
				"imagen"           => $imagenes,
			);

		$this->load->view("productos/ver_detalle.php", $data);
		}

	}

	/**
	 * Send data in JSON format to products.js to draw the DataTable
	 *
	 * @return void
	 */
	function get_data()
	{
		$draw   = intval($this->input->post("draw"));
		$start  = intval($this->input->post("start"));
		$length = intval($this->input->post("length"));
		$order  = $this->input->post("order");
		$search = $this->input->post("search");
		$search = $search['value'];
		$col    = 0;
		$dir    = "";
		if (!empty($order)) {
			foreach ($order as $o) {
				$col = $o['column'];
				$dir = $o['dir'];
			}
		}

		if ($dir != "asc" && $dir != "desc") {
			$dir = "desc";
		}
		$valid_columns = array(
			0 => 'p.id_producto',
			1 => 'p.codigo_barra',
			2 => 'c.nombre',
			3 => 'p.nombre',

		);
		if (!isset($valid_columns[$col])) {
			$order = NULL;
		} else {
			$order = $valid_columns[$col];
		}
		$where=array(
				'p.deleted'=>0,
		);
		//create query based on mariadb tables required
		$query_val=$this->productos->create_query();
		$options_dt=array(
				'order'					=>$order,
				'search'				=>$search,
				'valid_columns'	=>$valid_columns,
				'length'				=>$length,
				'start'					=>$start,
				'dir'						=>$dir,
				'table'					=>$query_val['table'],
				'query'					=>$query_val['query'],
		);
		if(isset($query_val['join'])){
				$options_dt['join'] = $query_val['join'];
		}
		if(isset($where)){
				$options_dt['where'] = $where;
		}
		$options_dt['count']=FALSE;
		$row= $this->utils->get_collection($options_dt);

		if ($row != 0) {
			$data = array();
			foreach ($row as $rows) {

				$menudrop  = "<div class='btn-group'>";
				$menudrop .= "<button data-toggle='dropdown'";
				$menudrop .= "class='btn btn-success dropdown-toggle' aria-expanded='false'>";
				$menudrop .= "<i class='mdi mdi-menu' aria-haspopup='false'></i> Menú</button>";
				$menudrop .= "<ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";
				$filename  = base_url("productos/editar/");
				$menudrop .= "<li><a role='button' href='" . $filename.$rows->id_producto;
				$menudrop .= "' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";
				$menudrop .= "<li><a data-toggle='modal' data-target='#viewModal' data-refresh='true'";
				$menudrop .= "role='button'  class='detail' data-state='Detalle' data-id=" . $rows->id_producto;
				$menudrop .= "  id=" . $rows->id_producto;
				$menudrop .= " ><i class='mdi mdi-information-outline'></i> Detalle</a></li>";
				$state     = $rows->activo;
				if ($state == 1) {
					$txt       = "Desactivar";
					$show_text = "<span class='badge badge-success font-bold'>Activo<span>";
					$icon      = "mdi mdi-toggle-switch-off";
				} else {
					$txt       = "Activar";
					$show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$icon      = "mdi mdi-toggle-switch";
				}
				$menudrop .= "<li><a  class='state_change' data-state='$txt'  id=" . $rows->id_producto;
				$menudrop .= " ><i class='$icon'></i> $txt</a></li>";
				//obtener reservado por sucursal asi como stock de todas las sucursales, valor se suma y si es mayor que cero no se puede borrar!
				$totalStock=0;
				$totalReserv=0;
				$totalExistencia=0;

				$rowcolorprod = $this->utils->get_detail_rows("producto_color",array('id_producto' =>  $rows->id_producto));
				if ($rowcolorprod) {
					foreach ($rowcolorprod as $colprod) {
						$rowsuc = $this->utils->get_detail_rows("sucursales", array(NULL => NULL,)); //
						foreach ($rowsuc as $suc) {
							$reserv = $this->ventas->get_reserved_stock($suc->id_sucursal, $rows->id_producto, $colprod->id_color);
								$totalReserv+=$reserv;
							// get product stock
							$stock_data = $this->ventas->get_stock($rows->id_producto,$colprod->id_color,	$suc->id_sucursal);
							$totalStock+=$stock_data->cantidad;
						}
					}
				}
				$totalExistencia=$totalStock-$totalReserv;
				if($totalStock<=0 && $totalReserv<=0 ){
					$menudrop .= "<li><a  class='delete_row'  id=" . $rows->id_producto;
					$menudrop .= " ><i class='mdi mdi-trash-can-outline'></i> Eliminar</a></li>";
				}
				$menudrop .= "</ul></div>";

				$data[] = array(
					$rows->codigo_barra,
					$rows->categoria,
					$rows->nombre,

					$show_text,
					$menudrop
				);
			}

			$options_dt['count']=TRUE;
			$total =$this->utils->get_collection($options_dt);
			$output = array(
				"draw"            => $draw,
				"recordsTotal"    => $total,
				"recordsFiltered" => $total,
				"data"            => $data
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
			);
			$output = array(
				"draw"            => $draw,
				"recordsTotal"    => 0,
				"recordsFiltered" => 0,
				"data"            => $data
			);
		}
		echo json_encode($output);
		exit();
	}

	/**
	 * Add/Create a new product record
	 *
	 * @return void
	 */
	function agregar()
	{
		if ($this->input->method(TRUE) == "GET") {
			$categorias       = $this->productos->get_categorias();
			$precios          = $this->productos->get_precios();
			$dias             = 0;
			$config_impuestos = $this->utils->get_one_row("configuracion",array('1' => 1));
			$data = array(
				"precios"          => $precios,
				"categorias"       => $categorias,
				"dias"             => $dias,
				"config_impuestos" => $config_impuestos,
			);
			$extras = array(
				'css' => array(
				),
				'js'  => array(
					"js/scripts/productos.js"
				),
			);
			layout("productos/agregar",$data,$extras);
		} else if($this->input->method(TRUE) == "POST") {
			$exento_iva          = $this->input->post("exento_iva");
			$nombre              = strtoupper($this->input->post("nombre"));
			$categoria           = strtoupper($this->input->post("categoria"));
			$codigo_barra        = strtoupper($this->input->post("codigo_barra"));
			$costo_s_iva         = $this->input->post("costo_s_iva");
			$costo_c_iva         = $this->input->post("costo_c_iva");
			$precio_sugerido     = $this->input->post("precio_sugerido");
			$dias_garantia       = 0;
			$dias_garantia_usado = 0;
			$precio_seguro       = $this->input->post("precio_seguro");
			$coloresg            = $this->input->post("coloresg");
			$preciosg            = $this->input->post("preciosg");
			$descripcion         = strtoupper($this->input->post("descripcion"));
			$upload_path         = "assets/img/productos/";
			$path                = "assets/img/productos/";

			$data = array(
				"nombre"              => $nombre,
				"id_categoria"        => $categoria,
				"codigo_barra"        => $codigo_barra,
				"costo_s_iva"         => $costo_s_iva,
				"costo_c_iva"         => $costo_c_iva,
				"precio_sugerido"     => $precio_sugerido,
				"dias_garantia"       => $dias_garantia,
				"activo"              => 1,
				"exento"              => $exento_iva,
				"descripcion"         => $descripcion,
			);
			$this->utils->begin();
			$row_existe = $this->utils->get_one_row("producto", array('nombre'=> $nombre,'codigo_barra'=> $codigo_barra));
			if ($row_existe==NULL) {
			$id_producto = $this->productos->insertar_producto($data);

			if ($id_producto!=NULL) {

				foreach ($_FILES["photos"]["name"] as $photo=>$tmp_name) {

					if ($_FILES["photos"]["name"][$photo] != "") {
						$imagen = upload_multiple_image("photos",$upload_path,$photo);
						/*resize_image($imagen, $upload_path,1000,1000,100,0,"");*/
						$url=$path.$imagen;
						$data_img = array(
							"id_producto"=>$id_producto,
							"url"=>$url,
						);
						$this->utils->insert("producto_imagen",$data_img);
					}
				}
				$tabla_precios = "producto_precio";
				$n=0;
				$array = json_decode($preciosg, true);
				foreach ($array as $fila) {
					$desc = $fila["desc"];
					$costo = $fila["costo"];
					$costo_iva = $fila["costo_iva"];
					$ganancia = $fila["ganancia"];
					$preciolista = $fila["preciolista"];
					$idpreciolista = $fila["idpreciolista"];
					$precio = $fila["precio_iva"];
					$precio_iva = $fila["precio_iva"];

					$lista = array(
						'id_producto' => $id_producto,
						'descripcion' => $desc,
						'costo' => $costo,
						'costo_iva' => $costo_iva,
						'ganancia' => $ganancia,
						'porcentaje' => $preciolista,
						'precio_venta' => $preciolista,
						'total' => $precio,
						'total_iva' => $precio_iva,
						'id_listaprecio' => $idpreciolista ,
					);
					$insert_precio = $this->utils->insert($tabla_precios, $lista);
					if($lista) {
						$n = 1;
					}
				}
				$tabla_colores = "producto_color";
				$n=0;

				$array = json_decode($coloresg, true);
				foreach ($array as $fila) {
					$colora = $fila["colora"];

					$lista = array(
						'id_producto' => $id_producto,
						'color' => $colora,
					);
					$insert_color = $this->utils->insert($tabla_colores, $lista);
					if ($insert_color) {
						$n = 1;
					}
				}
				$this->utils->commit();
				$xdatos["type"]="success";
				$xdatos['title']='Información';
				$xdatos["msg"]="Registro ingresado correctamente!";
			} else {
				$this->utils->rollback();
				$xdatos["type"]="error";
				$xdatos['title']='Alerta';
				$xdatos["msg"]="Error al ingresar el registro";
			}
		} else {
			$this->utils->rollback();
			$xdatos["type"]="error";
			$xdatos['title']='Alerta';
			$xdatos["msg"]="Ya  existe un registro con esta descripcion y codigo de barra!!!";
		}

			echo json_encode($xdatos);
		}
	}

	function get_colores(){
		$row = array("colores"=>$this->colores->get_colores());
		$respuesta = $this->load->view("productos/select_get", $row, TRUE);
		echo $respuesta;
	}
	/* traer  modelos, en base a un id marca seleccionado */
	function get_modelos($id=-1)
	{
			if ($this->input->post("id_marca")!=false){
				$id_marca = $this->input->post("id_marca");
			}else{
					$id_marca =$id;
			}
			$row = array("modelos"=>$this->productos->get_modelos($id_marca));
			$respuesta = $this->load->view("productos/select_get", $row, TRUE);
			echo $respuesta;

	}
	/**
	 * Edit an existing product record
	 *
	 * @param int $id Product registration ID
	 *
	 * @return void
	 */
	function editar($id=-1)
	{
		if ($this->input->method(TRUE) == "GET") {
			$id  = $this->uri->segment(3);
			$row = $this->productos->get_row_info($id);
			if ($row && $id != "") {
				$categorias       = $this->productos->get_categorias();
				$precios          = $this->productos->get_precios_exis($id);
				$colores          = $this->productos->get_colores_exis($id);
				$config_impuestos = $this->utils->get_one_row("configuracion",array('1' => 1));

				$data = array(
					"row"              => $row,
					"categorias"       => $categorias,
					"precios"          => $precios,
					"colores"          => $colores,
					"config_impuestos" => $config_impuestos,

				);
				$extras = array(
					'css' => array(
						"libs/jquery_image_multiple/image-uploader.min.css"
					),
					'js'  => array(
						"libs/jquery_image_multiple/image-uploader.min.js",
						"js/scripts/productos.js"
					),
				);
				layout("productos/editar",$data,$extras);
			} else {
				redirect('errorpage');
			}
		} else if ($this->input->method(TRUE) == "POST") {
			$id_producto         = strtoupper($this->input->post("id_producto"));
			$exento_iva          = $this->input->post("exento_iva");
			$nombre              = strtoupper($this->input->post("nombre"));
			$categoria           = strtoupper($this->input->post("categoria"));
			$codigo_barra        = strtoupper($this->input->post("codigo_barra"));

			$costo_s_iva         = $this->input->post("costo_s_iva");
			$costo_c_iva         = $this->input->post("costo_c_iva");
			$precio_sugerido     = $this->input->post("precio_sugerido");
			$dias_garantia       = $this->input->post("dias_garantia");
			$dias_garantia_usado = $this->input->post("dias_garantia_usado");
			$precio_seguro       = $this->input->post("precio_seguro");
			$preciosg            = $this->input->post("preciosg");

			$coloresg            = $this->input->post("coloresg");

			$descripcion         = strtoupper($this->input->post("descripcion"));
			$upload_path         = "assets/img/productos/";
			$path                = "assets/img/productos/";

			$data = array(
				"nombre"              => $nombre,
				"id_categoria"        => $categoria,
				"codigo_barra"        => $codigo_barra,
				"costo_s_iva"         => $costo_s_iva,
				"costo_c_iva"         => $costo_c_iva,
				"precio_sugerido"     => $precio_sugerido,
				"dias_garantia"       => 0,
				"exento"              => $exento_iva,
				"descripcion"         => $descripcion,
			);
			$where = "id_producto='".$id_producto."'";
			$this->utils->begin();
			$update = $this->utils->update($this->table,$data,$where);
			if ($update) {
				$delete = array();
				//Insertar nuevas imagenes
				foreach ($_FILES["photos"]["name"] as $photo=>$tmp_name) {

					if ($_FILES["photos"]["name"][$photo] != "") {
						$imagen = upload_multiple_image("photos",$upload_path,$photo);
						$url=$path.$imagen;
						$data_img = array(
							"id_producto"=>$id_producto,
							"url"=>$url,
						);
						$id_imagen = $this->productos->insertar_imagen($data_img);
						array_push($delete,array('id_imagen'=>$id_imagen));
					}
				}
				//Eliminar imagenes
				if (isset($_POST['old'])) {
					foreach($_POST['old'] as $value) {
						if ($value != 0) array_push($delete, array('id_imagen'=>$value));
					}
				}

				$this->productos->eliminar_imagen($delete,$id_producto);

				$tabla_precios = "producto_precio";
				$n             = 0;
				$array         = json_decode($preciosg, true);
				$this->utils->delete($tabla_precios,$where);
				foreach ($array as $fila) {
					$desc          = $fila["desc"];
					$costo         = $fila["costo"];
					$costo_iva     = $fila["costo_iva"];
					$ganancia      = $fila["ganancia"];
					$preciolista   = $fila["preciolista"];
					$idpreciolista = $fila["idpreciolista"];

					$precio        = $fila["precio_iva"];
					$precio_iva    = $fila["precio_iva"];
					$precio_venta  = $preciolista;
					$lista = array(
						'id_producto'    => $id_producto,
						'descripcion'    => $desc,
						'costo'          => $costo,
						'costo_iva'      => $costo_iva,
						'ganancia'       => $ganancia,
						'porcentaje'     => $preciolista,
						'precio_venta'   => $preciolista,
						'id_listaprecio' => $idpreciolista,
						'total'          => $precio,
						'total_iva'      => $precio_iva,
					);
					$insert_precio = $this->utils->insert($tabla_precios, $lista);
					if ($lista) {
						$n = 1;
					}
				}

				$tabla_colores = "producto_color";
				$n             = 0;
				$array         = json_decode($coloresg, true);

				foreach ($array as $fila) {
					$colora = $fila["colora"];
					$existeColor=$this->get_Color($id_producto,$colora);
					if ($existeColor==-1) {
						$lista = array(
							'id_producto' => $id_producto,
							'color'       => $colora,
						);
						$insert_color = $this->utils->insert($tabla_colores, $lista);
						if ($insert_color) {
							$n = 1;
						}
					}
				}

				$this->utils->commit();
				$xdatos["type"]  = "success";
				$xdatos['title'] = "Información";
				$xdatos["msg"]   = "Registro actualizado correctamente!";
			} else {
				$this->utils->rollback();
				$xdatos["type"]  = "error";
				$xdatos['title'] = "Alerta";
				$xdatos["msg"]   = "Error al actualizar el registro";
			}

			echo json_encode($xdatos);
		}
	}
	public function precios()
	{
		$icesc = $this->input->post("cesc");
		$costo = $this->input->post("costo");
		$exento_iva = $this->input->post("exento_iva");
		$id_producto = $this->input->post("id_producto");
		$lista = "";
		$precios = $this->productos->get_precios();

		if ($exento_iva==1) {
			// si el producto es exento de iva...
			$iva = 0;
		}
		else {
			// code...
			$iva = round($costo * 0.13,2);
		}
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

			$id = $row_por->id;
			$detalle = $row_por->descripcion;
			//	$dias = $this->inventario->get_one_row("dias_garantia",array('1' => 1));
			$pv_row= $this->utils->get_one_row("producto_precio",array('id_producto' => $id_producto,'id_listaprecio' => $id,));
			if($pv_row!=NULL){
					$pv=$pv_row->precio_venta;
					$gana = $pv - $ctotal;
			}
			else {
				$pv=0.0;
				$gana =0.0;
			}
			$resultado2 = round($costo * 1.13, 2);

			$lista .= "<tr>";

			$lista .= "<td style='text-align: right' class='td_desc'><input type='hidden' class='form-control lista_pr' id='id_lista_pr' name='id_lista_pr' value='".$id."'><input type='text' style='width:350px;' class='form-control desc_td' id='desc_td' name='desc_td' value='".$detalle."' readonly></td>";
			$lista .= "<td style='text-align: right' class='td_costo'><input type='hidden' class='form-control costo_td' id='costo_td' name='costo_td' value='".$costo."'>$ ".number_format($costo,2, '.', '')."</td>";
			$lista .= "<td style='text-align: right' class='td_costo_iva'><input type='hidden' class='form-control costo_td_iva' id='costo_td_iva' name='costo_td_iva' value='".$iva."'>$ ".number_format($iva,2, '.', '')."</td>";
			$lista .= "<td style='text-align: right' class='td_precio_iva'><input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='".$ctotal."'>$ ".number_format($ctotal,2, '.', '')."</td>";
			$lista .= "<td style='text-align: right' class='td_preciolista'><input type='text' class='form-control listaprecios' id='preciolista' name='preciolista'  value='".number_format($pv,2, '.', '')."'></td>";
			$lista .= "<td style='text-align: right' class='td_ganancia'><input type='hidden' class='form-control ganancia_td' id='ganancia' name='ganancia' value='".$gana."'>$ ".number_format($gana, 2, '.', '')."</td>";
			$lista .= "</tr>";
		}

	/*	$costo_iva = round($costo * 1.13, 2);
		$lista = "";
		$precios = $this->productos->get_precios();
		foreach ($precios as $row_por) {
			$id = $row_por->id;
			$preciolista = $row_por->preciolista;
			$detalle = $row_por->descripcion;
			$resultado = round($costo * ($preciolista / 100) , 2);
			$resultado1 = $costo + $resultado;
			$resultado2 = round($resultado1 * 1.13, 2);
			$lista .= "<tr>";
			$lista .= "<td style='text-align: right' class='td_desc'><input type='text' style='width:350px;' class='form-control desc_td' id='desc_td' name='desc_td' value='".$detalle."' readonly></td>";
			$lista .= "<td style='text-align: right' class='td_costo'><input type='hidden' class='form-control costo_td' id='costo_td' name='costo_td' value='".$costo."'>$ ".number_format($costo,2)."</td>";
			$lista .= "<td style='text-align: right' class='td_costo_iva'><input type='hidden' class='form-control costo_td_iva' id='costo_td_iva' name='costo_td_iva' value='".$costo_iva."'>$ ".number_format($costo_iva,2)."</td>";
			$lista .= "<td style='text-align: right' class='td_precio'><input type='hidden' class='form-control precio_td' id='precio_td' name='precio_td' value='".$resultado1."'>$ ".number_format($resultado1, 2)."</td>";
			$lista .= "<td style='text-align: right' class='td_precio_iva'><input type='hidden' class='form-control precio_td_iva' id='precio_td_iva' name='precio_td_iva' value='".$resultado2."'>$ ".number_format($resultado2, 2)."</td>";
			$lista .= "<td style='text-align: right' class='td_preciolista'><input type='hidden' class='form-control preciolista' id='preciolista' name='preciolista' value='".$preciolista."'>".number_format($preciolista,2)."%</td>";
			$lista .= "<td style='text-align: right' class='td_ganancia'><input type='hidden' class='form-control ganancia_td' id='ganancia_td' name='ganancia_td' value='".$resultado."'>$ ".number_format($resultado, 2)."</td>";
			$lista .= "<td style='text-align: right'><button type='button' id='delete' class='btn btn-success delete'><i class='mdi mdi-delete'></i></button></td>";
			$lista .= "</tr>";
		}
		*/
		echo $lista;
	}
	function proveedores(){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->productos->get_row_info($id);
			if($row && $id!=""){
				$proveedores = $this->productos->get_proveedores($id);
				$data = array(
					"row"=>$row,
					"proveedores"=>$proveedores,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/productos.js"
					),
				);
				layout("productos/proveedores",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}else if($this->input->method(TRUE) == "POST"){
			$id_producto = $this->input->post("id_producto");
			$proveedores = $this->input->post("proveedores");

			$this->utils->begin();
			if(isset($proveedores)) {
				foreach ($proveedores as $rec) {

					if ($rec["id_pp"] == 0) {
						$form_detalle = array(
							"id_producto" => $id_producto,
							"id_proveedor" => $rec["id_proveedor"],
						);
						$this->utils->insert("producto_proveedor", $form_detalle);
					} else {
						$form_detalle = array(
							"id_producto" => $id_producto,
							"id_proveedor" => $rec["id_proveedor"],
						);
						$wherer = " id_pp='" . $rec["id_pp"] . "'";
						$this->utils->update("producto_proveedor", $form_detalle, $wherer);
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

	/*function precios(){
	if($this->input->method(TRUE) == "GET"){
	$id = $this->uri->segment(3);
	$row = $this->productos->get_row_info($id);
	if($row && $id!=""){
	$precios = $this->productos->get_precios($id);
	$data = array(
	"row"=>$row,
	"precios"=>$precios,
);
$extras = array(
'css' => array(
),
'js' => array(
"js/scripts/productos.js"
),
);
layout("productos/precios",$data,$extras);
}else{
redirect('errorpage');
}
}else if($this->input->method(TRUE) == "POST"){
$id_producto = $this->input->post("id_producto");
$proveedores = $this->input->post("proveedores");
$this->utils->begin();
if(isset($proveedores)) {
foreach ($proveedores as $rec) {
if ($rec["id_pp"] == 0) {
$form_detalle = array(
"id_producto" => $id_producto,
"id_proveedor" => $rec["id_proveedor"],
);
$this->utils->insert("producto_proveedor", $form_detalle);
} else {
$form_detalle = array(
"id_producto" => $id_producto,
"id_proveedor" => $rec["id_proveedor"],
);
$wherer = " id_pp='" . $rec["id_pp"] . "'";
$this->utils->update("producto_proveedor", $form_detalle, $wherer);
}
}
}
$this->utils->commit();
$xdatos["type"]="success";
$xdatos['title']='Exito';
$xdatos["msg"]="Registo guardado correctamente!";
echo json_encode($xdatos);
}
}*/

function get_proveedor_autocomplete(){
	$query = $this->input->post("query");
	$rows = $this->productos->get_proveedor_autocomplete($query);
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
		$delete = $this->utils->delete("producto_proveedor",$where);
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
		$productos = $this->productos->get_images($id);
		$new = [];
		foreach ($productos as $row){
			array_push($new, array('id'=>$row->id_imagen, 'imagen'=>base_url($row->url)));
		}
		echo json_encode($new);
	}
}

	/**
	 * Erasing an existing product record. Always visible in the database
	 * (virtual delete)
	 *
	 * @return void
	 */
function delete(){
	if($this->input->method(TRUE) == "POST"){
		$id = $this->input->post("id");
		$response = safe_delete($this->table,$this->pk,$id);
		echo json_encode($response);
	}
}

	/**
	 * Change the active state to inactive from an existing product record
	 *
	 * @return void
	 */
function state_change(){
	if($this->input->method(TRUE) == "POST"){
		$id = $this->input->post("id");
		$active = $this->productos->get_state($id);
		$response = change_state($this->table,$this->pk,$id,$active);
		echo json_encode($response);
	}
}

function eliminar_color(){
	if($this->input->method(TRUE) == "POST"){
		$id = $this->input->post("id");
		$color = $this->input->post("color");
		$where = " id_producto='".$id."' AND color ='".$color."'";
		$this->utils->begin();
		$delete = $this->utils->delete("producto_color",$where);
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
		$id_producto = $this->input->post("id");
		$color = $this->input->post("color");

		$row = $this->productos->get_idColor($id_producto,$color);
		//var_dump($row);
		if($row!=NULL){
			$id_color=$row->id_color;
		}
		else{
			/*
			$id_color=-1;
			$data["type"] = "Error";
			$data["title"] = "Alerta!";
			$data["msg"] = "Registro no pudo ser eliminado, tiene stock!";
			*/
			$id_color=-1;
			$data["type"] = "success";
			$data["title"] = "Eliminado!";
			$data["msg"] = "Color eliminado";
			echo json_encode($data);

			}
		//ver si tiene stock
		if($id_color>0){
			$row2 = $this->productos->get_color_stock($id_producto,$id_color);
			if($row2!=NULL){
				$id_stock=$row2->id_stock;

				$cantidad=$row2->cantidad;
				$data["type"] = "Error";
				$data["title"] = "Alerta!";
				$data["msg"] = "Registro no pudo ser eliminado, tiene stock!";

				echo json_encode($data);
			}
			else{

				 $this->borrar_color($id_producto,$id_color);
			}
		}
	}
}
	function borrar_color($id_producto,$id_color){
			$where = " id_producto='".$id_producto."' AND id_color ='".$id_color."'";
			$this->utils->begin();
			$delete = $this->utils->delete("producto_color",$where);
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
	function get_Color($id_producto,$color){
		$row = $this->productos->get_idColor($id_producto,$color);
		if($row==NULL){
			return -1;
		}else{
			$id_color=$row->id_color;
			return $id_color;
		}

	}
}
/* end of file ./application/controllers/Productos.php */
