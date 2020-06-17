<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class Mserialnumber extends CI_Model
{
	public $validation_errors = array();
	private $post = array();
	protected $table = "serial_number";

	function __construct()
	{
		parent::__construct();
		$this->load->model('mvalidation');
	}


	public function set_data($post_array)
	{

	}

	public function is_valid()
	{
		$result = true;

		return $result;
	}


	public function create($location_id = NULL)
	{

	}


	public function get($id)
	{
		$query_result = $this->get_record($id);
		return $query_result;
	}


	private function get_record($id)
	{
		$this->db->select('id,serial_number');
		$this->db->from($this->table);
		$this->db->where('id', $id);
		return $this->db->get()->row();
	}




	public function valid_serial_number($id)
	{
		$this->db->select('id');
		$this->db->from($this->table);
		$this->db->where('id', $id);
		$this->db->where('is_deleted', 0);
		$this->db->where('is_active', 1);

		$result = $this->db->get();

		if ($result->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
