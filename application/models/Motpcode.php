<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class Motpcode extends CI_Model
{
	public $validation_errors = array();
	private $post = array();
	protected $table = "otp_code";

	function __construct()
	{
		parent::__construct();
		$this->load->model('mvalidation');
		$this->load->model('mmodel');
		$this->load->model('mlogin');
		$this->load->library('Messagesender');

	}


	public function set_data($post_array)
	{
		if (isset($post_array['otp_code']))
			$this->post['otp_code'] = $post_array['otp_code'];
	}

	public function is_valid($clinic_id)
	{
		$result = true;

		if (!(isset($this->post['otp_code']) && $this->post['otp_code'] != NULL && $this->post['otp_code'] != '')) {
			array_push($this->validation_errors, "Invalid OTP code..");
			$result = false;
		} else if (!$this->check_otp_code($this->post['otp_code'], $clinic_id)) {
			array_push($this->validation_errors, "OTP code not matched..");
			$result = false;
		}

		return $result;
	}


	public function create($clinic_id, $mobile)
	{
		$otp_id = trim($this->mmodel->getGUID(), '{}');
		$this->post['id'] = $otp_id;
		$this->post['clinic_id'] = $clinic_id;
		$this->post['device_mobile'] = $mobile;
		$this->post['otp_code'] = $this->generateCode();
		$this->post['send_at'] = date("Y-m-d H:i:s");
		$this->post['expire_at'] = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($this->post['send_at'])));
		$this->post['is_confirmed'] = 0;
		$this->post['is_deleted'] = 0;
		$this->post['is_active'] = 1;
		$this->post['updated'] = date("Y-m-d H:i:s");
		$this->post['created'] = date("Y-m-d H:i:s");
		$this->post['updated_by'] = $otp_id;
		$this->post['created_by'] = $otp_id;

		if ($this->messagesender->send_otp($this->post['device_mobile'], $this->post['otp_code'])) {

			$this->mmodel->insert($this->table, $this->post);

			return ($this->db->affected_rows() > 0);
		}

		return false;
	}


	function check_otp_code($otp = '', $clinic_id = '')
	{
		$length = strlen($otp);

		if (is_numeric($otp) && $length == 6 && $this->get_otp_code($clinic_id) == $otp) {

			$this->db
				->set('is_confirmed', 1)
				->set('confirmed_at', date("Y-m-d H:i:s"))
				->set('updated', date("Y-m-d H:i:s"))
				->where('clinic_id', $clinic_id)
				->where('otp_code', $otp)
				->update($this->table);
			return true;
		}

		return false;
	}

	public function resend_otp($public_id = '')
	{
		$login_details = $this->mlogin->get_login_for_entity_not_confirm($public_id);

		if ($this->get_otp_code($public_id) == null) {

			if ($login_details != null) {

				return $this->create($public_id, $login_details->mobile);

			} else {
				return false;
			}
		} else {
			return $this->messagesender->send_otp($login_details->mobile, $this->get_otp_code($public_id));
		}

	}


	public function get_otp_code($clinic_id)
	{
		$query_result = $this->db
			->select('otp_code')
			->from($this->table)
			->where('clinic_id', $clinic_id)
			->where('expire_at >', date("Y-m-d H:i:s"))
			->where('is_confirmed', 0)
			->where('is_active', 1)
			->where('is_deleted', 0)
			->limit(1)
			->order_by('send_at', 'DESC')
			->get();

		if ($query_result->num_rows() > 0) {
			return $query_result->row()->otp_code;
		}
		return null;
	}

	private function get_record($id)
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('id', $id);
		return $this->db->get()->row();
	}

	public function generateCode()
	{
		$length = 6;
		$characters = "123456789";
		$charactersLength = strlen($characters);
		$code = '';
		for ($i = 0; $i < $length; $i++) {
			$code .= $characters[rand(0, $charactersLength - 1)];
		}
		return $code;
	}

}
