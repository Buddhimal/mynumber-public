<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class Mclinicholidays extends CI_Model
{
    public $validation_errors = array();
    private $post = array();
    protected $table = "clinic_holidays";

    function __construct()
    {
        parent::__construct();
        $this->load->model('mvalidation');
    }


    public function set_data($post_array)
    {
        if (isset($post_array['holiday']))
            $this->post['holiday'] = $post_array['holiday'];
        if (isset($post_array['title']))
            $this->post['title'] = $post_array['title'];
        if (isset($post_array['note']))
            $this->post['note'] = $post_array['note'];
    }

    public function is_valid()
    {
        unset($this->validation_errors);
        $this->validation_errors = array();

        $result = true;

        if (!(isset($this->post['holiday']) && $this->post['holiday'] != NULL && $this->post['holiday'] != '' && $this->mvalidation->valid_date($this->post['holiday']) == TRUE)) {
            array_push($this->validation_errors, 'Invalid Date format.');
            $result = false;
        }

        return $result;
    }

    /*
    *
    */
    public function create($clinic_id)
    {
        $result = null;

        $session_id = trim($this->mmodel->getGUID(), '{}');

        $this->post['id'] = $session_id;
        $this->post['clinic_id'] = $clinic_id;
        $this->post['is_deleted'] = 0;
        $this->post['is_active'] = 1;
        $this->post['updated'] = date("Y-m-d H:i:s");
        $this->post['created'] = date("Y-m-d H:i:s");
        $this->post['updated_by'] = $session_id;
        $this->post['created_by'] = $session_id;

        $this->mmodel->insert($this->table, $this->post);

        if ($this->db->affected_rows() > 0) {
            $result = $this->get($session_id);
        }

        return $result;
    }

    public function delete($clinic_id, $holiday_id)
    {
        $this->db
            ->set('is_active', 0)
            ->set('is_deleted', 1)
            ->set('updated', 1)
            ->set('updated', date("Y-m-d H:i:s"))
            ->where('clinic_id', $clinic_id)
            ->where('id', $holiday_id)
            ->update($this->table);

        return true;
    }


    public function get($id)
    {

        $query_result = $this->get_record($id);

        return $query_result;
    }

    private function get_record($id)
    {

        $this->db->select('id,holiday,title,note');
        $this->db->from($this->table);
        $this->db->where('id', $id);
        return $this->db->get()->row();
    }

    public function get_holidays($clinic_id, $year = null)
    {

        $output = array();
        $date = $year;
        if ($date == null) {
            $date = date("Y");
        }

        $start_date = date("Y-m-d", strtotime(sprintf("%s-01-01", $date)));
        $end_date = date("Y-m-d", strtotime(sprintf("%s-12-31", $date)));

        $all_holidays = $this->db
            ->select(array('id', 'holiday', 'title', 'note'))
            ->from($this->table)
            ->where(sprintf("holiday > '%s' and holiday < '%s' and clinic_id ='%s'", $start_date, $end_date, $clinic_id))
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get();

        foreach ($all_holidays->result() as $holiday_data) {
            $holiday['id'] = $holiday_data->id;
            $holiday['holiday'] = $holiday_data->holiday;
            $holiday['note'] = $holiday_data->note;
            $holiday['title'] = $holiday_data->title;
            $output[] = $holiday;
        }
        return $output;

    }

    public function valid_holiday($id)
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
