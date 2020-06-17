<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'entities/EntityLocation.php');

class Mlocations extends CI_Model
{

    public $validation_errors = array();
    private $post = array();
    protected $table = "locations";

    function __construct()
    {
        parent::__construct();
        $this->load->model('mvalidation');
    }


    public function set_data($post_array)
    {
        if (isset($post_array['location_street_address']))
            $this->post['street_address'] = $post_array['location_street_address'];
        if (isset($post_array['location_address_line_ii']))
            $this->post['address_line_ii'] = $post_array['location_address_line_ii'];
        if (isset($post_array['location_address_line_iii']))
            $this->post['address_line_iii'] = $post_array['location_address_line_iii'];
        if (isset($post_array['location_city']))
            $this->post['city'] = $post_array['location_city'];
        if (isset($post_array['location_district']))
            $this->post['district'] = $post_array['location_district'];
        if (isset($post_array['location_province']))
            $this->post['province'] = $post_array['location_province'];
//        if (isset($post_array['location_long_lat'])) {
//            $this->post['long_lat'] = json_encode($post_array['location_long_lat']);
        if (isset($post_array['lat']))
            $this->post['lat'] = $post_array['lat'];
        if (isset($post_array['long']))
            $this->post['long'] = $post_array['long'];
    }

//        var_dump(json_encode($post_array['location_long_lat']));
//        die();


    public
    function is_valid()
    {
        $result = true;

        if (!(isset($this->post['street_address']) && $this->post['street_address'] != NULL && $this->post['street_address'] != '')) {
            array_push($this->validation_errors, 'Invalid Street Address.');
            $result = false;
        }

//		if (!(isset($this->post['address_line_ii']) && $this->post['address_line_ii'] != NULL && $this->post['address_line_ii'] != '')) {
//			array_push($this->validation_errors, 'Invalid Address Line ii.');
//			$result = false;
//		}
        if (!(isset($this->post['city']) && $this->post['city'] != NULL && $this->post['city'] != '')) {
            array_push($this->validation_errors, 'Invalid Location City.');
            $result = false;
        }

        if (!(isset($this->post['long']) && $this->mvalidation->isGeoValid('longitude', $this->post['long']) == true)) {

            array_push($this->validation_errors, 'Invalid Location longitude.');
            $result = false;
        }
        if (!(isset($this->post['lat']) && $this->mvalidation->isGeoValid('latitude', $this->post['lat']) == true)) {

            array_push($this->validation_errors, 'Invalid Location latitude.');
            $result = false;
        }


//        if ((isset($this->post['long_lat']) && $this->post['long_lat'] != NULL && $this->post['long_lat'] != '')) {
//
//            $loc = (array)json_decode($this->post['long_lat']);
//
//            if (!(isset($loc['lat']) && $this->mvalidation->isGeoValid('latitude', $loc['lat']) == true)) {
//
//                array_push($this->validation_errors, 'Invalid Location latitude.');
//                $result = false;
//            }
//            if (!(isset($loc['long']) && $this->mvalidation->isGeoValid('longitude', $loc['long']) == true)) {
//
//                array_push($this->validation_errors, 'Invalid Location longitude.');
//                $result = false;
//            }
//
//        } else {
//            array_push($this->validation_errors, 'Invalid Location latitude and longitude.');
//            $result = false;
//        }

//		if (!(isset($this->post['district']) && $this->post['district'] != NULL && $this->post['district'] != '')) {
//			array_push($this->validation_errors, 'Invalid Location District.');
//			$result = false;
//		}

//		if (!(isset($this->post['province']) && $this->post['province'] != NULL && $this->post['province'] != '')) {
//			array_push($this->validation_errors, 'Invalid Location Province.');
//			$result = false;
//		}

        return $result;
    }

    /*
    *
    */
    public
    function create()
    {
        $result = null;

        $location_id = trim($this->mmodel->getGUID(), '{}');
        $this->post['id'] = $location_id;
        $this->post['is_deleted'] = 0;
        $this->post['is_active'] = 1;
        $this->post['updated'] = date("Y-m-d H:i:s");
        $this->post['created'] = date("Y-m-d H:i:s");
        $this->post['updated_by'] = $location_id;
        $this->post['created_by'] = $location_id;

        $this->mmodel->insert($this->table, $this->post);

        if ($this->db->affected_rows() > 0) {
            $result = $this->get($location_id);
        }

        return $result;
    }

    private
    function get_record($id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('id', $id);
        return $this->db->get()->row();
    }

    public
    function get($id)
    {
        $query_result = $this->get_record($id);
        return  new EntityLocation($query_result);
    }

    public
    function test_location()
    {
        $res = $this->db->query("select * from locations where id='1F25722A-64A4-4C1D-A23D-7418CF7F9269' ");

        var_dump($res->row()->long_lat);
    }


}
