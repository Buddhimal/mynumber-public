<?php

class EntityAppointments
{
    public $patient_id = null;
    public $session_id = null;
    public $appointment_id = null;
    public $appointment_status = null;
    public $serial_number = null;
	public $doctor_name = null;
	public $starting_time = null;
	public $end_time = null;
	public $clinic_name = null;
	public $clinic_address = null;
	public $lat = null;
    public $long = null;

    function __construct($data = null)
    {
        if (!is_null($data)) {
            $this->patient_id = $data->patient_id;
            $this->session_id = $data->session_id;
            $this->appointment_id = $data->appointment_id;
            $this->appointment_status = $data->appointment_status;
            $this->serial_number = $data->serial_number;
            $this->doctor_name = $data->doctor_name;
            $this->clinic_name = $data->clinic_name;
            $this->clinic_address = $data->clinic_address;
            $this->starting_time = $data->starting_time;
            $this->end_time = $data->end_time;
            $this->lat = $data->lat;
            $this->long = $data->long;
        }
    }
}
