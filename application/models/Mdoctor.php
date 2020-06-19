<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'entities/EntityConsultant.php');

class Mdoctor extends CI_Model
{
	public $validation_errors = array();
	private $post = array();
	protected $table = "doctor";

	function __construct()
	{
		parent::__construct();
		$this->load->model('mvalidation');
	}

	public function get($id)
	{
		$query_result = $this->get_record($id);
		return new EntityConsultant($query_result);
	}

	private function get_record($id)
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('id', $id);
		return $this->db->get()->row();
	}

	public function get_consultants($clinic_id) {

		$output = null;

		$all_sessions = $this->db
			->select( '*' )
			->from( sprintf( "%s D", $this->table) )
			->join( 'consultant_pool P', 'P.consultant_id= D.id' )
			->where( sprintf( "P.clinic_id='%s' and D.is_deleted=0 and D.is_active=1", $clinic_id ) )
			->get();
		foreach($all_sessions->result() as $session_data) {
			$output[] = new EntityConsultant($session_data);
		}

		return $output;
	}

	function valid_doctor($id)
	{
		$this->db->select('id');
		$this->db->from($this->table);
		$this->db->where('id', $id);

		$result = $this->db->get();

		if ($result->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}
