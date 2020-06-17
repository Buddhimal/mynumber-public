<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EntityLocation
{

	public $id = null;
	public $location_street_address = null;
	public $location_address_line_ii = null;
	public $location_address_line_iii = null;
	public $location_city = null;
	public $location_district = null;
	public $location_province = null;
	public $location_long_lat = null;
	public $lat = null;
	public $long = null;
//	public $created = null;
//	public $updated = null;
//	public $is_active = null;
//	public $is_deleted = null;
//	public $updated_by = null;
//	public $created_by = null;

	function __construct($data = null)
	{
		if (!is_null($data)) {
			$this->id = $data->id;
			$this->location_street_address = $data->street_address;
			$this->location_address_line_ii = $data->address_line_ii;
			$this->location_address_line_iii = $data->address_line_iii;
			$this->location_city = $data->city;
			$this->location_district = $data->district;
			$this->location_province = $data->province;
			$this->lat = $data->lat;
			$this->long = $data->long;
//			$this->location_long_lat = json_decode($data->long_lat);
//			$this->created = $data->created;
//			$this->updated = $data->updated;
//			$this->is_active = $data->is_active;
//			$this->is_deleted = $data->is_deleted;
//			$this->updated_by = $data->updated_by;
//			$this->created_by = $data->created_by;
		}
	}

}
