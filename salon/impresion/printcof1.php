<?php
/* Este script es el que se redirecciona a localhost donde esta el printer
y debe haber un apache corriendo con soporte php
Agregar el usuario al grupo en debian
usermod -a -G lp www-data
Permisos al puerto
su -c 'chmod 777 /dev/usb/lp0'
*/
header("Access-Control-Allow-Origin: *");

$printer="/dev/Matrix1";

$total_letras = $_REQUEST['total_letras'];
$e= strtoupper($_REQUEST['encabezado']);
$c = strtoupper($_REQUEST['cuerpo']);
$p = strtoupper($_REQUEST['pie']);
$totales= $_REQUEST['totales'];
try {
	//iniciar string

$string="";

	$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü','°');
	$encoded = array("\xa5","A","E","I","O","U","\x9a","\xa5","A","E","I","O","U","\x9a","\xf8");
	
	$encabezado = str_replace($latinchars, $encoded, $e);
	$cuerpo = str_replace($latinchars, $encoded, $c);
	$pie = str_replace($latinchars, $encoded, $p);

	$string="";
	
	$string.= chr(27).chr(64); //clean config
	$string.= chr(27).chr(97).chr(0); //Left
	$string.= chr(27).chr(50); //espacio entre lineas 6 x pulgada
	//$string.= chr(27).chr(80); //10 cpi pica
	//$string.= chr(27).chr(77); //12 cpi pica
	$string.= chr(27).chr(33).chr(4); //FONT  Condensed
	
	$string.=chr(13).$encabezado."\n";
	$string.=chr(13).$cuerpo."\n";
	$string.=chr(13).$totales."\n";
	//$string.=chr(13).$total_letras."\n";

	$string.=chr(13).$pie."\n";

	$string.= chr(12); //page Feed


	//FIN ENVIO DATOS COMUN LINUX WIN
	//send data to USB printer
	$fp0=fopen($printer, 'wb');
	fwrite($fp0,$string);
	fclose($fp0);

}
 catch (Throwable $t) {
    // Executed only in PHP 7, will not match in PHP 5.x
     echo 'NO SE PUDO IMPRIMIR: " Excepción capturada: ',  $t->getMessage(), "\n";
} catch (Exception $e) {
    // Executed only in PHP 5.x, will not be reached in PHP 7
    echo 'NO SE PUDO IMPRIMIR: " Excepción capturada: ',  $e->getMessage(), "\n";
}
?>
