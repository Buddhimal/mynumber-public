<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/REST_Controller.php');

class Auth extends REST_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model("mmodel");
        $this->load->model("mvalidation");
        $this->load->model("mlogin");
        $this->load->model("mclinic");
        $this->load->model("mpublic");
        $this->load->model("mclinicholidays");
        $this->load->model("mclinicsession");
        $this->load->model("mdoctor");
        $this->load->library('Utilityhandler');

    }

    //region Index
    public function index_get()
    {
        $response = new stdClass();
        $response->status = REST_Controller::HTTP_BAD_REQUEST;
        $response->msg = 'Invalid Request.';
        $response->error_msg = 'Invalid Request.';
        $response->response = NULL;
        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
    }

    public function index_post()
    {
        $response = new stdClass();
        $response->status = REST_Controller::HTTP_BAD_REQUEST;
        $response->msg = 'Invalid Request received to Auth Controller.';
        $response->error_msg = 'Invalid Request.';
        $response->response = NULL;
        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
    }

    public function index_put()
    {
        $response = new stdClass();
        $response->status = REST_Controller::HTTP_BAD_REQUEST;
        $response->msg = 'Invalid Request.';
        $response->error_msg = 'Invalid Request.';
        $response->response = NULL;
        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
    }

    public function index_delete()
    {
        $response = new stdClass();
        $response->status = REST_Controller::HTTP_BAD_REQUEST;
        $response->msg = 'Invalid Request.';
        $response->error_msg = 'Invalid Request.';
        $response->response = NULL;
        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
    }

    //endregion


    public function checkin_post()
    {
        $response = new stdClass();
        try {

            $check_auth_client = $this->mmodel->check_auth_client();
            if ($check_auth_client == true) {

                $inputs = $this->post('json_data');

                $this->mlogin->set_data($inputs);

                if ($this->mlogin->is_valid()) {

                    $this->mlogin->post['password'] = $this->utilityhandler->_salt($inputs["password"], $inputs['username']);

                    $consultant_login_data = $this->mlogin->get_login(EntityType::Patient);

//                    DatabaseFunction::last_query();

                    if ($consultant_login_data == NULL)
                        throw new Exception("Account not found");

                    if ($consultant_login_data->is_deleted == true)
                        throw new Exception("Trying to access deleted account");

                    if ($consultant_login_data->is_active === 0)
                        throw new Exception("Trying to access inactive account");

                    $clinic = $this->mpublic->get($consultant_login_data->entity_id);

                    //Sending back the reponse
                    $response->status = REST_Controller::HTTP_OK;
                    $response->status_code = APIResponseCode::SUCCESS;
                    $response->msg = 'Login Successfull';
                    $response->error_msg = null;
                    $response->response = $clinic;

                } else {
                    // Either username is empty or not an email or else password is empty
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Invalid Request.';
                    $response->error_msg[] = $this->mlogin->validation_errors;
                    $response->response = NULL;
                }
            }
        } catch (Exception $ex) {
            $response->status = REST_Controller::HTTP_BAD_REQUEST;
            $response->status_code = APIResponseCode::BAD_REQUEST;
            $response->msg = 'Failed to serve your request';
            $response->error_msg[] = $ex->getMessage();
            $response->response = NULL;
        }

        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function ResetPassword_put()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();
        if ($method == 'PUT') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                $inputs = $this->put('json_data');

                if ($this->mlogin->check_valid_account($inputs['username'])) {

                    $this->mlogin->set_data($inputs);

                    if ($this->mlogin->is_valid()) {

                        $this->mlogin->post['password'] = $this->utilityhandler->_salt($inputs["password"], $inputs['username']);

                        if ($this->mlogin->reset_password()) {
                            $response->status = REST_Controller::HTTP_OK;
                            $response->status_code = APIResponseCode::SUCCESS;
                            $response->msg = 'Password Reset Successful';
                            $response->error_msg = null;
                            $response->response['msg'] = 'Password Reset Successful';
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
                    $response->status = REST_Controller::HTTP_BAD_REQUEST;
                    $response->status_code = APIResponseCode::BAD_REQUEST;
                    $response->msg = 'Validation Failed.';
                    $response->response = NULL;
                    $response->error_msg = $this->mlogin->validation_errors;
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
            $response->error_msg[] = 'Invalid Request Method.';
            $response->response = NULL;
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

    public function ChangePassword_put($public_id = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $response = new stdClass();
        if ($method == 'PUT') {

            $check_auth_client = $this->mmodel->check_auth_client();

            if ($check_auth_client == true) {

                if ($this->mpublic->valid_public($public_id)) {

                    $inputs = $this->put('json_data');

                    $username = $this->mlogin->check_old_password($public_id, $inputs['old_password']); // returns username if old password match

                    if ($this->mvalidation->valid_password($inputs['new_password'])) {

                        if (!is_null($username)) {

                            if ($this->mlogin->change_password($public_id, $this->utilityhandler->_salt($inputs['new_password'], $username))) {
                                $response->status = REST_Controller::HTTP_OK;
                                $response->status_code = APIResponseCode::SUCCESS;
                                $response->msg = 'Password Reset Successful';
                                $response->error_msg = NULL;
                                $response->response['msg'] = 'Password Reset Successful';
                                $this->response($response, REST_Controller::HTTP_OK);
                            } else {
                                $response->status = REST_Controller::HTTP_BAD_REQUEST;
                                $response->status_code = APIResponseCode::BAD_REQUEST;
                                $response->msg = 'Failed to change your password.';
                                $response->error_msg = NULL;
                                $response->response = NULL;
                                $this->response($response, REST_Controller::HTTP_OK);
                            }

                        } else {
                            $response->status = REST_Controller::HTTP_BAD_REQUEST;
                            $response->status_code = APIResponseCode::BAD_REQUEST;
                            $response->msg = 'Invalid Old Password.';
                            $response->response = NULL;
                            $response->error_msg = NULL;
                            $this->response($response, REST_Controller::HTTP_OK);
                        }
                    } else {
                        $response->status = REST_Controller::HTTP_BAD_REQUEST;
                        $response->status_code = APIResponseCode::BAD_REQUEST;
                        $response->msg = 'Validation Failed.';
                        $response->error_msg = array('New Password must be between 6 and 20 digits long and include at least one numeric digit.');
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
                $response->error_msg[] = 'Invalid Authentication Key.';
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

}
