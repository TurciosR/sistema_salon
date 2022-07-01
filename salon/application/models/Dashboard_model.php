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

class Dashboard_model extends CI_Model {

	//Retorna el saldo de vacaciones que tiene el colaborador
	function get_saldo($id_colaborador){
		$this->db->where('id_colaborador', $id_colaborador);
		$query = $this->db->get("vacaciones_colaboradores");
		if($query->num_rows()>0){
			return $query->row();
		}else{
			return 0;
		}
	}

	//Devuelve el dato de permisos en un rango de fechas
	function grafica_permiso($actual,$anterior){
		$this->db->select('COUNT("id_permiso") as total');
		$this->db->where('estado','APROBADA');
		$this->db->where("fecha_solicitud BETWEEN '$actual' AND '$anterior'");
		$query=$this->db->get('permisos');
		if($query->num_rows()>0){
			return $query->row();
		}else{
			return 0;
		}
	}

	//Devuelve el dato de vacaciones en un rango de fechas
	function grafica_vacacion($actual,$anterior){
		$this->db->select('COUNT("id_vacacion") as total');
		$this->db->where('estado','APROBADA');
		$this->db->where("fecha_solicitud BETWEEN '$actual' AND '$anterior'");
		$query=$this->db->get('vacaciones');
		if($query->num_rows()>0){
			return $query->row();
		}else{
			return 0;
		}
	}

	//Retorna las solicitudes pendientes de permisos del administrador
	function get_permisos_admin(){
		$this->db->select("DATE_FORMAT(p.fecha_solicitud,'%d-%m-%Y') as fecha");
		$this->db->select("TIME_FORMAT(p.hora_solicitud,'%H:%i %p') as hora");
		$this->db->select("CONCAT(c.nombre,' ',c.apellido) as colaborador");
		$this->db->select('u.nombre as unidad');
		$this->db->order_by('p.id_permiso', 'desc');
		$this->db->join('colaboradores as c', 'c.id_colaborador = p.id_colaborador');
		$this->db->join('unidades as u', 'u.id_unidad = c.id_unidad');
		$this->db->limit(5);
		$permisos=$this->db->get('permisos as p');
		if($permisos->num_rows()>0){
			return $permisos->result();
		}else{
			return 0;
		}
	}

	//Retorna las solicitudes pendientes de vacaciones del administrador
	function get_permisos($id_colaborador){
		$this->db->select("DATE_FORMAT(p.fecha_solicitud,'%d-%m-%Y') as fecha");
		$this->db->select("TIME_FORMAT(p.hora_solicitud,'%H:%i %p') as hora");
		$this->db->select("CONCAT(c.nombre,' ',c.apellido) as colaborador");
		$this->db->select('u.nombre as unidad');
		$this->db->order_by('p.id_permiso', 'desc');
		$this->db->where('c.jefe_inmediato', $id_colaborador);
		$this->db->or_where('c.jefe_superior', $id_colaborador);
		$this->db->join('colaboradores as c', 'c.id_colaborador = p.id_colaborador');
		$this->db->join('unidades as u', 'u.id_unidad = c.id_unidad');
		$this->db->limit(5);
		$permisos=$this->db->get('permisos as p');
		if($permisos->num_rows()>0){
			return $permisos->result();
		}else{
			return 0;
		}
	}

	//Retorna las solicitudes pendientes de permisos
	function get_vacaciones_admin(){
		$this->db->select("DATE_FORMAT(v.fecha_solicitud,'%d-%m-%Y') as fecha");
		$this->db->select("TIME_FORMAT(v.hora_solicitud,'%H:%i %p') as hora");
		$this->db->select("CONCAT(c.nombre,' ',c.apellido) as colaborador");
		$this->db->select('u.nombre as unidad');
		$this->db->order_by('v.id_vacacion', 'desc');
		$this->db->join('colaboradores as c', 'c.id_colaborador = v.id_colaborador');
		$this->db->join('unidades as u', 'u.id_unidad = c.id_unidad');
		$this->db->limit(5);
		$query=$this->db->get('vacaciones as v');
		if($query->num_rows()>0){
			return $query->result();
		}else{
			return 0;
		}
	}

