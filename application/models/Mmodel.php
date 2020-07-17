<?php


if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mmodel extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}


//	var $client_service = "frontend-client";
	var $auth_key       = APIKeys::PATIENT_API_KEY;


	public function check_auth_client(){
		$client_service = $this->input->get_request_header('Client-Service', TRUE);
		$auth_key  = $this->input->get_request_header('Auth-Key', TRUE);

//		if($client_service == $this->client_service && $auth_key == $this->auth_key){
		if( $auth_key == $this->auth_key){
			return true;
		} else {
			return false;
		}
	}


	public function login($username,$password)
	{
		$q  = $this->db->select('password,id')->from('users')->where('username',$username)->get()->row();

		if($q == ""){
			return array('status' => 204,'message' => 'Username not found.');
		} else {
			$hashed_password = $q->password;
			$id              = $q->id;
			echo $hashed_password ." ".$password;
			//exit;
			if (hash_equals($hashed_password, crypt($password, $hashed_password))) {
				$last_login = date('Y-m-d H:i:s');
				$token = crypt(substr( md5(rand()), 0, 7));
				$expired_at = date("Y-m-d H:i:s", strtotime('+12 hours'));
				$this->db->trans_start();
				$this->db->where('id',$id)->update('users',array('last_login' => $last_login));
				$this->db->insert('users_authentication',array('users_id' => $id,'token' => $token,'expired_at' => $expired_at));
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					return array('status' => 500,'message' => 'Internal server error.');
				} else {
					$this->db->trans_commit();
					return array('status' => 200,'message' => 'Successfully login.','id' => $id, 'token' => $token);
				}
			} else {
				echo "Wrong password";
				exit();
				return array('status' => 204,'message' => 'Wrong password.');
			}
		}
	}

	public function logout()
	{
		$users_id  = $this->input->get_request_header('User-ID', TRUE);
		$token     = $this->input->get_request_header('Authorization', TRUE);
		$this->db->where('users_id',$users_id)->where('token',$token)->delete('users_authentication');
		return array('status' => 200,'message' => 'Successfully logout.');
	}

	public function auth()
	{
		$users_id  = $this->input->get_request_header('User-ID', TRUE);
		$token     = $this->input->get_request_header('Authorization', TRUE);
		$q  = $this->db->select('expired_at')->from('users_authentication')->where('users_id',$users_id)->where('token',$token)->get()->row();
		if($q == ""){
			return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
		} else {
			if($q->expired_at < date('Y-m-d H:i:s')){
				return json_output(401,array('status' => 401,'message' => 'Your session has been expired.'));
			} else {
				$updated_at = date('Y-m-d H:i:s');
				$expired_at = date("Y-m-d H:i:s", strtotime('+12 hours'));
				$this->db->where('users_id',$users_id)->where('token',$token)->update('users_authentication',array('expired_at' => $expired_at,'updated_at' => $updated_at));
				return array('status' => 200,'message' => 'Authorized.');
			}
		}
	}

	function getGUID(){
		if (function_exists('com_create_guid')){
			return com_create_guid();
		}
		else {
			mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);// "-"
			$uuid = chr(123)// "{"
				.substr($charid, 0, 8).$hyphen
				.substr($charid, 8, 4).$hyphen
				.substr($charid,12, 4).$hyphen
				.substr($charid,16, 4).$hyphen
				.substr($charid,20,12)
				.chr(125);// "}"
			return $uuid;
		}
	}


	public function get_all($table) {
		return $this->db->get($table);
	}

	public function get_all_order($table, $order) {
		$this->db->order_by($order);
		return $this->db->get($table);
	}

	public function get_where($column, $table, $common, $id) {
		$this->db->select($column);
		$this->db->from($table);
		$this->db->where($common, $id);
		return $this->db->get();
	}

	public function get_where_2($column, $table, $common, $id, $common_2, $id_2) {
		$this->db->select($column);
		$this->db->from($table);
		$this->db->where($common, $id);
		$this->db->where($common_2, $id_2);
		return $this->db->get();
	}

	public function delete_where ($table, $column, $value) {
		$this->db->where($column, $value);
		$this->db->delete($table);
	}

	public function insert ($table, $data) {
		$this->db->insert($table, $data);
	}

	public function update ($column, $id, $table, $data) {
		$this->db->where($column, $id);
		$this->db->update($table, $data);
	}

	public function mike_delta_5 ($password) {
		$this->db->select('MD5("'.$password.'")"password";');
		return $this->db->get();
	}

}
