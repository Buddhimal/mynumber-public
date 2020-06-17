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

$route['api/app/version/(:any)']['GET'] = 'api/consultant/GetAppVersion/$1';

//region Clinic Routes
$route['api/clinic']['POST'] = 'api/consultant/CreateClinic';
$route['api/clinic/(:any)']['GET'] = 'api/consultant/ClinicByUniqueId/$1';
$route['api/clinic/(:any)/consultant']['POST'] = 'api/consultant/RegisterConsultant/$1';
$route['api/clinic/consultant/(:any)']['GET'] = 'api/consultant/ConsultantByUniqueId/$1';
$route['api/clinic/(:any)/consultant']['GET'] = 'api/consultant/GetConsultantforClinic/$1';
$route['api/clinic/consultant/(:any)']['PUT'] = 'api/consultant/UpdateConsultant/$1';
$route['api/clinic/(:any)/sessions']['POST'] = 'api/consultant/AddClinicSessions/$1';
$route['api/clinic/(:any)/sessions/(:any)']['PUT'] = 'api/consultant/UpdateClinicSessions/$1/$2';
$route['api/clinic/(:any)/sessions/(:any)/ontheway']['PUT'] = 'api/consultant/SendOntheWayMessage/$1/$2';
$route['api/clinic/(:any)/holidays']['POST'] = 'api/consultant/AddHolidays/$1';
$route['api/clinic/(:any)/holidays']['GET'] = 'api/consultant/GetHolidaysByClinic/$1';
$route['api/clinic/(:any)/holidays/(:any)']['DELETE'] = 'api/consultant/DeleteHolidays/$1/$2';
$route['api/clinic/(:any)/session']['GET'] = 'api/consultant/ViewSessionsBClinic/$1';
$route['api/clinic/(:any)/session/day/(:any)']['GET'] = 'api/consultant/ViewSessionsByDay/$1/$2';
$route['api/clinic/(:any)/session/date/(:any)']['GET'] = 'api/consultant/ViewSessionsByDate/$1/$2';
$route['api/clinic/(:any)/session/today']['GET'] = 'api/consultant/ViewSessionsforToday/$1';
$route['api/clinic/(:any)/session/(:any)']['GET'] = 'api/consultant/ViewSessionsByID/$1/$2';
$route['api/clinic/(:any)/consultant/(:any)/sessions']['GET'] = 'api/consultant/ViewSessionsByConsultant/$1/$2';
$route['api/clinic/(:any)/session/(:any)/start']['PUT'] = 'api/consultant/StartSession/$1/$2';
$route['api/clinic/(:any)/session/(:any)/end']['PUT'] = 'api/consultant/EndSession/$1/$2';
$route['api/clinic/(:any)/session/(:any)/appointment/(:any)/next']['PUT'] = 'api/consultant/NextNumber/$1/$2/$3';
$route['api/clinic/(:any)/session/(:any)/appointment/(:any)/skip']['PUT'] = 'api/consultant/SkipNumber/$1/$2/$3';
// 

// payments routes
$route['api/clinic/(:any)/pay']['POST'] = 'api/consultant/DoPayment/$1';
$route['api/clinic/(:any)/payments/pending']['GET'] = 'api/consultant/ViewPaymentsPending/$1';
$route['api/clinic/(:any)/payments/done']['GET'] = 'api/consultant/ViewPaymentsDone/$1';
//endregion


//region Login & OTP Routes
$route['api/clinic/(:any)/otp/validate']['PUT'] = 'api/consultant/ValidateOTP/$1';
$route['api/clinic/(:any)/otp/resend']['PUT'] = 'api/consultant/ResendOTP/$1';
$route['api/otp/send/username']['PUT'] = 'api/consultant/SendOTPforUsername';
$route['api/clinic/auth/checkin']['POST'] = 'api/auth/checkin';
$route['api/clinic/auth/reset']['PUT'] = 'api/auth/ResetPassword';
$route['api/clinic/(:any)/auth/change']['PUT'] = 'api/auth/ChangePassword/$1';
//endregion


//region Public Routes
$route['api/patient/(:any)/session/(:any)/appointment']['POST'] = 'api/consultant/BookAppointment/$1/$2';
$route['api/patient/(:any)/session/(:any)/number']['GET'] = 'api/consultant/GetAppointmentNumber/$1/$2';
//endregion

// errors
$route['404_override'] = 'errors/index';
$route['translate_uri_dashes'] = FALSE;
// 