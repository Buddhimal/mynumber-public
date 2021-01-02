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
            $data['app_name'] = $res->row()->app_name;
            $data['current_app_version'] = $res->row()->current_app_version;
            $data['is_mandatory'] = (bool)$res->row()->is_mandatory;

            return $data;
        }
        return null;

    }

    public function get_image_name(){

		$url = '';

    	$res = $this->db
			->select('image_name')
			->from($this->table)
			->where('app_name',AppPackage::PACKAGE_NAME)
			->get();

    	if ($res->num_rows()>0){
    		$url = $res->row()->image_name;
		}

		return $url;
	}


}
