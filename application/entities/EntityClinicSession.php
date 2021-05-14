<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class EntityClinicSession
{
	public $id = null;
	public $clinic_id=null;
	public $avg_time_per_patient = null;
	public $max_patients = null;
	public $consultant = null;
	public $session_name = null;
	public $session_description = null;

	function __construct($data = null)
	{
		if (!is_null($data)) {
			$this->id = $data->id;
			$this->clinic_id= $data->clinic_id;
			$this->avg_time_per_patient = $data->avg_time_per_patient;
			$this->max_patients = $data->max_patients;
			$this->consultant = $data->consultant;
			$this->session_name = $data->session_name;
			$this->session_description = $data->session_description;
		}
	}
}
