<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class Appointmentserialnumber extends CI_Model
{
	public $validation_errors = array();
	private $post = array();
	protected $table = "appointment_serial_number";

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

		unset($this->validation_errors);
		$this->validation_errors = array();

		$result = true;

		return $result;
	}

	public function create($patient_id, $session_id)
	{
		$result = null;

		$number_id = trim($this->mmodel->getGUID(), '{}');

		$this->db->trans_start();

        $number = $this->get_appointment_number($patient_id, $session_id);

		if (is_null($number)) {

			$this->post['id'] = $number_id;
			$this->post['patient_id'] = $patient_id;
			$this->post['session_id'] = $session_id;
			$this->post['is_deleted'] = 0;
			$this->post['is_active'] = 1;
			$this->post['is_confirmed'] = 0;
			$this->post['appointment_date'] = DateHelper::slk_date();
			$this->post['serial_number_id'] = $this->get_next_available_number($patient_id, $session_id);
			$this->post['issued_at'] = date("Y-m-d H:i:s");
			$this->post['expire_at'] = date('Y-m-d H:i:s', strtotime('+3 minutes', strtotime($this->post['issued_at'])));
			$this->post['updated'] = date("Y-m-d H:i:s");
			$this->post['created'] = date("Y-m-d H:i:s");
			$this->post['updated_by'] = $number_id;
			$this->post['created_by'] = $number_id;

			$this->db->insert($this->table, $this->post);

			if ($this->db->affected_rows() > 0) {
				$result = $this->get($number_id);
			}

		} else {
			return $number;
		}

		$this->db->trans_complete();
		return $result;
	}

	public function get_appointment_number($patient_id = '', $session_id = '')
	{
		$res = $this->db
			->select('id,	patient_id,	session_id,	serial_number_id,	appointment_date,	issued_at,	expire_at')
			->from($this->table)
			->where('patient_id', $patient_id)
			->where('session_id', $session_id)
			->where('appointment_date', DateHelper::slk_date())
			->where('expire_at >', date("Y-m-d H:i:s"))
			->where('is_confirmed', 0)
			->where('is_active', 1)
			->where('is_deleted', 0)
			->limit(1)
			->order_by('issued_at', 'DESC')
			->get()->row();

		return $res;
	}

	public function get_next_available_number($patient_id = '', $session_id = '')
	{
        $slk_date = DateHelper::slk_date();

		$res = $this->db
			->query("SELECT 
								sn.id AS serial_number_id,
								sn.serial_number 
							FROM
								serial_number AS sn 
							WHERE
								sn.is_active = 1 
								AND sn.is_deleted = 0 
								AND sn.id NOT IN (
								SELECT
									asn.serial_number_id 
								FROM
									appointment_serial_number AS asn 
								WHERE
									( asn.is_confirmed = 1 OR '" . date("Y-m-d  H:i:s") . "' < asn.expire_at   ) 
									AND asn.appointment_date = '" . $slk_date . "' 
									AND asn.is_active = 1 
									AND asn.is_deleted = 0 
									AND asn.session_id='$session_id'
								) 
							ORDER BY
								serial_number ASC 
								LIMIT 1")
			->row();
		return $res->serial_number_id;
	}

	public function confirm_number($appointment_serial_number_id)
	{
		$this->db
			->set('is_confirmed', SerialNumberStatus::CONFIRM)
			->set('confirmed', date("Y-m-d  H:i:s"))
			->set('updated', date("Y-m-d  H:i:s"))
			->where('id', $appointment_serial_number_id)
			->where('is_confirmed', SerialNumberStatus::PENDING)
			->update($this->table);

		return true;
	}


	public function get($id)
	{
		$query_result = $this->get_record($id);
		return $query_result;
	}

	private function get_record($id)
	{
		$this->db->select('id,	patient_id,	session_id,	serial_number_id,	appointment_date,	issued_at,	expire_at');
		$this->db->from($this->table);
		$this->db->where('id', $id);
		return $this->db->get()->row();
	}


	public function valid_serial_number($session_id,$serial_number_id)
	{

	}


}
