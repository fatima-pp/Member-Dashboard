<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BDF_model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
	}

    public function log($function_name = '',$function_status='',$info='',$err = ''){
        $log_data = array(
            'function_name'=>$function_name,
            'function_status'=>$function_status,
            'info'=>json_encode($info),
            'error'=>serialize($err),
            'date_time'=>date('Y-m-d H:i:s')
        );

        $this->db->trans_start();
            $this->db->insert('application_logs',$log_data);
        $this->db->trans_complete();
        return true;
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
        $vld_mob_qry = $this->db->select('*')->from('customers c')->join('account_copy_2021 a','a.customers_id = c.id')->where(array('mobile_number'=>$mobile_number,'active'=>1))->get();
        return ($vld_mob_qry->num_rows() > 0) ? ($vld_mob_qry->row_array()) : 0;
    }

    public function get_dept($email_phone = '',$srch_param = ''){
        $dept_qry = $this->db->select('*')->from('customers c')->join('account_copy_2021 a','a.customers_id = c.id')->join('organization o','o.customers_id = c.id')->where(array($srch_param=>$email_phone,'active'=>1))->get();
        return ($dept_qry->num_rows() > 0) ? ($dept_qry->row_array()) : 0;
    }

    public function get_memberships($email_phone = '',$srch_param = ''){
        $membership_qry = $this->db->select('*,a.id as account_id')
                            ->from('customers c')
                            ->join('account_copy_2021 a','a.customers_id = c.id')
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

    public function random_bytes_gen(){
        try {
            $string = openssl_random_pseudo_bytes(10);
            // $string = random_bytes(10);
        } catch (TypeError $e) {
            // Well, it's an integer, so this IS unexpected.
            die("An unexpected error has occurred"); 
        } catch (Error $e) {
            // This is also unexpected because 32 is a reasonable integer.
            die("An unexpected error has occurred");
        } catch (Exception $e) {
            // If you get this message, the CSPRNG failed hard.
            die("Could not generate a random string. Is our OS secure?");
        }

        return(bin2hex($string));
    }

    public function gen_tkn_fgt_pass($email = '',$account_info){
        // $rndm_num = rand();

        $token = $this->random_bytes_gen();

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



    public function authenticate($user_id = '',$password = '',$id_type = 'email',$id = ''){
        $auth_qry = $this->db->get_where('customers c',array('id' =>$id, $id_type=>$user_id,'password'=>md5($password)));
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
        $acc_qry = $this->db->get_where('account_copy_2021',array('id'=>$account_id,'active'=>1));
        return $acc_qry;
    }

    public function get_membership_types($renewal_types = []){
        $mem_qry = $this->db->select('*')->from('membership_types mt')->where_in('mt.id',$renewal_types)->get();
        return $mem_qry->result_array();
    }

    public function get_current_membership($account_id = ''){
        $mem_qry = $this->db->select('*,p.name as parking_name,p.id as parking_id,ps.id as parkspace_id,ps.name as parkspace_name,p.annual_rent as annual_rent')
        ->from('reserved_space rs')
        ->join('park_space ps','rs.parkspace_id = ps.id','left')
        ->join('parking p','p.id = ps.Parking_id','left')
        ->join('accounts_membership_types amt','amt.account_id = rs.account_id','left')
        ->join('membership_types mt','amt.membership_type_id = mt.id','left')
        ->where('rs.account_id',$account_id)
        ->get();
        return $mem_qry->row_array();
    }

    public function get_upgraded_membership($current_park = 0,$annual_rent = 0){
        $active = 1 ;
        $location_id =  19;//bdf
        
        $upg_qry = $this->db->select("*,p.name as parking_name,p.id as parking_id,SUM(CASE WHEN status = 1 AND vacant LIKE '%free%' THEN 1 ELSE 0 END) AS capacity")
        ->from('parking p')
        ->join('park_space ps','ps.Parking_id = p.id')
        ->where("p.annual_rent >=", $annual_rent)
        ->where("p.active", $active)
        ->where("p.id !=", $current_park)
        ->where("location_id = $location_id ")
        ->group_by("p.id")
        ->get();
        return $upg_qry->result_array();
    }


    // existing functions from BDFModel
    public function get_upgraded_parkings($id = 0) {
		
		$query 					= $this->db->get_where('parking',array('id'=>$id));
		$current_parking 		= $query->row_array();
		$current_parking_rent 	= $current_parking['annual_rent'];
		$location_id =  19;//bdf

        
		$this->db->select("parking.*,SUM(CASE WHEN status = 1 AND vacant LIKE '%free%' THEN 1 ELSE 0 END) AS capacity");
		$this->db->from('parking');
		$this->db->join('park_space ps','ps.Parking_id = parking.id');
		$this->db->where("annual_rent >= $current_parking_rent ");
		$this->db->where("location_id = $location_id ");
		$this->db->where("parking.active",1);
		$this->db->group_by("parking.id ");
		$result = $this->db->get();

       return $result;
	}


    public function getFreeParkSpots($id = 0) {
        $result = $this->db->select('ps.*,p.annual_rent,p.name as parking_name')->from('park_space ps')->join('parking p','p.id = ps.Parking_id')->where(array('ps.Parking_id' => $id, 'ps.status' => '1', 'ps.vacant' => 'free','p.active'=>1))->get();
        return $result;
	}

    public function get_dscnt_dtls($account_id = ''){
        $active = 1;
        $this->db->select("
            park_space.*,park_space.id as ps_id,
            parking.name AS parking_lot,
            parking.annual_rent,
            reserved_space.account_id,
            customers.*,
            account_copy_2021.*,organization.*,
            annualDiscount.discount,
            account_cars.*,subs_transactions.*,amt.membership_type_id,mt.*,

            (CASE WHEN renew_discount != 0 AND account_copy_2021.`years_active` != 0 THEN ((annual_rent) - (amount)) 
                WHEN membership_type_id = 5 THEN 'cardiac' ELSE 0 END )AS discount_amount_received,
            
            (CASE WHEN renew_discount != 0 AND account_copy_2021.`years_active` != 0 THEN  (((annual_rent) - (amount))/(annual_rent))*100 
                WHEN membership_type_id = 5 THEN 'cardiac' ELSE 0 END )AS discount_percentage_received,

            (CASE WHEN renew_discount != 0  AND account_copy_2021.`years_active` != 0 THEN (amount - (amount * discount))
                WHEN membership_type_id = 5 THEN 'cardiac' ELSE 0 END )AS discount_amount,
                
            (CASE WHEN renew_discount != 0 AND account_copy_2021.`years_active` != 0 THEN  (((annual_rent) - (amount))/(annual_rent))*100 
                WHEN membership_type_id = 5 THEN 'cardiac' ELSE 0 END )AS discount_percentage
        ");

        $this->db->from('park_space');
        $this->db->join('reserved_space', 'reserved_space.`parkspace_id` = park_space.`id`', 'inner');
        $this->db->join('customers', 'customers.id = reserved_space.`account_id`', 'inner');
        $this->db->join('account_copy_2021', 'account_copy_2021.id = customers.id', 'inner');
        $this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
        $this->db->join('annualDiscount', 'parking.id = annualDiscount.`parking_id`', 'inner');
        $this->db->join('account_cars', 'account_cars.account_id = account_copy_2021.id', 'left');
        $this->db->join('organization', 'organization.customers_id = account_copy_2021.id', 'left');
        $this->db->join('subs_transactions', 'subs_transactions.customers_id = account_copy_2021.id', 'inner');
        
        $this->db->join('accounts_membership_types amt', 'amt.account_id = customers.id', 'left');
        $this->db->join('membership_types mt', 'mt.id = amt.membership_type_id', 'left');
    
        $this->db->where('customers.id', $account_id);
        $this->db->where('amt.active', $active);
        // $this->db->where('park_space.parking_id', $parkingLot);
        // $this->db->where('park_space.name', $parkingSpace);
        $this->db->order_by('reserved_space.id','DESC');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_location_name($location_id = 0){
		$query = $this->db->select('*')->from('location')->where(array('Active'=>1,'id'=>$location_id))->get();
		return $query->row_array();
	}

    public function getParkingName($parking_id = ""){
		if(isset($parking_id) && $parking_id != null){
			$query = $this->db->get_where('parking',array('id'=>$parking_id));
			return $query->row_array(); 
		}
	}

    public function getParkingLotNameAndParkingSpaceName($parkingSpace) {
		$this->db->select('park_space.name AS park_name, parking.`name`');
		$this->db->from('park_space');
		$this->db->join('parking', 'parking.id = park_space.parking_id', 'inner');
		$this->db->where('park_space.id', $parkingSpace);
		$result = $this->db->get();
		return $result->result();
	}

    public function generateInvoiceNumber($location_id = 19) {
		if($location_id == 19){
			return $this->db->select('id')->from('subs_transactions')->order_by('id', 'DESC')->limit(1)->get();
		}
		else if($location_id != 19) {
			return $this->db->select('invoice_id as id')->from('membership_transactions')->order_by('invoice_id', 'DESC')->limit(1)->get();
		}
		else{
			return 0;
		}
	}

    public function getCustomerInformation($id = ''){
		if($id != null && $id != ''){
			$query = $this->db->get_where('customers',array('id'=>$id));
			return $query->row_array();
		}
		return false;
	}

    
    public function getReceiptInfo($id,$status = null) {
		if (strpos($id, 'BDF') !== false) {
			$this->db->select('customers.first_name,
			customers.last_name,
			customers.CPR,
			customers.id,
			customers.mobile_number,
			organization.organization,
			organization.department,
			organization.profession,
			subs_transactions.amount,
			subs_transactions.invoice_date,
			subs_transactions.payment_mode,
			subs_transactions.`id` AS invoice_number,
			account_copy_2021.create_date,
			account_copy_2021.expiry_date,
			account_cars.car_plate_number');
		}
		else{
			$this->db->select('customers.first_name,
			customers.last_name,
			customers.CPR,
			customers.id,
			customers.mobile_number,
			organization.organization,
			organization.department,
			organization.profession,
			membership_transactions.amount,
			membership_transactions.invoice_date,
			membership_transactions.payment_mode,
			membership_transactions.`invoice_id` AS invoice_number,
			account_copy_2021.create_date,
			account_copy_2021.expiry_date,
			account_cars.car_plate_number');
		}

		$this->db->from('customers');
		$this->db->join('account_copy_2021', 'account_copy_2021.customers_id = customers.id ', 'INNER');
		$this->db->join('organization', 'organization.customers_id = customers.id', 'INNER');
		
		if (strpos($id, 'BDF') !== false) {
			$this->db->join('subs_transactions', 'subs_transactions.customers_id = customers.id', 'INNER');
			if($status != null){
				$this->db->where("subs_transactions.status LIKE '%$status'");
			}
		}
		else{
			$this->db->join('membership_transactions', 'membership_transactions.account_id = customers.id', 'INNER');
			if($status != null){
				$this->db->where("membership_transactions.status LIKE '%$status'");
			}
		}

		$this->db->join('account_cars', 'account_cars.account_id = customers.id', 'INNER');
		$this->db->where('customers.id', $id);
		$this->db->order_by('membership_transactions.invoice_id', 'DESC');
		

		return $this->db->get();
	}

    public function getReceiptPark($id,$location_id) {
		$this->db->select("parking.name,
		park_space.name AS park_name");
		$this->db->from('location');
		$this->db->join('parking', 'parking.location_id = location.id', 'INNER');
		$this->db->join('park_space', 'park_space.Parking_id = parking.id', 'INNER');
		$this->db->join('reserved_space', 'reserved_space.parkspace_id = park_space.id', 'INNER');
		$this->db->where('location.id', $location_id);
		$this->db->where('reserved_space.account_id', $id);

		return $this->db->get();
	}






}
?>