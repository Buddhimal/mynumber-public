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
        $appointment_date = $myDateTime->format('d F');
        $myDateTime = DateTime::createFromFormat('H:i:s', $data['starting_time']);
        $appointment_time = $myDateTime->format('h:i A');

        return '' . $data['patient_name'] . ', Your appointment No. '.$data['serial_number'].' with Dr. '.$data['doctor_name'] .' at Clinic '.$data['clinic_name'].' '.$data['clinic_city'].', on '.$appointment_date.' '.$appointment_time.' is confirmed.';
    }

}

