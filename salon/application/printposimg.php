<?php
/* Este script es el que se redirecciona a localhost donde esta el printer
y debe haber un apache corriendo con soporte php
Agregar el usuario al grupo en debian
usermod -a -G lp www-data
Permisos al puerto
su -c 'chmod 777 /dev/usb/lp0'
*/
header("Access-Control-Allow-Origin: *");
const ESC = "\x1b";
require __DIR__ . '/vendor/mike42/escpos-php/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\EscposImage;
	
try {
    // Enter the device file for your USB printer here
 $connector = new FilePrintConnector("/dev/usb/lp0");
    //$connector = new FilePrintConnector("/dev/usb/lp1");
    //$connector = new FilePrintConnector("/dev/usb/lp2");

    /* Print a "Hello world" receipt" */
    $printer = new Printer($connector);
//$connector = new WindowsPrintConnector("ticket");
$printer = new Printer($connector);
$printer->setJustification(Printer::JUSTIFY_CENTER);

//cargar e imprimir el logo
$logo = EscposImage::load("logo/mega.png", false);
$printer->bitImage($logo);
$string="";
/*

$texto = strtoupper($_REQUEST['datosventa']);
$efectivo = $_REQUEST['efectivo'];
$cambio = $_REQUEST['cambio'];
$headers = $_REQUEST['headers'];
$footers = $_REQUEST['footers'];
$info = $_SERVER['HTTP_USER_AGENT'];
$msj_fin='GRACIAS POR SU COMPRA, VUELVA PRONTO !';

$line=str_repeat("_",20)."\n";
$line1=str_repeat("_",20)."\n";

$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");
$textoencodificado = str_replace($latinchars, $encoded, $texto);
list($empresa,$sucursal,$razonsocial,$giro,$nit,$nrc,$tiquete,$datos,$encab_tabla,$venta,$total,$totaltxt,$vendedor)=explode("|",$textoencodificado);
$empresa=trim($empresa)."\n";
$razonsocial=trim($razonsocial)."\n";
$sucursal=trim($sucursal)."\n";
$giro=trim($giro)."\n";

$head= str_replace($latinchars, $encoded, $headers);
$foot= str_replace($latinchars, $encoded, $footers);

list($h1,$h2,$h3,$h4,$h5,$h6,$h7,$h8,$h9,$h10 )=explode("|",$head);
list($f1,$f2,$f3,$f4,$f5,$f6,$f7,$f8,$f9,$f10 )=explode("|",$foot);
//iniciar string

$string="";
//DESCOMENTAR PARA BEMATECH
$line1=str_repeat("_",40)."\n";

$string.=chr(13).$h1."\n";
if($h2!='')
	$string.=chr(13).$h2."\n";
if($h3!='')
	$string.=chr(13).$h3."\n";
if($h4!='')
	$string.=chr(13).$h4."\n";
if($h5!='')
	$string.=chr(13).$h5."\n";
if($h6!='')
	$string.=chr(13).$h6."\n";
if($h7!='')
	$string.=chr(13).$h7."\n";
if($h8!='')
	$string.=chr(13).$h8."\n";
if($h9!='')
	$string.=chr(13).$h9."\n";
if($h10!='')
	$string.=chr(13).$h10."\n";
$string.=chr(13).$tiquete."\n"; //  Print text
$string.=chr(13).$line1; // Print text Line


$printer->text($string);
$string="";
$printer->setJustification(Printer::JUSTIFY_LEFT);

$string.=chr(13).$datos."\n"; // Print text
$string.=chr(13).$line1; // Print text
//COMENTAR PARA BEMATECH
//$string.=chr(13)."DESCRIPCION      CANT.   P.U.    SUBT. "."\n";
//COMENTAR PARA EPSON TMU 220
$string.=chr(13)."CANT. DESCRIPCION            P.U    SUBT"."\n";
$string.=chr(13).$line1; // Print text
$string.=chr(13).$venta; // Print text
$string.=chr(13).$line1; // Print text Line
$string.=chr(13).$total; // Print text


$printer->text($string);
$string="";
$printer->setJustification(Printer::JUSTIFY_CENTER);

if ($efectivo>0){
	$efectivo=sprintf("%.2f", $efectivo);
	$cambio=sprintf("%.2f", $cambio);
	$string.=chr(13)."\n"; // Print text
	$string.=chr(13)."EFECTIVO $ ".$efectivo."  CAMBIO   $ ".$cambio."\n"; // Print text
}

$string.=chr(13)."E = EXENTO G = GRAVADO \n"; // Print text

$string.=chr(13).$f1."\n";
if($f2!='')
	$string.=chr(13).$f2."\n";
if($f3!='')
	$string.=chr(13).$f3."\n";
if($f4!='')
	$string.=chr(13).$f4."\n";
if($f5!='')
	$string.=chr(13).$f5."\n";
if($f6!='')
	$string.=chr(13).$f6."\n";
if($f7!='')
	$string.=chr(13).$f7."\n";
if($f8!='')
	$string.=chr(13).$f8."\n";
if($f9!='')
	$string.=chr(13).$f9."\n";
if($f10!='')
	$string.=chr(13).$f10."\n";

for($n=0;$n<2;$n++){
	$string.=chr(13)."\n"; // Print text
}
*/
$string="prueba";
$printer->text($string);
$printer -> cut();
$printer -> close();

}
catch (Exception $e) {
	    echo "NO SE PUDO IMPRIMIR: " . $e -> getMessage() . "\n";
	}
?>
