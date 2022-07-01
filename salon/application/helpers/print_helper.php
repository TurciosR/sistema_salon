<?php

defined('BASEPATH') OR exit('No direct script access allowed');
include APPPATH . 'libraries/AlignMarginText.php';
if(!function_exists("print_ticket")){
function print_ticket($id_venta,$id_sucursal){
  $ci =& get_instance();

	$info_factura="";
	//header print ticket
	$row_confpos=$ci->ventas->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));
	$info_factura.="DESCRIPCION     CANT.     P. UNIT    SUBTOT.\n";
	//encabezado de la venta
	$rowvta = $ci->ventas->get_one_row("ventas", array('id_venta' => $id_venta,));
	//detalles productos
	$detalleproductos = $ci->ventas->get_detail_ci($id_venta);
	$espacio="&nbsp;";
	$espacio=" ";
	$margen_izq1=AlignMarginText::leftmargin($espacio,2);
	$margen_izq2=AlignMarginText::leftmargin($espacio,10);
	if($detalleproductos !=NULL){
		foreach ($detalleproductos as $detalle){
			$id_producto = $detalle->id_producto;
			$descripcion=$detalle->marca." ".$detalle->modelo." ".$detalle->color;
			$descripcion=substr($descripcion,0,30);
			$precio_fin = $detalle->precio_fin;
			$cantidad = $detalle->cantidad;
			$subtotal = $detalle->subtotal;
			//AlignMarginText::onelineleft(texto,longitud,margin_izq,caracter_espacios);
			//AlignMarginText::rightaligner($input,$caracter = " ",$width)
			$desc = AlignMarginText::onelineleft($descripcion,30,1,$espacio);

			$pre=AlignMarginText::rightaligner($precio_fin,$espacio,12);
			$cant=AlignMarginText::rightaligner($cantidad,$espacio,12);
			$subt=AlignMarginText::rightaligner($subtotal,$espacio,12);
			$info_factura.=$desc." \n";
			$info_factura.=$margen_izq2.$cantidad.$margen_izq1.$pre.$margen_izq1.$subt." \n";

		}
	}
	//detalles servicios
	$detalleservicios = $ci->ventas->get_detail_serv($id_venta);
	if($detalleservicios !=NULL){
		foreach ($detalleservicios as $detalle){
			$id_producto = $detalle->id_producto;
			$descripcion=$detalle->nombre;
			$descripcion=substr($descripcion,0,30);
			$precio_fin = $detalle->precio_fin;
			$cantidad = $detalle->cantidad;
			$subtotal = $detalle->subtotal;
			$desc = AlignMarginText::onelineleft($descripcion,30,1,$espacio);
			//$espacio="#";
			$pre=AlignMarginText::rightaligner($precio_fin,$espacio,12);
			$cant=AlignMarginText::rightaligner($cantidad,$espacio,12);
			$subt=AlignMarginText::rightaligner($subtotal,$espacio,12);
			$info_factura.=$desc." \n";
			$info_factura.=$margen_izq2.$cantidad.$margen_izq1.$pre.$margen_izq1.$subt." \n";
		}
	}
	return $info_factura;
}
}
if(!function_exists("print_cof")){
function print_cof($id_venta,$id_sucursal){
	$ci =& get_instance();

	$info_factura="";
	//header print_cof
	$row_confpos=$ci->ventas->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));
	$info_factura.="DESCRIPCION  CANT.  P. UNIT    SUBTOT.\n|";
	//encabezado de la venta
	$rowvta = $ci->ventas->get_one_row("ventas", array('id_venta' => $id_venta,));
	//detalles productos
	$detalleproductos = $ci->ventas->get_detail_ci($id_venta);
	$espacio="&nbsp;";
	$espacio=" ";
	$margen_izq=AlignMarginText::leftmargin($espacio,2);
	if($detalleproductos !=NULL){
		foreach ($detalleproductos as $detalle){
			$id_producto = $detalle->id_producto;
			$descripcion=$detalle->marca." ".$detalle->modelo." ".$detalle->color;
			$precio_fin = $detalle->precio_fin;
			$cantidad = $detalle->cantidad;
			$subtotal = $detalle->subtotal;
			//AlignMarginText::onelineleft(texto,longitud,margin_izq,caracter_espacios);
			//AlignMarginText::rightaligner($input,$caracter = " ",$width)
			$desc = AlignMarginText::onelineleft($descripcion,20,1,$espacio);
			$pre=AlignMarginText::rightaligner($precio_fin,$espacio,12);
			$cant=AlignMarginText::rightaligner($cantidad,$espacio,12);
			$subt=AlignMarginText::rightaligner($subtotal,$espacio,12);
			$info_factura.=$desc.$margen_izq.$cantidad.$margen_izq.$pre.$margen_izq.$subt." \n";
		}
	}
	//detalles servicios
	$detalleservicios = $ci->ventas->get_detail_serv($id_venta);
	if($detalleservicios !=NULL){
		foreach ($detalleservicios as $detalle){
			$id_producto = $detalle->id_producto;
			$descripcion=$detalle->nombre;
			$precio_fin = $detalle->precio_fin;
			$cantidad = $detalle->cantidad;
			$subtotal = $detalle->subtotal;
			$desc = AlignMarginText::onelineleft($descripcion,20,1,$espacio);
			$pre=AlignMarginText::rightaligner($precio_fin,$espacio,12);
			$cant=AlignMarginText::rightaligner($cantidad,$espacio,12);
			$subt=AlignMarginText::rightaligner($subtotal,$espacio,12);
			$info_factura.=$desc.$margen_izq.$cantidad.$margen_izq.$pre.$margen_izq.$subt." \n";
		}
	}
	return $info_factura;
}
}
if(!function_exists("print_ccf")){
function print_ccf($id_venta,$id_sucursal){
$ci =& get_instance();
	$info_factura="";
	//header print_cof
	$row_confpos=$ci->ventas->get_one_row("config_dir", array('id_sucursal' => $id_sucursal,));
	$info_factura.="DESCRIPCION  CANT.  P. UNIT    SUBTOT.\n|";
	//encabezado de la venta
	$rowvta = $ci->ventas->get_one_row("ventas", array('id_venta' => $id_venta,));
	//detalles productos
	$detalleproductos = $ci->ventas->get_detail_ci($id_venta);
	$espacio="&nbsp;";
	$espacio=" ";
		$margen_izq=AlignMarginText::leftmargin($espacio,2);
	if($detalleproductos !=NULL){

		foreach ($detalleproductos as $detalle){
			$id_producto = $detalle->id_producto;
			$descripcion=$detalle->marca." ".$detalle->modelo." ".$detalle->color;
			$precio_fin = $detalle->precio_fin;
			$cantidad = $detalle->cantidad;
			$subtotal = $detalle->subtotal;
			//AlignMarginText::onelineleft(texto,longitud,margin_izq,caracter_espacios);
			//AlignMarginText::rightaligner($input,$caracter = " ",$width)
			$desc = AlignMarginText::onelineleft($descripcion,20,1,$espacio);
			$pre=AlignMarginText::rightaligner($precio_fin,$espacio,12);
			$cant=AlignMarginText::rightaligner($cantidad,$espacio,12);
			$subt=AlignMarginText::rightaligner($subtotal,$espacio,12);
			$info_factura.=$desc.$margen_izq.$cantidad.$margen_izq.$pre.$margen_izq.$subt." \n";
		}
	}
	//detalles servicios
	$detalleservicios = $ci->ventas->get_detail_serv($id_venta);
	if($detalleservicios !=NULL){
		foreach ($detalleservicios as $detalle){
			$id_producto = $detalle->id_producto;
			$descripcion=$detalle->nombre;
			$precio_fin = $detalle->precio_fin;
			$cantidad = $detalle->cantidad;
			$subtotal = $detalle->subtotal;
			$desc = AlignMarginText::onelineleft($descripcion,20,1,$espacio);
			$pre=AlignMarginText::rightaligner($precio_fin,$espacio,12);
			$cant=AlignMarginText::rightaligner($cantidad,$espacio,12);
			$subt=AlignMarginText::rightaligner($subtotal,$espacio,12);
			$info_factura.=$desc.$margen_izq.$cantidad.$margen_izq.$pre.$margen_izq.$subt." \n";
		}
	}
	return $info_factura;
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
if(!function_exists("generate_dt")){

    function generate_dt($model,$colums =  array() ){

        $ci =& get_instance();
        $input = $ci->input->post();
        $ci->load->model($model,"collection_model");

        $draw = intval($input["draw"]);
        $start = intval($input["start"]);
        $length = intval($input["length"]);

        $order = $input["order"];
        $search = $input["search"];
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

        if (!isset($colums[$col])) {
            $order = null;
        } else {
            $order = $colums[$col];
        }
        $row = $ci->collection_model->get_collection($order, $search, $colums, $length, $start, $dir);
        if($row!=NULL){
            return array("row"=>$row,"draw"=>$draw);
        }else{
            return 0;
        }

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
