<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'entities/EntityPublic.php');

class Mpublic extends CI_Model
{
	public $validation_errors = array();
	private $post = array();
	protected $table = "public";

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
			$this->post['first_name'] = ucwords($post_array['firstname']);

		if (isset($post_array['lastname']))
			$this->post['last_name'] = ucwords($post_array['lastname']);

		if (isset($post_array['address']))
			$this->post['address'] = ucwords($post_array['address']);

		if (isset($post_array['nic']))
			$this->post['nic'] = $post_array['nic'];

		if (isset($post_array['dob']))
			$this->post['dob'] = $post_array['dob'];

		if (isset($post_array['location']))
			$this->post['location'] = $post_array['location'];

		if (isset($post_array['telephone']))
			$this->post['telephone'] = $post_array['telephone'];

		if (isset($post_array['email']))
			$this->post['email'] = $post_array['email'];

		if (isset($post_array['patient_code']))
			$this->post['patient_code'] = $post_array['patient_code'];

		$this->post['device'] = (isset($post_array['device'])) ? $post_array['device'] : "Smart Phone";		
		$this->post['os'] = (isset($post_array['os'])) ? $post_array['os'] : "Android";
	}

	public function is_valid()
	{
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

		if (!(isset($this->post['address']) && $this->post['address'] != NULL && $this->post['address'] != '')) {
			array_push($this->validation_errors, 'Invalid Address.');
			$result = false;
		}

		if (!(isset($this->post['email']) && $this->mvalidation->email($this->post['email']))) {
			array_push($this->validation_errors, 'Invalid Email.');
			$result = false;
		}
		// elseif ($this->mvalidation->already_exists($this->table, 'email', $this->post['email']) == TRUE) {
		//     array_push($this->validation_errors, 'Email already registered.');
		//     $result = false;
		// }

		if (!(isset($this->post['telephone']) && $this->mvalidation->telephone($this->post['telephone']))) {
			array_push($this->validation_errors, 'Invalid Telephone.');
			$result = false;
		}

		if (!(isset($this->post['dob']) && $this->post['dob'] != NULL && $this->post['dob'] != '') ) {
			array_push($this->validation_errors, 'Invalid Birthday.');
			$result = false;
		}

		return $result;
	}


	public function create()
	{
		$result = null;

		$public_id = trim($this->mmodel->getGUID(), '{}');
		$this->post['id'] = $public_id;
		$this->post['is_deleted'] = 0;
		$this->post['is_active'] = 0;
		$this->post['updated'] = date("Y-m-d H:i:s");
		$this->post['created'] = date("Y-m-d H:i:s");
		$this->post['updated_by'] = $public_id;
		$this->post['created_by'] = $public_id;

		$this->mmodel->insert($this->table, $this->post);

		if ($this->db->affected_rows() > 0) {
			$result = $this->get($public_id);
		}

		return $result;
	}

	public function update($public_id)
	{
		$result = null;
		$update_data = array();

		$current_public_data = $this->get_record($public_id);

		if (isset($this->post['first_name']) && $this->post['first_name'] != $current_public_data->first_name)
			$update_data['first_name'] = $this->post['first_name'];

		if (isset($this->post['last_name']) && $this->post['last_name'] != $current_public_data->last_name)
			$update_data['last_name'] = $this->post['last_name'];

		if (isset($this->post['nic']) && $this->post['nic'] != $current_public_data->nic)
			$update_data['nic'] = $this->post['nic'];

		if (isset($this->post['telephone']) && $this->post['telephone'] != $current_public_data->telephone)
			$update_data['telephone'] = $this->post['telephone'];

		//		if (isset($this->post['email']) && $this->post['email'] != $current_public_data->email)
		//			$update_data['email'] = $this->post['email'];

		if (isset($this->post['location']) && $this->post['location'] != $current_public_data->location)
			$update_data['location'] = $this->post['location'];

		if (isset($this->post['patient_code']) && $this->post['patient_code'] != $current_public_data->patient_code)
			$update_data['patient_code'] = $this->post['patient_code'];

		if (isset($this->post['dob']) && $this->post['dob'] != $current_public_data->patient_code)
			$update_data['dob'] = $this->post['dob'];

		if (isset($this->post['address']) && $this->post['address'] != $current_public_data->patient_code)
			$update_data['address'] = $this->post['address'];

		if (sizeof($update_data) > 0) {
			$update_data['updated'] = date("Y-m-d H:i:s");
			$update_data['updated_by'] = $public_id;

			$this->db->where('id', $public_id);
			$this->db->update($this->table, $update_data);

			if ($this->db->affected_rows() > 0) {
				// update successful
				$result = $this->get($public_id);
			}
		}

		return $result;
	}

	public function update_firebase_id($public_id,$firebase_id)
	{
		$this->db
			->set('firebase_id', $firebase_id)
			->set('updated', date("Y-m-d H:i:s"))
			->where('id', $public_id)
			->update($this->table);
		return true;
	}


	public function update_mobile_mask($public_id, $mask){
		return $this->db
			->set('mobile_mask', $mask)
			->set('updated', date("Y-m-d H:i:s"))
			->where('id', $public_id)
			->update($this->table);
	}

	public function get_record($id)
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('id', $id);
		return $this->db->get()->row();
	}

	public function get($id)
	{
		$query_result = $this->get_record($id);
		//print_r($query_result);
		return new EntityPublic($query_result);
	}


	public function valid_public($id)
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
