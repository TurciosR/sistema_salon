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

class Sucursales extends CI_Controller {

	/*
	Global table name
	*/
	private $file = "sucursales";
	private $table = "sucursales";
	private $pk = "id_sucursal";

	function __construct()
	{
		parent::__construct();
		$this->load->Model("SucursalesModel","sucursal");
		$this->load->Model("UtilsModel","utils");
	}

	public function index()
	{
		if(isset($this->session->super_admin) && $this->session->super_admin==1){
			/* Test if more than one branch (sucursal) exist if so add button
			 * "Agregar sucursal. This is a pay per view module. So when the client
			 * ask for to add new branch (sucursal) the OSS' staff can go directly to
			 * the URL base_url()/sucursales/agregar_suc/ to add a new branch
			 * (sucursal). When two or more sucursales exist the button "Agregar
			 * sucursal" will display thenceforth.
			 * HEADS UP: Only Super Admin can access this module. For other kind
			 * of admin user is deactivated by default.
			**/

			$total_suc = $this->sucursal->total_rows();
			if ($total_suc > 1) {
				$button_suc = array(
					0 => array(
						'icon'  => "mdi mdi-plus",
						'url'   => 'sucursales/agregar_suc',
						'txt'   => 'Agregar sucursales',
						'modal' => false,
						)
					);
			} else {
				$button_suc = NULL;
			}

			$data = array(
				"titulo"  => " Sucursales",
				"icono"   => "mdi mdi-office-building",
				"buttons" => $button_suc,
				"table"   => array(
					"Id"        => 1,
					"Nombre"    => 4,
					"Dirección" => 4,
					"Estado"    => 2,
					"Acciones"  => 1
				),
			);
			$extras = array(
				'css' => array(
				),
				'js' => array(
					"js/scripts/sucursal.js",
				),
			);
			layout("template/admin", $data, $extras);
		}else {
			 redirect('errorpage');
		 }
	}

