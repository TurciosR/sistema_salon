<?php

use \Firebase\JWT\JWT;

class Authorization
{

	public static function generateToken($tokenData){

		$ci =& get_instance();

		$token = array(
			"iss" => base_url(),
			"aud" => base_url(),
			"exp" => $ci->config->item('expiration_time'),
			"iat" => strtotime("now"),
			"nbf" => strtotime("now"),
			"data" => $tokenData
		);

		// generate jwt
		return JWT::encode($token, $ci->config->item('jwt_key'));
	}

	public static function verifyToken(){
		$ci =& get_instance();
		$token = str_replace("Bearer ", "", $ci->input->get_request_header("Authorization"));
		$data = array();

		if(empty($token)){
			$data['hasError'] = TRUE;
			$data['message'] = "Unauthorized";
		}else{
			try{
				$data['data'] = JWT::decode($token, $ci->config->item('jwt_key'), array('HS256'))->data;
				$data['hasError'] = FALSE;
			}catch (\Firebase\JWT\BeforeValidException $e){
				$data['hasError'] = TRUE;
				$data['message'] = $e->getMessage();
			}catch (\Firebase\JWT\ExpiredException $e){
				$data['hasError'] = TRUE;
				$data['message'] = $e->getMessage();
			}catch (UnexpectedValueException $e){
				$data['hasError'] = TRUE;
				$data['message'] = $e->getMessage();
			}
		}

		if($data['hasError']){
			$data['code'] = UNAUTHORIZED;
			$data['data'] = self::pack(UNAUTHORIZED, $data['message']);
		}

		return $data;
	}

	private static function pack($code, $message){
		return array(
			"code" => $code,
			"message" => $message,
		);
	}

}
