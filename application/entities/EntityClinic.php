<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class EntityClinic
{
	public $id=null;
	public $name = null;
	public $contact_telephone = null;
	public $contact_mobile = null;
	public $device_mobile = null;
	public $email = null;
	public $web = null;
	public $location = null;
	public $created = null;
	
	function __construct($data=null){
		if(!is_null($data)){
			$this->id = $data->id;
			$this->name = $data->clinic_name;
			$this->contact_telephone = $data->contact_telephone;
			$this->contact_mobile = $data->contact_mobile;
			$this->device_mobile = $data->device_mobile;
			$this->email = $data->email;
			$this->web = $data->web;
			$this->location = $data->location_id;
			$this->created = $data->created;
		}
	}
}
