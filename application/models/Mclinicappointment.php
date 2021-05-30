<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'entities/EntityAppointments.php');

class Mclinicappointment extends CI_Model
{

	public $validation_errors = array();
	private $post = array();
	protected $table = "clinic_appointments";

	function __construct()
	{
		parent::__construct();
		$this->load->model('mvalidation');
		$this->load->model('appointmentserialnumber');
		$this->load->model('mclinicappointmenttrans');
		$this->load->model('mpublic');
		$this->load->model('mcommunicatoremailqueue', 'memail');
		$this->load->library('Messagesender');

	}

	public function set_data($post_array)
	{
		if (isset($post_array['serial_number_id']))
			$this->post['serial_number_id'] = $post_array['serial_number_id'];
		if (isset($post_array['name']))
			$this->post['patient_name'] = $post_array['name'];
		if (isset($post_array['address']))
			$this->post['patient_address'] = $post_array['address'];
		if (isset($post_array['phone']))
			$this->post['patient_phone'] = $post_array['phone'];
		if (isset($post_array['is_myself']))
			$this->post['is_myself'] = $post_array['is_myself'];
	}

	public function is_valid()
	{
		$result = true;

		if (!(isset($this->post['serial_number_id']) && $this->post['serial_number_id'] != NULL && $this->post['serial_number_id'] != '')) {
			array_push($this->validation_errors, 'Invalid serial number.');
			$result = false;
		}
		if (!(isset($this->post['patient_name']) && $this->post['patient_name'] != NULL && $this->post['patient_name'] != '')) {
			array_push($this->validation_errors, 'Invalid patient name.');
			$result = false;
		}
		if (!(isset($this->post['patient_address']) && $this->post['patient_address'] != NULL && $this->post['patient_address'] != '')) {
			array_push($this->validation_errors, 'Invalid patient address.');
			$result = false;
		}
		if (!(isset($this->post['patient_phone']) && $this->post['patient_phone'] != NULL && $this->post['patient_phone'] != '')) {
			array_push($this->validation_errors, 'Invalid patient phone.');
			$result = false;
		}

		return $result;
	}

	/*
	*
	*/
	public function create($patient_id, $session_id, $appointment_serial_number_id)
	{

		$patient = $this->mpublic->get($patient_id);

		$this->db->trans_start();

		$result = null;

		//confirm number
		if ($this->appointmentserialnumber->confirm_number($appointment_serial_number_id)) {

			$appointment_id = trim($this->mmodel->getGUID(), '{}');

			$this->post['id'] = $appointment_id;
			$this->post['session_id'] = $session_id;
			$this->post['appointment_date'] = DateHelper::slk_date();
//		$this->post['serial_number_id'] = $serial_number_id;
			$this->post['patient_id'] = $patient_id;
			$this->post['is_canceled'] = 0;
			$this->post['appointment_status'] = AppointmentStatus::PENDING;
			if ($patient->is_clinic) {
				$this->post['appointment_charge'] = 0;
				$this->post['doctors_pay'] = 0;
				$this->post['net_pay'] = 0;
			} else {
				$this->post['appointment_charge'] = Payments::DEFAULT_CHARGE;
				$this->post['doctors_pay'] = Payments::DOCTORS_PAY;
				$this->post['net_pay'] = Payments::DEFAULT_CHARGE;
			}
			$this->post['appointment_status_updated'] = date("Y-m-d H:i:s");
			$this->post['is_deleted'] = 0;
			$this->post['is_active'] = 1;
			$this->post['updated'] = date("Y-m-d H:i:s");
			$this->post['created'] = date("Y-m-d H:i:s");
			$this->post['updated_by'] = $appointment_id;
			$this->post['created_by'] = $appointment_id;

			//create appointment
			$this->mmodel->insert($this->table, $this->post);

			if ($this->db->affected_rows() > 0) {
				echo "appointment id: " . $appointment_id . " ]";
				$appointment = $this->get_appointment_full_detail($appointment_id);

				$this->messagesender->send_sms($patient->telephone, SMSTemplate::NewAppointmentSMS((array)$appointment));
				//create email record
				$email_data['sender_name'] = EmailSender::mynumber_info;
				$email_data['send_to'] = $this->mpublic->get($patient_id)->email;
				$email_data['template_id'] = EmailTemplate::public_new_appointment;
				$email_data['content'] = NULL;
				$email_data['email_type_id'] = EmailType::appointment_email;

				$this->memail->set_data($email_data);
				$this->memail->create();

				return $this->get($appointment_id);
			}
		}

		$this->db->trans_complete();

		return $result;
	}


	public function get($id)
	{
		$query_result = $this->get_record($id);
		return $query_result;
	}

