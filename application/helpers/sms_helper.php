<?php


class SMSSender{

    const mynumber_info = 'My Number';
}

class SMSType{

    const new_user_email = 1;
    const promotion_email = 2;
}


class SMSTemplate{

    public static function NewAppointmentSMS($data)
    {
        $myDateTime = DateTime::createFromFormat('Y-m-d', $data['appointment_date']);
        $appointment_date = $myDateTime->format('dS M');
        $myDateTime = DateTime::createFromFormat('H:i:s', $data['starting_time']);
        $appointment_time = $myDateTime->format('h:i A');
		$avg_time_per_patient = $data['avg_time_per_patient'];

		$date=DateTime::createFromFormat('Y-m-d H:i:s', $data['appointment_date'].' '.$data['starting_time']);;
		$minutes_to_add = $avg_time_per_patient*($data['serial_number']-1);
//		$est_date = new DateTime($est_date);
		$date->add(new DateInterval('PT' . $minutes_to_add . 'M'));
		$est_date= $date->format('Y-m-d H:i:s');
		$est_time= $date->format('h:i A');

		return "Your appointment with Dr. ".$data['doctor_name']." is confirmed.\nAppointment No: ".$data['serial_number']."\nStart Time: ".$appointment_date.' '.$appointment_time."\nEst.Time: ".$est_time;

//		return '' . $data['patient_name'] . ', Your appointment No. '.$data['serial_number'].' with Dr. '.$data['doctor_name'] .' at Clinic '.$data['clinic_name'].' '.$data['clinic_city'].', on '.$appointment_date.' '.$appointment_time.' is confirmed.';
    }

}

