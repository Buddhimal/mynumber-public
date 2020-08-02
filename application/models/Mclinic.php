<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'entities/EntityClinic.php');
require_once(APPPATH . 'entities/EntityClinicSearchResult.php');

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
		$doctor_name=urldecode($doctor_name);

		$result = null;
		$output = null;
		if ($doctor_name != '') {

			$res = $this->db->query("SELECT
                                                c.id AS clinic_id,
                                                c.clinic_name,
                                                l.city,
                                                l.lat,
                                                l.long 
                                            FROM
                                                consultant_pool AS cp
                                                INNER JOIN doctor AS d ON cp.consultant_id = d.id
                                                INNER JOIN clinic AS c ON c.id = cp.clinic_id
                                                INNER JOIN locations AS l ON c.location_id = l.id 
                                            WHERE
                                                c.is_active = 1 
                                                AND c.is_deleted = 0 
                                                AND l.is_active = 1 
                                                AND l.is_deleted = 0 
                                                AND cp.is_active = 1 
                                                AND cp.is_deleted = 0 
                                                AND CONCAT( d.salutation, ' ', d.first_name, ' ', d.last_name ) LIKE '%" . $doctor_name . "%'  ");

			foreach ($res->result() as $clinic_data) {
				$result = new EntityClinicSearchResult($clinic_data);
				$result->session_count=$this->mclinicsession->get_session_count_for_today($result->clinic_id);
				$output[] = $result;
			}
		}
		return $output;
	}

	public function get_clinics_by_name($clinic_name)
	{
		$clinic_name=urldecode($clinic_name);

		$result = null;
		$output = null;
		if ($clinic_name != '') {

			$res = $this->db->query("SELECT
                                                c.id AS clinic_id,
                                                c.clinic_name,
                                                l.city,
                                                l.lat,
                                                l.long 
                                            FROM
                                                clinic AS c 
                                                INNER JOIN locations AS l ON c.location_id = l.id 
                                            WHERE
                                                c.is_active = 1 
                                                AND c.is_deleted = 0 
                                                AND l.is_active = 1 
                                                AND l.is_deleted = 0 
                                                AND c.clinic_name LIKE '%" . $clinic_name . "%'  ");

			foreach ($res->result() as $clinic_data) {
				$result = new EntityClinicSearchResult($clinic_data);
				$result->session_count=$this->mclinicsession->get_session_count_for_today($result->clinic_id);
				$output[] = $result;
			}
		}
		return $output;
	}


	public function get_clinics_by_location($lat, $long)
	{

		$result = null;
		$output = null;

		$parameters = array($lat, $long);

		$sql = "CALL `sp_get_nearby_locations`(?, ?)";
		$res = $this->db->query($sql, $parameters);

		foreach ($res->result() as $clinic_data) {
			$result[] = new EntityClinicSearchResult($clinic_data);
		}

		//to fix the Commands out of sync; issue
		$res->next_result();
		$res->free_result();

		foreach ($result as $res) {
			$res->session_count = $this->mclinicsession->get_session_count_for_today($res->clinic_id);
			$output[] = $res;
		}

		return $output;

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
