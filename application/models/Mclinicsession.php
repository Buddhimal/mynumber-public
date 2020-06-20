<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'entities/EntityClinicSession.php');

class Mclinicsession extends CI_Model
{

    public $validation_errors = array();
    private $post = array();
    protected $table = "clinic_session";

    function __construct()
    {
        parent::__construct();
        $this->load->model('mvalidation');
        $this->load->model('mdoctor');
    }


    public function get($id)
    {
        $query_result = $this->get_record($id);

        return ($query_result);
    }

    public function get_full_session($session_id)
    {
        $query_result = $this->get_record($session_id);

        $sessions = new EntityClinicSession($query_result);
        $sessions->days = $this->mclinicsessiondays->get_days_by_session($session_id);
        $output[] = $sessions;

        return $output;
    }

    private function get_record($id)
    {
        $this->db->select('id,clinic_id,consultant,session_name,session_description,avg_time_per_patient,max_patients,');
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $this->db->where('is_deleted', 0);
        $this->db->where('is_active', 1);
        return $this->db->get()->row();
    }

    function valid_session($id)
    {
        $this->db->select('id');
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $this->db->where('is_deleted', 0);
        $this->db->where('is_active', 1);
        $result = $this->db->get();

        return ($result->num_rows() > 0);
    }

    public function get_sessions($clinic_id)
    {
        $output = null;

        $all_sessions = $this->db
            ->select('id,clinic_id,consultant,session_name,avg_time_per_patient,max_patients')
            ->from(sprintf("%s S", $this->table))
            ->where(sprintf("S.clinic_id='%s' and S.is_deleted=0 and S.is_active=1", $clinic_id))
            ->get();

        foreach ($all_sessions->result() as $session_data) {
            // $output[] = new EntityClinicSession($session_data);

            $result = $session_data;
            $result->days = $this->mclinicsessiondays->get_days_by_session($session_data->id);

            $output[] = $session_data;
        }
        return $output;
    }

//    public function get_sessions_for_day($clinic_id = '', $day = '')
//    {
//        $output = null;
//
//        $all_sessions = $this->db
//            ->select("c.*,d.day")
//            ->from('clinic_session as c')
//            ->join('clinic_session_days as d', 'd.session_id=c.id')
//            ->where(sprintf("c.clinic_id='%s' and c.is_deleted=0 and c.is_active=1 and d.is_deleted=0 and d.is_active=1", $clinic_id))
//            ->where('d.day', $day)
//            ->get();
//
////        DatabaseFunction::last_query();
//
//        foreach ($all_sessions->result() as $session_data) {
//            $sessions = new EntityClinicSession($session_data);
//            $sessions->days = $this->mclinicsessiondays->get_today_session($sessions->id, $day);
//            $sessions->days->appointment_count = $this->mclinicappointment->get_appointment_count_for_today($sessions->id);
//            $sessions->consultant = $this->mdoctor->get($sessions->consultant);
//            $output[] = $sessions;
//        }
//
//        return $output;
//    }
//
//    public function get_sessions_for_clinic($clinic_id = '')
//    {
//        $output = null;
//
//        $all_sessions = $this->db
//            ->select('c.*')
//            ->from('clinic_session as c')
//            // ->join('clinic_session_days as d', 'd.session_id=c.id')
//            ->where(sprintf("c.clinic_id='%s' and c.is_deleted=0 and c.is_active=1", $clinic_id))
//            ->get();
//
//        foreach ($all_sessions->result() as $session_data) {
//            $sessions = new EntityClinicSession($session_data);
//            $sessions->days = $this->mclinicsessiondays->get_days_by_session($sessions->id);
//            $sessions->consultant = $this->mdoctor->get($sessions->consultant);
//            $output[] = $sessions;
//        }
//
//        return $output;
//    }
//
//
//    public function get_sessions_for_date($clinic_id = '', $date = '')
//    {
//        if ($date == '') {
//            $day = date('N');
//        } else {
//            $day = date('N', strtotime($date));
//        }
//        return $this->get_sessions_for_day($clinic_id, $day);
//
//    }
//
//    public function get_sessions_for_consultant($clinic_id = '', $consultant_id = '')
//    {
//        $all_sessions = $this->db
//            ->select('*')
//            ->from($this->table)
//            ->where(sprintf("clinic_id='%s' and is_deleted=0 and is_active=1", $clinic_id))
//            ->where('consultant', $consultant_id)
//            ->get();
//
//        foreach ($all_sessions->result() as $session_data) {
//            $output[] = new EntityClinicSession($session_data);
//        }
//        return $output;
//    }
//
//    public function get_session_meta($clinic_id, $session_id)
//    {
//        $session_meta = null;
//
//        $day = date('N');
//
//        $session_meta['total_consulted'] = $this->mclinicappointment->get_appointment_count($session_id, AppointmentStatus::CONSULTED);
//        $session_meta['total_skipped'] = $this->mclinicappointment->get_appointment_count($session_id, AppointmentStatus::SKIPPED);
//        $session_meta['total_time_elapsed'] = $this->get_session_time_elapsed($session_id);
//        $session_meta['cumulative_amount'] = $this->mclinicappointment->get_cumulative_amount($session_id);
//        // cumulative_amount
//        return $session_meta;
//    }
//
//    public function get_session_time_elapsed($session_id)
//    {
//        $started_at = $this->mclinicsessiontrans->get_session_trans_by_action($session_id, SessionStatus::START)->action_datetime;
//        // $start_time = $this->mclinicsessiondays->get_today_session($session_id,date('N'))->starting_time;
//        $total_time_elapsed = strtotime(date('Y-m-d H:i:s')) - strtotime($started_at);
//        return gmdate("H:i:s", $total_time_elapsed);
//    }


    

    

}