<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'entities/EntityPaymentReceival.php');

class Mpaymentreceivals extends CI_Model{

	public $validation_errors = array();
	private $post = array();
	protected $table = "payment_receivals";

	function __construct()
	{
		parent::__construct();
		$this->load->model('mvalidation');
	}


}
