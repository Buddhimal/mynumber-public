<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


class Mappversion extends CI_Model
{

    public $validation_errors = array();
    private $post = array();
    protected $table = "app_version";

    function __construct()
    {
        parent::__construct();
        $this->load->model('mvalidation');
    }


    public function get_app_version($app_name)
    {

        $res = $this->db
            ->select('app_name,	current_app_version,	is_mandatory')
            ->from($this->table)
            ->where('app_name', $app_name)
            ->where('is_deleted', 0)
            ->where('is_active', 1)
            ->get();

        if($res->num_rows()>0){
            return $res->row();
        }
        return null;

    }


}