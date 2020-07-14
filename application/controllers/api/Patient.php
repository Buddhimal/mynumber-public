<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/REST_Controller.php');

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
        $this->load->model('mcommunicatoremailqueue','memail');
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

    public function ResendOTP_put($public_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();
        if ($method == 'PUT') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                if ($this->mpublic->valid_public($public_id)) {

                    if ($this->motpcode->resend_otp($public_id)) {
                        $response->status = REST_Controller::HTTP_OK;
                        $response->status_code = APIResponseCode::SUCCESS;
                        $response->msg = 'OTP send successfully..';
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

    //region All API for Public

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

                // model it self will validate the input data
                if ($this->mpublic->is_valid()) {

                    // create the doctor record as the given data is valid
                    $public = $this->mpublic->create();

//                    if (!is_null($public)) {
//                        $response->status = REST_Controller::HTTP_OK;
//                        $response->msg = 'New Public Added Successfully';
//                        $response->error_msg = NULL;
//                        $response->response = $public;
//                        $this->response($response, REST_Controller::HTTP_OK);
//                    }

                    if (!is_null($public)) {

                        $login_data['username'] = $json_data['email'];
                        $login_data['password'] = $json_data["password"];
                        $login_data['mobile'] = $json_data["telephone"];

                        $this->mlogin->set_data($login_data);

                        if ($this->mlogin->is_valid()) {

                            $login = $this->mlogin->create($public->id, EntityType::Patient);// return true or false

                            if ($login) {
                                $this->motpcode->create($public->id, $login_data['mobile']);
                            }

                            $response->status = REST_Controller::HTTP_OK;
                            $response->status_code = APIResponseCode::SUCCESS;
                            $response->msg = 'New Public Added Successfully';
                            $response->error_msg = NULL;
                            $response->response = $public;
                            $this->response($response, REST_Controller::HTTP_OK);
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
				$response->status_code = APIResponseCode::HTTP_UNAUTHORIZED;
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
				$response->status_code = APIResponseCode::HTTP_UNAUTHORIZED;
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

                    $numbers_count = $this->mclinicappointment->get_appointment_count($session_id,AppointmentStatus::PENDING);

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
                    $response->response=$payment_dues;
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
                    $response->response['appointments']=$appointments;
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

    public function GetAppointmentsHistory_get($patient_id = '',$month='')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();
        if ($method == 'GET') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                if ($this->mpublic->valid_public($patient_id)) {

					$appointments = $this->mclinicappointment->get_appointments_monthly($patient_id,$month);

                    $response->status = REST_Controller::HTTP_OK;
                    $response->status_code = APIResponseCode::SUCCESS;
                    $response->msg = 'Appointments Today';
                    $response->error_msg = NULL;
                    $response->response['appointments']=$appointments;
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
                    $response->response['session_id']=$session_id;
                    $response->response['on_going_number']=$get_ongoing_number;
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

    //endregion


    //region All API For Clinic

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

    //endregion

}
