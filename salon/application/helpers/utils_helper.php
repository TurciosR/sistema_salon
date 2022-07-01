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

if(!function_exists("encrypt")){
	function encrypt($val){
		$ci =& get_instance();
		return $ci->encryption->encrypt($val);
	}
}

if(!function_exists("decrypt")){
	function decrypt($val){
		$ci =& get_instance();
		return $ci->encryption->decrypt($val);
	}
}


if(!function_exists("get_code")){
	function get_code($length = 6)
	{
		$number = '0123456789';
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$numbersLength = strlen($number);
		$randomString = '';
		$randomString .= $characters[rand(0, $charactersLength - 1)];
		for ($i = 0; $i < $length; $i++)
		{
				$randomString .= $number[rand(0, $numbersLength - 1)];
		}
		$randomString .= $characters[rand(0, $charactersLength - 1)];
		return $randomString;
	}
}


if(!function_exists("parse_values")){
	function parse_values(&$data = array()){

		$data = stdToArray($data);

		foreach ($data as $key => $val){
			foreach ($val as $field => $val_field){
				if(is_numeric($val_field)){
					$data[$key][$field] = (float)$val_field;
				}
			}
		}
	}
}

if(!function_exists("stdToArray")){
	function stdToArray($obj){
		$reaged = (array)$obj;
		foreach($reaged as $key => &$field){
			if(is_object($field))$field = stdToArray($field);
		}
		return $reaged;
	}
}


if(!function_exists("safe_delete")){
	function safe_delete($table,$key,$id){
        $ci =& get_instance();
        $form = array(
            "activo" =>0,
            "deleted" =>1,
        );
        $where = $key."='".$id."'";
        $ci->utils->begin();
        $update = $ci->utils->update($table,$form,$where);
        if($update) {
            $ci->utils->commit();
            $response["type"] = "success";
            $response["title"] = "Información";
            $response["msg"] = "Registro eliminado con éxito!";
        }
        else {
            $ci->utils->rollback();
            $response["type"] = "Error";
            $response["title"] = "Alerta!";
            $response["msg"] = "Registro no pudo ser eliminado!";
        }
		return $response;
	}
}

if(!function_exists("delete")){
	function delete($table,$key,$id){
        $ci =& get_instance();
        $where = $key."='".$id."'";
        $ci->utils->begin();
        $delete = $ci->utils->delete($table,$where);
        if($delete) {
            $ci->utils->commit();
            $response["type"] = "success";
            $response["title"] = "Información";
            $response["msg"] = "Registro eliminado con éxito!";
        }
        else {
            $ci->utils->rollback();
            $response["type"] = "Error";
            $response["title"] = "Alerta!";
            $response["msg"] = "Registro no pudo ser eliminado!";
        }
		return $response;
	}
}

if(!function_exists("change_state")){
	function change_state($table,$key,$id,$active){
        $ci =& get_instance();
        if($active==0){
            $state = 1;
            $text = 'activado';
        }else{
            $state = 0;
            $text = 'desactivado';
        }
        $form = array(
            "activo" =>$state
        );
        $where = $key."='".$id."'";
        $ci->utils->begin();
        $update = $ci->utils->update($table,$form,$where);
        if($update) {
            $ci->utils->commit();
            $response["type"] = "success";
            $response["title"] = "Información";
            $response["msg"] = "Registro $text con exito!";
        }
        else {
            $ci->utils->rollback();
            $response["type"] = "Error";
            $response["title"] = "Alerta!";
            $response["msg"] = "Registro no pudo ser $text!";
        }
		return $response;
	}
}

