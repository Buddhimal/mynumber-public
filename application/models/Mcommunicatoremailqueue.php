<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mcommunicatoremailqueue extends CI_Model{

    public $validation_errors = array();
    private $post = array();
    protected $table = "communicator_email_queue";

    function __construct()
    {
        parent::__construct();
        $this->load->model('mvalidation');
    }


    public function set_data($post_array)
    {
        if (isset($post_array['sender_name']))
            $this->post['sender_name'] = $post_array['sender_name'];
        if (isset($post_array['send_to']))
            $this->post['send_to'] = $post_array['send_to'];
        if (isset($post_array['template_id']))
            $this->post['template_id'] = $post_array['template_id'];
        if (isset($post_array['content']))
            $this->post['content'] = $post_array['content'];
        if (isset($post_array['email_type_id']))
            $this->post['email_type_id'] = $post_array['email_type_id'];
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
    public function create()
    {
        $email_id = trim($this->mmodel->getGUID(), '{}');
        $this->post['id'] = $email_id;
        $this->post['is_deleted'] = 0;
        $this->post['is_active'] = 1;
        $this->post['delivery_status'] = 0;
        $this->post['updated'] = date("Y-m-d H:i:s");
        $this->post['created'] = date("Y-m-d H:i:s");
        $this->post['updated_by'] = $email_id;
        $this->post['created_by'] = $email_id;

        $this->mmodel->insert($this->table, $this->post);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

}
