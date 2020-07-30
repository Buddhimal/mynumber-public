<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class Mcomplaints extends CI_Model
{
    public $validation_errors = array();
    private $post = array();
    protected $table = "complaints";

    function __construct()
    {
        parent::__construct();
        $this->load->model('mvalidation');
    }


    public function set_data($post_array)
    {
        if (isset($post_array['app_name']))
            $this->post['app_name'] = $post_array['app_name'];
        if (isset($post_array['app_version']))
            $this->post['app_version'] = $post_array['app_version'];
        if (isset($post_array['mobile_version']))
            $this->post['mobile_version'] = $post_array['mobile_version'];
        if (isset($post_array['contact_number']))
            $this->post['contact_number'] = $post_array['contact_number'];
        if (isset($post_array['complaint_type']))
            $this->post['complaint_type'] = $post_array['complaint_type'];
        if (isset($post_array['complaint']))
            $this->post['complaint'] = $post_array['complaint'];
    }

    public function is_valid()
    {
    }

    public function create($public_id)
    {
        $result = null;

        $complaint_id = trim($this->mmodel->getGUID(), '{}');

        $this->post['id'] = $complaint_id;
        $this->post['status'] = 0;
        $this->post['entity_id'] = $public_id;
        $this->post['is_deleted'] = 0;
        $this->post['is_active'] = 1;
        $this->post['updated'] = date("Y-m-d H:i:s");
        $this->post['created'] = date("Y-m-d H:i:s");
        $this->post['updated_by'] = $complaint_id;
        $this->post['created_by'] = $complaint_id;

        $this->mmodel->insert($this->table, $this->post);

        if ($this->db->affected_rows() > 0) {
            $result = $this->get($complaint_id);
        }

        return $result;
    }

    public function get($id)
    {
        $query_result = $this->get_record($id);
        return $query_result;
    }

    private function get_record($id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('id', $id);
        return $this->db->get()->row();
    }
}
