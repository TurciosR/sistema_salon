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

class Colores extends CI_Controller {

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
	}

	public function index()
	{
		$data = array(
			"titulo"  => "Colores",
			"icono"   => "mdi mdi-format-list-bulleted",
			"buttons" => array(
				0 => array(
					"icon"  => "mdi mdi-plus",
					'url'   => 'colores/agregar',
					'txt'   => 'Agregar colores',
					'modal' => false,
				),
			),
			"table" => array(
				"ID"     => 4,
				"Color"  => 4,
				"Accion" => 4,
			),
		);

		$extras = array(
			'css' => array(
			),
			'js' => array(
				"js/scripts/colores.js",
			),
		);

		layout("template/admin", $data, $extras);
	}

	function get_data()
	{

		$valid_columns = array(
			0 => 'id_color',
			1 => 'color',
		);
		// Create query based on mariadb tables required
		$query_val  = $this->colores->create_dt_query();

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
				$filename  = base_url("colores/editar_colores/");
				$menudrop .= "<li><a role='button' href='" . $filename. $rows->id_color;
				$menudrop .= "' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";
				$menudrop .= "</ul></div>";
				$data[]    = array(
					$rows->id_color,
					$rows->color,
					$menudrop,
				);
			}

			$total  = generate_dt("UtilsModel", $options_dt, TRUE);
			$output = array(
				"draw"            => $draw,
				"recordsTotal"    => $total,
				"recordsFiltered" => $total,
				"data"            => $data,
			);
		} else {
			$data[] = array(
				"",
				"No se encontraron registros",
				"",
			);
			$output = array(
				"draw"            => $draw,
				"recordsTotal"    => 0,
				"recordsFiltered" => 0,
				"data"            => $data,
			);
		}
		echo json_encode($output);
	}

	function agregar()
	{

		if($this->input->method(TRUE) == "GET"){
			$data = array(
			);
			$extras = array(
				'css' => array(
				),
				'js' => array(
					"js/scripts/colores.js",
				),
			);
			layout("productos/agregar_colores",$data,$extras);
		}
		else if($this->input->method(TRUE) == "POST"){

			$color = strtoupper($this->input->post("color"));

		$data = array(
			"color"=>$color,
		);
				$this->utils->begin();
				//procedemos a validar si no se ha agregado un color con el mismo nombre
				$validar = $this->colores->get_existe($color);
				//echo $validar;
				if ($validar==0) {
					$update = $this->utils->insert($this->table,$data);

					if($update){
							$this->utils->commit();
						$xdatos["type"]="success";
						$xdatos['title']='Exito';
						$xdatos["msg"]="Registo actualizado correctamente!";
					}
					else {
						$this->utils->rollback();
						$xdatos["type"]="error";
						$xdatos['title']='Alerta';
						$xdatos["msg"]="Error al actualizar el registro";
					}
				}
				else{
					$xdatos["type"]="error";
					$xdatos['title']='Alerta';
					$xdatos["msg"]="Nombre de color ya existe";
				}

		  echo json_encode($xdatos);
		}
	}

	function editar_colores($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->colores->get_row_color($id);

			if($row && $id!=""){
				$data = array(
					"row"=>$row,
					"id"=>$id,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/colores.js"
					),
				);
				layout("productos/editar_colores",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$color = strtoupper($this->input->post("color"));
			$id_color = strtoupper($this->input->post("id_color"));
			$where = $this->pk."='".$id_color."'";

			$data = array(
				"color"=>$color,
			);
						$this->utils->begin();

						//procedemos a validar si no se ha agregado un color con el mismo nombre
						$validar = $this->colores->get_existe($color);
						//echo $validar;
						if ($validar==0) {
							// code...
							$update = $this->utils->update($this->table,$data,$where);

							if($update){
								$this->utils->commit();
								$xdatos["type"]="success";
								$xdatos['title']='Exito';
								$xdatos["msg"]="Registo actualizado correctamente!";
							}
							else {
								$this->utils->rollback();
								$xdatos["type"]="error";
								$xdatos['title']='Alerta';
								$xdatos["msg"]="Error al actualizar el registro";
							}
						}
						else{
							$xdatos["type"]="error";
							$xdatos['title']='Alerta';
							$xdatos["msg"]="Nombre de color ya existe";
						}
				  echo json_encode($xdatos);
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
