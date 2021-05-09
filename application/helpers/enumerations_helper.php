<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('SOFTGEN_MOBITEL_ACC_NUMBER', '94706908200');

class AppPackage{
	const PACKAGE_NAME = 'com.mynumber.patient';
}


class EntityType
{
	const Patient = 0;
	const Consultant = 1;
	const SalesRep = 2;
}


class APIResponseCode{
	
	const CONTINUE = 1000;
	const SUCCESS = 2000;
	const ALLREADY_EXISTS = 2008;
	const SUCCESS_WITH_ERRORS = 2001;
	const INTERNAL_SERVER_ERROR = 5000;
	const BAD_REQUEST = 4000;
	const UNAUTHORIZED = 4010;
	const METHOD_NOT_ALLOWED = 4050;
}


class AppointmentStatus{
	const PENDING =0;
	const CONSULTED =1;
	const NOT_CONSULTED =2;
	const CANCELED =3;
	const SKIPPED =4;
	const FINISH =5;
	const PAYMENT_COLLECT =6;
	const DOCTOR_CANCELED =7;

}


class StatusCode{
	const TRUE = 1;
	const FALSE = 0;
}

class SessionStatus{
	const PENDING = 0;
	const START = 1;
	const CANCELED = 2;
	const TIME_REVISED = 3;
	const FINISHED = 4;
	const TERMINATED = 5;
	const ON_THE_WAY = 6;
	const TIME_PASSED = 7;
}

class APIKeys{
	const SMS_API_KEY = 'eE1A9BvAe0ginsLlP9nhXvCbPAD5jJBw';
	const SMS_API_TOKEN = '2rRC1591199529';
	const DIALOG_API_KEY = '15958280864876';
	const SMS_SENDER_ID = 'MyNumber.lk';
	const PATIENT_API_KEY = 'E80C9BDE-3CC2-459B-991F-833F5356D731';
//	const PATIENT_API_KEY = 'simplerestapi';
}

class PayHerePaymentStatus{
	const OK =2;
	const PENDING =0;
	const CANCELLED = -1;
	const FAILED = -2;
	const CHARGEDBACK = -3;
}

class PaymentType
{
	const Mobile = 1;
	const Ipg = 2;
}

class PaymentStatus
{
	const Pending = 0;
	const Success = 1;
	const Credit = 2;
	const Failed = 3;
}


class Payments{
    const DEFAULT_CHARGE=50.00;
	// const DOCTORS_PAY = 30.00;

	// public static function get_percentage(){
	// 	return (self::DOCTORS_PAY/self::DEFAULT_CHARGE)*100;
	// }
}

// class PaymentCollectionStatus{
// 	const Pending=0;
// 	const Collected = 1;
// }

// class PaymentPaidStatus{
// 	const Pending=0;
// 	const Paid = 1;
// }

class SerialNumberStatus{
    const CONFIRM = 1;
    const PENDING = 0;
}

class MobileCareer{
	const Mobitel = 1;
	const Dialog = 2;
	const Hutch = 3;
	const Airtel = 4;
}

