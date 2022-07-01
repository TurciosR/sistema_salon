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

class ErrorPage extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		validar_session($this);
	}
	public function index()
	{
		layout("404");
	}

}

/* End of file Controllername.php */
