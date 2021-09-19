<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zain_model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
        $this->load->database('new');
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
        $act_stat = 'active';
        if($mobile !== null && $mobile !== 0){
            $qry = $this->db->get_where('client_user',array('phoneNo'=>$mobile,'status'=>$act_stat));
            if($qry->num_rows() > 0){
                return $qry->row_array();
            }
        }
        return false;
    }

}
?>