if (!function_exists("generate_dt")) {

	/**
	 * Función para las etiquetas dt cuando se cargar dataTables la primera vez, o hacer
	 * busquedas en la caja de búsqueda (del dataTable), y así refrescar el total de registros
	 * de manera correcta
	 *
	 * @param class-string $model pase el modelo que quiera invocar
	 * @param array $options_dt pase las toda la información de la query
	 * @param bool $total si es false entonces retorna el array con los resultados de
	 * la query. Si es true entonces devuelve un integer con el total de la query (num_rows)
	 * @return array|int retorna la query de la búsqueda en un array. Si no encuentra datos
	 * entonces retornará cero. Si se utiliza $total a true devolverá un integer con el total
	 * de resultados o lo equivalente a $rows->num_rows()
	 */

    function generate_dt(string $model, array $options_dt = array(), bool $count)
    {
        // Singleton
        $ci     =& get_instance();
        $input  = $ci->input->post();
        $ci->load->model($model, "utils");

        $options_dt['start']  = intval($input["start"]);
        $options_dt['length'] = intval($input["length"]);
        $options_dt['order']  = $input["order"];
				$options_dt['search'] = $input["search"]['value'];
        $options_dt['col']    = 0;
        $options_dt['dir']    = "";

        if (!empty($options_dt['order'])) {
            foreach ($options_dt['order'] as $o) {
                $options_dt['col'] = $o['column'];
                $options_dt['dir'] = $o['dir'];
            }
        }

        if ($options_dt['dir'] != "asc" && $options_dt['dir'] != "desc") {
            $options_dt['dir'] = "desc";
        }

        if (!isset($options_dt['valid_columns'])) {
            $options_dt['order'] = NULL;
        } else {
            extract($options_dt);
            $options_dt['order'] = $options_dt['valid_columns'][$col];
        }

        $options_dt['count'] = $count;
        return $ci->utils->get_collection($options_dt);
    }
}

if(!function_exists("insert_row")){

    function insert_row($table,$form =  array()){
        $ci =& get_instance();
        $ci->load->model("UtilsModel","utils");
        $ci->utils->begin();
        $insert = $ci->utils->insert($table,$form);
        if($insert){
            $ci->utils->commit();
            $data["type"]="success";
            $data['title']='Información';
            $data["msg"]="Registo insertado correctamente!";
        }
        else {
            $ci->utils->rollback();
            $data["type"]="error";
            $data['title']='Alerta';
            $data["msg"]="Error al insertar el registro!";
        }
        return $data;
    }
}
if(!function_exists("edit_row")){

    function edit_row($table,$form =  array(),$where){
        $ci =& get_instance();
        $ci->load->model("UtilsModel","utils");
        $ci->utils->begin();
        $update = $ci->utils->update($table,$form,$where);
        if($update){
            $ci->utils->commit();
            $data["type"]="success";
            $data['title']='Información';
            $data["msg"]="Registo editado correctamente!";
        }
        else {
            $ci->utils->rollback();
            $data["type"]="error";
            $data['title']='Alerta';
            $data["msg"]="Error al editar el registro!";
        }
        return $data;
    }
}



