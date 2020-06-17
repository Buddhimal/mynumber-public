<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'entities/EntityClinic.php');

class Mclinic extends CI_Model
{
    public $validation_errors = array();
    private $post = array();
    protected $table = "clinic";

    function __construct()
    {
        parent::__construct();
        $this->load->model('mvalidation');
    }


    public function set_data($post_array)
    {
        if (isset($post_array['name']))
            $this->post['clinic_name'] = $post_array['name'];
        if (isset($post_array['contact_telephone']))
            $this->post['contact_telephone'] = $post_array['contact_telephone'];
        if (isset($post_array['contact_mobile']))
            $this->post['contact_mobile'] = $post_array['contact_mobile'];
        if (isset($post_array['device_mobile']))
            $this->post['device_mobile'] = $post_array['device_mobile'];
        if (isset($post_array['email']))
            $this->post['email'] = $post_array['email'];
        if (isset($post_array['web']))
            $this->post['web'] = $post_array['web'];
    }

    public function is_valid()
    {
        $result = true;

        if (!(isset($this->post['clinic_name']) && $this->post['clinic_name'] != NULL && $this->post['clinic_name'] != '')) {
            array_push($this->validation_errors, 'Invalid Clinic Name.');
            $result = false;
        }

        if (!(isset($this->post['email']) && $this->mvalidation->email($this->post['email']))) {
            array_push($this->validation_errors, 'Invalid Email.');
            $result = false;
        } elseif ($this->mvalidation->already_exists($this->table, 'email', $this->post['email']) == TRUE) {
            array_push($this->validation_errors, 'Email already registered.');
            $result = false;
        }

        if (!(isset($this->post['device_mobile']) && $this->mvalidation->telephone($this->post['device_mobile']))) {
            array_push($this->validation_errors, 'Invalid Telephone.');
            $result = false;
        }
        
        //		elseif ($this->mvalidation->already_exists($this->table, 'device_mobile', $this->post['device_mobile']) == TRUE) {
        //			array_push($this->validation_errors, 'Mobile already registered.');
        //			$result = false;
        //		}    //Uncomment when goes live. comment for testing purposes

        return $result;
    }


    public function create($location_id = NULL)
    {
        $result = null;

        $clinic_id = trim($this->mmodel->getGUID(), '{}');
        $this->post['id'] = $clinic_id;
        $this->post['location_id'] = $location_id;
        $this->post['is_deleted'] = 0;
        $this->post['is_active'] = 1;
        $this->post['updated'] = date("Y-m-d H:i:s");
        $this->post['created'] = date("Y-m-d H:i:s");
        $this->post['updated_by'] = $clinic_id;
        $this->post['created_by'] = $clinic_id;

        $this->mmodel->insert($this->table, $this->post);

        if ($this->db->affected_rows() > 0) {
            $result = $this->get($clinic_id);
        }

        return $result;
    }

    //	public function update($public_id)
        //	{
        //		$result = null;
        //		$update_data = array();
        //
        //		$current_public_data = $this->get_record($public_id);
        //
        //		if (isset($this->post['first_name']) && $this->post['first_name'] != $current_public_data->first_name)
        //			$update_data['first_name'] = $this->post['first_name'];
        //
        //		if (isset($this->post['last_name']) && $this->post['last_name'] != $current_public_data->last_name)
        //			$update_data['last_name'] = $this->post['last_name'];
        //
        //		if (isset($this->post['nic']) && $this->post['nic'] != $current_public_data->nic)
        //			$update_data['nic'] = $this->post['nic'];
        //
        //		if (isset($this->post['telephone']) && $this->post['telephone'] != $current_public_data->telephone)
        //			$update_data['telephone'] = $this->post['telephone'];
        //
        //		if (isset($this->post['email']) && $this->post['email'] != $current_public_data->email)
        //			$update_data['email'] = $this->post['email'];
        //
        //		if (isset($this->post['location']) && $this->post['location'] != $current_public_data->location)
        //			$update_data['location'] = $this->post['location'];
        //
        //		if (isset($this->post['patient_code']) && $this->post['patient_code'] != $current_public_data->patient_code)
        //			$update_data['patient_code'] = $this->post['patient_code'];
        //
        //		if (sizeof($update_data) > 0) {
        //			$update_data['updated'] = date("Y-m-d H:i:s");
        //			$update_data['updated_by'] = $public_id;
        //
        //			$this->db->where('id', $public_id);
        //			$this->db->update($this->table, $update_data);
        //
        //			if ($this->db->affected_rows() > 0) {
        //				// update successful
        //				$result = $this->get($public_id);
        //			}
        //		}
        //
        //		return $result;
    //	}

    private function get_record($id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('id', $id);
        return $this->db->get()->row();
    }

    public function get($id)
    {
        $query_result = $this->get_record($id);
        return new EntityClinic($query_result);
    }


    public function get_clinics_by_doctor_name($doctor_name)
    {
        $clinic_result = null;
        if ($doctor_name != '') {

            $res = $this->db->query("SELECT
                                        c.id as clinic_id,
                                        l.id as location_id,
                                        d.id as doctor_id
                                    FROM
                                        consultant_pool AS cp
                                        INNER JOIN doctor AS d ON cp.consultant_id = d.id
                                        INNER JOIN clinic AS c ON c.id = cp.clinic_id
                                        INNER JOIN locations AS l ON c.location_id = l.id
                                    WHERE 
                                    c.is_active = 1 AND
	                                c.is_deleted = 0 AND
	                                l.is_active = 1 AND
	                                l.is_deleted = 0 AND
	                                cp.is_active = 1 AND
	                                cp.is_deleted = 0 AND
                                    CONCAT(d.salutation,' ',d.first_name,' ',d.last_name) LIKE '%" . $doctor_name . "%'  ");

            foreach ($res->result() as $clinic_data) {
                $clinic = $this->mclinic->get($clinic_data->clinic_id);
                $clinic->location = $this->mlocations->get($clinic_data->location_id);
                $clinic->consultant = $this->mdoctor->get($clinic_data->doctor_id);

                $clinic_result[] = $clinic;
            }
        }
        return $clinic_result;
    }


    public function get_clinics_by_location($lat,$long)
    {

        $clinic = null;

        $parameters = array($lat, $long);

        $sql = "CALL `sp_get_nearby_locations`(?, ?)";
        $res = $this->db->query($sql,$parameters);

        foreach ($res->result() as $clinic_data) {
            $clinic[] = $clinic_data;
        }

        return $clinic;

    }


    public function valid_clinic($id)
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
