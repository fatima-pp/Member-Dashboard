<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BDF_model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
	}


    public function sign_in(){
        return true;
    }

    //if email registered or not
    public function validate_email($email = ''){
        $vld_email_qry = $this->db->select('*')->from('customers c')->join('account a','a.customers_id = c.id')->where(array('email'=>$email,'active'=>1))->get();
        return ($vld_email_qry->num_rows() > 0) ? ($vld_email_qry->row_array()) : 0;
    }

    //though registered but is verified or not
    public function verify_email($email = ''){
        $vrfy_email_qry = $this->db->select('*')->from('customers c')->where(array('email'=>$email,'is_verified'=>1))->get();
        return ($vrfy_email_qry->num_rows() > 0) ? ($vrfy_email_qry->row_array()) : 0;
    }

    //if registered or not
    public function validate_mobile($mobile_number = ''){
        $vld_mob_qry = $this->db->select('*')->from('customers c')->join('account a','a.customers_id = c.id')->where(array('mobile_number'=>$mobile_number,'active'=>1))->get();
        return ($vld_mob_qry->num_rows() > 0) ? ($vld_mob_qry->row_array()) : 0;
    }

    public function get_dept($email_phone = '',$srch_param = ''){
        $dept_qry = $this->db->select('*')->from('customers c')->join('account a','a.customers_id = c.id')->join('organization o','o.customers_id = c.id')->where(array($srch_param=>$email_phone,'active'=>1))->get();
        return ($dept_qry->num_rows() > 0) ? ($dept_qry->row_array()) : 0;
    }

    public function get_memberships($email_phone = '',$srch_param = ''){
        $membership_qry = $this->db->select('*,a.id as account_id')
                            ->from('customers c')
                            ->join('account a','a.customers_id = c.id')
                            ->join('account_types at','at.id = a.account_types_id')
                            ->where(array($srch_param=>$email_phone,'a.active'=>1,'at.at_isActive'=>1))->get();

        return ($membership_qry->num_rows() > 0) ? ($membership_qry->result_array()) : 0;
    }


    // if account_type status is sub then goto reserved_space,park_space and parking to get parking/location
    public function get_sub_locs($account_id = ''){
        $sub_loc_qry = $this->db->select('*,p.name as parking_name')->from('reserved_space rs')->join('park_space ps','ps.id = rs.parkspace_id')->join('parking p','p.id = ps.Parking_id')->where('rs.account_id',$account_id)->get();
        return ($sub_loc_qry->num_rows() > 0) ? ($sub_loc_qry->row_array()) : 0;
    }


    // if account_type status is valet then goto client location to know applicable locations
    public function get_valet_locs($client_id = 0,$account_type_id = 0){
        $val_loc_qry = $this->db->select('*')->from('client_location cl')->where(array('cl.account_type_id'=>$account_type_id,'cl.client_id'=>$client_id))->get();
        return ($val_loc_qry->num_rows() > 0) ? ($val_loc_qry->row_array()) : 0;
    }


    public function gen_tkn_fgt_pass($email = '',$account_info){
        $token = bin2hex(random_bytes(10));
        $this->db->trans_start();
            $pass_res_data = array(
                'account_id' => $account_info['customers_id'],
                'email' => $email, 
                'created_at' => date('Y-m-d h:i:s'), 
                'token'=> $token
            );
            $this->db->insert('pass_reset', $pass_res_data);
            $insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        return $token;
    }

    public function validate_token($token = ''){
        $date_time_now = date('Y-m-d h:i:s');

        $this->db->select('*')->from('pass_reset');
        $this->db->where('token',$token);
        $this->db->where("created_at <= '$date_time_now'");
        $is_valid_qry = $this->db->get();

        return $is_valid_qry->row_array();

    }

    public function dlt_tkn($account_id = ''){

        $this->db->trans_start();
			$this->db->delete('pass_reset', array('account_id' => $account_id));
		$this->db->trans_complete();

        return true;
    }


    public function change_password($account_id = '',$password = ''){

        $acc_data = array(
			'password'=>md5($password),
		);
	
		$this->db->trans_start();

			$this->db->where('id',$account_id);
			$this->db->update('customers', $acc_data);

        $this->db->trans_complete();

        return true;
   
    }

    public function reg_email($account_id = '',$email = ''){

        $acc_data = array(
			'email'=>$email,
            'is_verified'=>0
		);
	
		$this->db->trans_start();

			$this->db->where('id',$account_id);
			$this->db->update('customers', $acc_data);

        $this->db->trans_complete();

        return true;
   
    }



    public function authenticate($user_id = '',$password = '',$id_type = 'email'){
        $auth_qry = $this->db->get_where('customers c',array($id_type=>$user_id,'password'=>md5($password)));
        return $auth_qry->row_array();       
    }

    public function tokenize_acc($account_id = ''){
        return bin2hex($account_id);
    }


    public function verify_mail($token = ''){
        $account_id =  hex2bin($token);

        $is_active = $this->acc_active($account_id);
        if($is_active->num_rows()  > 0){
            
            $acc_data = array(
                'is_verified'=>1
            );
            
            $this->db->trans_start();
            
			$this->db->where('id',$account_id);
			$this->db->update('customers', $acc_data);
            
            $is_trns_comp = $this->db->trans_complete();
            
            return $is_trns_comp;
        }
    
    }

    public function acc_active($account_id = ''){
        $acc_qry = $this->db->get_where('account',array('id'=>$account_id,'active'=>1));
        return $acc_qry;
    }



}
?>