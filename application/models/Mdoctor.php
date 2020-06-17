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


	public function set_data($post_array)
	{
		if (isset($post_array['salutation']))
			$this->post['salutation'] = $post_array['salutation'];
		if (isset($post_array['firstname']))
			$this->post['first_name'] = $post_array['firstname'];
		if (isset($post_array['lastname']))
			$this->post['last_name'] = $post_array['lastname'];
		if (isset($post_array['nic']))
			$this->post['nic'] = $post_array['nic'];
		if (isset($post_array['known_name']))
			$this->post['known_name'] = $post_array['wellknownas'];
		if (isset($post_array['location']))
			$this->post['location'] = $post_array['location'];
		if (isset($post_array['contact_telephone']))
			$this->post['contact_telephone'] = $post_array['contact_telephone'];
		if (isset($post_array['email']))
			$this->post['email'] = $post_array['email'];
		if (isset($post_array['specialities']))
			$this->post['specialities'] = $post_array['specialities'];
		if (isset($post_array['doctor_code']))
			$this->post['doctor_code'] = $post_array['doctor_code'];
		if (isset($post_array['slmc_reg_number']))
			$this->post['slmc_reg_number'] = $post_array['slmc_reg_number'];
		if (isset($post_array['consulting_hospitals']))
			$this->post['consulting_hospitals'] = $post_array['consulting_hospitals'];
	}

	public function is_valid()
	{

		unset($this->validation_errors);
		$this->validation_errors = array();

		$result = true;


		if (!(isset($this->post['salutation']) && $this->post['salutation'] != NULL && $this->post['salutation'] != '')) {
			array_push($this->validation_errors, 'Invalid Salutation.');
			$result = false;
		}

		if (!(isset($this->post['first_name']) && $this->post['first_name'] != NULL && $this->post['first_name'] != '')) {
			array_push($this->validation_errors, 'Invalid First Name.');
			$result = false;
		}

		if (!(isset($this->post['last_name']) && $this->post['last_name'] != NULL && $this->post['last_name'] != '')) {
			array_push($this->validation_errors, 'Invalid Last Name.');
			$result = false;
		}

//		if (!(isset($this->post['slmc_reg_number']) && $this->post['slmc_reg_number'] != NULL && $this->post['slmc_reg_number'] != '')) {
//			array_push($this->validation_errors, 'Invalid SLMC Reg Number..');
//			$result = false;
//		}

		if ((isset($this->post['email']) && ! $this->mvalidation->email($this->post['email']))) {
			array_push($this->validation_errors, 'Invalid Email.');
			$result = false;
		}
//
//		if (!(isset($this->post['contact_telephone']) && $this->mvalidation->telephone($this->post['contact_telephone']))) {
//			array_push($this->validation_errors, 'Invalid Contact Mobile.');
//			$result = false;
//		}

		return $result;
	}


	public function create()
	{

		$result = null;

		$doctor_id = trim($this->mmodel->getGUID(), '{}');

		$this->post['id'] = $doctor_id;
		$this->post['is_deleted'] = 0;
		$this->post['is_active'] = 1;
		$this->post['updated'] = date("Y-m-d H:i:s");
		$this->post['created'] = date("Y-m-d H:i:s");
		$this->post['updated_by'] = $doctor_id;
		$this->post['created_by'] = $doctor_id;

		$this->mmodel->insert($this->table, $this->post);

		if ($this->db->affected_rows() > 0) {
			$result = $this->get($doctor_id);
		}

		return $result;
	}

	public function update($doctor_id)
	{
		$result = null;
		$update_data = array();

		$current_doctor_data = $this->get_record($doctor_id);

		if (isset($this->post['first_name']) && $this->post['first_name'] != $current_doctor_data->first_name)
			$update_data['first_name'] = $this->post['first_name'];

		if (isset($this->post['last_name']) && $this->post['last_name'] != $current_doctor_data->last_name)
			$update_data['last_name'] = $this->post['last_name'];

		if (isset($this->post['nic']) && $this->post['nic'] != $current_doctor_data->nic)
			$update_data['nic'] = $this->post['nic'];

		if (isset($this->post['contact_telephone']) && $this->post['contact_telephone'] != $current_doctor_data->contact_telephone)
			$update_data['contact_telephone'] = $this->post['contact_telephone'];

		if (isset($this->post['contact_mobile']) && $this->post['contact_mobile'] != $current_doctor_data->contact_mobile)
			$update_data['contact_mobile'] = $this->post['contact_mobile'];

		if (isset($this->post['device_mobile']) && $this->post['device_mobile'] != $current_doctor_data->device_mobile)
			$update_data['device_mobile'] = $this->post['device_mobile'];

		if (isset($this->post['email']) && $this->post['email'] != $current_doctor_data->email)
			$update_data['email'] = $this->post['email'];

		if (isset($this->post['known_name']) && $this->post['known_name'] != $current_doctor_data->wellknownas)
			$update_data['known_name'] = $this->post['wellknownas'];

		if (isset($this->post['location']) && $this->post['location'] != $current_doctor_data->location)
			$update_data['location'] = $this->post['location'];

		if (isset($this->post['specialities']) && $this->post['specialities'] != $current_doctor_data->specialities)
			$update_data['specialities'] = $this->post['specialities'];

		if (isset($this->post['doctor_code']) && $this->post['doctor_code'] != $current_doctor_data->doctor_code)
			$update_data['doctor_code'] = $this->post['doctor_code'];

		if (isset($this->post['slmc_reg_number']) && $this->post['slmc_reg_number'] != $current_doctor_data->slmc_reg_number)
			$update_data['slmc_reg_number'] = $this->post['slmc_reg_number'];

		if (isset($this->post['consulting_hospitals']) && $this->post['consulting_hospitals'] != $current_doctor_data->consulting_hospitals)
			$update_data['consulting_hospitals'] = $this->post['consulting_hospitals'];


		if (sizeof($update_data) > 0) {
			$update_data['updated'] = date("Y-m-d H:i:s");
			$update_data['updated_by'] = $doctor_id;

			$this->db->where('id', $doctor_id);
			$this->db->update($this->table, $update_data);

			if ($this->db->affected_rows() > 0) {
				// update successful
				$result = $this->get($doctor_id);
			}
		}

		return $result;
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