	function get_data(){
	
		$valid_columns = array(
			0 => 'id_sucursal',
			1 => 'nombre',
			2 => 'direccion',
		);

		// Create query based on mariadb tables required
		$query_val  = $this->sucursal->create_dt_query();

		/* You can pass where and join clauses as necessary or include it on model
		 * function as necessary. If no join includ it set to NULL.
		 */
		$options_dt = array(
						'valid_columns' => $valid_columns,
						'join'          => NULL,
		);
		if(isset($where)){
				$options_dt['where'] = $where;
		}
		$options_dt = array_merge($query_val, $options_dt);
		$draw       = intval($this->input->post("draw"));
		$row        = generate_dt("UtilsModel", $options_dt, FALSE);

		//$row = $this->sucursal->get_collection($order, $search, $valid_columns, $length, $start, $dir);

		if ($row != 0) {
			$data = array();
			foreach ($row as $rows) {
				$menudrop = "<div class='btn-group'>
					<button data-toggle='dropdown' class='btn btn-primary dropdown-toggle' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menú</button>
					<ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>";
				$filename = base_url($this->file."/editar/".$rows->id_sucursal);
				$menudrop .= "<li><a sucursale='button' href='" . $filename. "' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li>";

				$state = $rows->activo;
				if($state==1){
					$txt = "Desactivar";
					$show_text = "<span class='badge badge-success font-bold'>Activo<span>";
					$icon = "mdi mdi-toggle-switch-off";
				}
				else{
					$txt = "Activar";
					$show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$icon = "mdi mdi-toggle-switch";
				}
				$menudrop .= "<li><a  class='state_change' data-state='$txt'  id=" . $rows->id_sucursal . " ><i class='$icon'></i> $txt</a></li>";

				//$menudrop .= "<li><a  class='delete_row'  id=" . $rows->id_sucursal . " ><i class='mdi mdi-trash-can-outline'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";

				$data[] = array(
					$rows->id_sucursal,
					$rows->nombre,
					$rows->direccion,
					$show_text,
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
				"No se encontraron registros",
				"",
				"",
			);
			$output = array(
				"draw" => 0,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => $data
			);
		}
		echo json_encode($output);
	}

	function agregar_suc(){

		if($this->input->method(TRUE) == "GET"){


			$extras = array(
				'css' => array(
				),
				'js' => array(
					"js/scripts/sucursal.js",
				),
			);
			layout("sucursales/agregar_suc",$extras);
		}

		else if($this->input->method(TRUE) == "POST"){
			$direccion = strtoupper($this->input->post("direccion"));
			$nombre = strtoupper($this->input->post("nombre"));
			$correo = $this->input->post("correo");
			$telefono = $this->input->post("telefono");
			$fecha=date("Y-m-d");
			//Headers and footers
			$h1=$this->input->post("h1");
			$h2=$this->input->post("h2");
			$h3=$this->input->post("h3");
			$h4=$this->input->post("h4");
			$h5=$this->input->post("h5");
			$h6=$this->input->post("h6");
			$h7=$this->input->post("h7");
			$h8=$this->input->post("h8");
			$h9=$this->input->post("h9");
			$h10=$this->input->post("h10");

			$f1=$this->input->post("f1");
			$f2=$this->input->post("f2");
			$f3=$this->input->post("f3");
			$f4=$this->input->post("f4");
			$f5=$this->input->post("f5");
			$f6=$this->input->post("f6");
			$f7=$this->input->post("f7");
			$f8=$this->input->post("f8");
			$f9=$this->input->post("f9");
			$f10=$this->input->post("f10");

			$hv1=$this->input->post("hv1");
			$hv2=$this->input->post("hv2");
			$hv3=$this->input->post("hv3");
			$hv4=$this->input->post("hv4");
			$hv5=$this->input->post("hv5");
			$hv6=$this->input->post("hv6");
			$hv7=$this->input->post("hv7");
			$hv8=$this->input->post("hv8");
			$hv9=$this->input->post("hv9");
			$hv10=$this->input->post("hv10");

			$fv1=$this->input->post("fv1");
			$fv2=$this->input->post("fv2");
			$fv3=$this->input->post("fv3");
			$fv4=$this->input->post("fv4");
			$fv5=$this->input->post("fv5");
			$fv6=$this->input->post("fv6");
			$fv7=$this->input->post("fv7");
			$fv8=$this->input->post("fv8");
			$fv9=$this->input->post("fv9");
			$fv10=$this->input->post("fv10");

			$hc1=$this->input->post("hc1");
			$hc2=$this->input->post("hc2");
			$hc3=$this->input->post("hc3");
			$hc4=$this->input->post("hc4");
			$hc5=$this->input->post("hc5");
			$hc6=$this->input->post("hc6");
			$hc7=$this->input->post("hc7");
			$hc8=$this->input->post("hc8");
			$hc9=$this->input->post("hc9");
			$hc10=$this->input->post("hc10");

			$fc1=$this->input->post("fc1");
			$fc2=$this->input->post("fc2");
			$fc3=$this->input->post("fc3");
			$fc4=$this->input->post("fc4");
			$fc5=$this->input->post("fc5");
			$fc6=$this->input->post("fc6");
			$fc7=$this->input->post("fc7");
			$fc8=$this->input->post("fc8");
			$fc9=$this->input->post("fc9");
			$fc10=$this->input->post("fc10");
			//Fin headers and footer
			//RUTAS
			$l1=$this->input->post("l1");
			$l2=$this->input->post("l2");
			$l3=$this->input->post("l3");
			$l4=$this->input->post("l4");

			$data = array(
				"direccion"=>$direccion,
				"nombre"=>$nombre,
				"telefono" =>$telefono,
				"correo" =>  $correo,
				"activo"=>1,
			);
			$tabla_conf="config_pos";
			$data_tik = array(
			  "header1"=>$h1,
			  "header2"=>$h2,
			  "header3"=>$h3,
			  "header4"=>$h4,
			  "header5"=>$h5,
			  "header6"=>$h6,
			  "header7"=>$h7,
			  "header8"=>$h8,
			  "header9"=>$h9,
			  "header10"=>$h10,
			  "footer1"=>$f1,
			  "footer2"=>$f2,
			  "footer3"=>$f3,
			  "footer4"=>$f4,
			  "footer5"=>$f5,
			  "footer6"=>$f6,
			  "footer7"=>$f7,
			  "footer8"=>$f8,
			  "footer9"=>$f9,
			  "footer10"=>$f10,
			  "alias_tipodoc"=>"TIK",
			);
			$data_vale = array(
			  "header1"=>$hv1,
			  "header2"=>$hv2,
			  "header3"=>$hv3,
			  "header4"=>$hv4,
			  "header5"=>$hv5,
			  "header6"=>$hv6,
			  "header7"=>$hv7,
			  "header8"=>$hv8,
			  "header9"=>$hv9,
			  "header10"=>$hv10,

			  "footer1"=>$fv1,
			  "footer2"=>$fv2,
			  "footer3"=>$fv3,
			  "footer4"=>$fv4,
			  "footer5"=>$fv5,
			  "footer6"=>$fv6,
			  "footer7"=>$fv7,
			  "footer8"=>$fv8,
			  "footer9"=>$fv9,
			  "footer10"=>$fv10,
			  "alias_tipodoc"=>"VALE",
			);
			$data_cort = array(
			  "header1"=>$hc1,
			  "header2"=>$hc2,
			  "header3"=>$hc3,
			  "header4"=>$hc4,
			  "header5"=>$hc5,
			  "header6"=>$hc6,
			  "header7"=>$hc7,
			  "header8"=>$hc8,
			  "header9"=>$hc9,
			  "header10"=>$hc10,

			  "footer1"=>$fc1,
			  "footer2"=>$fc2,
			  "footer3"=>$fc3,
			  "footer4"=>$fc4,
			  "footer5"=>$fc5,
			  "footer6"=>$fc6,
			  "footer7"=>$fc7,
			  "footer8"=>$fc8,
			  "footer9"=>$fc9,
			  "footer10"=>$fc10,
			  "alias_tipodoc"=>"CORT",
			);
			$this->utils->begin();
			$insert = $this->utils->insert($this->table,$data);
			$id_sucursal= $this->utils->insert_id();
			$data_tik["id_sucursal"]=$id_sucursal;
			$data_vale["id_sucursal"]=$id_sucursal;
			$data_cort["id_sucursal"]=$id_sucursal;
			$tabla_corr="correlativo";
			$data_corr= array(
				"id_sucursal"=>$id_sucursal,
				"fecha"=>$fecha,
			);
			$tabla_conf_dir="config_dir";
			$data_conf_dir= array(
				"id_sucursal"=>$id_sucursal,
				"dir_print_script"=>$l1,
				"shared_printer_matrix"=>$l2,
				"shared_printer_pos"=>$l3,
				"shared_printer_barcode"=>$l4,
			);
			//SELECT `id_config_dir`, `dir_print_script`, `shared_printer_matrix`,
			// `shared_printer_pos`, `shared_printer_barcode`, `id_sucursal`
			 // FROM `config_dir` WHERE 1
			if($insert){

			  $insert2 = $this->utils->insert($tabla_conf,$data_tik);
			  $insert3 = $this->utils->insert($tabla_conf,$data_vale);
			  $insert4 = $this->utils->insert($tabla_conf,$data_cort);
			  $insert5 = $this->utils->insert($tabla_corr,$data_corr);
			  $insert6 = $this->utils->insert($tabla_conf_dir,$data_conf_dir);
				$this->utils->commit();
				$xdatos["type"]="success";
				$xdatos['title']='Información';
				$xdatos["msg"]="Registo editado correctamente!";
			}
			else {
				$this->utils->rollback();
				$xdatos["type"]="error";
				$xdatos['title']='Alerta';
				$xdatos["msg"]="Error al editar el registro!";
			}

			echo json_encode($xdatos);
		}
	}

	function editar($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(3);
			$row = $this->sucursal->get_row_info($id);
			$row_confdir=$this->utils->get_one_row("config_dir", array('id_sucursal' => $id,));
			$tabla="config_pos";
			$where1 = array(
				"id_sucursal"=>$id,
				"alias_tipodoc"=>"TIK",
			);
			$row_headfoot_tik = $this->utils->get_one_row($tabla,$where1);
			$where1["alias_tipodoc"]="VALE";
			$row_headfoot_vale = $this->utils->get_one_row($tabla,$where1);
			$where1["alias_tipodoc"]="CORT";
			$row_headfoot_cort = $this->utils->get_one_row($tabla,$where1);

			if($row && $id!=""){
				$data = array(
					"row"=>$row,
					"row_headfoot_tik"=>$row_headfoot_tik,
					"row_headfoot_vale"=>$row_headfoot_vale,
					"row_headfoot_cort"=>$row_headfoot_cort,
					"row_confdir"=>$row_confdir,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"js/scripts/sucursal.js"
					),
				);
				layout("sucursales/editar",$data,$extras);
			}else{
				redirect('errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$direccion = strtoupper($this->input->post("direccion"));
			$nombre = strtoupper($this->input->post("nombre"));
			$id_sucursal = $this->input->post("id_sucursal");
			$correo = $this->input->post("correo");
			$telefono = $this->input->post("telefono");
			//Headers and footers
			$h1=$this->input->post("h1");
			$h2=$this->input->post("h2");
			$h3=$this->input->post("h3");
			$h4=$this->input->post("h4");
			$h5=$this->input->post("h5");
			$h6=$this->input->post("h6");
			$h7=$this->input->post("h7");
			$h8=$this->input->post("h8");
			$h9=$this->input->post("h9");
			$h10=$this->input->post("h10");

			$f1=$this->input->post("f1");
			$f2=$this->input->post("f2");
			$f3=$this->input->post("f3");
			$f4=$this->input->post("f4");
			$f5=$this->input->post("f5");
			$f6=$this->input->post("f6");
			$f7=$this->input->post("f7");
			$f8=$this->input->post("f8");
			$f9=$this->input->post("f9");
			$f10=$this->input->post("f10");

			$hv1=$this->input->post("hv1");
			$hv2=$this->input->post("hv2");
			$hv3=$this->input->post("hv3");
			$hv4=$this->input->post("hv4");
			$hv5=$this->input->post("hv5");
			$hv6=$this->input->post("hv6");
			$hv7=$this->input->post("hv7");
			$hv8=$this->input->post("hv8");
			$hv9=$this->input->post("hv9");
			$hv10=$this->input->post("hv10");

			$fv1=$this->input->post("fv1");
			$fv2=$this->input->post("fv2");
			$fv3=$this->input->post("fv3");
			$fv4=$this->input->post("fv4");
			$fv5=$this->input->post("fv5");
			$fv6=$this->input->post("fv6");
			$fv7=$this->input->post("fv7");
			$fv8=$this->input->post("fv8");
			$fv9=$this->input->post("fv9");
			$fv10=$this->input->post("fv10");

			$hc1=$this->input->post("hc1");
			$hc2=$this->input->post("hc2");
			$hc3=$this->input->post("hc3");
			$hc4=$this->input->post("hc4");
			$hc5=$this->input->post("hc5");
			$hc6=$this->input->post("hc6");
			$hc7=$this->input->post("hc7");
			$hc8=$this->input->post("hc8");
			$hc9=$this->input->post("hc9");
			$hc10=$this->input->post("hc10");

			$fc1=$this->input->post("fc1");
			$fc2=$this->input->post("fc2");
			$fc3=$this->input->post("fc3");
			$fc4=$this->input->post("fc4");
			$fc5=$this->input->post("fc5");
			$fc6=$this->input->post("fc6");
			$fc7=$this->input->post("fc7");
			$fc8=$this->input->post("fc8");
			$fc9=$this->input->post("fc9");
			$fc10=$this->input->post("fc10");
			//Fin headers and footer
			//RUTAS
			$l1=$this->input->post("l1");
			$l2=$this->input->post("l2");
			$l3=$this->input->post("l3");
			$l4=$this->input->post("l4");
			$where = $this->pk."='".$id_sucursal."'";
			$data = array(
				"direccion"=>$direccion,
				"nombre"=>$nombre,
				"telefono" =>$telefono,
				"correo" =>  $correo,
			);
			$tabla_conf="config_pos";
			$data_tik = array(
				"header1"=>$h1,
				"header2"=>$h2,
				"header3"=>$h3,
				"header4"=>$h4,
				"header5"=>$h5,
				"header6"=>$h6,
				"header7"=>$h7,
				"header8"=>$h8,
				"header9"=>$h9,
				"header10"=>$h10,
				"footer1"=>$f1,
				"footer2"=>$f2,
				"footer3"=>$f3,
				"footer4"=>$f4,
				"footer5"=>$f5,
				"footer6"=>$f6,
				"footer7"=>$f7,
				"footer8"=>$f8,
				"footer9"=>$f9,
				"footer10"=>$f10,
			);
			$data_vale = array(
				"header1"=>$hv1,
				"header2"=>$hv2,
				"header3"=>$hv3,
				"header4"=>$hv4,
				"header5"=>$hv5,
				"header6"=>$hv6,
				"header7"=>$hv7,
				"header8"=>$hv8,
				"header9"=>$hv9,
				"header10"=>$hv10,

				"footer1"=>$fv1,
				"footer2"=>$fv2,
				"footer3"=>$fv3,
				"footer4"=>$fv4,
				"footer5"=>$fv5,
				"footer6"=>$fv6,
				"footer7"=>$fv7,
				"footer8"=>$fv8,
				"footer9"=>$fv9,
				"footer10"=>$fv10,
			);
			$data_cort = array(
				"header1"=>$hc1,
				"header2"=>$hc2,
				"header3"=>$hc3,
				"header4"=>$hc4,
				"header5"=>$hc5,
				"header6"=>$hc6,
				"header7"=>$hc7,
				"header8"=>$hc8,
				"header9"=>$hc9,
				"header10"=>$hc10,

				"footer1"=>$fc1,
				"footer2"=>$fc2,
				"footer3"=>$fc3,
				"footer4"=>$fc4,
				"footer5"=>$fc5,
				"footer6"=>$fc6,
				"footer7"=>$fc7,
				"footer8"=>$fc8,
				"footer9"=>$fc9,
				"footer10"=>$fc10,
			);

			$this->utils->begin();
			$insert = $this->utils->update($this->table,$data,$where);
			if($insert){

			  $tabla_conf_dir="config_dir";
			  $data_conf_dir= array(
				  "id_sucursal"=>$id_sucursal,
				  "dir_print_script"=>$l1,
				  "shared_printer_matrix"=>$l2,
				  "shared_printer_pos"=>$l3,
				  "shared_printer_barcode"=>$l4,
			  );

				$where1 = "id_sucursal"."='".$id_sucursal."' ";
				$where1.= "AND alias_tipodoc='TIK'";
				$insert = $this->utils->update($tabla_conf,$data_tik,$where1);
				$where2 = "id_sucursal"."='".$id_sucursal."' ";
				$where2.= "AND alias_tipodoc='VALE'";
				$insert = $this->utils->update($tabla_conf,$data_vale,$where2);
				$where3 = "id_sucursal"."='".$id_sucursal."' ";
				$where3.= "AND alias_tipodoc='CORT'";
				$insert = $this->utils->update($tabla_conf,$data_cort,$where3);

				$where4 = "id_sucursal"."='".$id_sucursal."' ";
				$insert = $this->utils->update($tabla_conf_dir,$data_conf_dir,$where4);

				$this->utils->commit();
				$xdatos["type"]="success";
				$xdatos['title']='Información';
				$xdatos["msg"]="Registo editado correctamente!";
			}
			else {
				$this->utils->rollback();
				$xdatos["type"]="error";
				$xdatos['title']='Alerta';
				$xdatos["msg"]="Error al editar el registro!";
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
			$active = $this->sucursal->get_state($id);
			$response = change_state($this->table,$this->pk,$id,$active);
			echo json_encode($response);
		}
	}

}

/* End of file Productos.php */
