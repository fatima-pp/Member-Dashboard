<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zain_model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
        $this->load->database('new');

        $default_db = $this->load->database('default', TRUE);
        $new_db = $this->load->database('new', TRUE);
	}


    public function save_log($function_name = '',$function_status='',$info='',$err = ''){
        
        $log_data = array(
            'function_name'=>$function_name,
            'function_status'=>$function_status,
            'info'=>$info,
            'err'=>$err
        );

        $this->default_db->trans_start();
            $this->default_db->insert('application_logs',$log_data);
        $this->default_db->trans_complete();
        return true;
    }

    public function get_client_info($client_id = 0){
        $act_stat = 'active';
        if($client_id !== null && $client_id !== 0){
            // expirydate has to be checked 
            try{
                $qry = $this->new_db->select('*')->from('client')->where(array('id'=>$client_id,'status'=>$act_stat))->get();
                if($qry->num_rows() > 0){
                    return $qry->row_array();
                }
            }
            catch(Exception $e){
                $this->save_log('get_client_info','error','error getting client info from zain_model',$e);
                return false;
            }
        }
        return false;
    }

    public function get_mem_client_info($mobile = 0){

        $act_stat = 'active';
        if($mobile !== null && $mobile !== 0){
            $qry = $this->new_db->select('*')
                            ->from('client_user cu')
                            ->join('privilege p','p.`id`=  cu.`privilegeId`','left')
                            ->join('merchant m',' m.`id` = cu.`merchantId``','left')
                            ->where(array('cu.phoneNo'=>$mobile,'cu.status'=>$act_stat,'p.status'=>$act_stat,'m.status'=>$act_stat))
                            ->get();

            if($qry->num_rows() > 0){
                return $qry->row_array();
            }
        }
        return false;
    }

    public function verify_zain($mobile = 0){
        $act_stat = 'active';
        if($mobile !== null && $mobile !== 0){
            $qry = $this->new_db->get_where('client_user',array('phoneNo'=>$mobile,'clientStatus'=>$act_stat));
            if($qry->num_rows() > 0){
                return $qry->row_array();
            }
        }
        return false;
    }

    public function verify_ppass($mobile = 0){
        if($mobile !== null && $mobile !== 0){
            $qry = $this->new_db->get_where('client_user',array('phoneNo'=>$mobile));
            if($qry->num_rows() > 0){
                return $qry->row_array();
            }
        }
        return false;
    }

    public function get_mem_id($client_id = 0){
        $qry = $this->default_db->select('*')->from('customers')->where(array('client_id'=>$client_id))->order_by('id','DESC')->limit(1)->get();
        if($qry->num_rows() > 0){
            return $qry->row_array();
        }
        else{
            $client_info = $this->get_client_info($client_id);
            $first_id = $client_info['prefix'] . '0000' . '0001';
            return $first_id;
        }
    }

    public function save_mem_info($customers = null ,$wallet = null ,$accountRecord = null ,$mobile = null ,$address = null ,$activeRecord = null ,$carRecord = null){

        if(isset($customers) && isset($wallet) && isset($accountRecord) && isset($mobile) && isset($address) && isset($activeRecord) && isset($carRecord)){

            $this->default_db->trans_start();
                $this->default_db->insert('customers', $customers);
                $this->default_db->insert('wallet', $wallet);
                $this->default_db->insert('account', $accountRecord);
                $this->default_db->insert('mobile', $mobile);
                $this->default_db->insert('address', $address);
                $this->default_db->insert('active', $activeRecord);
                $this->default_db->insert('account_cars', $carRecord);
            return $this->default_db->trans_complete();
        }
        else{
            return false;
        }

    }

}
?>