<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zain_model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
        $this->load->database('new');
	}


    public function get_client_info($client_id = 0){
        $act_stat = 'active';
        if($client_id !== null && $client_id !== 0){
            // expirydate has to be checked 
            $qry = $this->db->select('*')->from('client')->where(array('id'=>$client_id,'status'=>$act_stat))->get();
            if($qry->num_rows() > 0){
                return $qry->row_array();
            }
        }
        return false;
    }

    public function verify_zain($mobile = 0){
        $act_stat = 'active';
        if($mobile !== null && $mobile !== 0){
            $qry = $this->db->get_where('client_user',array('phoneNo'=>$mobile,'clientStatus'=>$act_stat));
            if($qry->num_rows() > 0){
                return $qry->row_array();
            }
        }
        return false;
    }

    public function verify_ppass($mobile = 0){
        if($mobile !== null && $mobile !== 0){
            $qry = $this->db->get_where('client_user',array('phoneNo'=>$mobile));
            if($qry->num_rows() > 0){
                return $qry->row_array();
            }
        }
        return false;
    }

}
?>