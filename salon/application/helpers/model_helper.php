<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists("allowed_to_use")) {
	function allowed_to_use($fields = array(), &$data = array())
	{
		foreach ($data as $key => $val) {
			$data[$key] = remove_invisible_characters($data[$key]);
			$data[$key] = strip_tags($data[$key]);

			if (array_search($key, $fields) === FALSE) {
				unset($data[$key]);
			}
		}
	}
}

if (!function_exists("toArray")) {
	function toArray($data)
	{
		if (is_object($data)) {
			$data = get_object_vars($data);
		}
		if (is_array($data)) {
			return array_map('toArray', $data);
		} else {
			return $data;
		}
	}
}

if (!function_exists("parse")) {
	function parse(&$array = array()){

		$array = toArray($array);

		foreach ($array as $key => $value){
			if(is_numeric($value)){
				$array[$key] = (float)$value;
			}else if(is_array($value)){
				parse($array[$key]);
			}
		}
	}
}


if (!function_exists("url")) {
	function url($field, &$array){

		$array = toArray($array);

		if(is_array($array)){
			foreach ($array as $key => $value){
				$value = toArray($value);
				foreach ($value as $key2 => $value2){
					if($key2 == $field){
						$array[$key][$field] = base_url($value2);
					}
				}
			}
		}else{
			foreach ($array as $key => $value){
				if($key == $field){
					$array[$field] = base_url($value);
				}
			}
		}
	}
}
