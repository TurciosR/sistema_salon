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

class Tests_encrypt extends CI_Controller {
	function encode()
	{
		echo "<title> Tutorial and Example Encrypt</title>";
		// $this->load->library('encrypt');
		$this->load->library('encryption');

		$data = "adm1n$";
		$key ="y/B?E(H+MbQeThWm5u8x/A?D(G+KbPeSn2r5u7x!A%D*G-KaTjWnZr4u7w!z%C*FbQeThWmZq4t7w9z$";
		// $encrypted_data = $this->encryption->encrypt($data,  ['key' => $key]);
		$encrypted_data = $this->encryption->encrypt($data);
		echo "<pre>$encrypted_data </pre>";
		$decrypted_data = $this->encryption->decrypt($encrypted_data );
		echo "<br>";
		echo "<pre>$decrypted_data </pre>";
	}
}

?>
