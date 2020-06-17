<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mvalidation extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function email($email = '')
    {
        return preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email) === 1;
    }

    function telephone($tp = '')
    {
        $length = strlen($tp);
        return ($length == 10);
    }

    function already_exists($table, $column, $value)
    {
        $this->db->select($column);
        $this->db->from($table);
        $this->db->where($column, $value);
        $this->db->where('is_active', 1);
        $this->db->where('is_deleted', 0);
        $result = $this->db->get();
        return ($result->num_rows() > 0);
    }


    function valid_id($table, $value)
    {
        $this->db->select('id');
        $this->db->from($table);
        $this->db->where('id', $value);
        $this->db->where('is_active', 1);
        $this->db->where('is_deleted', 0);
        $result = $this->db->get();
        return ($result->num_rows() > 0);
    }

    function valid_time($time)
    {
//		if (preg_match("/^(?:2[0-4]|[01][1-9]|10):([0-5][0-9])$/", $time)) {
        if (preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $time)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function valid_date($date)
    {
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function valid_day($day)
    {
        if (preg_match("/^[1-7]{1}+$/", $day)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function valid_password($password)
    {
        if (preg_match("/^(?=.*\d).{6,20}$/", $password)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function isGeoValid($type, $value)
    {
        /*
        *     The valid range:
        *     - in degrees
        *         - latitude -90 and +90
        *         - longitude -180 and +180
        *     - in decimals
        *         - latitude precision=10, scale=8
        *         - longitude precision=11, scale=8
        */

        $pattern = ($type == 'latitude')
            ? '/^(\+|-)?(?:90(?:(?:\.0{1,8})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,8})?))$/'
            : '/^(\+|-)?(?:180(?:(?:\.0{1,8})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,8})?))$/';

        return (preg_match($pattern, $value));
    }


}