	private function get_record($id)
	{
		$this->db->select('id, patient_id, session_id, serial_number_id,appointment_date');
		$this->db->from($this->table);
		$this->db->where('id', $id);
		// $this->db->where('appointment_status', AppointmentStatus::PENDING);
		$this->db->where('is_deleted', 0);
		$this->db->where('is_active', 1);
		return $this->db->get()->row();
	}

	public function get_appointment_count_for_today($session_id = '')
	{
		$this->db->select('id, patient_id, session_id, serial_number_id,appointment_date');
		$this->db->from($this->table);
		$this->db->where('session_id', $session_id);
		$this->db->where('appointment_date', DateHelper::slk_date());
		$this->db->where('appointment_status !=', AppointmentStatus::CANCELED);
		$this->db->where('is_deleted', 0);
		$this->db->where('is_active', 1);
		return $this->db->get()->num_rows();
	}

	public function get_appointment_count($session_id, $status)
	{
		$res = $this->db
			->select('*')
			->from($this->table)
			->where('session_id', $session_id)
			->where('appointment_date', DateHelper::slk_date())
			->where('appointment_status', $status)
			->where('is_active', 1)
			->where('is_deleted', 0)
			->get();

		return $res->num_rows();
	}

	public function get_last_appointment_date($patient_id = '')
	{
		$res = $this->db->query("SELECT
											a.appointment_date,
											a.session_id
										FROM
											clinic_appointments AS a
										WHERE
											a.patient_id='$patient_id'
											AND a.is_deleted = 0 
											AND a.is_active = 1 
										ORDER BY
											a.appointment_date DESC 
											LIMIT 1");

		if ($res->num_rows() > 0)
			return $res->row()->appointment_date;

		return null;
	}

	public function get_payment_dues($patient_id)
	{
		$payment_dues = null;
		$appointment = null;
		$due_amount = 0;

		$public = $this->mpublic->get($patient_id);

		if (!$public->is_clinic) {
			$res = $this->db
				->select('a.appointment_date,a.appointment_charge')
				->from('clinic_appointments as a')
				->join('clinic_session as s', 's.id=a.session_id')
				->where('a.patient_id', $patient_id)
				->where('a.appointment_status', AppointmentStatus::PENDING)
				->where('a.is_active', 1)
				->where('s.is_active', 1)
				->where('a.is_deleted', 0)
				->where('s.is_deleted', 0)
				->get();
			foreach ($res->result() as $due) {
				$payment_dues[] = $due;
				$due_amount += $due->appointment_charge;
			}
			$patient['default_charge'] = Payments::DEFAULT_CHARGE;
		} else {
			$patient['default_charge'] = 0;
		}

		$patient['due_amount'] = $due_amount;
		$patient['due_dates'] = $payment_dues;
		$patient['last_appointment'] = $this->get_last_appointment_date($patient_id);

		return $patient;
	}

	public function get_ongoing_number($session_id = '')
	{
		$res = $this->db
			->select('sn.serial_number')
			->from($this->table . ' as ca')
			->join('serial_number as sn', 'sn.id=ca.serial_number_id')
			->where('ca.session_id', $session_id)
			->where('ca.appointment_date', DateHelper::slk_date())
			->where('ca.appointment_status', AppointmentStatus::PENDING)
			->where('ca.is_active', 1)
			->where('ca.is_deleted', 0)
			->where('sn.is_active', 1)
			->where('sn.is_deleted', 0)
			->order_by('sn.serial_number', 'ASC')
			->limit(1)
			->get();

		if ($res->num_rows() > 0)
			return $res->row()->serial_number;

		return null;
	}

	public function get_appointments_today($patient_id = '')
	{
		$slk_date = DateHelper::slk_date();
		$slk_day = DateHelper::utc_day();
		$appointments = null;

		$res = $this->db->query("SELECT
											a.patient_id,
											a.session_id,
											sd.id as topic,
											a.id as appointment_id,
											a.appointment_status,
											a.appointment_date,
											sn.serial_number,
											concat(d.salutation,' ',d.first_name,' ',d.last_name) as doctor_name,
											c.clinic_name as clinic_name,
											concat(l.street_address,', ',l.city) as clinic_address,
											l.lat,
											l.long,
											sd.starting_time,
											sd.end_time
										FROM
											clinic_appointments AS a
											INNER JOIN serial_number AS sn ON a.serial_number_id = sn.id 
											INNER JOIN clinic_session AS s ON s.id = a.session_id
											INNER JOIN clinic_session_days AS sd ON sd.`day` = $slk_day AND sd.session_id=s.id
											INNER JOIN doctor AS d ON d.id = s.consultant
											INNER JOIN clinic AS c ON c.id = s.clinic_id
											INNER JOIN locations AS l ON l.id = c.location_id
										WHERE 
											a.patient_id = '$patient_id'
											AND a.appointment_date = '$slk_date'
											AND a.is_deleted = 0 
											AND a.is_active = 1
											AND sn.is_deleted = 0 
											AND sn.is_active = 1
											AND s.is_deleted = 0 
											AND s.is_active = 1
											AND sd.is_deleted = 0 
											AND sd.is_active = 1
											AND d.is_deleted = 0 
											AND d.is_active = 1
											AND c.is_deleted = 0 
											AND c.is_active = 1
											AND l.is_deleted = 0 
											AND l.is_active = 1");

		foreach ($res->result() as $appointment) {
			$appointments[] = new EntityAppointments($appointment);
		}

		return $appointments;
	}

	public function get_appointments_monthly($patient_id, $year, $month)
	{
		$slk_date = DateHelper::slk_date();
		$appointments = null;

		$res = $this->db->query("SELECT
											a.patient_id,
											sd.id as topic,
											a.session_id,
											a.id as appointment_id,
											a.appointment_status,
											a.appointment_date,
											sn.serial_number,
											concat(d.salutation,' ',d.first_name,' ',d.last_name) as doctor_name,
											concat(c.clinic_name,' Clinic') as clinic_name,
											concat(l.street_address,', ',l.city) as clinic_address,
											l.lat,
											l.long,
											sd.starting_time,
											sd.end_time
										FROM
											clinic_appointments AS a
											INNER JOIN serial_number AS sn ON a.serial_number_id = sn.id 
											INNER JOIN clinic_session AS s ON s.id = a.session_id
											INNER JOIN clinic_session_days AS sd ON sd.`day` = DAYOFWEEK(a.appointment_date) AND sd.session_id=s.id
											INNER JOIN doctor AS d ON d.id = s.consultant
											INNER JOIN clinic AS c ON c.id = s.clinic_id
											INNER JOIN locations AS l ON l.id = c.location_id
										WHERE 
											a.patient_id = '$patient_id'
											AND MONTH(a.appointment_date) = $month
											AND YEAR(a.appointment_date) = $year
											AND a.is_deleted = 0 
											AND a.is_active = 1
											AND sn.is_deleted = 0 
											AND sn.is_active = 1
											AND s.is_deleted = 0 
											AND s.is_active = 1
											AND sd.is_deleted = 0 
											AND sd.is_active = 1
											AND d.is_deleted = 0 
											AND d.is_active = 1
											AND c.is_deleted = 0 
											AND c.is_active = 1
											AND l.is_deleted = 0 
											AND l.is_active = 1");

		foreach ($res->result() as $appointment) {
			$appointments[] = new EntityAppointments($appointment);
		}

		return $appointments;
	}

	public function get_appointment_full_detail($appointment_id)
	{
		$day = DateHelper::utc_day();

		$res = $this->db
			->query("SELECT
                            ca.id,
                            ca.id as appointment_id,
                            ca.patient_name,
                            ca.patient_address,
                            ca.patient_phone,
                            CONCAT(  d.first_name ) AS doctor_name,
                            c.clinic_name,
                            l.city AS clinic_city,
                            ca.appointment_date,
                            sn.serial_number,
                            sd.starting_time,
                            s.avg_time_per_patient                            
                        FROM
                            clinic_appointments AS ca
                            INNER JOIN clinic_session AS s ON ca.session_id = s.id
                            INNER JOIN doctor AS d ON s.consultant = d.id
                            INNER JOIN serial_number AS sn ON ca.serial_number_id = sn.id
                            INNER JOIN clinic_session_days AS sd ON s.id = sd.session_id 	
                            INNER JOIN clinic AS c ON c.id=s.clinic_id 
                            INNER JOIN locations AS l ON l.id = c.location_id
                        WHERE 
                        	$day=sd.day
                         	AND ca.id='$appointment_id'
                            AND ca.is_canceled=0
                            AND ca.is_active=1
                            AND ca.is_deleted=0
                            AND d.is_active=1
                            AND d.is_deleted=0
                            AND sn.is_active=1
                            AND sn.is_deleted=0
                            AND sd.is_active=1
                            AND sd.is_deleted=0
                            AND c.is_active=1
                            AND c.is_deleted=0
                            AND l.is_active=1
                            AND l.is_deleted=0                        
                        ");

		return $res->row();
	}

	public function send_test_msg(){
		$appointment = $this->get_appointment_full_detail("85773742-0E44-4279-4735-7B7D69336894");

		$this->messagesender->send_sms('0770427277', SMSTemplate::NewAppointmentSMS((array)$appointment));

	}

}
