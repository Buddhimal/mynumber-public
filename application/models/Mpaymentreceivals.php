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


	public function set_data($post_array)
	{
		$this->post = $post_array;
	}

	public function is_valid()
	{
		$result = true;
		/*
		 Validation logics goes here
		*/

		if(!isset($this->post['clinic_id']) && empty($this->post['clinic_id'])){
		 	array_push($this->validation_errors, 'Clinic Id missing');
		 	$result = false;
		}
		
		if(!isset($this->post['start']) && empty($this->post['start'])){
			array_push($this->validation_errors, 'Start date missing or invalid');
			$result = false;
		}

		if(!isset($this->post['end']) && empty($this->post['end'])){
			array_push($this->validation_errors, 'End date missing or invalid');
			$result = false;
		}

		if(!isset($this->post['session_tasks']) && empty($this->post['session_tasks'])){
			array_push($this->validation_errors, 'sessions list missing or invalid');
			$result = false;
		}

		return $result;
	}

	/*
	*
	*/
	public function create()
	{
		$receival_id = trim($this->mmodel->getGUID(), '{}');
		$now = date("Y-m-d h:i:s");
		$new_record = array(
			"id" => $receival_id,
			"clinic_id" => $this->post['clinic_id'],
			"pay_start" => $this->post['start'],
			"pay_end" => $this->post['end'],
			"daily_breakdown" => json_encode($this->post["session_tasks"]),
			"total_amount" => $this->post['total'],
			"pay_requested" => $now,
			"collection_status" => PaymentCollectionStatus::Pending,
			"is_deleted" => 0,
			"is_active" => 1,
			"updated" =>  $now,
			"created" => $now,
			"updated_by" => $this->post['clinic_id'],
			"created_by" => $this->post['clinic_id']
		);

		$this->mmodel->insert($this->table, $new_record);

        if ($this->db->affected_rows() > 0) {
            $result = new EntityPaymentReceival( $this->get_record($receival_id) );
        }

        return $result;
	}

	public function get_record($receival_id){

       	$query_result = $this->db->from($this->table)
	        ->where('id', $receival_id)
	        ->where('is_deleted', 0)
	        ->where('is_active', 1)->get()->row();
        return $query_result;
	}

	public function get_last_paid_date( $clinic_id ){
		$output = null;
		$result_set = $this->db->select_max('collected')
			->from($this->table)
			->where('clinic_id', $clinic_id)
			->where('collection_status', PaymentCollectionStatus::Pending)
			->where('is_active', 1)
			->where('is_deleted', 0)->get();

		if ( $result_set->num_rows() > 0 ) {
            $output = $result_set->row()->collected;
		}

		return $output;
	}

    public function get_paid_records($clinic_id)
    {
        $output = null;

        $result_set= $this->db
            ->select('total_amount,pay_start,pay_end')
            ->from($this->table)
            ->where('clinic_id',$clinic_id)
            ->where('collection_status',PaymentCollectionStatus::Collected)
            ->where('paid_status',PaymentPaidStatus::Paid)
            ->where('is_active', 1)
            ->where('is_deleted', 0)->get();

        foreach ($result_set->result() as $item) {
            $output[] = $item;
        }

        return $output;
    }

    public function get_cumulative_paid_amount($clinic_id)
    {
        $output = null;

        $result_set= $this->db
            ->select('sum(total_amount) as total_amount')
            ->from($this->table)
            ->where('clinic_id',$clinic_id)
            ->where('collection_status',PaymentCollectionStatus::Collected)
            ->where('paid_status',PaymentPaidStatus::Paid)
            ->where('is_active', 1)
            ->where('is_deleted', 0)->get();

        if ( $result_set->num_rows() > 0 ) {
            $output =$result_set->row()->total_amount;
        }

        return $output;
    }


}
