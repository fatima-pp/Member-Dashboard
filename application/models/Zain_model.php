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
            $this->save_log($function_name,'error','error '.$function_name.' from zain_model',$error);
            return false;
        } 
        catch(Exception $e){
            $this->save_log($function_name,'error','error '.$function_name.' from zain_model',$e);
            return false;
        }
    }


    public function get_client_info($client_id = 0){
        $new_db = $this->load->database('new', TRUE);

        $act_stat = 'active';
        if($client_id !== null && $client_id !== 0){
            // expirydate has to be checked 

            $query = $new_db->select('*')->from('client')->where(array('id'=>$client_id,'status'=>$act_stat))->get();
            $response = $this->exec_qry($query,'get_client_info');
            if($response){ //which is actual data,not false or 0 (no rows)
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

        $qry = $default_db->select('*')->from('customers')->where(array('client_id'=>$client_id))->order_by('id','DESC')->limit(1)->get();        
        $response = $this->exec_qry($qry,'verify_ppass');
        return $response;
    }

    public function get_mem_id($client_id = 0){

        $default_db = $this->load->database('default', TRUE);
        
        $prev_id = $this->get_prev_id($client_id);
        if($prev_id){ //getting anythind but false as response
            $next_id = $this->next_id($prev_id['id']);
            return $next_id;
        }
        
        else if($prev_id === 0){ //got no previous id found --returning number of rows as 0
            $client_info = $this->get_client_info($client_id);

            if($client_info){
                $first_id = $client_info['prefix'] . '0000' . '0000';
                $next_new_id = $this->next_id($first_id);
                return $next_new_id; 
            }            
            return false;                                
        }
        else{
            return false;
        }
    }

    public function next_id($mem_id = 0){
        $prefix = substr($mem_id,0,3);

        $length_id = strlen($mem_id);
        $id_len = 3 - $length_id;
        $id = substr($mem_id,$id_len);
        
        $int_id = (int)$id;
        $inc_id = ++$int_id;
        $str_id = (string)$inc_id;

        $full_id = str_pad($str_id, 8, "0", STR_PAD_LEFT);
        $new_id = $prefix . $full_id;
        return $new_id;
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
                $is_complete = $default_db->trans_complete();
                
                if ($this->db->trans_status() === false) {
                    $error = $this->db->error(); 
                }
                else{
                    return $is_complete;
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

    public function is_ppass_mem($mobile = 0){
        if($mobile !== null && strlen($mobile) == 8){
            $default_db = $this->load->database('default', TRUE);

            $qry = $default_db->select('*')->from('customers c')->where('c.mobile_number',$mobile)->get();
            $response = $this->exec_qry($qry,'is_ppass_mem');
            if($response){
                return $response;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }


    
    public function set_zain_account($customer = null,$client_id = 0){

        $default_db = $this->load->database('default', TRUE);

        if(isset($customer)){

            $account_id = $this->get_mem_id($client_id);
            $mem_client_info = $this->get_mem_client_info($customer['mobile_number']);

            if(isset($account_id) && $account_id !== null && $account_id !== ''){
            
                $default_expiry = '2058-12-31 23:59:59';
                $accountRecord['create_date']      = date("Y-m-d H:i:s"); //datetime now
                $accountRecord['expiry_date']      = date('Y-m-d H:i:s',strtotime($default_expiry)); //have to define either 2058 or some other
                $accountRecord['active']           = 1; //self activated
                $accountRecord['id']               = $account_id; //
                $accountRecord['customers_id']     = $customer['id']; // from customers table
                $accountRecord['account_types_id'] = $mem_client_info['accountTypeId']; //from privilege type

                try{
                    $default_db->trans_start();
                        $default_db->insert('account', $accountRecord);
                    $is_complete = $default_db->trans_complete();
                    
                    if ($this->db->trans_status() === false) {
                        $error = $this->db->error(); 
                    }
                    else{
                        return $is_complete;
                    }
                }
                catch (Error $e) {
                    $this->save_log('set_zain_account','error','error setting zain account from zain_model',$error);
                    return false;
                } 
                catch(Exception $e){
                    $this->save_log('set_zain_account','error','error setting zain account from zain_model',$e);
                    return false;
                }
            }
            else{
                return false;
            }
        }       

        else{
            return false;
        }

    }

    public function check_if_activated($mobile = 0){
        $default_db = $this->load->database('default', TRUE);

        if(isset($mobile) && $mobile !== null && $mobile !== '' && strlen($mobile) === 8){

            //CHECK IF ACCOUNT WITH ACCOUNT TYPE OF ZAIN EXISTS
            $account_type = $this->get_new_account_type($mobile);
            if($account_type){
                $account_type_id = $account_type['accountTypeId'];
                $qry = $default_db->select('*')->from('customers c')->join('account a','a.customers_id = c.id')->where(array('a.account_types_id'=>$account_type_id,'a.active'=>1,'mobile_number'=>$mobile))->get();
                $response = $this->exec_qry($qry,'check_if_activated');
                
                if($response){
                    return $response;
                }
                else{
                    return false;
                }
            }
        }
        else{
            return false;
        }       

    }

    public function get_new_account_type($mobile=  0){
        if(isset($mobile) && $mobile !== null && $mobile !== '' && strlen($mobile) === 8){
            $qry = $this->db->select('*')->from('client_user cu')->join('privilege p','p.id = cu.privilegeId','left')->where('cu.phoneNo',$mobile)->get();
            $response = $this->exec_qry($qry,'get_new_account_type');
            if($response){
                return $response;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        } 
    }

}
?>