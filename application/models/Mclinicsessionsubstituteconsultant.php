<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mclinicsessionsubstituteconsultant  extends CI_Model{

	public $validation_errors = array();
	private $post = array();
	protected $table = "clinic_session_substitute_consultant";

	function __construct()
	{
		parent::__construct();
		$this->load->model('mvalidation');
	}


	public function get($id)
	{

		$query_result = $this->get_record($id);

		return $query_result;
	}

	private function get_record($id)
	{

		$this->db->select('id,clinic_session_id,clinic_date,substitute');
		$this->db->from($this->table);
		$this->db->where('id', $id);
		return $this->db->get()->row();
	}

}
