<?php

if (!function_exists('layout')) {
	function layout($view, $view_data = array(), $extras = array()) {
        $ci =& get_instance();
		validar_session($ci);
        $ci->load->model('Menu_model');
		$usuario = $ci->session->usuario;
		$nombre = $ci->session->nombre;
		$id_usuario = $ci->session->id_usuario;
		$admin = $ci->session->admin;
		$super = $ci->session->super_admin;
		if($super==1){
			$menus = $ci->Menu_model->get_menu_super_admin($id_usuario);
			$modulos_base = $ci->Menu_model->get_controller_super_admin($id_usuario);
		}
		else{
			$menus = $ci->Menu_model->get_menu($id_usuario,$admin);
			$modulos_base = $ci->Menu_model->get_controller($id_usuario,$admin);
		}
		$modulos = array();
		if($menus!=false && $modulos_base!=false){
			foreach ($menus as $menu)
			{
				$id_menu = $menu->id_menu;
				$modulos[$id_menu] = array_filter($modulos_base, function($modulo) use ($id_menu)
				{
					return $modulo->id_menu == $id_menu;
				});
			}
			$menu_data = array(
				'menus'=>$menus,
				'modulos' => $modulos,
				'usuario' => $usuario,
				'nombre' => $nombre,
			);
		}else{
			$menu_data = array(
				'usuario' => $usuario,
				'nombre' => $nombre,
			);
		}
		$ci->load->view('template/header',$extras);
		$ci->load->view('template/menu',$menu_data);
		$ci->load->view($view, $view_data);
		$ci->load->view('template/footer');
		$ci->load->view('template/scripts',$extras);
        return true;
	}
}

?>
