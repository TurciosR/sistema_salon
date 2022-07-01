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
 * Marcas Controller
 *
 * Display the Brand Module (Marcas)
 *
 * @package		OpenPyme2
 * @subpackage	Controllers
 * @category	Controllers
 * @author		OpenPyme Dev Team
 * @link		sftp://docs.apps-oss.com/classes/Marcas.html
 */
class Marcas extends CI_Controller {


	// Variables de entorno
	private $table = "marca";
	private $pk    = "id_marca";

	function __construct()
	{
		parent::__construct();
		$this->load->Model("MarcasModel", "marcas");
		$this->load->helper("upload_file");
		$this->load->model('UtilsModel', "utils");
	}

	/**
	 * Displays the main page (admin) of the Brand Module (Marcas)
	 *
	 * @return void
	 */
	public function index()
	{
		$data = array(
			"titulo"  => "Marcas",
			"icono"   => "mdi mdi-trademark",
			"buttons" => array(
				0 => array(
					"icon"  => "mdi mdi-plus",
					'url'   => 'marcas/agregar',
					'txt'   => 'Agregar Marca',
					'modal' => false,
				),
			),
			"table"=>array(
				"ID"          => 1,
				"Nombre"      => 4,
				"Descripción" => 4,
				"Estado"      => 2,
				"Acciones"    => 1,
			),
		);

		$extras = array(
			'css' => array(
			),
			'js'  => array(
				"js/scripts/marcas.js",
			),
		);

		layout("template/admin", $data, $extras);
	}//fin index()

	/**
	 * Send data in JSON to Marcas.js format to draw the DataTable
	 *
	 * @return void
	 */
	function get_data()
	{

		$draw = intval($this->input->post("draw"));
		$start = intval($this->input->post("start"));
		$length = intval($this->input->post("length"));
		$id_sucursal = $this->input->post("id_sucursal");

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
		$valid_columns = array(
			0 => 'ma.id_marca',
			1 => 'ma.nombre',
			2 => 'ma.descripcion',
		);
		if (!isset($valid_columns[$col])) {
			$order = null;
		} else {
			$order = $valid_columns[$col];
		}
		$where=array(
				'ma.deleted'=>0,
		);
		//create query based on mariadb tables required
		$query_val=$this->marcas->create_query();
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
		$response = $this->utils->get_collection($options_dt);
		if ($response != 0) {
			$data = array();
			foreach ($response as $rows) {
				$menudrop  = "<div class='btn-group'><button data-toggle='dropdown'";
				$menudrop .= " class='btn btn-success dropdown-toggle' aria-expanded='false'>";
				$menudrop .= "<i class='mdi mdi-menu' aria-haspopup='false'></i> Menú</button>";
				$menudrop .= "<ul class='dropdown-menu dropdown-menu-right'";
				$menudrop .= " x-placement='bottom-start'>";

				$filename  = base_url("marcas/editar/");
				$menudrop .= "<li><a role='button' href='" . $filename.$rows->id_marca;
				$menudrop .= "'><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";
				$state = $rows->activo;
				if ($state == 1) {
					$txt       = "Desactivar";
					$show_text = "<span class='badge badge-success font-bold'>Activo<span>";
					$icon      = "mdi mdi-toggle-switch-off";
				} else {
					$txt       = "Activar";
					$show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$icon      = "mdi mdi-toggle-switch";
				}

				$menudrop .= "<li><a  class='state_change' data-state='$txt'  id=";
				$menudrop .= $rows->id_marca . " ><i class='$icon'></i> $txt</a></li>";

				$menudrop .= "<li><a  class='delete_row'  id=" . $rows->id_marca;
				$menudrop .= " ><i class='mdi mdi-trash-can-outline'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";

				$data[] = array(
					$rows->id_marca,
					$rows->nombre,
					$rows->descripcion,
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
	}//fin get_data


	/**
	 * Add/Create a new brand(marca) record
	 *
	 * @return void
	 */
	function agregar()
	{
		if ($this->input->method(TRUE) == "GET") {
			$data = array(
			);
			$extras = array(
				'css' => array(
				),
				'js'  => array(
					"js/scripts/marcas.js",
				),
			);
			layout("productos/agregar_marcas", $data, $extras);

		} else if ($this->input->method(TRUE) == "POST") {
			$descripcion = strtoupper($this->input->post("descripcion"));
			$nombre = strtoupper($this->input->post("nombre"));

			$data = array(
				"nombre"      => $nombre,
				"descripcion" => $descripcion,
				"activo"      => 1
			);
			$response = insert_row($this->table, $data);
			echo json_encode($response);
		}
	}


	/**
	 * Edit an existing brand (marca) record
	 *
	 * @param int $id of brand (marca) record
	 *
	 * @return void
	 */
	function editar($id=-1)
	{
		if ($this->input->method(TRUE) == "GET") {
			$id  = $this->uri->segment(3);
			$row = $this->marcas->get_row_info($id);
			if($row && $id != ""){
				$data = array(
					"row" => $row
				);
				$extras = array(
					'css' => array(
					),
					'js'  => array(
						"js/scripts/marcas.js"
					)
				);
				layout("productos/editar_marcas", $data, $extras);
			} else {
				redirect('errorpage');
			}
		} else if ($this->input->method(TRUE) == "POST") {
			$nombre      = strtoupper($this->input->post("nombre"));
			$descripcion = strtoupper($this->input->post("descripcion"));
			$id_marca    = $this->input->post("id_marca");
			$where       = $this->pk."='".$id_marca."'";

			$data = array(
				"nombre"      => $nombre,
				"descripcion" => $descripcion
			);
			$response = edit_row($this->table, $data, $where);
			echo json_encode($response);
		}
	}

	/**
	 * Erasing an existing brand (marca) record. Always visible in the database
	 * (virtual delete)
	 *
	 * @return void
	 */
	function delete()
	{
		if ($this->input->method(TRUE) == "POST") {
			$id = $this->input->post("id");
			$response = safe_delete($this->table, $this->pk, $id);
			echo json_encode($response);
		}
	}

	/**
	 * Change the active state to inactive from an existing brand (marca) record
	 *
	 * @return void
	 */
	function state_change(){
		if ($this->input->method(TRUE) == "POST") {
			$id       = $this->input->post("id");
			$active   = $this->marcas->get_state($id);
			$response = change_state($this->table, $this->pk, $id, $active);
			echo json_encode($response);
		}
	}

}

/* fin ./application/controllers/Marcas.php */
