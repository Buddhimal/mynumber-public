<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'libraries/REST_Controller.php');
require_once(APPPATH . 'helpers/mobile_career_helper.php');
require_once(APPPATH . 'libraries/Dialogpin.php');
/**
 *
 */
class Patient extends REST_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model("mmodel");
		$this->load->model("mvalidation");
		$this->load->model("mlogin");
		$this->load->model("mdoctor");
		$this->load->model("mpublic");
		$this->load->model("mclinic");
		$this->load->model("mlocations");
		$this->load->model("mconsultantpool");
		$this->load->model("mclinicsession");
		$this->load->model("mclinicsessiondays");
		$this->load->model("mclinicholidays");
		$this->load->model("mclinicsessionsubstituteconsultant");
		$this->load->model("motpcode");
		$this->load->model('appointmentserialnumber');
		$this->load->model('mserialnumber');
		$this->load->model('mclinicappointment');
		$this->load->model('mclinicsessiontrans');
		$this->load->model('mappversion');
		$this->load->model('mcomplaints');
		$this->load->model('minquiry');
		$this->load->model('mcommunicatoremailqueue', 'memail');
		 
		/*$this->load->helper('enumerations', 'enums');*/
		/*$this->load->helper('mobile_career', 'CareerMap');*/
		$this->load->library('Mobitelcass');

		$this->dialogpin = Dialogpin::get();

		$this->load->model('mpayments', 'payments');


	}

	//region Index
		public function index_get()
		{
			$response = new stdClass();
			$response->status = REST_Controller::HTTP_BAD_REQUEST;
			$response->msg = 'Invalid Request.';
			$response->error_msg[] = 'Invalid Request.';
			$response->response = NULL;
			$this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}

		public function index_post()
		{
			$response = new stdClass();
			$response->status = REST_Controller::HTTP_BAD_REQUEST;
			$response->msg = 'Invalid Request.';
			$response->error_msg[] = 'Invalid Request.';
			$response->response = NULL;
			$this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}

		public function index_put()
		{
			$response = new stdClass();
			$response->status = REST_Controller::HTTP_BAD_REQUEST;
			$response->msg = 'Invalid Request.';
			$response->error_msg[] = 'Invalid Request.';
			$response->response = NULL;
			$this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}

		public function index_delete()
		{
			$response = new stdClass();
			$response->status = REST_Controller::HTTP_BAD_REQUEST;
			$response->msg = 'Invalid Request.';
			$response->error_msg[] = 'Invalid Request.';
			$response->response = NULL;
			$this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}

	//endregion

	//region All API for Public
		public function SendVerificationCode_post()
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();

			if ($method == 'POST') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					//code...

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}

			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function ResendOTP_put($public_id)
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'PUT') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					$public = $this->mpublic->get($public_id);

					if (!empty($public) && is_object($public)){

						$mapper = new CareerMap($public->telephone);
						$career = $mapper->get_career_id();

						if($career == MobileCareer::Mobitel ){

							$request = MobitelRequestFactory::otp_request($public); 
							$apiresponse = $this->mobitelcass->send_otp($request);

							$this->payments->log(json_encode($apiresponse), $public_id);

							$otp_record =  $this->motpcode->create_mobitel_otp($public_id, $public->telephone, $apiresponse);


							if(false !== $otp_record){
								$public = new stdClass();
								$public->otp_ref = $otp_record['id'];
								$response->status = REST_Controller::HTTP_OK;
								$response->status_code = APIResponseCode::SUCCESS;
								$response->msg = 'OTP sent successfully via mobitel';
								$response->error_msg = NULL;
								$response->response = $public;
								$this->response($response, REST_Controller::HTTP_OK);	
							}else{

								$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
								$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
								$response->msg = 'Failed to send OTP..';
								$response->error_msg[] = 'Failed to send OTP..';
								$response->response = NULL;
								$this->response($response, REST_Controller::HTTP_OK);
							}							

						}elseif ($this->motpcode->resend_otp($public_id)) {
							$response->status = REST_Controller::HTTP_OK;
							$response->status_code = APIResponseCode::SUCCESS;
							$response->msg = 'OTP sent successfully..';
							$response->error_msg = NULL;
							$response->response = NULL;
							$this->response($response, REST_Controller::HTTP_OK);
						} else {
							$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
							$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
							$response->msg = 'Failed to send OTP..';
							$response->error_msg[] = 'Failed to send OTP..';
							$response->response = NULL;
							$this->response($response, REST_Controller::HTTP_OK);
						}

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid Public Id';
						$response->error_msg[] = 'Invalid Public Id';
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}
				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->error_msg[] = 'Invalid Authentication Key.';
					$response->response = NULL;
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->error_msg[] = 'Invalid Request Method.';
				$response->response = NULL;
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function GetAppVersion_put($public_id, $app_name)
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();

			if ($method == 'PUT') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					$app_info = $this->mappversion->get_app_version($app_name);

					$firebase_id = $this->put('firebase_id');

					$this->mpublic->update_firebase_id($public_id, $firebase_id);

					if (!is_null($app_info)) {

						$response->status = REST_Controller::HTTP_OK;
						$response->status_code = APIResponseCode::SUCCESS;
						$response->msg = 'App Details';
						$response->response = $app_info;
						$response->error_msg = null;
						$this->response($response, REST_Controller::HTTP_OK);

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid App Name';
						$response->response = NULL;
						$response->error_msg = null;
						$this->response($response, REST_Controller::HTTP_OK);
					}

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}

			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
			}
		}


		public function GetAppVersion_get($app_name)
		{

			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();

			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					$app_info = $this->mappversion->get_app_version($app_name);

					if (!is_null($app_info)) {

						$response->status = REST_Controller::HTTP_OK;
						$response->status_code = APIResponseCode::SUCCESS;
						$response->msg = 'App Details';
						$response->response = $app_info;
						$response->error_msg = null;
						$this->response($response, REST_Controller::HTTP_OK);

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid App Name';
						$response->response = NULL;
						$response->error_msg = null;
						$this->response($response, REST_Controller::HTTP_OK);
					}

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}

			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
			}
		}



		public function RegisterPublic_post()
		{

			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();

			if ($method == 'POST') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					$json_data = $this->post('json_data');

					// Passing post array to the model.
					$this->mpublic->set_data($json_data);

					if ($this->mvalidation->already_exists('public', 'email', $json_data['email']) != TRUE) {
						// model it self will validate the input data
						if ($this->mpublic->is_valid()) {

							// create the doctor record as the given data is valid
							$public = $this->mpublic->create();

							if (!is_null($public)) {

								$login_data['username'] = $json_data['email'];
								$login_data['password'] = $json_data["password"];
								$login_data['mobile'] = $json_data["telephone"];

								$this->mlogin->set_data($login_data);

								if ($this->mlogin->is_valid()) {

									$login = $this->mlogin->create($public->id, EntityType::Patient);// return true or false

									if ($login) {

										$mapper = new CareerMap( $login_data['mobile'] );
										$career = $mapper->get_career_id();

										if($career == MobileCareer::Mobitel){

											$request = MobitelRequestFactory::otp_request($public);
											$apiresponse = $this->mobitelcass->send_otp($request);

											//Log
											$this->payments->log(json_encode($apiresponse), $public->id);


											$otp_record =  $this->motpcode->create_mobitel_otp($public->id, $public->telephone, $apiresponse);
											
											if(false !== $otp_record){

												$career_reference = json_decode( $otp_record['career_reference'] );

												if(strtoupper($apiresponse->statusCode) == "S1000" ){

													//sending the otp ref back so app can advertise it back when making otp verify call
													$public->otp_ref = $otp_record['id'];
													$response->status = REST_Controller::HTTP_OK;
													$response->status_code = APIResponseCode::SUCCESS;
													$response->msg = 'OTP sent successfully via mobitel';
													$response->error_msg = NULL;
													$response->response =  $public; //
													$this->response($response, REST_Controller::HTTP_OK);

												} else if(isset($career_reference) && strtoupper($career_reference->statusCode) == "E1351"){
													//Customer has already registered with mobitel
													//sending the otp ref back so app can advertise it back when making otp verify call
													$public->otp_ref = $otp_record['id'];
													$response->status = REST_Controller::HTTP_OK;
													$response->status_code = APIResponseCode::ALLREADY_EXISTS;
													$response->msg = 'Patient has already subscribed';
													$response->error_msg = NULL;
													$response->response =  $public; //
													$this->response($response, REST_Controller::HTTP_OK);
												}

											}else{

												$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
												$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
												$response->msg = 'Failed to send OTP..';
												$response->error_msg[] = 'Failed to send OTP..';
												$response->response = NULL;
												$this->response($response, REST_Controller::HTTP_OK);
											}
										}//career
										else{

											$this->motpcode->create($public->id, $login_data['mobile']);
											$public->otp_ref = null;
											$response->status = REST_Controller::HTTP_OK;
											$response->status_code = APIResponseCode::SUCCESS;
											$response->msg = 'New Public Added Successfully';
											$response->error_msg = NULL;
											$response->response = $public;
											$this->response($response, REST_Controller::HTTP_OK);
										}
									}//login if
									
								} else {
									$response->status = REST_Controller::HTTP_BAD_REQUEST;
									$response->msg = 'Validation Failed.';
									$response->response = NULL;
									$response->error_msg = $this->mlogin->validation_errors;
									$this->response($response, REST_Controller::HTTP_OK);
								}

							} else {
								$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
								$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
								$response->msg = NULL;
								$response->error_msg[] = 'Internal Server Error';
								$response->response = NULL;
								$this->response($response, REST_Controller::HTTP_OK);
							}

						} else {
							$response->status = REST_Controller::HTTP_BAD_REQUEST;
							$response->msg = 'Validation Failed.';
							$response->response = NULL;
							$response->error_msg = $this->mpublic->validation_errors;
							$this->response($response, REST_Controller::HTTP_OK);
						}
					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Validation Failed.';
						$response->response = NULL;
						$response->error_msg = 'Email Already Exists.';
						$this->response($response, REST_Controller::HTTP_OK);
					}
				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}



		}

		public function PublicByUniqueId_get($public_id)
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					$public = $this->mpublic->get($public_id);

					$response->status = REST_Controller::HTTP_OK;
					$response->status_code = APIResponseCode::SUCCESS;
					$response->msg = 'Public Details';
					$response->error_msg = NULL;
					$response->response = $public;
					$this->response($response, REST_Controller::HTTP_OK);


				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::HTTP_METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function UpdatePublic_put($public_id)
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'PUT') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					// Passing put array to the model.
					$this->mpublic->set_data($this->put());

					// model it self will validate the input data
					if ($this->mpublic->is_valid()) {

						// update the public record as the given data is valid
						$public = $this->mpublic->update($public_id);

						if (!is_null($public)) {

							$response->status = REST_Controller::HTTP_OK;
							$response->status_code = APIResponseCode::SUCCESS;
							$response->msg = 'Public Updated Successfully';
							$response->error_msg = NULL;
							$response->response = $public;
							$this->response($response, REST_Controller::HTTP_OK);
						} else {
							$response->status = REST_Controller::HTTP_OK;
							$response->status_code = APIResponseCode::SUCCESS;
							$response->msg = 'No Records to Update';
							$response->error_msg = NULL;
							$response->response = $public;
							$this->response($response, REST_Controller::HTTP_OK);
						}
					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Validation Failed.';
						$response->response = NULL;
						$response->error_msg = $this->mpublic->validation_errors;
						$this->response($response, REST_Controller::HTTP_OK);
					}
				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::HTTP_METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function GetAppointmentNumber_get($patient_id = '', $session_id = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					if ($this->mpublic->valid_public($patient_id)) {

						if ($this->mclinicsession->valid_session($session_id)) {

							$number = $this->appointmentserialnumber->create($patient_id, $session_id);

							if (!is_null($number)) {

								$number->serial_number = $this->mserialnumber->get($number->serial_number_id);

								unset($number->serial_number_id);

								$response->status = REST_Controller::HTTP_OK;
								$response->status_code = APIResponseCode::SUCCESS;
								$response->msg = 'Number Details';
								$response->error_msg = '';
								$response->response['appointment_number'] = $number;
								$this->response($response, REST_Controller::HTTP_OK);
							} else {
								$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
								$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
								$response->msg = 'Failed to create number';
								$response->error_msg[] = 'Failed to create number';
								$response->response = null;
								$this->response($response, REST_Controller::HTTP_OK);
							}

						} else {
							$response->status = REST_Controller::HTTP_BAD_REQUEST;
							$response->status_code = APIResponseCode::BAD_REQUEST;
							$response->msg = 'Invalid Session Id';
							$response->error_msg[] = 'Invalid Session Id';
							$response->response = NULL;
							$this->response($response, REST_Controller::HTTP_OK);
						}

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid Public Id';
						$response->error_msg[] = 'Invalid Public Id';
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->error_msg[] = 'Invalid Authentication Key.';
					$response->response = NULL;
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->error_msg[] = 'Invalid Request Method.';
				$response->response = NULL;
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function BookAppointment_post($patient_id = '', $session_id = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];

			$response = new stdClass();
			if ($method == 'POST') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {


					if ($this->mpublic->valid_public($patient_id)) {

						if ($this->mclinicsession->valid_session($session_id)) {

							$json_data = $this->post('json_data');

							$number = $this->appointmentserialnumber->get_appointment_number($patient_id, $session_id);

							if (!is_null($number)) {
								if ($json_data['serial_number_id'] == $number->serial_number_id) {

									$this->mclinicappointment->set_data($json_data);

									if ($this->mclinicappointment->is_valid()) {

										//confirm booking
										$appointment = $this->mclinicappointment->create($patient_id, $session_id, $number->id);

										if (!is_null($appointment)) {

											$appointment->serial_number = $this->mserialnumber->get($appointment->serial_number_id);

											unset($appointment->serial_number_id);

											$response->status = REST_Controller::HTTP_OK;
											$response->status_code = APIResponseCode::SUCCESS;
											$response->msg = 'Appointment Confirmed.';
											$response->error_msg = null;
											$response->response['appointment_details'] = $appointment;
											$this->response($response, REST_Controller::HTTP_OK);
										} else {
											$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
											$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
											$response->msg = 'Failed to confirm Appointment';
											$response->error_msg[] = 'Failed to create Appointment';
											$response->response = null;
											$this->response($response, REST_Controller::HTTP_OK);
										}
									} else {
										$response->status = REST_Controller::HTTP_BAD_REQUEST;
										$response->status_code = APIResponseCode::BAD_REQUEST;
										$response->msg = 'Validation Failed.';
										$response->error_msg = $this->mclinicappointment->validation_errors;
										$response->response = NULL;
										$this->response($response, REST_Controller::HTTP_OK);
									}


								} else {
									$response->status = REST_Controller::HTTP_BAD_REQUEST;
									$response->status_code = APIResponseCode::BAD_REQUEST;
									$response->msg = 'Invalid Serial Number';
									$response->error_msg[] = 'Invalid Serial Number';
									$response->response = NULL;
									$this->response($response, REST_Controller::HTTP_OK);
								}
							} else {
								$response->status = REST_Controller::HTTP_BAD_REQUEST;
								$response->status_code = APIResponseCode::BAD_REQUEST;
								$response->msg = 'Number Expired';
								$response->error_msg[] = 'Number Expired';
								$response->response = NULL;
								$this->response($response, REST_Controller::HTTP_OK);
							}


						} else {
							$response->status = REST_Controller::HTTP_BAD_REQUEST;
							$response->status_code = APIResponseCode::BAD_REQUEST;
							$response->msg = 'Invalid Session Id';
							$response->error_msg[] = 'Invalid Session Id';
							$response->response = NULL;
							$this->response($response, REST_Controller::HTTP_OK);
						}

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid Public Id';
						$response->error_msg[] = 'Invalid Public Id';
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}
				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}

		}

		public function SearchClinicByDoctor_get($doctor_name = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					$clinic = $this->mclinic->get_clinics_by_doctor_name($doctor_name);

					$response->status = REST_Controller::HTTP_OK;
					$response->status_code = APIResponseCode::SUCCESS;
					$response->msg = 'Clinic Details';
					$response->error_msg = NULL;
					$response->response['clinics'] = $clinic;
					$this->response($response, REST_Controller::HTTP_OK);

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function SearchClinicByName_get($clinic_name = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					$clinic = $this->mclinic->get_clinics_by_name($clinic_name);

					$response->status = REST_Controller::HTTP_OK;
					$response->status_code = APIResponseCode::SUCCESS;
					$response->msg = 'Clinic Details';
					$response->error_msg = NULL;
					$response->response['clinics'] = $clinic;
					$this->response($response, REST_Controller::HTTP_OK);

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function SearchClinicByLocation_get($lat = '', $long = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					$clinic = $this->mclinic->get_clinics_by_location($lat, $long);

					$response->status = REST_Controller::HTTP_OK;
					$response->status_code = APIResponseCode::SUCCESS;
					$response->msg = 'Clinic Details';
					$response->error_msg = NULL;
					$response->response['clinics'] = $clinic;
					$this->response($response, REST_Controller::HTTP_OK);

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function ClinicByUniqueId_get($clinic_id = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					if ($this->mclinic->valid_clinic($clinic_id)) {

						$clinic = $this->mclinic->get($clinic_id);
						$clinic->location = $this->mlocations->get($clinic->location);
						$clinic->consultants = $this->mdoctor->get_consultants($clinic->id);
						$clinic->sessions = $this->mclinicsession->get_sessions($clinic->id);
						$clinic->holidays = $this->mclinicholidays->get_holidays($clinic->id);

						$response->status = REST_Controller::HTTP_OK;
						$response->status_code = APIResponseCode::SUCCESS;
						$response->msg = 'Clinic Details';
						$response->error_msg = NULL;
						$response->response['clinic'] = $clinic;
						$this->response($response, REST_Controller::HTTP_OK);

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid Clinic Id';
						$response->error_msg[] = 'Invalid Clinic Id';
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}


				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function GetAppointmentCount_get($session_id = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					if ($this->mclinicsessiondays->valid_session_day($session_id)) {

						$numbers_count = $this->mclinicappointment->get_appointment_count($session_id, AppointmentStatus::PENDING);

						$response->status = REST_Controller::HTTP_OK;
						$response->status_code = APIResponseCode::SUCCESS;
						$response->msg = 'Ongoing Number';
						$response->error_msg = NULL;
						$response->response['session_id'] = $session_id;
						$response->response['session_date'] = DateHelper::utc_date();
						$response->response['ongoing_number'] = $numbers_count;
						$this->response($response, REST_Controller::HTTP_OK);

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid Session Id';
						$response->error_msg[] = 'Invalid Session Id';
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}


				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function GetPaymentDues_get($patient_id = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					if ($this->mpublic->valid_public($patient_id)) {

						$payment_dues = $this->mclinicappointment->get_payment_dues($patient_id);

						$response->status = REST_Controller::HTTP_OK;
						$response->status_code = APIResponseCode::SUCCESS;
						$response->msg = 'Payment Dues';
						$response->error_msg = NULL;
						$response->response = $payment_dues;
						$this->response($response, REST_Controller::HTTP_OK);

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid Public Id';
						$response->error_msg[] = 'Invalid Public Id';
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function GetAppointmentsToday_get($patient_id = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					if ($this->mpublic->valid_public($patient_id)) {

						$appointments = $this->mclinicappointment->get_appointments_today($patient_id);

						$response->status = REST_Controller::HTTP_OK;
						$response->status_code = APIResponseCode::SUCCESS;
						$response->msg = 'Appointments Today';
						$response->error_msg = NULL;
						$response->response['appointments'] = $appointments;
						$response->response['image'] = $this->mappversion->get_image_name();
						$this->response($response, REST_Controller::HTTP_OK);

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid Public Id';
						$response->error_msg[] = 'Invalid Public Id';
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function GetAppointmentsHistory_get($patient_id, $year, $month)
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					if ($this->mpublic->valid_public($patient_id)) {

						$appointments = $this->mclinicappointment->get_appointments_monthly($patient_id, $year, $month);

						$response->status = REST_Controller::HTTP_OK;
						$response->status_code = APIResponseCode::SUCCESS;
						$response->msg = 'Appointments Today';
						$response->error_msg = NULL;
						$response->response['appointments'] = $appointments;
						$this->response($response, REST_Controller::HTTP_OK);

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid Public Id';
						$response->error_msg[] = 'Invalid Public Id';
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function GetOngoingNumber_get($session_id = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					if ($this->mclinicsession->valid_session($session_id)) {

						$get_ongoing_number = $this->mclinicappointment->get_ongoing_number($session_id);

						$response->status = REST_Controller::HTTP_OK;
						$response->status_code = APIResponseCode::SUCCESS;
						$response->msg = 'Ongoing Number';
						$response->error_msg = NULL;
						$response->response['session_id'] = $session_id;
						$response->response['on_going_number'] = $get_ongoing_number;
						$response->response['session_status'] = $this->mclinicsessiontrans->get_last_states_of_session($session_id, DateHelper::slk_date());
						$this->response($response, REST_Controller::HTTP_OK);

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid Session Id';
						$response->error_msg[] = 'Invalid Session Id';
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function testAPI_post()
		{

			$this->mclinicappointment->send_test_msg();
		}

	//endregion


	//region All API For Clinic

		public function ValidateOTP_put($public_id = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'PUT') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					$this->motpcode->set_data($this->put('json_data'));

					if($this->motpcode->is_career_verification_needed()){

						$otp_record = $this->motpcode->get_record_by_reference();
						$public = $this->mpublic->get_record($otp_record->clinic_id); // this is the public id// public ID is stored onto this at the opt reference creation.

						$mapper = new CareerMap($public->telephone);
						$career = $mapper->get_career_id();

						if( $career == MobileCareer::Mobitel ) {

							$career_reference = json_decode($otp_record->career_reference);

							$otp_request_data = new stdClass();
							$otp_request_data->referenceNo = $career_reference->referenceNo;
							$otp_request_data->otp = $this->motpcode->otp_code();

							$request = MobitelRequestFactory::otp_verification_request( $otp_request_data );
							$apiresponse = $this->mobitelcass->verify_otp( $request );

							//Log
							$this->payments->log(json_encode($apiresponse), $public->id);

							if(isset($apiresponse) && !empty($apiresponse)){

								if($apiresponse->statusCode =="S1000" || strtolower($apiresponse->statusDetail) == "success"){

									//otp verification success
									$masked_subscriber_id = $apiresponse->subscriberId; 
									// save this to masked id column on public table;
									if( $this->mpublic->update_mobile_mask( $public_id, $masked_subscriber_id ) ) {
										$this->mlogin->confirm_login( $public_id );
										$response->status = REST_Controller::HTTP_OK;
										$response->status_code = APIResponseCode::SUCCESS;
										$response->msg = 'OTP Validation Successful..';
										$response->error_msg = NULL;
										$response->response['msg'] = 'OTP Validation Successful..';
										$this->response( $response, REST_Controller::HTTP_OK );

									}else{
										$response->status = REST_Controller::HTTP_BAD_REQUEST;
										$response->status_code = APIResponseCode::BAD_REQUEST;
										$response->msg = 'Validation Failed.';
										$response->response = NULL;
										$response->error_msg = "mask update failed";
										$this->response($response, REST_Controller::HTTP_OK);
									}

								}else{
									//subscriber not registered
									$response->status = REST_Controller::HTTP_BAD_REQUEST;
									$response->status_code = APIResponseCode::BAD_REQUEST;
									$response->msg = 'Validation Failed.';
									$response->response = NULL;
									$response->error_msg = "Subscriber not registered yet";
									$this->response($response, REST_Controller::HTTP_OK);
								}
							}else{
								//otp verification failed
								$response->status = REST_Controller::HTTP_BAD_REQUEST;
								$response->status_code = APIResponseCode::BAD_REQUEST;
								$response->msg = 'Validation Failed.';
								$response->response = NULL;
								$response->error_msg = "Server response bad";
								$this->response($response, REST_Controller::HTTP_OK);
							}
							//$otp_record =  $this->motpcode->create_mobitel_otp($public_id, $public->device_mobile, $apiresponse);
						}

					}else if ($this->motpcode->is_valid($public_id)) {

						$this->mlogin->confirm_login($public_id);

						$response->status = REST_Controller::HTTP_OK;
						$response->status_code = APIResponseCode::SUCCESS;
						$response->msg = 'OTP Validation Successful..';
						$response->error_msg = NULL;
						// $response->response = (object) array('OTP Validation Successful..');
						$response->response['msg'] = 'OTP Validation Successful..';
						$this->response($response, REST_Controller::HTTP_OK);

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Validation Failed.';
						$response->response = NULL;
						$response->error_msg = $this->motpcode->validation_errors;
						$this->response($response, REST_Controller::HTTP_OK);
					}

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function SendOTPforUsername_put()
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'PUT') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					$json_data = $this->put('json_data');

					if ($this->mlogin->check_valid_account($json_data['username'])) {

						if (isset($this->mlogin->get_login_for_username($json_data['username'])->entity_id)) {

							$public_id = $this->mlogin->get_login_for_username($json_data['username'])->entity_id;

							if ($this->motpcode->resend_otp($public_id)) {
								$response->status = REST_Controller::HTTP_OK;
								$response->status_code = APIResponseCode::SUCCESS;
								$response->msg = 'OTP send successfully..';
								$response->error_msg = NULL;
								$response->response['public_id'] = $public_id;
								$this->response($response, REST_Controller::HTTP_OK);
							}

						} else {
							$response->status = REST_Controller::HTTP_BAD_REQUEST;
							$response->status_code = APIResponseCode::BAD_REQUEST;
							$response->msg = 'Invalid Username..';
							$response->error_msg[] = 'Invalid Username..';
							$response->response['public_id'] = $public_id;
							$this->response($response, REST_Controller::HTTP_OK);
						}
					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Validation Failed.';
						$response->error_msg = $this->mlogin->validation_errors;
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}
				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->error_msg[] = 'Invalid Authentication Key.';
					$response->response = NULL;
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->error_msg[] = 'Invalid Request Method.';
				$response->response = NULL;
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function ViewSessionsBClinic_get($clinic_id = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					if ($this->mclinic->valid_clinic($clinic_id)) {

						$sessions = $this->mclinicsession->get_sessions_for_clinic($clinic_id);

						$response->status = REST_Controller::HTTP_OK;
						$response->status_code = APIResponseCode::SUCCESS;
						$response->msg = 'Session Details';
						$response->error_msg = NULL;
						$response->response['sessions'] = $sessions;
						$this->response($response, REST_Controller::HTTP_OK);

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid Clinic Id';
						$response->error_msg = NULL;
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function ViewSessionsByDay_get($clinic_id = '', $day = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					if ($this->mclinic->valid_clinic($clinic_id)) {

						if (!($day != '' && $this->mvalidation->valid_day($day))) {

							$response->status = REST_Controller::HTTP_BAD_REQUEST;
							$response->status_code = APIResponseCode::BAD_REQUEST;
							$response->msg = 'Invalid Date';
							$response->error_msg[] = 'Invalid Date';
							$response->response = NULL;
							$this->response($response, REST_Controller::HTTP_OK);

						} else {

							$sessions = $this->mclinicsession->get_sessions_for_day($clinic_id, $day);

							$response->status = REST_Controller::HTTP_OK;
							$response->status_code = APIResponseCode::SUCCESS;
							$response->msg = 'Session Details';
							$response->error_msg = NULL;
							$response->response['sessions'] = $sessions;
							$this->response($response, REST_Controller::HTTP_OK);
						}

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid Clinic Id';
						$response->error_msg = NULL;
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function ViewSessionsByDate_get($clinic_id = '', $date = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					if ($this->mclinic->valid_clinic($clinic_id)) {

						if (!($date != '' && $this->mvalidation->valid_date($date))) {

							$response->status = REST_Controller::HTTP_BAD_REQUEST;
							$response->status_code = APIResponseCode::BAD_REQUEST;
							$response->msg = 'Invalid Date';
							$response->error_msg[] = 'Invalid Date';
							$response->response = NULL;
							$this->response($response, REST_Controller::HTTP_OK);

						} else {

							$sessions = $this->mclinicsession->get_sessions_for_day($clinic_id, $date);

							$response->status = REST_Controller::HTTP_OK;
							$response->status_code = APIResponseCode::SUCCESS;
							$response->msg = 'Session Details';
							$response->error_msg = NULL;
							$response->response['sessions'] = $sessions;
							$this->response($response, REST_Controller::HTTP_OK);
						}

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid Clinic Id';
						$response->error_msg = NULL;
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function ViewSessionsByID_get($clinic_id = '', $session_id = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					if ($this->mclinic->valid_clinic($clinic_id)) {

						if ($this->mclinicsession->valid_session($session_id)) {

							$sessions = $this->mclinicsession->get_full_session($session_id);

							$response->status = REST_Controller::HTTP_OK;
							$response->status_code = APIResponseCode::SUCCESS;
							$response->msg = 'Session Details';
							$response->error_msg = NULL;
							$response->response['sessions'] = $sessions;
							$this->response($response, REST_Controller::HTTP_OK);

						} else {

							$response->status = REST_Controller::HTTP_BAD_REQUEST;
							$response->status_code = APIResponseCode::BAD_REQUEST;
							$response->msg = 'Invalid Session';
							$response->error_msg[] = 'Invalid Session';
							$response->response = NULL;
							$this->response($response, REST_Controller::HTTP_OK);
						}

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid Clinic Id';
						$response->error_msg = NULL;
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function ViewSessionsByConsultant_get($clinic_id = '', $consultant_id = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					if ($this->mclinic->valid_clinic($clinic_id)) {

						if ($this->mdoctor->valid_doctor($consultant_id)) {

							$sessions = $this->mclinicsession->get_sessions_for_consultant($clinic_id, $consultant_id);

							$response->status = REST_Controller::HTTP_OK;
							$response->status_code = APIResponseCode::SUCCESS;
							$response->msg = 'Session Details for Consultant';
							$response->error_msg = NULL;
							$response->response['sessions'] = $sessions;
							$this->response($response, REST_Controller::HTTP_OK);

						} else {
							$response->status = REST_Controller::HTTP_BAD_REQUEST;
							$response->status_code = APIResponseCode::BAD_REQUEST;
							$response->msg = 'Invalid Session';
							$response->error_msg[] = 'Invalid Session';
							$response->response = NULL;
							$this->response($response, REST_Controller::HTTP_OK);
						}

					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid Clinic Id';
						$response->error_msg = NULL;
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function ViewSessionsforToday_get($clinic_id = '')
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'GET') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					if ($this->mclinic->valid_clinic($clinic_id)) {

						$sessions = $this->mclinicsession->get_sessions_ongoing($clinic_id, DateHelper::utc_day());

						if (!is_null($sessions)) {
							$response->status = REST_Controller::HTTP_OK;
							$response->status_code = APIResponseCode::SUCCESS;
							$response->msg = 'Session Details for today';
							$response->error_msg = NULL;
							$response->response['sessions'] = $sessions;
							$this->response($response, REST_Controller::HTTP_OK);
						} else {
							$response->status = REST_Controller::HTTP_OK;
							$response->status_code = APIResponseCode::SUCCESS;
							$response->msg = 'Session Details for Consultant';
							$response->error_msg = NULL;
							$response->response['sessions'] = NULL;
							$this->response($response, REST_Controller::HTTP_OK);
						}
					} else {
						$response->status = REST_Controller::HTTP_BAD_REQUEST;
						$response->status_code = APIResponseCode::BAD_REQUEST;
						$response->msg = 'Invalid Clinic Id';
						$response->error_msg = NULL;
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}
				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg[] = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg[] = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

		public function MakeComplaint_post($patient_id)
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'POST') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					$json_data = $this->post('json_data');

					// Passing post array to the model.
					$this->mcomplaints->set_data($json_data);

					// create the doctor record as the given data is valid
					$complaint = $this->mcomplaints->create($patient_id);

					if (!is_null($complaint)) {

						$response->status = REST_Controller::HTTP_OK;
						$response->status_code = APIResponseCode::SUCCESS;
						$response->msg = 'Complaint Added Successfully';
						$response->error_msg = NULL;
						$response->response = $complaint;
						$this->response($response, REST_Controller::HTTP_OK);

					} else {
						$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
						$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
						$response->msg = NULL;
						$response->error_msg[] = 'Internal Server Error';
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}

				} else {
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->msg = 'Unauthorized';
					$response->response = NULL;
					$response->error_msg = 'Invalid Authentication Key.';
					$this->response($response, REST_Controller::HTTP_OK);
				}
			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

	//endregion



	//region All API For Clinic

		public function NewInquiry_post()
		{
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'POST') {

					$json_data = $this->post('json_data');

					// Passing post array to the model.
					$this->minquiry->set_data($json_data);

					// create the doctor record as the given data is valid
					$complaint = $this->minquiry->create();

					if (!is_null($complaint)) {

						$response->status = REST_Controller::HTTP_OK;
						$response->status_code = APIResponseCode::SUCCESS;
						$response->msg = 'Inquiry Sent Successfully';
						$response->error_msg = NULL;
						$response->response = $complaint;
						$this->response($response, REST_Controller::HTTP_OK);

					} else {
						$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
						$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
						$response->msg = NULL;
						$response->error_msg[] = 'Internal Server Error';
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}


			} else {
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->msg = 'Method Not Allowed';
				$response->response = NULL;
				$response->error_msg = 'Invalid Request Method.';
				$this->response($response, REST_Controller::HTTP_OK);
			}
		}

	//endregion

	//region Payment API
		public function PaymentInit_post( $patient_id ){

			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			if ($method == 'POST') {

				try
				{

					$check_auth_client = $this->mmodel->check_auth_client();

					if ($check_auth_client == true) {

						$json_data = $this->post('json_data'); // Expecting this to contain payment_type, public_id, 
						$json_data['public_id'] = $patient_id;

						$this->payments->set_data($json_data);
						
						if($this->payments->is_valid()){

							$public = $this->mpublic->get( $patient_id );

							if (!empty($public) && is_object($public)){

								$mapper = new CareerMap($public->telephone);
								$career = $mapper->get_career_id();

								$transaction = $this->payments->init_transaction($patient_id);

								if(isset($transaction) && !empty($transaction)){

									if(PaymentType::Mobile == $this->payments->pay_type() ){

										/* [2021-04-16]
										 * Mobitel charges payment and OTP verification done by them self at their side. we dont have to worry about it.
										 * Malith from mspace confirmed it over the phone. 
										 */
										if($career == MobileCareer::Mobitel ){

											$request = MobitelRequestFactory::charge_request($public->mobile_mask, $transaction->transaction_id);

											$apiresponse = $this->mobitelcass->charge($request);

											//Log
											$this->payments->log(json_encode($apiresponse), $public->id);
											
											if(isset($apiresponse) && !empty($apiresponse) && is_object($apiresponse)){

												$this->payments->update_payment_ref($transaction->id, $apiresponse);

												if( strtoupper($apiresponse->statusCode) == "P1003" ){
													/*
													 * Mobitel will send/POST a notification back to given notification URL.
													 * We can generate an appointment to this patient only when we received that confirmation from mobitel
													 * hence we do not generate an appointment here
													 */
													$response->status = REST_Controller::HTTP_OK;
													$response->status_code = APIResponseCode::CONTINUE;
													$response->msg = 'Transaction initiated successfully';
													$response->error_msg = NULL;
													$response->response =  array( 'id'=> $transaction->id, 'pin_required' => false, 'appointment' => null );
													$this->response($response, REST_Controller::HTTP_OK);

												}else{

													// return failed include apirespons's error message
													$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
													$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
													$response->msg = NULL;
													$response->error_msg[] = 'Internal Server Error';
													$response->response = NULL;
													$this->response($response, REST_Controller::HTTP_OK);
												}
											}else{
												// return failed. unknown error
												$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
												$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
												$response->msg = NULL;
												$response->error_msg[] = 'Internal Server Error';
												$response->response = NULL;
												$this->response($response, REST_Controller::HTTP_OK);
											}

										}else if($career == MobileCareer::Dialog ){

											//echo "dialog ";
											$request = DialogRequestFactory::charge_request($public->telephone, "mynumber appointment charge", $transaction->id);
											$apiresponse = $this->dialogpin->charge($request);

											//Log
											$this->payments->log(json_encode($apiresponse), $public->id);
											
											//echo "charge response rcvd : " . print_r($apiresponse, true);



											if(isset($apiresponse) && !empty($apiresponse) && is_object($apiresponse)){

												$this->payments->update_payment_ref($transaction->id, $apiresponse);

												if( strtoupper($apiresponse->statusCode) == "SUCCESS" ){
													//return success
													$response->status = REST_Controller::HTTP_OK;
													$response->status_code = APIResponseCode::SUCCESS;
													$response->msg = 'Transaction initiated successfully, waiting for PIN';
													$response->error_msg = NULL;
													$response->response = array( 'id'=> $transaction->id, 'pin_required' => true , 'appointment' => null );
													$this->response($response, REST_Controller::HTTP_OK);
												}else{
													// return failed include apirespons's error message
													$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
													$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
													$response->msg = NULL;
													$response->error_msg[] = 'Internal Server Error- no success';
													$response->response = NULL;
													$this->response($response, REST_Controller::HTTP_OK);
												}
											}else{
												// return failed. unknown error
												$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
												$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
												$response->msg = NULL;
												$response->error_msg[] = 'Internal Server Error - if isset failed';
												$response->response = NULL;
												$this->response($response, REST_Controller::HTTP_OK);
											}//if apiresponse not null
										}// if mobitel else dialog

									} else {

										$response->status = REST_Controller::HTTP_OK;
										$response->status_code = APIResponseCode::SUCCESS;
										$response->msg = 'Transaction initiated successfully, waiting for IPG completion';
										$response->error_msg = NULL;
										$response->response =  array( 'id'=> $transaction->id, 'pin_required' => false , 'appointment' => null );
										$this->response($response, REST_Controller::HTTP_OK);
									}

								}//isset $transaction_id
								else{
									$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
									$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
									$response->msg = NULL;
									$response->error_msg[] = "Transaction initiation failed";
									$response->response = NULL;
									$this->response($response, REST_Controller::HTTP_OK);
								}

							} // isset public
							else{
								$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
								$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
								$response->msg = NULL;
								$response->error_msg[] = 'Patient not found';
								$response->response = NULL;
								$this->response($response, REST_Controller::HTTP_OK);
							}

						}//is post data vaid
						else{
							$response->status = REST_Controller::HTTP_BAD_REQUEST;
							$response->status_code = APIResponseCode::BAD_REQUEST;
							$response->msg = NULL;
							$response->error_msg[] = 'Invalid data';
							$response->response = NULL;
							$this->response($response, REST_Controller::HTTP_BAD_REQUEST);
						}
					}else{
						// Authentication failed. probabaly an attack
						$response->status = REST_Controller::HTTP_UNAUTHORIZED;
						$response->status_code = APIResponseCode::UNAUTHORIZED;
						$response->msg = NULL;
						$response->error_msg[] = 'Agent Authentication failed';
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_OK);
					}

				} catch(Exception $ex){
					$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
					$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
					$response->msg = NULL;
					$response->error_msg[] = sprintf('Internal server error %s', $ex->getMessage());
					$response->response = NULL;
					$this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
				}
			}//
			else{
				$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
				$response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
				$response->msg = NULL;
				$response->error_msg[] = 'Internal server error';
				$response->response = NULL;
				$this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
			}
		}


		public function MobilePayComplete_put($patient_id, $order_ref){
			//
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();
			
			// 
			// echo "$method";

			if ($method == 'PUT') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {
					
					$transaction = $this->payments->get($order_ref);

					// print_r($transaction);

					if(!is_null($transaction) && $transaction->public_id == $patient_id){

						$public = $this->mpublic->get( $patient_id );
						
						$mapper = new CareerMap($public->telephone);
						$career = $mapper->get_career_id();

						$post = $this->put('json_data');

						////////////////////
						// echo "post array: ";
						// print_r($post);
						// echo "---------------------";

						// die();

						$ipg_response = null;
						if( $career == MobileCareer::Dialog ){
							//echo  "dialog <br/>";
							//
							try{

								$otp_ref = json_decode($transaction->mobile_verification_ref);

								//////////////////////
								// echo "otpref: ";
								// print_r($otp_ref); echo "<br/>";

								$pin_verification_request = DialogRequestFactory::pin_verification_request($post['pin'], $otp_ref->data->serverRef);
								
								////////////////
								// echo "pin verification request: ";
								// print_r($pin_verification_request);

								$ipg_response =  $this->dialogpin->pin($pin_verification_request);

								////////////////
								// echo "ipg response: ";
								// print_r($ipg_response);

								if( !isset($ipg_response) || empty($ipg_response)){
									throw new Exception("response empty");
								}

								//Log
								$this->payments->log(json_encode($ipg_response), $public->id);

								$now = strtotime('now');
								$data['payment_date_time'] = date("Y-m-d H:i:s", $now);
								//set below fields only after receiving the response form mobile career
								$data['ipg_response'] = (empty($ipg_response)) ? null : json_encode($ipg_response);
								$data['ipg_response_time'] = date("Y-m-d H:i:s", $now);

								if( strtoupper( $ipg_response->statusCode ) == "SUCCESS") {

									$data['payment_status'] = PaymentStatus::Success;

									// have to grab an appointment number since this is success
									$number = $this->appointmentserialnumber->create($patient_id, $transaction->session_id);
									$appointment = $this->mclinicappointment->create($patient_id, $transaction->session_id, $number->serial_number_id);
									$appointment->serial_number = $this->mserialnumber->get($appointment->serial_number_id);
									$data['appointment_id']= $appointment->id; // grab this using get appointment id function;


									$this->payments->complete_payment($order_ref, $data);

									$response->status = REST_Controller::HTTP_OK;
									$response->status_code = APIResponseCode::SUCCESS;
									$response->msg = 'Appointment Successfull';
									$response->error_msg = NULL;
									$response->response = $appointment;
									$this->response($response, REST_Controller::HTTP_OK);


								}else if(strtoupper( $ipg_response->statusCode ) == "ERROR" && strtoupper( $ipg_response->message ) == "WRONG PIN"){
									// send back the correct pin
									$response->status = REST_Controller::HTTP_BAD_REQUEST;
									$response->status_code = APIResponseCode::BAD_REQUEST;
									$response->msg = null;
									$response->error_msg[] = 'wrong pin';
									$response->response = null;
									$this->response($response, REST_Controller::HTTP_OK);

								}else{

									//failed
									$data['payment_status'] = PaymentStatus::Failed;
									$this->payments->complete_payment($order_ref, $data);

									$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
									$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
									$response->msg = 'Payment failed - not success not error or wrong pin';
									$response->error_msg[] = $ipg_response->message;
									$response->response = NULL;
									$this->response($response, REST_Controller::HTTP_OK);
								}

							}catch(Exception $ex){

								$data['payment_status'] = PaymentStatus::Failed;
								$this->payments->complete_payment($order_ref, $data);

								$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
								$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
								$response->msg = 'Payment failed';
								$response->error_msg[] = $ipg_response->message;
								$response->error_msg[] = $ex->getMessage();
								$response->response = NULL;
								$this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
							}

						} else if($career == MobileCareer::Mobitel ){
							/*
							 * This is the confirmation of patient that he has accepted the payment.
							 * Check for the payment response received. "ipg response" from mobitel
							 * if it reflects the acceptance then make an appointment otherwise don't
							 */
							//echo  "mobitel <br/>";
							$mobitel_response = json_decode($transaction->ipg_response);

							if(isset($mobitel_response)  && !empty($mobitel_response) && is_object($mobitel_response) ){
								//
								if( strtoupper($mobitel_response->statusCode) == 'S1000'){
									//echo  "S1000 <br/>";
									// have to grab an appointment number since this is success
									$number = $this->appointmentserialnumber->create($patient_id, $transaction->session_id);
									$appointment = $this->mclinicappointment->create($patient_id, $transaction->session_id, $number->serial_number_id);
									$appointment->serial_number = $this->mserialnumber->get($appointment->serial_number_id);
									
									$data['appointment_id']= $appointment->id; // grab this using get appointment id function;
									$this->payments->complete_payment($order_ref, $data);

									$response->status = REST_Controller::HTTP_OK;
									$response->status_code = APIResponseCode::SUCCESS;
									$response->msg = 'Appointment Successfull';
									$response->error_msg = NULL;
									$response->response = $appointment;
									$this->response($response, REST_Controller::HTTP_OK);

								}else if(strtoupper($mobitel_response->statusCode) == 'E1406'){
									// rejected
									// no need to make appoitnment. customer has rejected the payment
									//
									//failed
									//echo  "E1406 <br/>";
									$response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
									$response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
									$response->msg = 'Payment failed, You have rejected the payment confirmation';
									$response->error_msg[] = $mobitel_response->statusDetail;
									$response->response = NULL;
									$this->response($response, REST_Controller::HTTP_OK);
								}
							} else {
								//echo  "mobitel response is null or not an object <br/>";
								// incorrect transaction id
								$response->status = REST_Controller::HTTP_CONTINUE;
								$response->status_code = APIResponseCode::CONTINUE;
								$response->msg = NULL;
								$response->error_msg[] = 'Confirm the payment first';
								$response->response = NULL;
								$this->response($response, REST_Controller::HTTP_OK);
							}
						}// else if career == mobitel
					}// if transaction count > 0
					else{

						// incorrect transaction id
						$response->status = REST_Controller::HTTP_UNAUTHORIZED;
						$response->status_code = APIResponseCode::UNAUTHORIZED;
						$response->msg = NULL;
						$response->error_msg[] = 'Transaction did not found';
						$response->response = NULL;
						$this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
					}
				}// if Auth == true
				else{
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = NULL;
					$response->error_msg[] = 'Authentication failed';
					$response->response = NULL;
					$this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
				}

			}// if PUT
			else{
				$response->status = REST_Controller::HTTP_UNAUTHORIZED;
				$response->status_code = APIResponseCode::UNAUTHORIZED;
				$response->msg = NULL;
				$response->error_msg[] = 'Invalid request method';
				$response->response = NULL;
				$this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
			// $this->response($response, $response->status);
		}


		public function PaymentComplete_put($patient_id, $order_ref){
			//
			$method = $_SERVER['REQUEST_METHOD'];
			$response = new stdClass();

			if ($method == 'PUT') {

				$check_auth_client = $this->mmodel->check_auth_client();

				if ($check_auth_client == true) {

					
					$transaction = $this->payments->get($order_ref);
					
					// print_r($transaction);

					if(!is_null($transaction) && $transaction->public_id == $patient_id){
						$post = $this->put('json_data');
						$now = strtotime('now');

                        if(!empty($post) && !is_object($post)){
                            $post= json_decode($post);
                        }

						/*
						{
							"data":{
								"currency":"LKR",
								"message":"Successfully completed the payment.",
								"paymentNo":320025138685,
								"price":10000,
								"sign":"2C21CEFC67F1E5C4E3FEFC6D1F6BCB10",
								"status":2
							},
							"message":"Payment success. Check response data",
							"status":1
						}
						*/
						
						
						//Log
						$this->payments->log(json_encode($post), $transaction->public_id);
						
						$data['payment_date_time'] = $now;
						$data['ipg_response'] = json_encode($post);
						$data['ipg_response_time'] = $now;

						if(isset($post) && (int)$post->data->status == PayHerePaymentStatus::OK ) {

							$data['payment_status'] = PaymentStatus::Success;

							// have to grab an appointment number since this is success
							$number = $this->appointmentserialnumber->create($patient_id, $transaction->session_id);							
							$appointment = $this->mclinicappointment->create($patient_id, $transaction->session_id, $number->serial_number_id);
							$appointment->serial_number = $this->mserialnumber->get($appointment->serial_number_id);
							$data['appointment_id']= $appointment->id; // grab this using get appointment id function;

							$response->status = REST_Controller::HTTP_OK;
							$response->status_code = APIResponseCode::SUCCESS;
							$response->msg = 'Appointment Successfull';
							$response->error_msg = NULL;
							$response->response = $appointment;
							$this->response($response, REST_Controller::HTTP_OK);
						}else{
							$data['payment_status'] = PaymentStatus::Failed;

							$response->status = REST_Controller::HTTP_UNAUTHORIZED;
							$response->status_code = APIResponseCode::UNAUTHORIZED;
							$response->msg = NULL;
							$response->error_msg[] = 'Status not found';
							$response->response = NULL;
						}

						$this->payments->complete_payment($order_ref, $data);
					}// if transaction count > 0
					else{

						// incorrect transaction id
						$response->status = REST_Controller::HTTP_UNAUTHORIZED;
						$response->status_code = APIResponseCode::UNAUTHORIZED;
						$response->msg = NULL;
						$response->error_msg[] = 'Transaction not found';
						$response->response = NULL;
					}
				}// if Auth == true
				else{
					$response->status = REST_Controller::HTTP_UNAUTHORIZED;
					$response->status_code = APIResponseCode::UNAUTHORIZED;
					$response->msg = NULL;
					$response->error_msg[] = 'Authentication failed';
					$response->response = NULL;
				}

			}// if PUT
			else{
				$response->status = REST_Controller::HTTP_UNAUTHORIZED;
				$response->status_code = APIResponseCode::UNAUTHORIZED;
				$response->msg = NULL;
				$response->error_msg[] = 'Invalid request method';
				$response->response = NULL;
			}

			$this->response($response, $response->status);

		}


		/*
		 * This will receives the notification from mspace

		 * If accepted

		 	{
				"timeStamp":"04-May-2021 17:08",
				"totalAmount":"7.00",
				"externalTrxId":"256091234",
				"balanceDue":"0",
				"statusDetail":"Request was Successfully processed, Due amount fully paid.",
				"currency":"LKR",
				"version":"1.0",
				"internalTrxId":"121050417080046",
				"paidAmount":"7.00",
				"referenceId":"330",
				"statusCode":"S1000"
			}

			If rejected

			{
				"timeStamp":"04-May-2021 17:11",
				"totalAmount":"0",
				"externalTrxId":"256091234",
				"balanceDue":"7.00",
				"statusDetail":"Charging Authorization Rejected.",
				"currency":"LKR",
				"version":"1.0",
				"internalTrxId":"121050417100047",
				"paidAmount":"0",
				"referenceId":"331",
				"statusCode":"E1406"
			}

		 */
		public function MobitelNotification_post(){
			
			$method = $_SERVER['REQUEST_METHOD'];
			if($method == 'POST') {
				
				$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);

				//Log
				$this->payments->log(json_encode($stream_clean), null);

				$response = json_decode($stream_clean);
				if(isset($response) && !empty($response) && is_object($response)){

					$now = strtotime('now');
					$data['payment_date_time'] = date("Y-m-d H:i:s", $now);
					$data['ipg_response'] = $stream_clean;
					$data['ipg_response_time'] = date("Y-m-d H:i:s", $now);

					if( strtoupper($response->statusCode == 'S1000') ){
						$data['payment_status'] = PaymentStatus::Success;
						$this->payments->complete_mobitel_payment( $response->externalTrxId, $data);
					}else if( strtoupper($response->statusCode == 'E1406') ){
						$data['payment_status'] = PaymentStatus::Failed;
						$this->payments->complete_mobitel_payment( $response->externalTrxId, $data);
					}
				}
			}
		}
	//endregion

}
