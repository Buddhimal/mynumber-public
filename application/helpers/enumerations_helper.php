<?php

class EntityType
{
	const Patient = 0;
	const Consultant = 1;
	const SalesRep = 2;
}


class APIResponseCode{
	const SUCCESS = 2000;
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

}


class StatusCode{
	const TRUE = 1;
	const FALSE = 0;
}

class SessionStatus{
	const START = 1;
	const CANCELED = 2;
	const TIME_REVERSED = 3;
	const FINISHED = 4;
	const TERMINATED = 5;
}

class APIKeys{
    const SMS_API_KEY = 'eE1A9BvAe0ginsLlP9nhXvCbPAD5jJBw';
    const SMS_API_TOKEN = '2rRC1591199529';
    const SMS_SENDER_ID = 'MyNumber.lk';
}

class Payments{
    const DEFAULT_CHARGE=30.00;
}

class PaymentCollectionStatus{
	const Pending=0;
	const Collected = 1;
}

class PaymentPaidStatus{
	const Pending=0;
	const Paid = 1;
}

class SerialNumberStatus{
    const CONFIRM = 1;
    const PENDING = 0;
}


//class DateHelper
//{
//
//    public $minutes_to_add = 330;
//
//    public function utc_date($date){
//
//        $date = new DateTime($date);
//        $date->add(new DateInterval('PT' . $this->minutes_to_add . 'M'));
//        return $date->format('Y-m-d');
//    }
//
//    function utc_datetime($date){
//
//        $date = new DateTime($date);
//        $date->add(new DateInterval('PT' . $this->minutes_to_add . 'M'));
//        return $date->format('Y-m-d H:i:s');
//    }
//
//    function utc_time($date){
//
//        $date = new DateTime($date);
//        $date->add(new DateInterval('PT' . $this->minutes_to_add . 'M'));
//        return $date->format('H:i:s');
//    }
//}