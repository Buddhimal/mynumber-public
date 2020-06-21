<?php

class EntityClinicSearchResult
{
    public $clinic_id = null;
    public $clinic_name = null;
    public $city = null;
    public $lat = null;
    public $long = null;

    function __construct($data = null)
    {
        if (!is_null($data)) {
            $this->clinic_id = $data->clinic_id;
            $this->clinic_name = $data->clinic_name;
            $this->city = $data->city;
            $this->lat = $data->lat;
            $this->long = $data->long;
        }
    }
}