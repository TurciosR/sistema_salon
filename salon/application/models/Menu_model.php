<?php
/**
 * This file is part of the OpenPyme2.
 *
 * (c) Open Solution Systems <operaciones@tumundolaboral.com.sv>
 *
 * For the full copyright and license information, please refere to LICENSE file
 * that has been distributed with this source code.
 */

class Menu_model extends CI_Model {

	function get_menu($id_usuario,$admin)
	{
		$this->db->select('menu.*');
		$this->db->order_by('menu.prioridad', 'ASC');;
		$this->db->where("menu.visible", 1);
    $this->db->where("menu.admin", 0);
		if($admin==0){
			$this->db->where('permisos_usuario.id_usuario', $id_usuario);
			$this->db->join('modulo', 'modulo.id_menu = menu.id_menu', 'left');
			$this->db->join('permisos_usuario', 'permisos_usuario.id_modulo = modulo.id_modulo','left');
		}
		$this->db->group_by('menu.id_menu');
		$query = $this->db->get("menu");
		if ($query->num_rows() > 0)	return $query->result();
		return false;
	}

	function get_controller($id_usuario,$admin)
	{
		if($admin==0){
			$this->db->where('permisos_usuario.id_usuario', $id_usuario);
			$this->db->join('permisos_usuario', 'permisos_usuario.id_modulo = modulo.id_modulo');
		}
		$this->db->where("modulo.mostrarmenu", 1);
		$this->db->where("modulo.admin", 0);
		$query = $this->db->get("modulo");
		if ($query->num_rows() > 0)	return $query->result();
		return false;
	}

	function get_menu_super_admin($id_usuario)
	{
		$this->db->select('menu.*');
		$this->db->order_by('menu.prioridad', 'ASC');;
		$this->db->where("menu.visible", 1);
        $this->db->join('modulo', 'modulo.id_menu = menu.id_menu', 'left');
		$this->db->group_by('menu.id_menu');
		$query = $this->db->get("menu");
		if ($query->num_rows() > 0)	return $query->result();
		return false;
	}

	function get_controller_super_admin($id_usuario)
	{
		$this->db->where("modulo.mostrarmenu", 1);
		$query = $this->db->get("modulo");
		if ($query->num_rows() > 0)	return $query->result();
		return false;
	}

	function permissions_user($id, $file){
		$this->db->join('modulo as m', 'm.id_modulo = p.id_modulo');
		$this->db->where("m.filename", $file);
		$this->db->where("p.id_usuario", $id);
		$query = $this->db->get("permisos_usuario as p");
		if ($query->num_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}
}
