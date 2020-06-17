<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/REST_Controller.php');

/**
 *
 */
class Consultant extends REST_Controller
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
        $this->load->model('mpaymentreceivals', "payment_receivals");
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
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
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

                if(!is_null($app_info)){

                    $response->status = REST_Controller::HTTP_OK;
                    $response->status_code = APIResponseCode::SUCCESS;
                    $response->msg = 'App Details';
                    $response->response = $app_info;
                    $response->error_msg = null;
                    $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);

                } else{
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid App Name';
                    $response->response = NULL;
                    $response->error_msg = null;
                    $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
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


    //region All API for Consultant
    //	public function RegisterConsultant_post()
    //	{
    //		$method = $_SERVER['REQUEST_METHOD'];
    //		$response = new stdClass();
    //		if ($method == 'POST') {
    //
    //			$check_auth_client = $this->mmodel->check_auth_client();
    //
    //			if ($check_auth_client == true) {
    //
    //				// Passing post array to the model.
    //				$this->mdoctor->set_data($this->input->post());
    //
    //				// model it self will validate the input data
    //				if ($this->mdoctor->is_valid()) {
    //
    //					// create the doctor record as the given data is valid
    //					$doctor = $this->mdoctor->create();
    //
    //					if (!is_null($doctor)) {
    //						$response->status = REST_Controller::HTTP_OK;
    //						$response->msg = 'New Doctor Added Successfully';
    //						$response->error_msg = NULL;
    //						$response->response = $doctor;
    //						$this->response($response, REST_Controller::HTTP_OK);
    //					}
    //				} else {
    //					$response->status = REST_Controller::HTTP_BAD_REQUEST;
    //					$response->msg = 'Validation Failed.';
    //					$response->response = NULL;
    //					$response->error_msg = $this->mdoctor->validation_errors;
    //					$this->response($response, REST_Controller::HTTP_BAD_REQUEST);
    //				}
    //			} else {
    //				$response->status = REST_Controller::HTTP_UNAUTHORIZED;
    //				$response->msg = 'Unauthorized';
    //				$response->response = NULL;
    //				$response->error_msg = 'Invalid Authentication Key.';
    //				$this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
    //			}
    //		} else {
    //			$response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
    //			$response->msg = 'Method Not Allowed';
    //			$response->response = NULL;
    //			$response->error_msg = 'Invalid Request Method.';
    //			$this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
    //		}
    //	}
    //endregion


    //region All API for Public
    public function RegisterPublic_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();
        if ($method == 'POST') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                // Passing post array to the model.
                $this->mpublic->set_data($this->input->post());

                // model it self will validate the input data
                if ($this->mpublic->is_valid()) {

                    // create the doctor record as the given data is valid
                    $public = $this->mpublic->create();

                    if (!is_null($public)) {
                        $response->status = REST_Controller::HTTP_OK;
                        $response->msg = 'New Public Added Successfully';
                        $response->error_msg = NULL;
                        $response->response = $public;
                        $this->response($response, REST_Controller::HTTP_OK);
                    }
                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->msg = 'Validation Failed.';
                    $response->response = NULL;
                    $response->error_msg = $this->mpublic->validation_errors;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    public function PublicByUniqueId_get()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();
        if ($method == 'GET') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                $public = $this->mpublic->get($this->input->get('id'));

                $response->status = REST_Controller::HTTP_OK;
                $response->msg = 'Public Details';
                $response->error_msg = NULL;
                $response->response = $public;
                $this->response($response, REST_Controller::HTTP_OK);


            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
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
                        $response->msg = 'Public Updated Successfully';
                        $response->error_msg = NULL;
                        $response->response = $public;
                        $this->response($response, REST_Controller::HTTP_OK);
                    } else {
                        $response->status = REST_Controller::HTTP_OK;
                        $response->msg = 'No Records to Update';
                        $response->error_msg = NULL;
                        $response->response = $public;
                        $this->response($response, REST_Controller::HTTP_OK);
                    }
                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->msg = 'Validation Failed.';
                    $response->response = NULL;
                    $response->error_msg = $this->mpublic->validation_errors;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
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
                            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                        } else {
                            $response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
                            $response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
                            $response->msg = 'Failed to create number';
                            $response->error_msg[] = 'Failed to create number';
                            $response->response = null;
                            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                        }

                    } else {
                        $response->status = REST_Controller::HTTP_BAD_REQUEST;
                        $response->status_code = APIResponseCode::BAD_REQUEST;
                        $response->msg = 'Invalid Session Id';
                        $response->error_msg[] = 'Invalid Session Id';
                        $response->response = NULL;
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }

                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Public Id';
                    $response->error_msg[] = 'Invalid Public Id';
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->error_msg[] = 'Invalid Authentication Key.';
                $response->response = NULL;
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->error_msg[] = 'Invalid Request Method.';
            $response->response = NULL;
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
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
                                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                                    } else {
                                        $response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
                                        $response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
                                        $response->msg = 'Failed to confirm Appointment';
                                        $response->error_msg[] = 'Failed to create Appointment';
                                        $response->response = null;
                                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                                    }
                                } else {
                                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                                    $response->status_code = APIResponseCode::BAD_REQUEST;
                                    $response->msg = 'Validation Failed.';
                                    $response->error_msg = $this->mclinicappointment->validation_errors;
                                    $response->response = NULL;
                                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                                }


                            } else {
                                $response->status = REST_Controller::HTTP_BAD_REQUEST;
                                $response->status_code = APIResponseCode::BAD_REQUEST;
                                $response->msg = 'Invalid Serial Number';
                                $response->error_msg[] = 'Invalid Serial Number';
                                $response->response = NULL;
                                $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                            }
                        } else {
                            $response->status = REST_Controller::HTTP_BAD_REQUEST;
                            $response->status_code = APIResponseCode::BAD_REQUEST;
                            $response->msg = 'Number Expired';
                            $response->error_msg[] = 'Number Expired';
                            $response->response = NULL;
                            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                        }


                    } else {
                        $response->status = REST_Controller::HTTP_BAD_REQUEST;
                        $response->status_code = APIResponseCode::BAD_REQUEST;
                        $response->msg = 'Invalid Session Id';
                        $response->error_msg[] = 'Invalid Session Id';
                        $response->response = NULL;
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }

                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Public Id';
                    $response->error_msg[] = 'Invalid Public Id';
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
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
                $response->response = $clinic;
                $this->response($response, REST_Controller::HTTP_OK);

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
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
                $response->response = $clinic;
                $this->response($response, REST_Controller::HTTP_OK);

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    //endregion


    //region All API For Clinic

    public function CreateClinic_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();
        if ($method == 'POST') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                $json_data = $this->post('json_data');

                // Passing post array to the model.
                $this->mclinic->set_data($json_data);

                // model it self will validate the input data
                if ($this->mclinic->is_valid()) {

                    $this->mlocations->set_data($json_data);

                    //Validate location data
                    if ($this->mlocations->is_valid()) {

                        // create the Location record as the given data is valid
                        $locations = $this->mlocations->create();

                        if (!is_null($locations)) {
                            // create the Clinic record as the given data is valid
                            $clinic = $this->mclinic->create($locations->id);


                            if (!is_null($clinic)) {

                                $login_data['username'] = $json_data['email'];
                                $login_data['password'] = $json_data["password"];
                                $login_data['mobile'] = $json_data["device_mobile"];

                                $this->mlogin->set_data($login_data);

                                $login = $this->mlogin->create($clinic->id, EntityType::Consultant); // return true or false

                                if ($login) {
                                    $this->motpcode->create($clinic->id, $login_data['mobile']);
                                }

                                $clinic->location = $locations;

                                $response->status = REST_Controller::HTTP_OK;
                                $response->status_code = APIResponseCode::SUCCESS;
                                $response->msg = 'New Clinic Added Successfully';
                                $response->error_msg = NULL;
                                $response->response = $clinic;
                                $this->response($response, REST_Controller::HTTP_OK);
                            } else {
                                $response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
                                $response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
                                $response->msg = NULL;
                                $response->error_msg[] = 'Internal Server Error';
                                $response->response = NULL;
                                $this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                            }
                        } else {
                            $response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
                            $response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
                            $response->msg = NULL;
                            $response->error_msg = 'Internal Server Error';
                            $response->response = NULL;
                            $this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    } else {
                        $response->status = REST_Controller::HTTP_BAD_REQUEST;
                        $response->status_code = APIResponseCode::BAD_REQUEST;
                        $response->msg = 'Validation Failed.';
                        $response->response = NULL;
                        $response->error_msg = $this->mlocations->validation_errors;
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }

                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Validation Failed.';
                    $response->response = NULL;
                    $response->error_msg = $this->mclinic->validation_errors;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }

    }

    public function ValidateOTP_put($clinic_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();
        if ($method == 'PUT') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                $this->motpcode->set_data($this->put('json_data'));


                if ($this->motpcode->is_valid($clinic_id)) {

                    $this->mlogin->confirm_login($clinic_id);

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
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    public function ResendOTP_put($clinic_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();
        if ($method == 'PUT') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                if ($this->mclinic->valid_clinic($clinic_id)) {

                    if ($this->motpcode->resend_otp($clinic_id)) {
                        $response->status = REST_Controller::HTTP_OK;
                        $response->status_code = APIResponseCode::SUCCESS;
                        $response->msg = 'OTP send successfully..';
                        $response->error_msg = NULL;
                        $response->response = NULL;
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    } else {
                        $response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
                        $response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
                        $response->msg = 'Failed to send OTP..';
                        $response->error_msg[] = 'Failed to send OTP..';
                        $response->response = NULL;
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }
                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->error_msg[] = 'Invalid Clinic Id';
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->error_msg[] = 'Invalid Authentication Key.';
                $response->response = NULL;
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->error_msg[] = 'Invalid Request Method.';
            $response->response = NULL;
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
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

                    if ($this->mlogin->get_login_for_username($json_data['username'])->entity_id != null) {

                        $this->ResendOTP_put($this->mlogin->get_login_for_username($json_data['username'])->entity_id);

                    } else {
                        $response->status = REST_Controller::HTTP_BAD_REQUEST;
                        $response->status_code = APIResponseCode::BAD_REQUEST;
                        $response->msg = 'Invalid Username..';
                        $response->error_msg[] = 'Invalid Username..';
                        $response->response = NULL;
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }
                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Validation Failed.';
                    $response->error_msg = $this->mlogin->validation_errors;
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->error_msg[] = 'Invalid Authentication Key.';
                $response->response = NULL;
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->error_msg[] = 'Invalid Request Method.';
            $response->response = NULL;
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
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
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }


            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    public function RegisterConsultant_post($clinic_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();
        $inserted_records = array();
        $validation_errors = array();


        if ($method == 'POST') {

            $json_data = ($this->post('json_data'));

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                if ($this->mclinic->valid_clinic($clinic_id)) {
                    foreach ($json_data['substitute'] as $substitute) {

                        // Passing post array to the model.
                        $this->mdoctor->set_data($substitute);

                        // model it self will validate the input data
                        if ($this->mdoctor->is_valid()) {

                            // create the doctor record as the given data is valid
                            $doctor = $this->mdoctor->create();

                            if (!is_null($doctor)) {

                                if ($this->mconsultantpool->create($doctor->id, $clinic_id) == true) {
                                    $inserted_records[] = $doctor;
                                }
                            }
                        } else {
                            $errors['msg'] = 'Validation Failed.';
                            // $errors['request_data'] = $substitute;
                            $errors['errors'] = $this->mdoctor->validation_errors;
                            $validation_errors[] = $errors;
                        }
                    }

                    if (sizeof($validation_errors) == 0) {
                        $response->status = REST_Controller::HTTP_OK;
                        $response->status_code = APIResponseCode::SUCCESS;
                        $response->msg = 'Success';
                        $response->error_msg = NULL;
                        $response->response['clinic'] = $clinic_id;
                        $response->response['substitutes'] = $inserted_records;
                        $response->error_msg = null;
                        $this->response($response, REST_Controller::HTTP_OK);
                    } else {
                        $response->status = REST_Controller::HTTP_OK;
                        $response->status_code = APIResponseCode::SUCCESS_WITH_ERRORS;
                        $response->msg = 'Success';
                        $response->error_msg = NULL;
                        $response->response['clinic'] = $clinic_id;
                        $response->response['substitutes'] = $inserted_records;
                        $response->error_msg = $validation_errors;
                        $this->response($response, REST_Controller::HTTP_OK);
                    }


                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->error_msg[] = 'Invalid Clinic Id';
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    public function ConsultantByUniqueId_get($consultant_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();
        if ($method == 'GET') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                $doctor = $this->mdoctor->get($consultant_id);

                $response->status = REST_Controller::HTTP_OK;
                $response->status_code = APIResponseCode::SUCCESS;
                $response->msg = 'Doctor Details';
                $response->error_msg = NULL;
                $response->response = $doctor;
                $this->response($response, REST_Controller::HTTP_OK);


            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    public function GetConsultantforClinic_get($clinic_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();
        if ($method == 'GET') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {


                if ($this->mclinic->valid_clinic($clinic_id)) {

                    $consultant_list = $this->mconsultantpool->get_consultant_for_clinic($clinic_id);


                    $response->status = REST_Controller::HTTP_OK;
                    $response->status_code = APIResponseCode::SUCCESS;
                    $response->msg = 'Consultants for Clinic';
                    $response->error_msg = NULL;
                    $response->response['consultant'] = $consultant_list;
                    $this->response($response, REST_Controller::HTTP_OK);

                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->error_msg[] = 'Invalid Clinic Id';
                    $response->response = NULL;
                    // $response->request_data = $this->post();
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);

                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    public function UpdateConsultant_put($doctor_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();
        if ($method == 'PUT') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                // Passing put array to the model.
                $this->mdoctor->set_data($this->put());

                // model it self will validate the input data
                if ($this->mdoctor->is_valid()) {

                    // update the doctor record as the given data is valid
                    $doctor = $this->mdoctor->update($doctor_id);

                    if (!is_null($doctor)) {

                        unset($mdoctor);

                        $response->status = REST_Controller::HTTP_OK;
                        $response->status_code = APIResponseCode::SUCCESS;
                        $response->msg = 'Doctor Updated Successfully';
                        $response->error_msg = NULL;
                        $response->response = $doctor;
                        $this->response($response, REST_Controller::HTTP_OK);
                    } else {
                        $response->status = REST_Controller::HTTP_OK;
                        $response->status_code = APIResponseCode::SUCCESS;
                        $response->msg = 'No Records to Update';
                        $response->error_msg = NULL;
                        $response->response = $doctor;
                        $this->response($response, REST_Controller::HTTP_OK);
                    }
                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Validation Failed.';
                    $response->response = NULL;
                    $response->error_msg = $this->mdoctor->validation_errors;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }  //need to recheck

    public function AddClinicSessions_post($clinic_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();
        $inserted_records = array();
        $validation_errors = array();

        if ($method == 'POST') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                if ($this->mclinic->valid_clinic($clinic_id)) {

                    $json_data = ($this->post('json_data'));

                    foreach ($json_data['sessions'] as $session) {

                        // Passing post array to the model.
                        $this->mclinicsession->set_data($session);

                        // model it self will validate the input data
                        if ($this->mclinicsession->is_valid()) {

                            $clinic_session = $this->mclinicsession->create($clinic_id);

                            if (!is_null($clinic_session)) {

                                foreach ($session['days'] as $days) {

                                    $this->mclinicsessiondays->set_data($days);

                                    if ($this->mclinicsessiondays->is_valid()) {

                                        $session_days = $this->mclinicsessiondays->create($clinic_id, $clinic_session->id);

                                        if (!is_null($session_days)) {

                                            $clinic_session->days[] = $session_days;

                                        } else {
                                            //internal server error
                                        }
                                    } else {
                                        $errors['msg'] = 'Validation Failed.';
                                        $errors['request_data'] = $days;
                                        $errors['errors'] = $this->mclinicsession->validation_errors;
                                        $validation_errors[] = $errors;
                                    }
                                }
                            } else {
                                //internal server error
                            }
                            if (!is_null($clinic_session))
                                $inserted_records[] = $clinic_session;
                            $clinic_session = null;

                        } else {
                            $errors['msg'] = 'Validation Failed.';
                            $errors['request_data'] = $session;
                            $errors['errors'] = $this->mclinicsession->validation_errors;
                            $validation_errors[] = $errors;
                        }
                    }

                    if (sizeof($validation_errors) == 0) {
                        $response->status = REST_Controller::HTTP_OK;
                        $response->status_code = APIResponseCode::SUCCESS;
                        $response->msg = 'Success';
                        $response->error_msg = NULL;
                        $response->response['sessions'] = $inserted_records;
                        $this->response($response, REST_Controller::HTTP_OK);
                    } else {
                        $response->status = REST_Controller::HTTP_OK;
                        $response->status_code = APIResponseCode::SUCCESS_WITH_ERRORS;
                        $response->msg = 'Success with errors';
                        $response->error_msg = $validation_errors;
                        $response->response['sessions'] = $inserted_records;
                        $this->response($response, REST_Controller::HTTP_OK);
                    }

                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->error_msg = NULL;
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->error_msg[] = 'Invalid Authentication Key.';
                $response->response = NULL;
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }

        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->error_msg[] = 'Invalid Request Method.';
            $response->response = NULL;
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    public function UpdateClinicSessions_put($clinic_id = '', $session_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();
        $inserted_records = array();
        $validation_errors = array();
        $sessions = array();

        if ($method == 'PUT') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                if ($this->mclinic->valid_clinic($clinic_id)) {

                    if ($this->mclinicsession->valid_session($session_id)) {

                        $json_data = $this->put('json_data');

                        foreach ($json_data['sessions'] as $session) {

                            // Passing post array to the model.
                            $this->mclinicsession->set_data($session);

                            // model it self will validate the input data
                            if ($this->mclinicsession->is_valid()) {

                                if ($this->mclinicsession->update($session_id)) {

                                    foreach ($session['days'] as $days) {

                                        $this->mclinicsessiondays->set_data($days);

                                        if ($this->mclinicsessiondays->is_valid()) {

                                            $this->mclinicsessiondays->update($days['id']);

                                        } else {
                                            $errors['msg'] = 'Validation Failed.';
                                            $errors['request_data'] = $days;
                                            $errors['errors'] = $this->mclinicsessiondays->validation_errors;
                                            $validation_errors[] = $errors;
                                        }
                                    }
                                }

                            } else {
                                $errors['msg'] = 'Validation Failed.';
                                $errors['request_data'] = $session;
                                $errors['errors'] = $this->mclinicsession->validation_errors;
                                $validation_errors[] = $errors;
                            }
                            $sessions = $this->mclinicsession->get_full_session($session_id);
                        }

                        if (sizeof($validation_errors) == 0) {
                            $response->status = REST_Controller::HTTP_OK;
                            $response->status_code = APIResponseCode::SUCCESS;
                            $response->msg = 'Success';
                            $response->error_msg = NULL;
                            $response->response['sessions'] = $sessions;
                            $this->response($response, REST_Controller::HTTP_OK);
                        } else {
                            $response->status = REST_Controller::HTTP_OK;
                            $response->status_code = APIResponseCode::SUCCESS_WITH_ERRORS;
                            $response->msg = 'Success with errors';
                            $response->error_msg = $validation_errors;
                            $response->response['sessions'] = $sessions;
                            $this->response($response, REST_Controller::HTTP_OK);
                        }

                    } else {
                        $response->status = REST_Controller::HTTP_BAD_REQUEST;
                        $response->status_code = APIResponseCode::BAD_REQUEST;
                        $response->msg = 'Invalid Session Id';
                        $response->error_msg = NULL;
                        $response->response = NULL;
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }
                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->error_msg = NULL;
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->error_msg[] = 'Invalid Authentication Key.';
                $response->response = NULL;
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }

        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->error_msg[] = 'Invalid Request Method.';
            $response->response = NULL;
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    } // not complete

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
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
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
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);

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
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
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
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);

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
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
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
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }

                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->error_msg = NULL;
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
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
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }

                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->error_msg = NULL;
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
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


                    $sessions = $this->mclinicsession->get_sessions_for_day($clinic_id, DateHelper::utc_day());

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
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    public function AddHolidays_post($clinic_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();

        if ($method == 'POST') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                if ($this->mclinic->valid_clinic($clinic_id)) {

                    $this->mclinicholidays->set_data($this->post('json_data'));

                    //Validate location data
                    if ($this->mclinicholidays->is_valid()) {


                        // create the holiday record as the given data is valid
                        $holiday = $this->mclinicholidays->create($clinic_id);

                        if (!is_null($holiday)) {
                            $response->status = REST_Controller::HTTP_OK;
                            $response->status_code = APIResponseCode::SUCCESS;
                            $response->msg = 'New Holiday Added Successfully';
                            $response->error_msg = NULL;
                            $response->response['holiday'] = $holiday;
                            $this->response($response, REST_Controller::HTTP_OK);
                        }
                    } else {
                        $response->status = REST_Controller::HTTP_BAD_REQUEST;
                        $response->status_code = APIResponseCode::BAD_REQUEST;
                        $response->msg = 'Validation Failed.';
                        $response->error_msg = $this->mclinicholidays->validation_errors;
                        $response->response = NULL;
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }


                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->error_msg[] = 'Invalid Clinic Id';
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->error_msg[] = 'Invalid Authentication Key.';
                $response->response = NULL;
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->error_msg[] = 'Invalid Request Method.';
            $response->response = NULL;
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }


    public function DeleteHolidays_delete($clinic_id = '', $holiday_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();

        if ($method == 'DELETE') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                if ($this->mclinic->valid_clinic($clinic_id)) {

                    if ($this->mclinicholidays->valid_holiday($holiday_id)) {

                        $this->mclinicholidays->delete($clinic_id, $holiday_id);

                        $holiday = $this->mclinicholidays->get_holidays($clinic_id);

                        $response->status = REST_Controller::HTTP_OK;
                        $response->status_code = APIResponseCode::SUCCESS;
                        $response->msg = 'Holiday Deleted Successfully';
                        $response->error_msg = NULL;
                        $response->response['holiday'] = $holiday;
                        $this->response($response, REST_Controller::HTTP_OK);

                    } else {
                        $response->status = REST_Controller::HTTP_BAD_REQUEST;
                        $response->status_code = APIResponseCode::BAD_REQUEST;
                        $response->msg = 'Invalid Holiday Id';
                        $response->error_msg[] = 'Invalid Holiday Id';
                        $response->response = NULL;
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }
                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->error_msg[] = 'Invalid Clinic Id';
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->error_msg[] = 'Invalid Authentication Key.';
                $response->response = NULL;
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->error_msg[] = 'Invalid Request Method.';
            $response->response = NULL;
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    public function GetHolidaysByClinic_get($clinic_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();

        if ($method == 'GET') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                if ($this->mclinic->valid_clinic($clinic_id)) {

                    $this->mclinicholidays->set_data($this->post('json_data'));

                    // create the holiday record as the given data is valid
                    $holiday = $this->mclinicholidays->get_holidays($clinic_id);

                    $response->status = REST_Controller::HTTP_OK;
                    $response->status_code = APIResponseCode::SUCCESS;
                    $response->msg = 'Holidays List';
                    $response->error_msg = NULL;
                    $response->response['holiday'] = $holiday;
                    $this->response($response, REST_Controller::HTTP_OK);

                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->error_msg[] = 'Invalid Clinic Id';
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->error_msg[] = 'Invalid Authentication Key.';
                $response->response = NULL;
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->error_msg[] = 'Invalid Request Method.';
            $response->response = NULL;
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    public function StartSession_put($clinic_id = '', $session_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();

        if ($method == 'PUT') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                if ($this->mclinic->valid_clinic($clinic_id)) {

                    if ($this->mclinicsession->valid_session($session_id)) {

                        if ($this->mclinicsessiontrans->start_session($session_id)) {

                            $appointment = $this->mclinicappointment->get_next_appointment($clinic_id, $session_id, null);

                            $response->status = REST_Controller::HTTP_OK;
                            $response->status_code = APIResponseCode::SUCCESS;
                            $response->msg = 'Session Start Successfully';
                            $response->error_msg = null;
                            $response->response['appointment'] = $appointment;
                            $response->response['session_meta'] = $this->mclinicsession->get_session_meta($clinic_id, $session_id);
                            $this->response($response, REST_Controller::HTTP_OK);

                        } else {
                            $response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
                            $response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
                            $response->msg = 'Internal Server Error';
                            $response->error_msg[] = "Internal Server Error";
                            $response->response = null;
                            $this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        }

                    } else {
                        $response->status = REST_Controller::HTTP_BAD_REQUEST;
                        $response->status_code = APIResponseCode::BAD_REQUEST;
                        $response->msg = 'Invalid Session Id';
                        $response->response = NULL;
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }

                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
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

    public function EndSession_put($clinic_id = '', $session_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();

        if ($method == 'PUT') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                if ($this->mclinic->valid_clinic($clinic_id)) {

                    if ($this->mclinicsession->valid_session($session_id)) {

                        if ($this->mclinicsessiontrans->finish_session($session_id)) {

//                            $appointment = $this->mclinicappointment->get_next_appointment($clinic_id, $session_id, null);

                            $response->status = REST_Controller::HTTP_OK;
                            $response->status_code = APIResponseCode::SUCCESS;
                            $response->msg = 'Session Finished Successfully';
                            $response->error_msg = null;
//                            $response->response['appointment'] = null;
                            $response->response['session_meta'] = $this->mclinicsession->get_session_meta($clinic_id, $session_id);
                            $this->response($response, REST_Controller::HTTP_OK);

                        } else {
                            $response->status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
                            $response->status_code = APIResponseCode::INTERNAL_SERVER_ERROR;
                            $response->msg = 'Internal Server Error';
                            $response->error_msg[] = "Internal Server Error";
                            $response->response = null;
                            $this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        }

                    } else {
                        $response->status = REST_Controller::HTTP_BAD_REQUEST;
                        $response->status_code = APIResponseCode::BAD_REQUEST;
                        $response->msg = 'Invalid Session Id';
                        $response->response = NULL;
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }

                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
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

    public function NextNumber_put($clinic_id = '', $session_id = '', $appointment_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();

        if ($method == 'PUT') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                if ($this->mclinic->valid_clinic($clinic_id)) {

                    if ($this->mclinicsession->valid_session($session_id)) {

                        $appointment_data = $this->mclinicappointment->get($appointment_id);

                        if (!is_null($appointment_data)) {

                            if ($this->mclinicappointment->update_appointment_status($appointment_id, AppointmentStatus::CONSULTED)) {

                                $appointment = $this->mclinicappointment->get_next_appointment($clinic_id, $session_id, $appointment_data->patient_id);

                                $response->status = REST_Controller::HTTP_OK;
                                $response->status_code = APIResponseCode::SUCCESS;
                                $response->msg = 'Next Appointment Number';
                                $response->error_msg = null;
                                $response->response['appointment'] = $appointment;
                                $response->response['session_meta'] = $this->mclinicsession->get_session_meta($clinic_id, $session_id);
                                $this->response($response, REST_Controller::HTTP_OK);

                            } else {
                                $response->status = REST_Controller::HTTP_BAD_REQUEST;
                                $response->status_code = APIResponseCode::BAD_REQUEST;
                                $response->msg = null;
                                $response->error_msg[] = 'Invalid Appointment Number';
                                $response->response = NULL;
                                $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                            }
                        } else {
                            $response->status = REST_Controller::HTTP_BAD_REQUEST;
                            $response->status_code = APIResponseCode::BAD_REQUEST;
                            $response->msg = null;
                            $response->error_msg[] = 'Invalid Appointment Number';
                            $response->response = NULL;
                            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                        }
                    } else {
                        $response->status = REST_Controller::HTTP_BAD_REQUEST;
                        $response->status_code = APIResponseCode::BAD_REQUEST;
                        $response->msg = 'Invalid Session Id';
                        $response->error_msg[] = 'Invalid Session Id';
                        $response->response = NULL;
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }

                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->error_msg[] = 'Invalid Clinic Id';
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }

        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    public function SkipNumber_put($clinic_id = '', $session_id = '', $appointment_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();

        if ($method == 'PUT') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                if ($this->mclinic->valid_clinic($clinic_id)) {

                    if ($this->mclinicsession->valid_session($session_id)) {

                        if ($this->mclinicappointment->update_appointment_status($appointment_id, AppointmentStatus::SKIPPED)) {

                            $appointment = $this->mclinicappointment->get_next_appointment($session_id);

                            $response->response['session_meta'] = $this->mclinicsession->get_session_meta($clinic_id, $session_id);

                            $response->status = REST_Controller::HTTP_OK;
                            $response->status_code = APIResponseCode::SUCCESS;
                            $response->msg = 'Next Appointment Number';
                            $response->error_msg = null;
                            $response->response['appointment'] = $appointment;
                            $response->response['session_meta'] = $this->mclinicsession->get_session_meta($clinic_id, $session_id);
                            $this->response($response, REST_Controller::HTTP_OK);

                        } else {
                            $response->status = REST_Controller::HTTP_BAD_REQUEST;
                            $response->status_code = APIResponseCode::BAD_REQUEST;
                            $response->msg = null;
                            $response->error_msg[] = 'Invalid Appointment Number';
                            $response->response = NULL;
                            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                        }

                    } else {
                        $response->status = REST_Controller::HTTP_BAD_REQUEST;
                        $response->status_code = APIResponseCode::BAD_REQUEST;
                        $response->msg = 'Invalid Session Id';
                        $response->error_msg[] = 'Invalid Session Id';
                        $response->response = NULL;
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }
                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->error_msg[] = 'Invalid Clinic Id';
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }

        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    public function SendOntheWayMessage_put($clinic_id = '', $session_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();

        if ($method == 'PUT') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                if ($this->mclinic->valid_clinic($clinic_id)) {

                    if ($this->mclinicsession->valid_session($session_id)) {

                        $response->status = REST_Controller::HTTP_OK;
                        $response->status_code = APIResponseCode::SUCCESS;
                        $response->msg = 'On the way message sent successfully.';
                        $response->error_msg = null;
                        $response->response['msg'] = "On the way message sent successfully.";
                        $this->response($response, REST_Controller::HTTP_OK);

                    } else {
                        $response->status = REST_Controller::HTTP_BAD_REQUEST;
                        $response->status_code = APIResponseCode::BAD_REQUEST;
                        $response->msg = 'Invalid Session Id';
                        $response->error_msg[] = 'Invalid Session Id';
                        $response->response = NULL;
                        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }

                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->error_msg[] = 'Invalid Clinic Id';
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Invalid Authentication Key.';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }

        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }


    public function ViewPaymentsPending_get($clinic_id)
    {
        // verify that the clinic exists
        $response = new stdClass();

        if (ucwords($_SERVER['REQUEST_METHOD']) == 'GET') {
            $check_auth_client = $this->mmodel->check_auth_client();
            if ($check_auth_client == true) {

                if ($this->mclinic->valid_clinic($clinic_id)) {
                    /* list the completed sessions from last paid date.*/

                    // determine the last paid date
                    $date_last_paid = $this->payment_receivals->get_last_paid_date($clinic_id);

                    if (!isset($date_last_paid) || empty($date_last_paid)) {
                        $date_last_paid = $this->mclinic->get($clinic_id)->created;
                    }

                    // get the list of session after the last paid date
                    $billable_sessions = $this->mclinicsessiontrans->get_sessions_tasks_completed_within($clinic_id, $date_last_paid);

                    // list the consulted transactions + per charge from appointment trans per each session.
                    if (isset($billable_sessions) && count($billable_sessions) > 0) {

                        // EntityClinicPendingPaymentDetails
                        $clinic_payment_pendings = $this->mclinicappointment->get_consulted_appoinments_for($clinic_id, $billable_sessions);
                        $clinic_payment_pendings->from = $date_last_paid;
                        $clinic_payment_pendings->to = date("Y-m-d");

                        $response->status = REST_Controller::HTTP_OK;
                        $response->status_code = APIResponseCode::SUCCESS;
                        $response->msg = 'successful';
                        $response->error_msg = null;
                        $response->response['pending_payments'] = $clinic_payment_pendings;

                        $this->response($response, REST_Controller::HTTP_OK);

                    } else {
                        // no billable sessions within the specified date range.
                        $response->status = REST_Controller::HTTP_OK;
                        $response->status_code = APIResponseCode::SUCCESS;
                        $response->msg = 'No payment pending for the given duration';
                        $response->error_msg = null;
                        $response->response = NULL;
                        $this->response($response, REST_Controller::HTTP_OK);
                    }

                } else {

                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->error_msg[] = 'Invalid Clinic';
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);

                }
            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Authentication Failed';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {

            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }

    }

    public function ViewPaymentsDone_get($clinic_id)
    {
        // verify that the clinic exists
        $response = new stdClass();

        if (ucwords($_SERVER['REQUEST_METHOD']) == 'GET') {
            $check_auth_client = $this->mmodel->check_auth_client();
            if ($check_auth_client == true) {

                if ($this->mclinic->valid_clinic($clinic_id)) {
                    /* list the completed sessions from last paid date.*/

                    // get last paid sessions
                    $paid_sessions = $this->payment_receivals->get_paid_records($clinic_id);

                    // list the consulted transactions + per charge from appointment trans per each session.
                    if (!is_null($paid_sessions)) {

                        // EntityClinicPendingPaymentDetails
                        $grand_total = $this->payment_receivals->get_cumulative_paid_amount($clinic_id);

                        $response->status = REST_Controller::HTTP_OK;
                        $response->status_code = APIResponseCode::SUCCESS;
                        $response->msg = 'successful';
                        $response->error_msg = null;
                        $response->response['paid_sessions'] = $paid_sessions;
                        $response->response['grand_total'] = $grand_total;

                        $this->response($response, REST_Controller::HTTP_OK);

                    } else {
                        // no billable sessions within the specified date range.
                        $response->status = REST_Controller::HTTP_OK;
                        $response->status_code = APIResponseCode::SUCCESS;
                        $response->msg = 'No payments done yet.';
                        $response->error_msg = null;
                        $response->response = NULL;
                        $this->response($response, REST_Controller::HTTP_OK);
                    }

                } else {

                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Clinic Id';
                    $response->error_msg[] = 'Invalid Clinic';
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);

                }
            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->response = NULL;
                $response->error_msg[] = 'Authentication Failed';
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {

            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->response = NULL;
            $response->error_msg[] = 'Invalid Request Method.';
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }


    public function DoPayment_post($clinic_id)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();

        if ($method == 'POST') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {
                $data = $this->post('json_data');


                $billable_sessions = $this->mclinicsessiontrans->get_sessions_tasks($clinic_id, $data['session_tasks']);
                $clinic_payment_pendings = $this->mclinicappointment->get_consulted_appoinments_for($clinic_id, $billable_sessions);

                $data['clinic_id'] = $clinic_id;
                $data['total'] = $clinic_payment_pendings->grand_total;

                $this->payment_receivals->set_data($data);

                if ($this->payment_receivals->is_valid()) {

                    $receival_record = $this->payment_receivals->create();

                    if (!is_null($receival_record)) {
                        $response->status = REST_Controller::HTTP_OK;
                        $response->status_code = APIResponseCode::SUCCESS;
                        $response->msg = 'Payment request Successful';
                        $response->error_msg = NULL;
                        $response->response['payment_request_id'] = $receival_record->id;
                        $this->response($response, REST_Controller::HTTP_OK);
                    }
                } else {
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Validation Failed.';
                    $response->error_msg = $this->payment_receivals->validation_errors;
                    $response->response = NULL;
                    $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                }

            } else {
                $response->status = REST_Controller::HTTP_UNAUTHORIZED;
                $response->status_code = APIResponseCode::UNAUTHORIZED;
                $response->msg = 'Unauthorized';
                $response->error_msg[] = 'Invalid Authentication Key.';
                $response->response = NULL;
                $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);

            }
        } else {
            $response->status = REST_Controller::HTTP_METHOD_NOT_ALLOWED;
            $response->status_code = APIResponseCode::METHOD_NOT_ALLOWED;
            $response->msg = 'Method Not Allowed';
            $response->error_msg[] = 'Invalid Request Method.';
            $response->response = NULL;
            $this->response($response, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }

        // $this->mclinicholidays->set_data($data);
        // $this->mclinicholidays->set_data();
    }


    public function send_email_get()
    {
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'mail.mynumber.lk',
            'smtp_port' => 465, //587
            'smtp_user' => 'info@mynumber.lk',
            'smtp_pass' => '!yoOA+3cwv&2',
            'mailtype' => 'html',
            'charset' => 'iso-8859-1',
            'newline' => "\r\n",
        );


//        $config = array(
//            'protocol' => 'smtp',
//            'smtp_host' => 'mail.smartloan.lk',
//            'smtp_port' => 587, //587
//            'smtp_user' => 'noreply@smartloan.lk',
//            'smtp_pass' => 'noreply//1',
//            'mailtype' => 'html',
//            'charset' => 'iso-8859-1',
//            'newline' => "\r\n",
//        );

        $this->load->library('email', $config);
        $ci = get_instance();
        $ci->email->initialize($config);

        // $ci->email->from('noreply@smartloan.lk', 'Smartloan.lk');
        // $ci->email->to("info@smartloan.lk");
        // $ci->email->subject("Message From Customer");
        // $ci->email->message("Customer Name ");
        // $ci->email->send();
        $data = null;

        $body = $this->load->view('fogot_password', $data, TRUE);

        $ci->email->from('MyNumber', 'MyNumber');
        $ci->email->to("bbb.navin@gmail.com");
        $ci->email->subject("Feedback");
        $ci->email->message($body);

        var_dump($this->email->send());
        // $this->load->view('fogot_password',$data);
    }


    //endregion

}
