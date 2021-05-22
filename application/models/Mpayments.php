<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'helpers/enumerations_helper.php');

class Mpayments extends CI_Model{

	public $validation_errors = array();
	protected $table = "payments";
	private $post;
	private $valid;
	function __construct(){
		parent::__construct();
		$this->post = null;
		$this->valid = false;
	}

	public function set_data($data){
		$this->post = $data;
	}

	public function get($txn_id){
		return $this->db->from($this->table)->where('id',$txn_id)->get()->row();
	}

	public function pay_type(){
		return $this->post['pay_by'];
	}

	public function init_transaction($public_id) {

		if($this->valid){

			$now = date("Y-m-d H:i:s");

			$payment_id = trim($this->mmodel->getGUID(), '{}');
			$transaction_id = str_replace('-', '', $payment_id);

			$insert = array(
				'id' => $payment_id,
				'transaction_id' => $transaction_id,
				'public_id' => $public_id,
				'clinic_id' => $this->post['clinic'],
				'session_id' => $this->post['session'],
				'payment_type' => $this->post['pay_by'],
				'amount' =>  Payments::DEFAULT_CHARGE,
				'payment_status' => PaymentStatus::Pending,
				'created' => $now
			);

			if($this->db->insert($this->table, $insert)){
				return $this->get($payment_id);
			}else{
				throw new Exception("Failed to Insert Pyament Record");
			}
		}else{
			throw new Exception("Invalid data provided");
		}
	}


	public function is_valid(){

		if(!isset($this->post['public_id'] ) || empty($this->post['public_id']) )
			throw new Exception("Public is Empty/Not Provided by you");
		
		if(!isset($this->post['clinic'] ) || empty($this->post['clinic']) )
			throw new Exception("Clinic is Empty/Not Provided");

		if(!isset($this->post['session'] ) || empty($this->post['session']) )
			throw new Exception("Session is Empty/Not Provided");

		if(!isset($this->post['pay_by'] ) || empty($this->post['pay_by']) ){
			if(PaymentType::Mobile !=  $this->post['pay_by'] && PaymentType::Ipg !=  $this->post['pay_by'] ){
				throw new Exception("Payment Type invalid");
			}
		}

		$this->valid = true;
		return $this->valid;
	}

	public function update_payment_ref($transaction_id, $api_response){
		return $this->db
			->set('mobile_verification_ref', json_encode($api_response))
			->where('id', $transaction_id)
			->update($this->table);
	}


	public function update($order_ref, $data){
		return $this->db->where('id', $order_ref)->update($this->table, $data);
	}

	public function complete_payment($order_ref, $data ){
		return $this->db->where('id', $order_ref)->update($this->table, $data);
	}

	public function complete_mobitel_payment($order_ref, $data ){
		return $this->db->where('transaction_id', $order_ref)->update($this->table, $data);
	}

	public function log($response, $owner){

		return $this->db->insert('payment_log', array(
				'id' => trim($this->mmodel->getGUID(), '{}'),
				'response'=> $response,
				'public_id' => $owner, 
				'received'=> date("Y-m-d H:i:s")
			)
		);
	}

}
