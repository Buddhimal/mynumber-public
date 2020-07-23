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
        $this->db->select('id,id as topic,day,starting_time,end_time,off');
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
