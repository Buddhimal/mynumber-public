<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'entities/EntityLocation.php');

class Mlocations extends CI_Model
{

    public $validation_errors = array();
    private $post = array();
    protected $table = "locations";

    function __construct()
    {
        parent::__construct();
        $this->load->model('mvalidation');
    }

    private
    function get_record($id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('id', $id);
        return $this->db->get()->row();
    }

    public
    function get($id)
    {
        $query_result = $this->get_record($id);
        return  new EntityLocation($query_result);
    }

    public
    function test_location()
    {
        $res = $this->db->query("select * from locations where id='1F25722A-64A4-4C1D-A23D-7418CF7F9269' ");

        var_dump($res->row()->long_lat);
    }


}
