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

class Categorias extends CI_Controller {

	/*
	Enviroment variables
	*/
	private $table = "categoria";
	private $pk = "id_categoria";

	function __construct()
	{
		parent::__construct();
		$this->load->Model("CategoriasModel","categorias");
		$this->load->helper("upload_file");
		$this->load->model('UtilsModel',"utils");
	}

	public function index()
	{
		$data = array(
			"titulo"  => "Categorías",
			"icono"   => "mdi mdi-format-list-bulleted",
			"buttons" => array(
				0 => array(
					"icon"  => "mdi mdi-plus",
					'url'   => 'categorias/agregar',
					'txt'   => 'Agregar Categoría',
					'modal' => false,
				),
			),
			"table"=> array(
				"ID"		  => 1,
				"Nombre"	  => 4,
				"Descripción" => 4,
				"Estado"	  => 2,
				"Acciones"	=> 1,
			),
		);

		$extras = array(
			'css' => array(
			),
			'js'  => array(
				"js/scripts/categorias.js",
			),
		);

		layout("template/admin",$data,$extras);
	}

	function get_data(){

		$valid_columns = array(
			0 => 'id_categoria',
			1 => 'nombre',
			2 => 'descripcion',
		);

		// Create query based on mariadb tables required
		$query_val  = $this->categorias->create_dt_query();

		/* You can pass where and join clauses as necessary or include it on model
		 * function as necessary. If no join includ it set to NULL.
		 */
		$options_dt = array(
				'valid_columns' => $valid_columns,
		);
		$options_dt = array_merge($query_val, $options_dt);
		$response   = generate_dt("UtilsModel", $options_dt, FALSE);
		$draw       = intval($this->input->post("draw"));
		if ($response != 0) {
			$data = array();
			foreach ($response as $rows) {

				$menudrop  = "<div class='btn-group'><button data-toggle='dropdown'";
				$menudrop .= " class='btn btn-success dropdown-toggle' aria-expanded='false'>";
				$menudrop .= "<i class='mdi mdi-menu' aria-haspopup='false'></i> Menú</button>";
				$menudrop .= "<ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";
				$filename  = base_url("categorias/editar/");
				$menudrop .= "<li><a role='button' href='" . $filename.$rows->id_categoria. "' >";
				$menudrop .= "<i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";


				$state = $rows->activo;
				if($state==1){
					$txt       = "Desactivar";
					$show_text = "<span class='badge badge-success font-bold'>Activo<span>";
					$icon      = "mdi mdi-toggle-switch-off";
				} else {
					$txt       = "Activar";
					$show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$icon      = "mdi mdi-toggle-switch";
				}

				$menudrop .= "<li><a  class='state_change' data-state='$txt'  id=" . $rows->id_categoria;
				$menudrop .= " ><i class='$icon'></i> $txt</a></li>";

				$menudrop .= "<li><a  class='delete_row'  id=" . $rows->id_categoria;
				$menudrop .= " ><i class='mdi mdi-trash-can-outline'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";

				$data[] = array(
					$rows->id_categoria,
					$rows->nombre,
					$rows->descripcion,
					$show_text,
					$menudrop,
				);
			}

			$total    = generate_dt("UtilsModel", $options_dt, TRUE);
			$output   = array(
				"draw"            => $draw,
				"recordsTotal"    => $total,
				"recordsFiltered" => $total,
				"data"            => $data
			);
		} else {
			$data[] = array(
				"",
				"",
				"No se encontraron registros",
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

	function agregar(){

		if($this->input->method(TRUE) == "GET"){
			$data = array(
			);
			$extras = array(
				'css' => array(
				),
				'js' => array(
					"js/scripts/categorias.js",
				),
			);
			layout("productos/agregar_categoria",$data,$extras);
		}
		else if($this->input->method(TRUE) == "POST"){

			$descripcion = strtoupper($this->input->post("descripcion"));
			$nombre = strtoupper($this->input->post("nombre"));
			$path = "assets/img/productos/";

			if ($_FILES["foto"]["name"] != "") {
				$imagen = upload_image("foto",$path);
				$url=$path.$imagen;
			}
			else $url = "";

			$data = array(
				"descripcion"=>$descripcion,
				"nombre"=>$nombre,
				"imagen"=>$url,
				"activo"=>1,
			);
			$response = insert_row($this->table,$data);
			echo json_encode($response);
		}
	}

	function editar($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->categorias->get_row_info($id);
			if($row && $id!=""){
				$data = array(
					"row"=>$row,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/categorias.js"
					),
				);
				layout("productos/editar_categoria",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$descripcion = strtoupper($this->input->post("descripcion"));
			$nombre = strtoupper($this->input->post("nombre"));
			$id_categoria = strtoupper($this->input->post("id_categoria"));
			$row = $this->categorias->get_row_info($id_categoria);
			$where = $this->pk."='".$id_categoria."'";

			$path = "assets/img/productos/";
			if ($_FILES["foto"]["name"] != "") {
				$imagen = upload_image("foto",$path);
				$url=$path.$imagen;
			}
			else{
				$url = $row->imagen;
			}

			$data = array(
				"descripcion"=>$descripcion,
				"nombre"=>$nombre,
				"imagen"=>$url,
			);
			$response = edit_row($this->table,$data,$where);
			echo json_encode($response);
		}
	}

	function delete(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
	  $response = safe_delete($this->table,$this->pk,$id);
			echo json_encode($response);
		}
	}

	function state_change(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$active = $this->categorias->get_state($id);
			$response = change_state($this->table,$this->pk,$id,$active);
			echo json_encode($response);
		}
	}

}

/* End of file Productos.php */
