<?php
/**
 * Clase que agrega margen y define longitud de un texto alineado a la izquierda
 * Soporte para PHP >= 5.4
 * @authorLuis Aguilar
 *
 */

class AlignMarginText
{
    public static function onelineleft( $text, $width = '80',$margin=0,  $caracter = " ",$break = '\n' ) {
  /*
	$wrappedarr = array();
	$wrappedtext = wordwrap( $text, $width, $break , true );
	$wrappedtext = trim( $wrappedtext );

	$arr = explode( $break, $wrappedtext );
  */
  $text=substr($text,0,$width);
	$out=self::addspaceright($text,$width,$margin,$caracter);
	return  $out;
	}
    private static function addspaceright($texto,$long,$margin,$caracter = " "){
		$countchars=0;
		$countch=0;
		$texto=trim($texto);
		$len_txt=strlen($texto);
		$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','Ñ','Á','É','Í','Ó','Ú','°');
		foreach($latinchars as $value){
			$countchars=substr_count($texto,$value);
			$countch= $countchars+$countch;
		}

		if($len_txt<=$long){
			if($countch>0)
			$n=($long+$countch)-$len_txt;
			else
			$n=$long-$len_txt;
			$texto_repeat=str_repeat($caracter,$n);

			$texto_salida=$texto.$texto_repeat;
		}
		else{
			$long=$long-1;
			$texto_salida=substr($texto,0,$long)." ";
		}
		$tamanio=$long+$margin;
		//echo($tamanio);
		$texto_sal=self::setmargin($texto_salida,$caracter,$tamanio,$margin);

		return $texto_sal;
	}
	private static function setmargin($texto,$caracter = " ",$long,$margin){
		$len_txt=strlen($texto);
		//$texto_salida=str_pad($texto, $long, $caracter,STR_PAD_LEFT );
		$texto_repeat=str_repeat($caracter,$margin);
		$texto_salida=$texto_repeat.$texto;
		return $texto_salida;
	}

	public static function rightalign($input,$caracter = " ",$width){
		return str_pad($input, $width, $caracter, STR_PAD_LEFT);
	}
  public static function rightaligner($input,$caracter = " ",$width){
      $len_txt=strlen($input);
      $diff=$width-$len_txt;
    	if($len_txt<=$width){
        $texto_repeat=str_repeat($caracter,$diff);
        $texto_salida=$texto_repeat.$input;
      }else{
    		$texto_salida=substr($input,0,$width);
    	}
      return $texto_salida;
  }
  public static function leftmargin($caracter = " ",$margin){
		$texto_salida=str_repeat($caracter,$margin);
		return $texto_salida;
	}
  public static  function wordwrap1( $text, $width = '80', $lines = '10', $break = '\n', $cut = 0 ) {
          $wrappedarr = array();
          $wrappedtext = wordwrap( $text, $width, $break , true );
          $wrappedtext = trim( $wrappedtext );
          $arr = explode( $break, $wrappedtext );
          return $arr;
          //return an array
  }

    public static function wordwrap2($string, $width = 80, $break = "\n") {
  // split on problem words over the line length
  $pattern = sprintf('/([^ ]{%d,})/', $width);
  $output = '';
  $words = preg_split($pattern, $string, -1, PREG_SPLIT_NO_EMPTY |     PREG_SPLIT_DELIM_CAPTURE);

    foreach ($words as $word) {
        if (false !== strpos($word, ' ')) {
            // normal behaviour, rebuild the string
            $output .= $word;
        } else {
            // work out how many characters would be on the current line
            $wrapped = explode($break, wordwrap($output, $width, $break));
            $count = $width - (strlen(end($wrapped)) % $width);

            // fill the current line and add a break
            $output .= substr($word, 0, $count) . $break;

            // wrap any remaining characters from the problem word
            $output .= wordwrap(substr($word, $count), $width, $break, true);
        }
    }
    //wrap over lines too long
      return $output;
  }

  public static function wordwrap3($input, $chars, $lines = false)
  {
    # the simple case - return wrapped words
    if(!$lines) return wordwrap($input, $chars, "\n");

    # truncate to maximum possible number of characters
    $retval = substr($input, 0, $chars * $lines);

    # apply wrapping and return first $lines lines
    $retval = wordwrap($retval, $chars, "\n");
    preg_match("/(.+\n?){0,$lines}/", $retval, $regs);
    return $regs[0];
    //limit the number of lines
  }
  public static function multiCol($string, $numcols)
  {
    $collength = ceil(strlen($string) / $numcols) + 3;
    $retval = explode("\n", wordwrap(strrev($string), $collength));
    if(count($retval) > $numcols) {
      $retval[$numcols-1] .= " " . $retval[$numcols];
      unset($retval[$numcols]);
    }
    $retval = array_map("strrev", $retval);
    return array_reverse($retval);
  }
}
?>
