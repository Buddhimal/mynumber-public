<?php

class EntityClinicSessionTask
{

    public $id = null;
    public $session_name = null;
    public $clinic_date = null;
    public $clinic_session_id = null;
    public $action = null;
    public $action_datetime = null;
    public $additional_data = null; //:string/json,
//	public $is_deleted = null;
//	public $is_active = null;
//	public $updated = null;
//	public $created = null;
//	public $updated_by = null;
//	public $created_by = null;

    public $total_appointments = null;
    public $total = null;

//	public $appointments = array();

    function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->{$name} = json_decode($value);
        }
    }

    function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
    }

    function __construct($data = null)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }
}



