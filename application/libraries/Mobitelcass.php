<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

define('MOBITEL_KEY', "5cd587455a3ad700f0e901b070575110");
define('MOBITEL_APP_ID', "APP_006837");

class Mobitelcass
{
	private $api_url = "https://api.mspace.lk/%s";

	public function __construct(){

	}

	private function post($url, $payload){

		try{

			$headers = array();

			$requesturl = sprintf($this->api_url, $url);

			$json_payload = json_encode($payload);

            $ch = curl_init();

			curl_setopt_array($ch, array(
				CURLOPT_URL => $requesturl,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => 'utf-8',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $json_payload,
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json;charset=utf-8',
					'Content-Length:' . strlen($json_payload)
				),
				CURLOPT_SSL_VERIFYPEER=> false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_HEADERFUNCTION =>  function($curl, $header) use (&$headers) {
				    $len = strlen($header);
				    $header = explode(':', $header, 2);
				    if (count($header) < 2) // ignore invalid headers
				    	return $len;

				    $headers[strtolower(trim($header[0]))][] = trim($header[1]);
				    return $len;
				}
			));

			$response = curl_exec($ch);
			curl_close($ch);

			if($response === false){
	            throw new Exception( 'Error: CURL request failed - '. stripslashes( curl_error( $ch ) ) );
	        }

			return $response;

		}catch(Exception $ex){
			// $ex->getMessage();
			throw $ex;
		}
	}

	public function send_otp(MobitelOtpRequest $payload){
		return $this->post('otp/request', $payload);
	}

	public function verify_otp(MobitelOtpVerifyRequest $payload){
		return $this->post('otp/verify', $payload);
	}

	public function subscribe(MobitelSubscribeRequest $payload){
		return $this->post('subscription/send', $payload);
	}

	// public function subscribe(MobitelSubscribeRequest $payload){
	// 	return $this->post('subscription/send', $payload);
	// }

	public function charge(MobitelDirectDebitRequest $payload){
		return $this->post('subscription/send', $payload);
	}
}


class MobitelRequestFactory {

	public static function otp_request($request_data){

		$request = new MobitelOtpRequest();

		$meta = new MobitelApplicationMetaData();
		$meta->client= "MOBILEAPP";
		$meta->device = $request_data->device;
		$meta->os = $request_data->os;
		$meta->appCode = "https://play.google.com/store/apps/details?id=com.mynumber.patient";

		$request->subscriberId = self::sanitize_telephone( $request_data->telephone );
		$request->applicationHash = md5( sprintf("%s%s",$request_data->id, time() ) );
		$request->applicationMetaData = $meta;

		return $request;
	}
	
	public static function otp_verification_request( $request_data ){

		$request = new MobitelOtpVerifyRequest();
		$request->referenceNo = $request_data->referenceNo;
		$request->otp = $request_data->otp;
		return $request;
	}

	public static function subscribe_request($subscriber_id){
		$request = new MobitelUnSubscribeRequest();
		$request->subscriberId = $subscriber_id;
		return $request;
	}

	public static function unsubscribe_request($subscriber_id){
		$request = new MobitelUnSubscribeRequest();
		$request->subscriberId = $subscriber_id;
		return $request;		
	}

	public static function balance_check($subscriber_id, $account_id=null){
		$request = new MobitelBalanceCheckRequest();
		$request->subscriberId = $subscriber_id;
		
		if(!empty($account_id) && null != $account_id) {
			$request->accountId = $account_id;
		}
		return $request;
	}

	public static function charge_request($subscriber_id, $transaction_id){
		$request = new MobitelDirectDebitRequest();
		$request->externalTrxId = $transaction_id;
		$request->subscriberId = $subscriber_id;
		return $request;
	}

	public static function sanitize_telephone($phone_number){
		if(!empty($phone_number)){
			return sprintf("tel:94%s", substr($phone_number, -9) );
		}else{
			throw new Exception("Telephone number is empty");
		}
	}
}

/**
 * 
 */
class MobitelRequest {
	public $applicationId;
	public $password;

	function __construct()
	{
		$this->applicationId = MOBITEL_APP_ID;
		$this->password = MOBITEL_KEY;
	}
}

class MobitelOtpRequest extends MobitelRequest{

	public $subscriberId;
	public $applicationHash;
	public $applicationMetaData;
	public function __construct(){
		parent::__construct();
	}
}

class MobitelApplicationMetaData  extends MobitelRequest {

	public $client;
	public $device;
	public $os;
	public $appCode;
	public function __construct(){
		parent::__construct();
	}
}

class MobitelOtpVerifyRequest  extends MobitelRequest {
    public $referenceNo;
    public $otp;
    public function __construct(){
		parent::__construct();
	}
}

class MobitelUnSubscribeRequest extends MobitelRequest{
	public $subscriberId;
	public $action;
	public function __construct(){
		$this->action = 0;
		parent::__construct();
	}
}

class MobitelSubscribeRequest extends MobitelRequest{
	public $subscriberId;
	public $action;
	public function __construct(){
		$this->action = 1;
		parent::__construct();
	}
}

class MobitelBalanceCheckRequest extends MobitelRequest {
	
	public $subscriberId;
	public $paymentInstrumentName;
	public $accountId;
	public $currency;

	public function __construct()
	{
		parent::__construct();
		$this->paymentInstrumentName = "Mobile Account";
		$this->currency = "BDT";
		$this->accountId = null; // change according to mobitels instructions. already asked for instructions
	}
}

class MobitelDirectDebitRequest extends MobitelRequest {
	
	public $externalTrxId;
	public $subscriberId;
	public $paymentInstrumentName;
	public $accountId;
	public $amount;
	public $currency;

	public function __construct() {
		parent::__construct();
		$this->accountId = SOFTGEN_MOBITEL_ACC_NUMBER;
		$this->paymentInstrumentName = "Mobile Account";
		$this->amount = Payments::DEFAULT_CHARGE;
		$this->currency = "BDT";
	}
}