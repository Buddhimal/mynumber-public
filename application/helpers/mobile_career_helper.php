<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'helpers/enumerations_helper.php');

class CareerMap{

	private $map = array(
		'071' => MobileCareer::Mobitel,
		'070' => MobileCareer::Mobitel,

		'072' => MobileCareer::Hutch,
		'078' => MobileCareer::Hutch,

		'075' => MobileCareer::Airtel,

		'076' => MobileCareer::Dialog,
		'077' => MobileCareer::Dialog,
	);

	private $name_map = array(
		'071' => "Mobitel",
		'070' => "Mobitel",

		'072' => "Huth/Etisalat",
		'078' => "Huth/Etisalat",

		'075' => "Airtel",

		'076' => "Dialog",
		'077' => "Dialog",
	);

	private $subject;

	public function __construct($number){
		if(!empty($number) && strlen($number) >= 10 ) {
			$phone_number = trim($number);
			$this->subject = substr( substr($phone_number, strlen($phone_number) - 10 ), 0, 3);
		}else{
			throw new Exception("Invalid mobile number");
		}
	}

	public function get_career_id() {

		return $this->map[$this->subject];
	}

	public function get_career_name(){

		return $this->name_map[$this->subject];
	}

}