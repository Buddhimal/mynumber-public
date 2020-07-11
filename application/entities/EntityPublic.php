<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EntityPublic
{
	public $id = null;
	public $salutation = null;
	public $firstname = null;
	public $lastname = null;
	public $address = null;
	public $nic = null;
	public $dob = null;
	public $age = null;
	public $telephone = null;
	public $email = null;
	public $location = null;
	public $patient_code = null;

	function __construct($data = null)
	{
		if (!is_null($data)) {
			$this->id = $data->id;
			$this->salutation = $data->salutation;
			$this->firstname = $data->first_name;
			$this->lastname = $data->last_name;
			$this->address = $data->address;
			$this->nic = $data->nic;
			$this->dob = $data->dob;
			if(!is_null($data->dob)){
                $date = new DateTime($data->dob);
                $now = new DateTime();
                $interval = $now->diff($date);
                $this->age= $interval->y;
            }
			$this->location = $data->location;
			$this->telephone = $data->telephone;
			$this->email = $data->email;
			$this->patient_code = $data->patient_code;
		}
	}
}
