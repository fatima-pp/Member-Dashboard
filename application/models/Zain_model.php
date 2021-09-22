<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zain_model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
        $this->load->database('new');
        // $New_db = $this->load->database('new', TRUE);
	}


    public function save_log($function_name = '',$function_status='',$info='',$err = ''){
        $default_db = $this->load->database('default', TRUE);

        $log_data = array(
            'function_name'=>$function_name,
            'function_status'=>$function_status,
            'info'=>$info,
            'error'=>($err['code'].' '. $err['message']),
            'date_time'=>date('Y-m-d H:i:s')
        );

        $default_db->trans_start();
            $default_db->insert('application_logs',$log_data);
        $default_db->trans_complete();
        return true;
    }

    public function exec_qry($query,$function_name = ''){
        try{
            $qry = $query;
            if(! $qry){
                $error = $this->db->error();
            }
            $x = $qry->num_rows(); 
            if($qry->num_rows() > 0){
                return $qry->row_array();
            }else{
                return 0;
            }
            $this->db->error(); 
        }
        catch (Error $e) {
            $this->save_log($function_name,'error','error getting client info from zain_model',$error);
            return false;
        } 
        catch(Exception $e){
            $this->save_log($function_name,'error','error getting client info from zain_model',$e);
            return false;
        }
    }


    public function get_client_info($client_id = 0){
        $act_stat = 'active';
        if($client_id !== null && $client_id !== 0){
            // expirydate has to be checked 

            $query = $this->db->select('*')->from('client')->where(array('id'=>$client_id,'status'=>$act_stat))->get();
            $response = $this->exec_qry($query,'get_client_info');
            if($response){
                return $response;
            }
        }
        return false;
    }

    public function get_mem_client_info($mobile = 0){

        $act_stat = 'active';
        if($mobile !== null && $mobile !== 0){
            $qry = $this->db->select('*')
                            ->from('client_user cu')
                            ->join('privilege p','p.`id`=  cu.`privilegeId`','left')
                            ->join('merchant m',' m.`id` = cu.`merchantId`','left')
                            ->where(array('cu.phoneNo'=>$mobile,'cu.status'=>$act_stat,'p.status'=>$act_stat,'m.status'=>$act_stat))
                            ->get();

            $response = $this->exec_qry($qry,'get_mem_client_info');
            return $response;
        }
        return false;
    }

    public function verify_zain($mobile = 0){
        $act_stat = 'active';
        if($mobile !== null && $mobile !== 0){
            $qry = $this->db->get_where('client_user',array('phoneNo'=>$mobile,'clientStatus'=>$act_stat));
            $response = $this->exec_qry($qry,'verify_zain');
            return $response;
        }
        return false;
    }

    public function verify_ppass($mobile = 0){
        if($mobile !== null && $mobile !== 0){
            $qry = $this->this->db->get_where('client_user',array('phoneNo'=>$mobile));
            $response = $this->exec_qry($qry,'verify_ppass');
            return $response;
        }
        return false;
    }

    public function get_prev_id($client_id = 0){
        $default_db = $this->load->database('default', TRUE);

        $qry = $default_db->select('*')->from('customers')->where(array('id'=>$client_id))->order_by('id','DESC')->limit(1)->get();        
        $response = $this->exec_qry($qry,'verify_ppass');
        return $response;
    }

    public function get_mem_id($client_id = 0){

        $default_db = $this->load->database('default', TRUE);
        
        $prev_id = $this->get_prev_id($client_id);
        if($prev_id){ //getting anythind but false as response
            return $prev_id['id'];
        }
        
        else if($prev_id === 0){ //got no previous id found --returning number of rows as 0
            $client_info = $this->get_client_info($client_id);

            if($client_info){
                $first_id = $client_info['prefix'] . '0000' . '0001';
                return $first_id; 
            }            
            return false;                                
        }
        else{
            return false;
        }
    }

    public function save_mem_info($customers = null ,$wallet = null ,$accountRecord = null ,$mobile = null ,$address = null ,$activeRecord = null ,$carRecord = null){

        $default_db = $this->load->database('default', TRUE);

        if(isset($customers) && isset($wallet) && isset($accountRecord) && isset($mobile) && isset($address) && isset($activeRecord) && isset($carRecord)){

            try{
                $default_db->trans_start();
                    $default_db->insert('customers', $customers);
                    $default_db->insert('wallet', $wallet);
                    $default_db->insert('account', $accountRecord);
                    $default_db->insert('mobile', $mobile);
                    $default_db->insert('address', $address);
                    $default_db->insert('active', $activeRecord);
                    $default_db->insert('account_cars', $carRecord);
                $default_db->trans_complete();
                
                if ($this->db->trans_status() === false) {
                    $error = $this->db->error(); 
                }

            }
            catch (Error $e) {
                $this->save_log('save_mem_info','error','error getting client info from zain_model',$error);
                return false;
            } 
            catch(Exception $e){
                $this->save_log('save_mem_info','error','error getting client info from zain_model',$e);
                return false;
            }
        }       

        else{
            return false;
        }

    }

}
?>