	//Retorna las solicitudes pendientes de vacaciones
	function get_vacaciones($id_colaborador){
		$this->db->select("DATE_FORMAT(v.fecha_solicitud,'%d-%m-%Y') as fecha");
		$this->db->select("TIME_FORMAT(v.hora_solicitud,'%H:%i %p') as hora");
		$this->db->select("CONCAT(c.nombre,' ',c.apellido) as colaborador");
		$this->db->select('u.nombre as unidad');
		$this->db->order_by('v.id_vacacion', 'desc');
		$this->db->join('colaboradores as c', 'c.id_colaborador = v.id_colaborador');
		$this->db->join('unidades as u', 'u.id_unidad = c.id_unidad');
		$this->db->limit(5);
		$query=$this->db->get('vacaciones as v');
		if($query->num_rows()>0){
			return $query->result();
		}else{
			return 0;
		}
	}

	//Retorna el saldo pendiente de vacaciones
	function get_saldo_dias($id_colaborador){
		$this->db->select('SUM(saldo) as num');
		$this->db->where('id_colaborador', $id_colaborador);
		$query=$this->db->get('vacaciones_colaboradores');
		if($query->num_rows()>0){
			return $query->row();
		}else{
			return 0;
		}
	}

	//Retorna el conteo de solicitudes de permisos pendientes
	function get_solicitudes_permisos($id_colaborador){
		$this->db->select('COUNT(p.id_permiso) as num');
		$this->db->where('p.estado=', 'ESPERANDO REVISION');
		$this->db->where('c.jefe_inmediato', $id_colaborador);
		$this->db->or_where('c.jefe_superior', $id_colaborador);
		$this->db->join('colaboradores as c', 'c.id_colaborador = p.id_colaborador');
		$query=$this->db->get('permisos as p');
		if($query->num_rows()>0){
			return $query->row();
		}else{
			return 0;
		}
	}

	//Retorna el conteo de solicitudes de vacaciones pendientes
	function get_solicitudes_vacaciones($id_colaborador){
		$this->db->select('COUNT(p.id_vacacion) as num');
		$this->db->where('p.estado=', 'ESPERANDO APROBACION');
		$this->db->where('c.jefe_inmediato', $id_colaborador);
		$this->db->or_where('c.jefe_superior', $id_colaborador);
		$this->db->join('colaboradores as c', 'c.id_colaborador = p.id_colaborador');
		$query=$this->db->get('vacaciones as p');
		if($query->num_rows()>0){
			return $query->row();
		}else{
			return 0;
		}
	}

	//Retorna el conteo de solicitudes de permisos pendientes de administrador
	function get_solicitudes_permisos_admin(){
		$this->db->select('COUNT(p.id_permiso) as num');
		$this->db->where('p.estado=', 'ESPERANDO REVISION');
		$query=$this->db->get('permisos as p');
		if($query->num_rows()>0){
			return $query->row();
		}else{
			return 0;
		}
	}

	//Retorna el conteo de solicitudes de vacaciones pendientes de administrador
	function get_solicitudes_vacaciones_admin(){
		$this->db->select('COUNT(v.id_vacacion) as num');
		$this->db->where('v.estado=', 'ESPERANDO APROBACIÓN');
		$query=$this->db->get('vacaciones as v');
		if($query->num_rows()>0){
			return $query->row();
		}else{
			return 0;
		}
	}

	//Retorna el historial de permisos
	function get_historial_permisos($id_colaborador){
		$this->db->select('COUNT(p.id_permiso) as num');
		$this->db->where('p.id_colaborador', $id_colaborador);
		$this->db->where('p.estado=', 'APROBADA');
		$this->db->where('YEAR(p.fecha_solicitud)', date("Y"));
		$query=$this->db->get('permisos as p');
		if($query->num_rows()>0){
			return $query->row();
		}else{
			return 0;
		}
	}

	//Retorna el historial de vacaciones
	function get_historial_vacaciones($id_colaborador){
		$this->db->select('COUNT(v.id_vacacion) as num');
		$this->db->where('v.id_colaborador', $id_colaborador);
		$this->db->where('v.estado=', 'APROBADA');
		$this->db->where('YEAR(v.fecha_solicitud)', date("Y"));
		$query=$this->db->get('vacaciones as v');
		if($query->num_rows()>0){
			return $query->row();
		}else{
			return 0;
		}
	}
	//otras consultas
	function get_one_row($tabla,$where){
		foreach ($where as $key => $value) {
			// code...
			$this->db->where($key, $value);
		}
		$data = $this->db->get($tabla);
		if ($data->num_rows() > 0) {
			return $data->row();
		} else {
			return 0;
		}
	}

}

/* End of file Dashboard_model.php */
