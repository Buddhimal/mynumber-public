<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');

class EntityPaymentReceival{
	public $id = null;
	public $clinic_id = null;
	public $pay_start = null;
	public $pay_end = null;
	public $daily_breakdown = null;
	public $total_amount = null;
	public $pay_requested = null;
	public $collection_status = null;
	public $collected = null;
	public $collected_by = null;
	public $paid_status = null;
	public $paid_on = null;
	public $is_deleted = null;
	public $is_active = null;
	public $updated = null;
	public $created = null;
	public $updated_by = null;
	public $created_by = null;

	function __set($name, $value){
		if(property_exists($this, $name)){
			switch($name){
				case "daily_breakdown":
					$this->{$name} = json_decode($value);
					break;
				default:
					$this->{$name} = $value;
					break;
			}
		}
	}

	function __get($name){
		if(property_exists($this, $name)){
			switch($name){
				case "daily_breakdown":
					return json_encode($this->{$name});
					break;
				default:
					return $this->{$name};
					break;
			}
		}
	}

	function __construct($data = null){
		foreach($data as $key =>$value){
			$this->{$key} = $value;
		}
	}
}