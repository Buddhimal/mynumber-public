<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mconsultantpool extends CI_Model{

	public $validation_errors = array();
	private $post = array();
	protected $table = "consultant_pool";

	function __construct()
	{
		parent::__construct();
		$this->load->model('mvalidation');
		$this->load->model('mdoctor');
	}


	public function get_consultant_for_clinic($clinic_id='')
	{
		$output=null;
		$consultant=$this->db
			->distinct()
			->select('consultant_id')
			->from($this->table)
			->where('clinic_id', $clinic_id)
			->where('is_deleted', 0)
			->where('is_active', 1)
			->get();

		foreach ($consultant->result() as $consultant_data) {
			$output[] = $this->mdoctor->get($consultant_data->consultant_id);
		}

		return $output;
	}

}
