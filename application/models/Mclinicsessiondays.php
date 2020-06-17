<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'entities/EntityClinicSession.php');

class Mclinicsessiondays extends CI_Model
{

    public $validation_errors = array();
    private $post = array();
    protected $table = "clinic_session_days";

    function __construct()
    {
        parent::__construct();
        $this->load->model('mvalidation');
        $this->load->model('mdoctor');
    }


    public function set_data($post_array)
    {
        if (isset($post_array['day']))
            $this->post['day'] = $post_array['day'];
        if (isset($post_array['off']))
            $this->post['off'] = $post_array['off'];
        if (isset($post_array['starting_time']))
            $this->post['starting_time'] = $post_array['starting_time'];
        if (isset($post_array['end_time']))
            $this->post['end_time'] = $post_array['end_time'];
    }

    public function is_valid()
    {
        unset($this->validation_errors);
        $this->validation_errors = array();

        $result = true;

		if (!(isset($this->post['day']) && $this->post['day'] != NULL && $this->post['day'] != '')) {
			array_push($this->validation_errors, 'Invalid Day.');
			$result = false;
		}

		if (!(isset($this->post['starting_time']) && $this->post['starting_time'] != NULL && $this->post['starting_time'] != '' && $this->mvalidation->valid_time($this->post['starting_time']) == TRUE)) {
			array_push($this->validation_errors, 'Invalid Start Time.');
			$result = false;
		}

		if (!(isset($this->post['end_time']) && $this->post['end_time'] != NULL && $this->post['end_time'] != '' && $this->mvalidation->valid_time($this->post['end_time']) == TRUE)) {
			array_push($this->validation_errors, 'Invalid Start Time.');
			$result = false;
		}

        return $result;
    }


    public function create($clinic_id,$session_id)
    {
        $result = null;

        $id = trim($this->mmodel->getGUID(), '{}');

        $this->post['id'] = $id;
        $this->post['clinic_id'] = $clinic_id;
        $this->post['session_id'] = $session_id;
        $this->post['is_deleted'] = 0;
        $this->post['is_active'] = 1;
        $this->post['updated'] = date("Y-m-d H:i:s");
        $this->post['created'] = date("Y-m-d H:i:s");
        $this->post['updated_by'] = $id;
        $this->post['created_by'] = $id;

        $this->mmodel->insert($this->table, $this->post);

        if ($this->db->affected_rows() > 0) {
            $result = $this->get($id);
        }

        return $result;
    }

    public function update($day_id)
    {
        $update_data = array();

        $current_session_days = $this->get_record($day_id);

        if (isset($this->post['day']) && $this->post['day'] != $current_session_days->day)
            $update_data['day'] = $this->post['day'];

        if (isset($this->post['off']) && $this->post['off'] != $current_session_days->off)
            $update_data['off'] = $this->post['off'];

        if (isset($this->post['starting_time']) && $this->post['starting_time'] != $current_session_days->starting_time)
            $update_data['starting_time'] = $this->post['starting_time'];

        if (isset($this->post['end_time']) && $this->post['end_time'] != $current_session_days->end_time)
            $update_data['end_time'] = $this->post['end_time'];

        if (sizeof($update_data) > 0) {
            $update_data['updated'] = date("Y-m-d H:i:s");
            $update_data['updated_by'] = $day_id;

            $this->db->where('id', $day_id);
            $this->db->update($this->table, $update_data);
        }

        return true;
    }


    public function get($id)
    {
        $query_result = $this->get_record($id);
        return $query_result;
    }

    private function get_record($id)
    {

        $this->db->select('id,day,starting_time,end_time,off');
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $this->db->where('is_deleted', 0);
        $this->db->where('is_active', 1);
        return $this->db->get()->row();
    }

    public function get_today_session($session_id,$day){
        $this->db->select('id,day,starting_time,end_time,off');
        $this->db->from($this->table);
        $this->db->where('session_id', $session_id);
        $this->db->where('day', $day);
        $this->db->where('is_deleted', 0);
        $this->db->where('is_active', 1);
        return $this->db->get()->row();
    }

    public function get_days_by_session($session_id){

        $output = null;

        $this->db->select('id,day,starting_time,end_time,off');
        $this->db->from($this->table);
        $this->db->where('session_id', $session_id);
        $this->db->where('is_deleted', 0);
        $this->db->where('is_active', 1);

        foreach ($this->db->get()->result() as $days) {
            $output[] = $days;
        }
        return $output;
    }


    function valid_session_day($id)
    {
        $this->db->select('id');
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $this->db->where('is_deleted', 0);
        $this->db->where('is_active', 1);
        $result = $this->db->get();

        return ($result->num_rows() > 0);
    }






}