if(!function_exists("Y_m_d"))
{
    function Y_m_d($fecha)
    {
        $a = substr($fecha,6,4);
        $mes = substr($fecha,3,2);
        $dia = substr($fecha,0,2);
        $fecha = "$a-$mes-$dia";
        return $fecha;
    }
}
if(!function_exists("d_m_Y"))
{
    function d_m_Y($fecha)
    {
        $a = substr($fecha,0,4);
        $mes = substr($fecha,5,2);
        $dia = substr($fecha,8,2);
        $fecha = "$dia-$mes-$a";
        return $fecha;
    }
}
if(!function_exists("hora_A_P"))
{
    function hora_A_P($hora)
    {
        $hora_pre = date_create($hora);
        $hora_pos = date_format($hora_pre, 'g:i A');
        return $hora_pos;
    }
}
if(!function_exists("quitar_tildes"))
{
    function quitar_tildes($cadena)
    {
        $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹"," ");
        $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E","_");
        $texto = str_replace($no_permitidas, $permitidas ,$cadena);
        return $texto;
    }
}
if(!function_exists("diferenciaDias"))
{
    function diferenciaDias($inicio, $fin)
    {
        $inicio = strtotime($inicio);
        $fin = strtotime($fin);
        $dif = $fin - $inicio;
        $diasFalt = (( ( $dif / 60 ) / 60 ) / 24);
        return ceil($diasFalt);
    }
}
if(!function_exists("divtextlin"))
{
    function divtextlin( $text, $width = '80', $lines = '10', $break = '\n', $cut = 0 ) {
        $wrappedarr = array();
        $wrappedtext = wordwrap( $text, $width, $break , true );
        $wrappedtext = trim( $wrappedtext );
        $arr = explode( $break, $wrappedtext );
        return $arr;
    }
}
if(!function_exists("array_procesor"))
{
    function array_procesor($array)
    {
        $ygg=0;
        $maxlines=1;
        $array_a_retornar=array();
        foreach ($array as $key => $value) {
            /*Descripcion*/
            $nombr=$value[0];
            /*character*/
            $longitud=$value[1];
            /*fpdf width*/
            $size=$value[2];
            /*fpdf alignt*/
            $aling=$value[3];
            if(strlen($nombr) > $longitud)
            {
                $i=0;
                $nom = divtextlin($nombr, $longitud);
                foreach ($nom as $nnon)
                {
                    $array_a_retornar[$ygg]["valor"][]=$nnon;
                    $array_a_retornar[$ygg]["size"][]=$size;
                    $array_a_retornar[$ygg]["aling"][]=$aling;
                    $i++;
                }
                $ygg++;
                if ($i>$maxlines) {
                    // code...
                    $maxlines=$i;
                }
            }
            else {
                // code...
                $array_a_retornar[$ygg]['valor'][]=$nombr;
                $array_a_retornar[$ygg]['size'][]=$size;
                $array_a_retornar[$ygg]["aling"][]=$aling;
                $ygg++;

            }
        }

        $ygg=0;
        foreach($array_a_retornar as $keys)
        {
            for ($i=count($keys["valor"]); $i <$maxlines ; $i++) {
                // code...
                $array_a_retornar[$ygg]["valor"][]="";
                $array_a_retornar[$ygg]["size"][]=$array_a_retornar[$ygg]["size"][0];
                $array_a_retornar[$ygg]["aling"][]=$array_a_retornar[$ygg]["aling"][0];
            }
            $ygg++;
        }
        return $array_a_retornar;

    }
}
if(!function_exists("dinero")){
    function dinero($dinero)
    {
        return number_format($dinero,"2",".",",");
    }
}
if(!function_exists("restar_meses")){
    function restar_meses($fecha, $cantidad)
    {
        $nuevafecha = strtotime ( '-'.$cantidad.' month' , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
        return $nuevafecha;
    }
}
if(!function_exists("sumar_meses")){
    function sumar_meses($fecha, $cantidad)
    {
        $nuevafecha = strtotime ( '+'.$cantidad.' month' , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
        return $nuevafecha;
    }
}
if(!function_exists("nombre_mes")){
    function nombre_mes($n){
        $mes = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
        return $mes[$n-1];
    }
}
if(!function_exists("edad_decimal")){
    function edad_decimal($fecha){
        $dob_day = substr($fecha,8,2);
        $dob_month = substr($fecha,5,2);
        $dob_year = substr($fecha,0,4);
        $year   = gmdate('Y');
        $month  = gmdate('m');
        $day    = gmdate('d');
        //seconds in a day = 86400
        $days = (mktime(0,0,0,$month,$day,$year) - mktime(0,0,0,$dob_month,$dob_day,$dob_year))/86400;
        return $days / 365.242199;
    }
}
if(!function_exists("salto")){
    function salto($lines,$n){
        $ln=$lines-$n;
        for($i=0;$i<$ln;$i++){
            echo "&nbsp;"."<br>";
        }
    }
}
if(!function_exists("img_exist")){
    function img_exist($url = NULL)
    {
        if (!$url) return FALSE;
        $rutaProd= base_url()."assets/";
        $noimage = 'img/productos/no_disponible.png';
        $noimage=$rutaProd."img/productos/no_disponible.png";
        $headers = get_headers($url);
        return stripos($headers[0], "200 OK") ? $url : $noimage;
    }
}

if(!function_exists("getLogo")){
    /**
     * Return logo pathgetLogo
     */
    function getLogo($absolute = false)
    {
        $ci =& get_instance();
        $ci->load->model("UtilsModel", "utils");
        if ($absolute) {
            return base_url() . $ci->utils->getLogo();
        }else {
            return $ci->utils->getLogo();

        }
    }
}
