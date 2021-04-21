<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dialogpin{

	private static $instance;

	private $password;
	private $username;
	private $base_url = "https://ideabiz.lk/apicall/%s";
	private $scope = "PRODUCTION";

	private $consumer_key = "uy6xJGLOOrOPlQXBQEJuspcZH4Ea";
	private $consumer_secret = "8gf00wY4_lMGeGqtGGo3OSpclxga";

	private $token_response;
	private $last_token_time;

	private function __construct(){
		$this->password = "2TXnx9EKM4eHjg@";
		$this->username = "softgen";
		$this->token();
	}

	private function get_bearer_token(){

		return base64_encode( sprintf("%s:%s", $this->consumer_key, $this->consumer_secret) );
	}

	private function token_grant($url, $url_params = null ){

		$response = null;
		try{

			$curl = curl_init();
			$url_parts = "?";

			if(is_array($url_params) ){
				$parts = array();
				foreach($url_params as $key => $value){
					$parts[] = sprintf("%s=%s", $key, $value);
				}

				$url_parts .= implode('&', $parts);
			}

			$full_url = sprintf($this->base_url, $url . $url_parts );

			$auth_bearer_token = $this->get_bearer_token();
			curl_setopt_array( $curl, array(
			  CURLOPT_URL => $full_url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_HTTPHEADER => array(
			    'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
			    'Authorization: Basic ' . $auth_bearer_token
			  ),
			));

			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // this can be removed when you put this live. better to do so

			$response = curl_exec($curl);
			curl_close($curl);

		}catch(Exception $ex){
			throw $ex;
		}

		return json_decode($response);
	}

	private function charge_post($url, $payload){
		$response = null;
		try
		{
			$full_url = sprintf($this->base_url, $url);
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => $full_url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => json_encode($payload),
			  CURLOPT_HTTPHEADER => array(
			    'Content-Type: application/json',
			    'Accept: application/json',
			    'Authorization: Bearer ' . $this->token_response->access_token
			  ),
			));

			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // this can be removed when you put this live. better to do so

			$response = curl_exec($curl);
			curl_close($curl);

		}catch(Exception $ex){
			throw $ex;
		}
		return json_decode( $response );
	}

	private function refresh_token(){

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => sprintf( $this->base_url, '?grant_type=refresh_token&refresh_token=' . $this->token_response->refresh_token ),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => array(
			'Content-Type: application/x-www-form-urlencoded',
			'Authorization: Basic ' . $this->get_bearer_token()
			),
		));

		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // this can be removed when you put this live. better to do so

		$response = curl_exec($curl);
		curl_close($curl);

		if(isset($response) && !empty($response)){
			$this->token_response = json_decode($response);
			$this->last_token_time = strtotime( sprintf("+%d seconds",$response->expires_in));
		}
	}

	private function pin_post($url, $payload){

		$curl = curl_init();

		$full_url = sprintf($this->base_url, $url);

		curl_setopt_array($curl, array(
		CURLOPT_URL => $full_url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => json_encode($payload),
		CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Accept: application/json',
				'Authorization: Bearer '. $this->token_response->access_token
			),
		));

		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // this can be removed when you put this live. better to do so
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode( $response );
	}


	public function token(){

		$param_parts = array(
			'username'=> $this->username, 
			'password' => $this->password, 
			'scope' => $this->scope, 
			'grant_type'=> 'password'
		);

		$response = $this->token_grant('token', $param_parts);

		if(isset($response) && !empty($response)){
			$this->token_response = $response;
			$this->last_token_time = strtotime( sprintf("+%d seconds", $this->token_response->expires_in));
		}else{
			throw new Exception("Token response is empty");
		}
		
	}

	public function charge(ChargeRequest $request){
		
		if( $this->last_token_time <= strtotime('now') ){
			$this->refresh_token();
		}

		return $this->charge_post('pin/payment/v1/charge', $request);
	}

	public function pin(PinVerificationRequest $request){
		if( $this->last_token_time <= strtotime('now') ){
			$this->refresh_token();
		}

		return $this->pin_post('pin/payment/v1/submitPin', $request);
	}

	public static function get(){

		if(is_null(self::$instance) || !isset(self::$instance) ){
			self::$instance = new Dialogpin();
		}

		return self::$instance;
	}

}

class DialogRequestFactory{
	public static function charge_request($customer_phone, $description, $transaction_ref){
		$request = new ChargeRequest();
		$request->set_phone($customer_phone);
		$request->description = $description;
		$request->taxable = false;
		$request->callbackURL = null;
		$request->txnRef = $transaction_ref;
		$request->amount = Payments::DEFAULT_CHARGE;

		return $request;
	}

	public static function pin_verification_request($pin, $server_ref){
		$request = new PinVerificationRequest();
		$request->pin = $pin;
		$request->serverRef = $server_ref;
		return $request;
	}
}

class ChargeRequest{

	public function set_phone($number){
		$phone_number_last_digits = substr($number, -9);
		$this->msisdn = sprintf("tel:+94%s", $phone_number_last_digits);
	}
	public $msisdn;// = "tel:+94777809046";
	public $description;// = "test pay";
	public $taxable;// = true;
	public $callbackURL;// = null;
	public $txnRef;// = "ABC-123";
	public $amount;// = 1.0;

}

class PinVerificationRequest{
  public $pin; // "614790",
  public $serverRef; // "5f6680c9d6674341b56ee9d1c8024829"
}