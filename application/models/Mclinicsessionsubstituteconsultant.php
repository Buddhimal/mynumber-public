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


	public function set_data($post_array)
	{
		if (isset($post_array['date']))
			$this->post['clinic_date'] = $post_array['date'];
		if (isset($post_array['substitute']))
			$this->post['substitute'] = $post_array['substitute'];
	}

	public function is_valid()
	{
		unset($this->validation_errors);
		$this->validation_errors = array();

		$result = true;

		if (!(isset($this->post['clinic_date']) && $this->post['clinic_date'] != NULL && $this->post['clinic_date'] != '' &&  $this->mvalidation->valid_date($this->post['clinic_date']) == TRUE) ) {
			array_push($this->validation_errors, 'Invalid Date.');
			$result = false;
		}

		if (!(isset($this->post['substitute']) && $this->post['substitute'] != NULL && $this->post['substitute'] != '' && $this->mdoctor->valid_doctor($this->post['substitute'])==TRUE )) {
			array_push($this->validation_errors, 'Invalid Substitute..');
			$result = false;
		}

		return $result;
	}


	public function create($clinic_session_id)
	{
		$result = null;

		$id = trim($this->mmodel->getGUID(), '{}');

		$this->post['id'] = $id;
		$this->post['clinic_session_id'] = $clinic_session_id;
		$this->post['is_deleted'] = 0;
		$this->post['is_active'] = 1;
		$this->post['updated'] = date("Y-m-d H:i:s");
		$this->post['created'] = date("Y-m-d H:i:s");
		$this->post['updated_by'] = $id;
		$this->post['created_by'] = $id;

		$this->mmodel->insert($this->table, $this->post);

		if ($this->db->affected_rows() > 0) {
			$result = $this->get($id);
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

		$this->db->select('id,clinic_session_id,clinic_date,substitute');
		$this->db->from($this->table);
		$this->db->where('id', $id);
		return $this->db->get()->row();
	}

}
