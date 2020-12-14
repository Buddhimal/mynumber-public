<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class Minquiry extends CI_Model
{
	public $validation_errors = array();
	private $post = array();
	protected $table = "inquiry";

	function __construct()
	{
		parent::__construct();
		$this->load->model('mvalidation');
	}


	public function set_data($post_array)
	{
		if (isset($post_array['name']))
			$this->post['name'] = $post_array['name'];
		if (isset($post_array['email']))
			$this->post['email'] = $post_array['email'];
		if (isset($post_array['phone']))
			$this->post['phone'] = $post_array['phone'];
		if (isset($post_array['message']))
			$this->post['message'] = $post_array['message'];
	}

	public function create()
	{
		$result = null;

		$complaint_id = trim($this->mmodel->getGUID(), '{}');

		$this->post['id'] = $complaint_id;
		$this->post['is_deleted'] = 0;
		$this->post['is_active'] = 1;
		$this->post['updated'] = date("Y-m-d H:i:s");
		$this->post['created'] = date("Y-m-d H:i:s");

		$this->mmodel->insert($this->table, $this->post);

		if ($this->db->affected_rows() > 0) {
			$result = $this->get($complaint_id);
		}

		return $result;
	}

	public function get($id)
	{
		$query_result = $this->get_record($id);
		return $query_result;
	}

	private function get_record($id)
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('id', $id);
		return $this->db->get()->row();
	}
}
