<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Airport_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
	}

    public function check_car_parked($job_id){
        $parked_phase = 3;
        $query = $this->db->get_where('job_history',array('job_id'=>$job_id,'phase'=>$parked_phase));
        return $query;
    }

    public function check_customer_create($job_id){
        $created_at = NULL;
        $this->db->select('*')->from('v_customer_details');
        $this->db->where('job_id',$job_id);
        $this->db->where('createdAt IS NOT NULL');
        $query = $this->db->get();
        return $query;
    }

    public function get_updated_seed(){
        $query = $this->db->select('*')->from('seed')->order_by('date','DESC')->limit(1)->get();
        return $query;
    }
}