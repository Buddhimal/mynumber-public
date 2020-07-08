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

//		$patient['id'] = $patient_id;
		$patient['default_charge'] = Payments::DEFAULT_CHARGE;
		$patient['due_amount'] = $due_amount;
		$patient['due_dates'] = $payment_dues;
		$patient['last_appointment'] = $this->get_last_appointment_date($patient_id);

		return $patient;
	}

	public function get_appointments_today($patient_id = '')
	{
		$slk_date = DateHelper::slk_date();
		$appointments = null;

		$res = $this->db->query("SELECT
											a.patient_id,
											a.session_id,
											a.id as appointment_id,
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

		foreach ($res->result() as $appointment){
			$appointments[] = new EntityAppointments($appointment);
		}

		return $appointments;
	}

}
