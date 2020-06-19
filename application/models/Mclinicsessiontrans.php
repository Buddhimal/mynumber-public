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




}
