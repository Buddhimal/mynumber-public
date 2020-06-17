<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'entities/EntityClinicSessionTask.php');

class Mclinicsessiontrans extends CI_Model
{

    public $validation_errors = array();
    private $post = array();
    protected $table = "clinic_session_trans";

    function __construct()
    {
        parent::__construct();
        $this->load->model('mvalidation');
    }


    public function set_data($post_array)
    {
        // $this->post = $post_array
    }

    public function is_valid()
    {
        $result = true;

        /*
         Validation logics goes here
        */

        return $result;
    }

    /*
    *
    */

    public function start_session($session_id)
    {

        if ($this->check_session_already_updated($session_id, SessionStatus::START)) {

            $id = trim($this->mmodel->getGUID(), '{}');
            $this->post['id'] = $id;
            $this->post['clinic_date'] = date("Y-m-d");
            $this->post['clinic_session_id'] = $session_id;
            $this->post['action'] = SessionStatus::START;

            $additional_data['action'] = "start";
            $additional_data['action_datetime'] = date("Y-m-d H:i:s");

            $this->post['additional_data'] = json_encode($additional_data);
            $this->post['action_datetime'] = date("Y-m-d H:i:s");
            $this->post['is_deleted'] = 0;
            $this->post['is_active'] = 1;
            $this->post['updated'] = date("Y-m-d H:i:s");
            $this->post['created'] = date("Y-m-d H:i:s");
            $this->post['updated_by'] = $id;
            $this->post['created_by'] = $id;

            $this->mmodel->insert($this->table, $this->post);
        }

        return true;
    }

    //create two functions for start and finish because additional data can be changed in future
    public function finish_session($session_id)
    {
        if ($this->check_session_already_updated($session_id, SessionStatus::FINISHED)) {

            $id = trim($this->mmodel->getGUID(), '{}');
            $this->post['id'] = $id;
            $this->post['clinic_date'] = date("Y-m-d");
            $this->post['clinic_session_id'] = $session_id;
            $this->post['action'] = SessionStatus::FINISHED;

            $additional_data['action'] = "finished";
            $additional_data['action_datetime'] = date("Y-m-d H:i:s");

            $this->post['additional_data'] = json_encode($additional_data);
            $this->post['action_datetime'] = date("Y-m-d H:i:s");
            $this->post['is_deleted'] = 0;
            $this->post['is_active'] = 1;
            $this->post['updated'] = date("Y-m-d H:i:s");
            $this->post['created'] = date("Y-m-d H:i:s");
            $this->post['updated_by'] = $id;
            $this->post['created_by'] = $id;

            $this->mmodel->insert($this->table, $this->post);

        }

        return true;
    }

    public function create()
    {
        $result = false;

        return $result;
    }

    public function check_session_already_updated($session_id, $status)
    {

        $res = $this->db
            ->select('*')
            ->from($this->table)
            ->where('clinic_session_id', $session_id)
            ->where('clinic_date', date("Y-m-d"))
            ->where('action', $status)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get();
        return ($res->num_rows() == 0);
    }

    public function get_session_trans_by_action($session_id,$action)
    {
        $res=$this->db
            ->select('*')
            ->from($this->table)
            ->where('clinic_session_id',$session_id)
            ->where('action',$action)
            ->where('clinic_date',date("Y-m-d"))
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get();

        return $res->row();

    }

    public function get_sessions_tasks_completed_within($clinic_id, $from ){
        $output = null;
        
        $result_set = $this->db->select("t.*,s.session_name")
            ->from( 'clinic_session_trans as t' )
            ->join( "clinic_session as s", "s.id = t.clinic_session_id" )
            ->where("t.action", SessionStatus::FINISHED)
            ->where("t.action_datetime >= ", $from )
            ->where("t.action_datetime < ", date("Y-m-d") )
            ->where("t.is_deleted=0 and t.is_active=1")
            ->where("s.clinic_id", $clinic_id)
            ->get();

        // DatabaseFunction::last_query();

        if($result_set->num_rows() > 0 ){
            foreach ($result_set->result() as $session_data) {
                $output[] = new EntityClinicSessionTask($session_data);
            }
        }

        return $output;
    }

    public function get_sessions_tasks($clinic_id, $ids ){
        $output = null;
        
        $result_set = $this->db->select("t.*")
            ->from( "clinic_session_trans as t" )
            ->join( "clinic_session as s", "s.id = t.clinic_session_id" )
            ->where_in("t.id ", $ids)
            ->where("s.clinic_id", $clinic_id)
            ->get();

        if($result_set->num_rows() > 0 ){
            foreach ($result_set->result() as $session_data) {
                $output[] = new EntityClinicSessionTask($session_data);
            }
        }

        return $output;
    }

}
