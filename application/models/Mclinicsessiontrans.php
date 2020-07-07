<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'entities/EntityClinicSessionTask.php');

class Mclinicsessiontrans extends CI_Model
{

    public $validation_errors = array();
    private $post = array();
    protected $table = "clinic_session_trans";

    function __construct()
    {
        parent::__construct();
        $this->load->model('mvalidation');
    }

	public function get_last_states_of_session($session_id, $date)
	{
		$res = $this->db
			->select('action')
			->from($this->table)
			->where('clinic_session_id', $session_id)
			->where('clinic_date', $date)
			->where('is_active', 1)
			->where('is_deleted', 0)
			->order_by('created DESC, updated DESC')
			->limit(1)
			->get();

		if ($res->num_rows() > 0)
			return $res->row()->action;

		return SessionStatus::PENDING;
	}


}
