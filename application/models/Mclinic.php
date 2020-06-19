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
