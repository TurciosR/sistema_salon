<?php
/**
 * This file is part of the OpenPyme2.
 *
 * (c) Open Solution Systems <operaciones@tumundolaboral.com.sv>
 *
 * For the full copyright and license information, please refere to LICENSE file
 * that has been distributed with this source code.
 */

defined('BASEPATH') or exit('No direct script access allowed');
include APPPATH . 'libraries/Rasteformat.php';
class Configuracion extends CI_Controller
{
	//Funcion index para enviar los datos de configuracion a la vista
	public function index()
	{
		validar_session($this);
		$this->load->model('Config_model');
		$rows = $this->Config_model->get_data();
		$nombre_empresa = $rows->nombre_empresa;
		$direccion_empresa = $rows->direccion_empresa;
		$telefono_empresa = $rows->telefono_empresa;
		$correo_empresa = $rows->correo_empresa;
		$web_empresa = $rows->web_empresa;
		$logo_empresa = $rows->logo_empresa;
		$data = array(
			'titulo' => "Configuración General",
			'nombre_empresa' => $nombre_empresa,
			'direccion_empresa' => $direccion_empresa,
			'telefono_empresa' => $telefono_empresa,
			'correo_empresa' => $correo_empresa,
			'web_empresa' => $web_empresa,
			'logo_empresa' => $logo_empresa,
		);
		$extras = array(
			'css' => array(),
			'js' => array(
				"js/scripts/general.js"
			),
		);
		layout('config/config',$data,$extras);
	}
	//Mediante la funcion cambios, guardamos los cambios enviados desde la vista
	public function cambios(){
		$this->load->model('UtilsModel');
		//$this->load->helper('utilities_helper');
		$nombre = $_POST["nombre"];
		$direccion = $_POST["direccion"];
		$telefono = $_POST["telefono"];
		$correo = $_POST["email"];
		$web = $_POST["web"];

		//Verificamos que la imagen sea distinta
		if ($_FILES["logo"]["name"] != "") {

			//Configuracion para los valores de la imagen de subir
			$_FILES['file']['name'] = $_FILES['logo']['name'];
			$_FILES['file']['type'] = $_FILES['logo']['type'];
			$_FILES['file']['tmp_name'] = $_FILES['logo']['tmp_name'];
			$_FILES['file']['error'] = $_FILES['logo']['error'];
			$_FILES['file']['size'] = $_FILES['logo']['size'];
			$config['upload_path'] = "./assets/img/";
			$config['allowed_types'] = 'jpg|jpeg|png|bmp|pbm';
			$info = new SplFileInfo( $_FILES['logo']['name']);
			$name0 = uniqid(date("dmYHi"));
			$name = $name0.".".$info->getExtension();
			$config['file_name'] = $name;
			$this->upload->initialize($config);
			$this->load->library('upload', $config);

			//Subimos la imagen al servidor
			if ($this->upload->do_upload('file')){
				$url = 'assets/img/'.$name;
				// convert to imagemagick
				$_FILES['file2']['name'] = $name0;
				$_FILES['file2']['type'] = 'bmp';
				$_FILES['file2']['tmp_name'] = $name0;
				$config['upload_path'] = "./assets/img/";
				$config['allowed_types'] = 'jpg|jpeg|png|bmp|pbm';
				$name1 = $name0.".pbm";
				$this->upload->do_upload('file2');
 				// end convert|
				$im=new Rasteformat();
			   $imagen= $im->init($url);

				//END CONVERT
				$table = "configuracion";
				$form_data = array(
					"nombre_empresa"=>$nombre,
					"direccion_empresa"=>$direccion,
					"telefono_empresa"=>$telefono,
					"correo_empresa"=>$correo,
					"web_empresa"=>$web,
					"logo_empresa"=>$url,
					"logoprintick"=>$name1, //add to pbm
				);
				$where = "id_configuracion=1";
				$insertar = $this->UtilsModel->update($table,$form_data,$where);
				if($insertar){
					$xdatos["type"]="success";
					$xdatos["title"]="Aviso";
					$xdatos["msg"]="Datos Actualizados";
				}else
				{
					$xdatos["type"]="error";
					$xdatos["title"]="Aviso";
					$xdatos["msg"]="Error al actualizar los datos";
				}
			}else{
				$xdatos["type"]="error";
				$xdatos["title"]="Aviso";
				$xdatos["msg"]="Error al actualizar los datos";
			}
		}
		//Si en caso no viene imagen, se guardan los cambios
		else{
			$table = "configuracion";
			$form_data = array(
				"nombre_empresa"=>$nombre,
				"direccion_empresa"=>$direccion,
				"telefono_empresa"=>$telefono,
				"correo_empresa"=>$correo,
				"web_empresa"=>$web,
			);
			$where = "id_configuracion=1";
			$insertar = $this->UtilsModel->update($table,$form_data,$where);

			if($insertar){
				$xdatos["type"]="success";
				$xdatos["title"]="Aviso";
				$xdatos["msg"]="Datos Actualizados";
			}else
			{
				$xdatos["type"]="error";
				$xdatos["title"]="Aviso";
				$xdatos["msg"]="Error al actualizar los datos";
			}
		}
		echo json_encode($xdatos);
	}
	public function toRasterFormat($im) {
		$im -> setFormat('pbm');
		$blob = $im -> getImageBlob();
		$i = strpos($blob, "\n", 3); // Find where header ends
		return substr($blob, $i + 1);
	}
}
