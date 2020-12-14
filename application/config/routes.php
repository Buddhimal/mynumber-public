<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'index';

$route['api/app/version/(:any)']['GET'] = 'api/patient/GetAppVersion/$1';
$route['api/public/(:any)/app/version/(:any)']['PUT'] = 'api/patient/GetAppVersion/$1/$2';
$route['api/public/(:any)/complaint']['POST'] = 'api/patient/MakeComplaint/$1';

//region Test Routes
//$route['api/public/test_api']['POST'] = 'api/patient/testAPI';
//endregion

//region Login & OTP Routes
$route['api/public/auth/checkin']['POST'] = 'api/auth/checkin';
$route['api/public/(:any)/otp/validate']['PUT'] = 'api/patient/ValidateOTP/$1';
$route['api/public/(:any)/otp/resend']['PUT'] = 'api/patient/ResendOTP/$1';
$route['api/otp/send/username']['PUT'] = 'api/patient/SendOTPforUsername';
$route['api/public/auth/reset']['PUT'] = 'api/auth/ResetPassword';
$route['api/public/(:any)/auth/change']['PUT'] = 'api/auth/ChangePassword/$1';
//endregion


//region Public Routes
$route['api/public']['POST'] = 'api/patient/RegisterPublic';
$route['api/public/(:any)']['GET'] = 'api/patient/PublicByUniqueId/$1';
$route['api/public/(:any)']['PUT'] = 'api/patient/UpdatePublic/$1';
$route['api/public/(:any)/payment/due']['GET'] = 'api/patient/GetPaymentDues/$1';
$route['api/public/(:any)/appointments/today']['GET'] = 'api/patient/GetAppointmentsToday/$1';
$route['api/public/(:any)/appointments/monthly/(:any)']['GET'] = 'api/patient/GetAppointmentsHistory/$1/$2';
//$route['api/public/(:any)/appointments/(:any)/sessions/(:any)/ongoing']['GET'] = 'api/patient/GetAppointmentsToday/$1/$2/$3';
$route['api/search/clinic/location/(:any)/(:any)']['GET'] = 'api/patient/SearchClinicByLocation/$1/$2';
$route['api/search/clinic/doctor/(:any)']['GET'] = 'api/patient/SearchClinicByDoctor/$1';
$route['api/search/clinic/(:any)']['GET'] = 'api/patient/SearchClinicByName/$1';
$route['api/clinic/(:any)']['GET'] = 'api/patient/ClinicByUniqueId/$1';
$route['api/clinic/(:any)/sessions/today']['GET'] = 'api/patient/ViewSessionsforToday/$1';
//$route['api/session/(:any)/ongoing']['GET'] = 'api/patient/GetAppointmentCount/$1';
$route['api/session/(:any)/ongoing']['GET'] = 'api/patient/GetOngoingNumber/$1';
$route['api/public/(:any)/session/(:any)/number']['GET'] = 'api/patient/GetAppointmentNumber/$1/$2';
$route['api/public/(:any)/session/(:any)/appointment']['POST'] = 'api/patient/BookAppointment/$1/$2';
//endregion

//region Website Routes
$route['api/public/inquiry']['POST'] = 'api/patient/NewInquiry';
//endregion

// errors
$route['404_override'] = 'errors/index';
$route['translate_uri_dashes'] = FALSE;
//


