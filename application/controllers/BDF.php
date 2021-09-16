<?php

require "vendor/autoload.php";
require "class.phpmailer.php";
require "class.smtp.php";

class BDF extends CI_Controller{

    public function __construct()
    {
        parent ::__construct();
        $this->load->model('BDF_model');
        $this->load->library('form_validation');
        $this->load->library('session');
    }

    public function sign_in(){
        $this->load->view('sign_in');
    }

    public function sign_in_email($email = ''){
        $is_email = strpos($email,'@');
        if($is_email){
            $data['email'] = hex2bin($email);
            $this->load->view('sign_in',$data);
        }
        else{
            redirect('sign_in');
        }
    }

    public function dont_have_pass(){
        $this->load->view('dont_have_pass_1');
    }

    // public function dont_have_pass_2(){
    //     $this->load->view('dont_have_pass_2');
    // }

    public function forgot_pass_1(){
        $this->load->view('forgot_pass_1');
    }

    public function forgot_pass_2($mobile = '' ){
        $session_mobile = $this->session->userdata('mobile');
        $data['mobile'] = (isset($mobile) && $mobile !== '') ? $mobile : ((  $session_mobile !== null && $session_mobile !== '') ? $session_mobile : null);
        $this->load->view('forgot_pass_2',$data);
    }

    // public function forgot_pass_3(){
    //     $this->load->view('forgot_pass_3');
    // }

    

    public function index()
    {
        print_r(phpinfo());
    }

    public function sign_in_sub(){
        $email_phone = $this->input->post('email_phone');
        $password = $this->input->post('password');

        $is_email = strpos($email_phone,'@');
        $is_number = is_numeric($email_phone);
        

        if($email_phone == null || $email_phone == '' || (!isset($email_phone))){
            $this->form_validation->set_rules('email_phone','Email/Phone','required',array('required'=>'Please enter registered Email/Phone'));
        }
        else if($is_number){
            $this->form_validation->set_rules('email_phone','Phone','min_length[8]',array('min_length'=>'Please enter valid mobile number'));
        }
        else{
            $this->form_validation->set_rules('email_phone','Email','valid_email',array('valid_email'=>'Please enter a valid email address'));
        }

        $this->form_validation->set_rules('password','Password','required',array('required'=>'Please enter a Password'));


		if($this->form_validation->run() === FALSE){
            $this->session->set_flashdata('errors',validation_errors());
            $this->load->view('sign_in');
		}
        else{
            if($is_email){
                //check if email or mobile is registered/verified/active account
                //registered and active
                $is_registered = $this->BDF_model->validate_email($email_phone);
                if($is_registered){
                    $this->session->set_userdata('member_info',$is_registered);

                    $is_verified = $this->BDF_model->verify_email($email_phone);
                    if($is_verified){
                        // authenticate email and password 
                        $is_auth = $this->auth_crd($email_phone,$password,'email');
                        if($is_auth){
                            // print_r('success');
                            //get account data and show dashboard
                            $data['customer'] = $is_registered;
                            $data['dept'] = $this->get_dept($email_phone,'email');
                            
                            $this->session->set_userdata('dept',$data['dept']);

                            $this->load->view('dashboard',$data);    
                        }else{
                           $this->session->set_flashdata('errors','Password Incorrect');
                           $this->load->view('sign_in');
                        }
                    }
                    else{
                        $this->session->set_flashdata('errors','Please ensure that Email is verified');
                        $this->load->view('sign_in');
                    }
                }
                else{
                    $this->session->set_flashdata('errors','Please ensure that this Email is registered');
                    $this->load->view('sign_in');
                }
            }
            else{
                $is_mob_reg = $this->BDF_model->validate_mobile($email_phone);

                if($is_mob_reg){
                    $this->session->set_userdata('member_info',$is_mob_reg);

                    // authenticate email and password 
                    $is_auth = $this->auth_crd($email_phone,$password,'mobile_number',$is_mob_reg['customers_id']);
                    if($is_auth){
                        // print_r('success');   
                        //get account data and show dashboard
                        $data['customer'] = $is_mob_reg;
                        $data['dept'] = $this->get_dept($email_phone,'mobile_number');

                        $this->session->set_userdata('dept',$data['dept']);

                        $data['memberships'] = $this->get_memberships($email_phone,'mobile_number');
                        $this->load->view('dashboard',$data);     

                    }
                    else{
                        $this->session->set_flashdata('errors','Password Incorrect');
                        $this->load->view('sign_in');
                    }
                }
                else{
                    $this->session->set_flashdata('errors','Please ensure that this number is registered');
                    $this->load->view('sign_in');
                }
            }
        }
    }

    public function get_dept($email_phone = '',$srch_param = 'mobile_number'){
        $dept = $this->BDF_model->get_dept($email_phone,$srch_param);
        return $dept;
    }

    public function get_memberships($email_phone = '',$srch_param = 'mobile_number'){
        $memberships = $this->BDF_model->get_memberships($email_phone,$srch_param);

        $membership_dtls = array(
            'membership_name'=>'',
            'location'=>'',
            'description'=>'',
            'validity'=>'',
            'logo'=>'',
            'account_id'=>''
        );
        
        foreach($memberships as $membership){
            $membership_name = $membership['account_types'];
            $account_id = $membership['account_id'];
            
            // if account_type status is valet then goto client location to know applicable locations
            // if account_type status is sub then goto reserved_space,park_space and parking to get parking/location
            
            $valet_loc_dtls = $this->get_valet_locs($membership['client_id'],$membership['account_types_id']);
            $sub_loc_dtls = $this->get_sub_locs($account_id);

            $location = ($membership['status'] == 'valet') ? ($valet_loc_dtls['parking_id'] .'-'.$valet_loc_dtls['location_id']) : $sub_loc_dtls['parking_name'];

            //get decsription from account_type
            $description = $membership['description'];

            //get validity from account expiry_date (color depends on period left to expire)
            $validity = $membership['expiry_date'];

            //get logo from account_type (must be saved in a new column)
            $logo = base_url('assets/images/tag.svg');

            $membership_dtls = array(
                'membership_name'=>$membership_name,
                'location'=>$location,
                'description'=>$description,
                'validity'=>$validity,
                'logo'=>$logo,
                'account_id'=>$account_id
            );

            $mems[] = $membership_dtls;
        }

        return $mems;
    }

    public function renew($account_id = ''){
        // print_r($account_id);
        $data = [] ;
        
        $current_mem = $this->BDF_model->get_current_membership($account_id);
        $upgraded_mem = $this->BDF_model->get_upgraded_membership($current_mem['parking_id'],$current_mem['annual_rent']);
        $dscnt_dtls  = $this->BDF_model->get_dscnt_dtls($account_id);

        $this->session->set_userdata('current_membership',$current_mem);

        if(isset($current_mem) && !empty($current_mem)){
            $data['current_mem'] = $current_mem;
            $data['upgraded_mem'] = $upgraded_mem;
            $data['dscnt_dtls'] = $dscnt_dtls;

            $renewal_types = explode(",",$current_mem['renewal_types']);//1,3,6
            $membership_types = $this->BDF_model->get_membership_types($renewal_types);
            
            $data['renewal_types'] = $membership_types;

            $years_active = $dscnt_dtls['years_active'];

            $discounts = explode(",",$dscnt_dtls['discount']);
            $disc = $discounts[$years_active];//0.7

            $disc_perc = 100 - (100 * $disc); //discount percentage will always be same regardless of parking
            $disc_amt = $current_mem['annual_rent'] - ($current_mem['annual_rent'] * $disc); //discount amount will differ as per parking ,initially the discount is given as per the current parking 

            $data['disc'] = $disc;
            $data['disc_perc'] = $disc_perc;
            $data['disc_amt'] = $disc_amt;
        }
        $this->load->view('renew',$data);
    }

    public function get_valet_locs($client_id = 0,$account_type_id = 0){
        $val_locs = $this->BDF_model->get_valet_locs($client_id,$account_type_id);
        return $val_locs;
    }


    public function get_sub_locs($account_id = ''){
        $sub_loc_res = $this->BDF_model->get_sub_locs($account_id);
        return $sub_loc_res;
    }

    //
    public function forgot_password(){
        $email = $this->input->post('email');

        $is_registered = 0;
        $is_verified = 0;

        //required and valid email
        $this->form_validation->set_rules('email','Email/Phone','required',array('required'=>'Please enter registered Email/Phone'));
        $this->form_validation->set_rules('email','Email','valid_email',array('valid_email'=>'Please enter a valid email address'));

        if($this->form_validation->run() === FALSE){
            $this->session->set_flashdata('errors',validation_errors());
            $this->load->view('forgot_pass_1');
		}
        else{
            //registered and active
            $is_registered = $this->BDF_model->validate_email($email);
            
            if($is_registered){
                //verify
                $is_verified = $this->BDF_model->verify_email($email);
                
                if($is_verified){
                    //register the token
                    $token = $this->BDF_model->gen_tkn_fgt_pass($email,$is_registered); 
                    $data['token'] = $token;
                    
                    //send an email with the link to new password creation page with token
                    $url = base_url() ."gen_new_pass/" .$token;
                    $data['url'] = $url;
                    
                    $is_mail_sent = $this->send_mail($email,$url,'forgot_pass_mail','ParkPoint - Account Password Reset');
                    $data['is_mail_sent'] = $is_mail_sent;
                    $this->load->view('forgot_pass_1',$data);                
                }
                else{
                    $data['not_verified'] = $is_verified;
                    $this->load->view('forgot_pass_1',$data);
                }
            }
            else{
                $data['not_registered'] = $is_registered;
                $this->load->view('forgot_pass_1',$data);
            }
        }

    }

    public function send_mail($email = '',$url = '',$mail_view = '',$mail_sub = ''){
        $data['url'] = $url;

        try{

            $mail = new PHPMailer();					
            $mail->isSMTP();					
            $mail->Host       = "smtp.gmail.com";
            $mail->Port       = 587; //you could use port 25, 587, 465 for googlemail					
            $mail->SMTPAuth   = true; 
            $mail->SMTPSecure = "tls";  //tls
            
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
    
            $mail->Username   = "info@park-pass.com";
            $mail->Password   = "abcd@0987";
    
            $mail->setFrom('info@park-pass.com','Park Point');
            $mail->addReplyTo('no-reply@park-point.com');
    
            
            //$mail->AddBCC($admin_mail,"Admin");
            //$mail->AddBCC($accounts_email,"Accounts");
    
            $mail->Subject = $mail_sub;
                
            $mail->CharSet = 'UTF-8';
            $mail->isHTML(true);
            //$mail->SMTPDebug =2;
    
            $mail->addAddress($email, $mail_sub);
            $mail->Body = $this->load->view($mail_view,$data,true);	
        
            
            $mail_response = 0;
            if(!$mail->send())
            {
                $errorMessage = $mail->ErrorInfo;
                $mail_response = 0;
            }
            else
            {
                $msg = array('status'=>'success','msg'=>'Email has been sent successfully');
                $mail_response = 1;
            }
            return  $mail_response;				
        }
            
        catch(phpmailerException $e){
            return  false;
        }
    }

    public function create_new_pass($token = ''){
        if(isset($token)){
            $is_valid = $this->BDF_model->validate_token($token);
            if($is_valid){
                $data['account_id'] =  $is_valid['account_id'];
                //display view for generating new password
                $this->load->view('forgot_pass_3',$data);
            }
            else{
                $data['title'] = 'Link Expired';
                $this->load->view('expired_link',$data);
            }
        }
    }

    public function set_new_pass(){
        $is_changed = false;

        $account_id = $this->input->post('account_id');

        $password = $this->input->post('password');
        $confirm_password = $this->input->post('confirm_password');
        
        //verify and validate password and confirm password
        $this->form_validation->set_rules('password','Password','required|exact_length[6]|regex_match[/^(?=.+\w)(?=.*[@#$%^&+=_]).{6}$/]',array('required'=>'Please enter your new password','exact_length'=>'Password must be 6 characters','regex_match'=>'Password must contain alphabets,numbers and atleast one character(@#$%^&+=_)'));
        $this->form_validation->set_rules('confirm_password','Confirm Password','required|matches[password]',array('matches'=>'Passwords do not match'));

        if($this->form_validation->run() === FALSE){
            $this->session->set_flashdata('errors',validation_errors());
            $this->load->view('forgot_pass_2');
		}
        else{
            //delete the token
            $dlt_tkn = $this->BDF_model->dlt_tkn($account_id);

            //change the password of this account
            $is_changed = $this->BDF_model->change_password($account_id,$password);
            $data['is_changed'] = $is_changed;

            //show success msg with taking to login page option
            $this->load->view('forgot_pass_3',$data);

        }
    }

    //verify if user is registered ,doesnot have a password already,and then send OTP 
    //if password is already available then redirect to reset with mobile already filled
    public function verify_send_otp(){
        $otp_sent = 0;
        $mobile = $this->input->post('mobile');

        if(isset($mobile) && is_numeric($mobile) && (strlen((string)$mobile) == 8) ){

            $is_mob_reg = $this->BDF_model->validate_mobile($mobile);
            if(!$is_mob_reg){
                echo json_encode('-1'); //mobile number is not registered yet i.e not a member or regsitered under another
            }
            else{
                $has_pass = ($is_mob_reg['password'] != null && $is_mob_reg['password'] != '') ? true : false;
                if($has_pass){
                    $this->session->set_userdata('mobile',$mobile);
                    echo json_encode('2'); //already has a password redirect to forget password reset using mobile
                    // $this->forgot_pass_2($mobile);
                }
                else{
                   // $otp_sent = $this->generate_OTP($mobile);
                    $otp_sent = 1234;
                    $this->session->set_userdata('code', $otp_sent);
        
                    echo json_encode($otp_sent); 
                }
                
            }
        }
        else{
            redirect('sign_in');
        }
         
    }

    public function generate_OTP($mobile = 0){
        require APPPATH . 'api/smsScript.php';

        if($mobile != null && $mobile !==0){

            $member_number = $mobile;
            $otp = rand(1000, 9999);

            if (!empty($member_number) && $member_number != '0') {
                sendMsg($member_number, "Your ParkPass activation PIN is "."$otp");
            }

            $generatedOTP = $otp;
            $json = array(
                'genotp' => $generatedOTP,
                'otp' => $otp
            );
            $this->session->set_userdata('code', $generatedOTP);
            $this->session->set_userdata('mobile', $member_number);  
            return $otp;   
        }
        else{
            return 0;
        } 
    }

    public function compare_otp($user_otp = 0000,$otp_sent = 0000){
        return ($user_otp === $otp_sent) ;
    }

    public function auth_crd($email = '',$password = '',$id_type ='email',$id = ''){
        $account_info = $this->BDF_model->authenticate($email,$password,$id_type,$id);
        if(!empty($account_info)){
            return $account_info;
        }
        return false;
    }

    public function get_token_set_new_pass(){
        $mobile = $this->input->post('phone');
        $otp = $this->input->post('otp');        

        if(isset($mobile) && isset($otp) && $mobile != null && $otp != null){
            $otp_sent = $this->session->userdata('code');
            
            //verify same otp is entered as sent
            $is_otp_verified = $this->compare_otp($otp,$otp_sent);
            if($is_otp_verified){
                $this->session->set_flashdata('errors',"OTP incorrect. {$otp_sent} , {$otp}");
                $this->load->view('forgot_pass_2');
            }

            else{

                //find account to register new token for resseting under it
                $account_info = $this->BDF_model->validate_mobile($mobile);
                if(!empty($account_info)){
                    $token = $this->BDF_model->gen_tkn_fgt_pass($mobile,$account_info); 
                    $data['token'] = $token;

                    $this->create_new_pass($token);
                }
                else{
                    $this->load->view('sign_in');
                }
            }
        }
        else{
            redirect('forgot_pass_2');
        }
    }

    public function create_pass(){
        $phone = $this->input->post('phone');
        $otp = $this->input->post('otp');
        
        $otp = (int)$otp;
        $otp_sent = $this->session->userdata('code');

        $this->session->set_userdata('user_phone', $phone);

        $this->form_validation->set_rules('phone','Phone','required|min_length[8]',array('min_length'=>'Please enter valid mobile number'));
        $this->form_validation->set_rules('otp','OTP','required',array('required'=>'Please enter the OTP sent to your mobile'));

        if($this->form_validation->run() === FALSE){
            $this->session->set_flashdata('errors',validation_errors());
            $this->load->view('dont_have_pass_1');
		}
        else{
            $is_mob_reg = $this->BDF_model->validate_mobile($phone);
            if($is_mob_reg){
                //compare otp
                $is_otp_verified = $this->compare_otp($otp,$otp_sent);
                if($is_otp_verified){
                    $this->load->view('dont_have_pass_2');
                }
                else{
                    $this->session->set_flashdata('errors',"OTP incorrect. {$otp_sent} , {$otp}");
                    $this->load->view('dont_have_pass_1');
                }                
            }
            else{
                $this->session->set_flashdata('errors','Please ensure that this number is registered');
                $this->load->view('dont_have_pass_1');
            }  
        }

    }

    public function create_account(){
        $vrfy_mail_view = 'verify_mail';
        $mobile = $this->session->userdata('user_phone');
        
        if(isset($mobile)){
        
            $email = $this->input->post('email');
            $this->session->set_userdata('user_email', $email);

            $password = $this->input->post('password');
            $confirm_password = $this->input->post('confirm_password');

            $this->form_validation->set_rules('email','Email','required',array('required'=>'Please enter your email address'));

            //verify and validate password and confirm password
            $this->form_validation->set_rules('password','Password','required|exact_length[6]|regex_match[/^(?=.+\w)(?=.*[@#$%^&+=_]).{6}$/]',array('required'=>'Please enter your new password','exact_length'=>'Password must be 6 characters','regex_match'=>'Password must contain alphabets,numbers and atleast one character(@#$%^&+=_)'));
            $this->form_validation->set_rules('confirm_password','Confirm Password','required|matches[password]',array('matches'=>'Passwords do not match'));

            if($this->form_validation->run() === FALSE){
                $this->session->set_flashdata('errors',validation_errors());
                $this->load->view('dont_have_pass_2');
            }
            else{
                //find account to register new token for resseting under it
                $account_info = $this->BDF_model->validate_mobile($mobile);
                if(!empty($account_info)){

                    //send an email with the link to new password creation page with token
                    $token = $this->BDF_model->tokenize_acc($account_info['customers_id']);
                    $url = base_url() ."verify_email/" .$token;
                    $data['url'] = $url;
                    $mail_sent = $this->send_mail($email,$url,$vrfy_mail_view,'ParkPoint - Email Verification');

                    $pass_created = $this->BDF_model->change_password($account_info['customers_id'],$password); 
                    $email_register = $this->BDF_model->reg_email($account_info['customers_id'],$email);
                    $data['pass_created'] = $pass_created;

                    $this->load->view('dont_have_pass_2',$data);
                }
                else{
                    $this->load->view('sign_in');
                } 
            }
        }
        else{
            redirect('dont_have_pass');
        }
        
    }


    public function verify_email($token = ''){
        $email = $this->session->userdata('user_email');

        if(isset($email)){
            $is_verified = $this->BDF_model->verify_mail($token);

            if($is_verified){                
                $data['is_verified'] = $is_verified ;
                $data['user_email'] = bin2hex($email) ;
                $this->load->view('email_verified',$data);
            }else{
                $data['title'] = 'Invalid Token';
                $this->load->view('expired_link',$data);
            }
        }
        else{
            $data['title'] = 'Invalid Token';
            $this->load->view('expired_link',$data);
        }
    }

    // existing functions from BDFApply

    public function getUpgradedParking(){
		$parking_id = $this->input->post('parking_id');

		$records = $this->BDF_model->get_upgraded_parkings($parking_id);
		echo json_encode($records->result());
	}

    //receiving additional values
    public function getVacantParks() {
		$id      = $this->input->post('parkingLot');
		$records = $this->BDF_model->getFreeParkSpots($id);

		echo json_encode($records->result());
	}

    public function renew_bdf(){
        
        $parking_area = $this->input->post('parking_area');
        $parking_space = $this->input->post('parking_space');
        $parking_type = $this->input->post('parking_type');
        $amt_payable = $this->input->post('amt_payable');
        $payment_method = $this->input->post('payment_method');

        $current_mem = $this->session->userdata('current_membership');
        $member_info = $this->session->userdata('member_info');
        $dept_info   = $this->session->userdata('dept_info');

        $park_type   = $parking_type .'-' .$amt_payable;
        $location_id = $current_mem['location_id'];
        $membership_type_id = $current_mem['membership_type_id'];

        $valid_from = $this->input->post('valid_from');
        $valid_until = $this->input->post('valid_until');

        $account_id =  $current_mem['account_id'];
        $first_name = $member_info['first_name'];
        $last_name = $member_info['last_name'];
        $email = $member_info['email'];
        $mobile = $member_info['mobile_number'];
        $cpr = $member_info['CPR'];
        $gender = $member_info['gender'];

        $dept = $dept_info['department'];
        $org = $dept_info['organization'];
        $prof = $dept_info['profession'];

        $old_parking = $current_mem['Parking_id'];
        $old_parkspace = $current_mem['parkspace_id'];

        $this->session->set_userdata('parkingLot',$parking_area);

        $this->reviewBDFRenewApplication($park_type ,$location_id ,$membership_type_id ,$account_id,$first_name,$last_name= '',$email ='',$mobile,$cpr ,$gender,$dept,$prof,$org,
        $parking_area ,$parking_space ,$old_parking ,$old_parkspace ,$valid_from,$valid_until,$amt_payable,$payment_method);
 
    }

    //existing BDFApply function

    	public function reviewBDFRenewApplication($park_type = 0,$location_id = 0,$membership_type_id = 0,$account_id = '',$first_name = '',$last_name= '',$email ='',$mobile= 0,$cpr = 0,$gender='',$department = '',$organization='',$profession = '',
        $new_parking = 0,$new_parkspace = 0,$old_parking = 0,$old_parkspace = 0,$start_date = '',$end_date='',$amount_payable = 0,$payment_method = '') {

            $parkTypeSelect  		  = $park_type;//annual/monthly or whatever
            $location_id 			  = $location_id;		

            $membership_type_id       = $membership_type_id;
            
            $nameInput       		  = $first_name;
            $lastNameInput  		  = $last_name;	
            
            $emailInput      		  = $email;
            $mobileInput     		  = $mobile;
            
            $cpr     		 		  = $cpr;
            $gender          		  = $gender;
            
            $department      		  = $department;
            $profession      		  = $profession;
            
            $new_parking_id	  	      = $new_parking; //id
            $new_park_space_id        = $new_parkspace;
            
            $existing_parking_id	  = $old_parking; //id
            $existing_park_space_id   = $old_parkspace;

            $cdate      			  = $start_date;
            $startDate      		  = $start_date;	//will be used later for monthly renewals	
            $to_date                  = $end_date;

            $amount     	 		  = $amount_payable;


            //if another parking selected then set the new pne in reserved and release old one
            if($new_park_space_id !== $existing_park_space_id){
                $parking = $new_parking_id;
                $park_space = $new_park_space_id;
                
            $reserved_array = array(
                    'account_id'=>$account_id,
                    'parkspace_id'=>$new_park_space_id
                );
                $this->session->set_userdata('reserved_space', $reserved_array); 
                $from_date  = $cdate;
            }
            else{
                $parking    = $existing_parking_id;
                $park_space = $existing_park_space_id;
                $from_date  = $startDate; 
            }

            $setting_session_data = $this->set_session_data($account_id,$emailInput,$nameInput,$lastNameInput,$gender,'1',$cpr,$department,$profession,$organization,$parking,$park_space,$parkTypeSelect,$from_date,$to_date,$amount,$location_id,$payment_method);

            if($setting_session_data){
                echo json_encode(true);
            }
            else{
                redirect('renew/'.$account_id);
            }
        }

    	public function set_session_data($account_id = '',$emailInput = '',$nameInput = '',$lastNameInput = '',$gender = '',$is_renew = '',$cpr = 0,$department= '',$profession= '',$organization = '',$parking= '',$park_space= '',$parkTypeSelect= '',$from_date= '',$to_date= '',$amount= '',$location_id = '',$payment_method = ''){

            $this->session->set_userdata('account_id', $account_id);
            $this->session->set_userdata('emailInput', $emailInput);
            $this->session->set_userdata('nameInput', $nameInput);
            $this->session->set_userdata('lastNameInput', $lastNameInput);
            $this->session->set_userdata('gender', $gender);
            $this->session->set_userdata('renewalOfApplication', $is_renew);
            $this->session->set_userdata('cpr', $cpr);
            
            $this->session->set_userdata('department', $department);
            $this->session->set_userdata('profession', $profession);
            $this->session->set_userdata('organization', $organization);
            
            $this->session->set_userdata('parkingSelect', $parking); //e-n id
            $this->session->set_userdata('parkSpaceSelect', $park_space);//existing & new  Id
            $this->session->set_userdata('parkTypeSelect', $parkTypeSelect);//existing & new  Id


            $this->session->set_userdata('startDate', $from_date);
            $this->session->set_userdata('endDate', $to_date);
            
            $this->session->set_userdata('price', $amount);
            $this->session->set_userdata('location_id', $location_id);
            
            $this->session->set_userdata('payment_method', $payment_method);

            return true;
        }

        public function reviewBDFOrder($is_bdf = true) {

            $location_id 	= $this->session->userdata('location_id');

            if($location_id != 19){
                $is_bdf = false;
            }

            $location_name  = $this->BDF_model->get_location_name($location_id);
            $data['location_name']  = $location_name['name'];

            
            $data['nameInput']         = $this->session->userdata('nameInput');
            $data['lastNameInput']     = $this->session->userdata('lastNameInput');
            
            $data['emailInput']        = $this->session->userdata('emailInput');
            $data['mobileInput']       = $this->session->userdata('mobileInput');
            
            $data['gender']            = $this->session->userdata('gender');
            $data['cpr']               = $this->session->userdata('cpr');
            
            $data['department']        = $this->session->userdata('department');
            $data['profession']        = $this->session->userdata('profession');            
            $data['organization']      = $this->session->userdata('organization');

            $data['location_id']       = $this->session->userdata('location_id');
            
            $data['parkSpaceSelectId'] = $this->session->userdata('parkSpaceSelect');// park_space_id ,parking_details['ps_id']
            $data['OTP']               = $this->session->userdata('OTP');
            $data['parkingSelectId']   = $this->session->userdata('parkingSelect');//parking_id
            
            $data['price']             = $this->session->userdata('price');
            
            
            $data['startDate']         = $this->session->userdata('startDate');
            $data['endDate']           = $this->session->userdata('endDate');

            $data['is_bdf']			   = $is_bdf;
        
            if($data['location_id'] != 19){
                //show error
                $parking_name 	 = $this->BDF_model->getParkingName($data['parkingSelectId']);
                $data['parkingSelectName'] = $parking_name['name'];
                $data['parkSpaceId'] 	 = $this->session->userdata('parkSpaceSelect');
            }
            else{
                $parkingNames = $this->BDF_model->getParkingLotNameAndParkingSpaceName($this->session->userdata('parkSpaceSelect'));
                $data['parkSpaceSelectName'] = $parkingNames['0']->park_name;
                $data['parkingSelectName'] 	 = $parkingNames['0']->name;
            }
                
            $data['Admin'] = $this->session->userdata('Admin');

            $this->paymentCheck(); //show a summary and selection of payment method
        }

        public function paymentCheck() {

            $location_id     = $this->session->userdata('location_id');

            $cardType        = $this->session->userdata('payment_method');
            $cpr_number      = $this->session->userdata('cpr');
            $price_val       = $this->session->userdata('price');
            
            $invoiceNumber   = $this->createInvoiceNumber($location_id);
            $the_price 		 = $this->session->userdata('price');
            $price           = urlencode($this->session->userdata('price'));
            $name            = urlencode($this->session->userdata('nameInput') . ' ' . $this->session->userdata('lastNameInput'));
            
            $parkinglot      = urlencode($this->session->userdata('parkingSelect'));
            $parkingspace    = urlencode($this->session->userdata('parkSpaceSelect'));

            $this->session->set_userdata('cprNumber', $cpr_number);
            $this->session->set_userdata('priceVal', $price_val);


            $first_name = $this->session->userdata('nameInput'); 
            $last_name  = $this->session->userdata('lastNameInput'); 

            if($location_id == 19){
                $trns_tbl_name = 'subs_transactions';
                $invoice_field = 'id';			
            }
            else{
                $trns_tbl_name = 'membership_transactions';
                $invoice_field = 'invoice_id';			
            }

            $this->session->set_userdata('card_type', $cardType);
            $this->session->set_userdata('trns_tbl_name', $trns_tbl_name);
            $this->session->set_userdata('invoice_field', $invoice_field);

            $invoiceNumber = sprintf("%'.06d", $invoiceNumber);

            $this->db->trans_start();
                $this->db->insert($trns_tbl_name, array($invoice_field => $invoiceNumber, 'invoice_date' => date('Y-m-d H:i:s')));
            $this->db->trans_complete();
            

            $this->session->set_userdata('invoiceNumber', $invoiceNumber);
            global $invoice_number;
            $invoice_number = $invoiceNumber;

            if($cardType == 'debit') {
                $name = $first_name .' '. $last_name;
                $fields = array(
                    'price' => $price_val, 
                    'trackid' => $invoiceNumber, 
                    'parkinglot' => $parkinglot, 
                    'parkingspace' => $parkingspace,
                    'name'=>$name,
                    'cpr_number'=>$cpr_number
                );
                
                $fields = array('price' => $price, 'name' => $name, 'trackid' => $invoiceNumber, 'parkinglot' => $parkinglot, 'parkingspace' => $parkingspace,'cpr_number'=>$cpr_number);
                $fields_string = Null;
                foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
                rtrim($fields_string, '&');			

                $this->session->set_userdata('paymentDetails', $fields);

                //benefit here
                    //open connection
                    $ch = curl_init();

                    //set the url, number of POST vars, POST data
                    curl_setopt($ch, CURLOPT_URL, "https://park-pass.com/api/prod/request.php");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

                    //execute post
                    curl_exec($ch);

                    //close connection
                    curl_close($ch);

                //benefit end
            } 
            else if ($cardType == 'test123'){
                $this->paymentSuccess();
            }
            else if($cardType = 'credit') {
                $this->paymentProcess();
            }		
        }


        public function createInvoiceNumber($location_id = 19) {
            $location_id = $this->session->userdata('location_id');
            $result      = $this->BDF_model->generateInvoiceNumber($location_id);
            $objResult   = $result->row();
            $membership  = $objResult->id;

            if($location_id == 19){
                $numberArray = str_split($membership);

                $numberInteger    = (int)$numberArray[0].$numberArray[1].$numberArray[2].$numberArray[3].$numberArray[4].$numberArray[5];
                $invoiceIncrement = $numberInteger += 1;
                $invoiceString    = (string)$invoiceIncrement;
                $invoiceArray     = str_split($invoiceString);
                
                $counting = count($invoiceArray);
                for($i = 0; $i < 6 - $counting; $i+=1) {
                    array_unshift($invoiceArray, 0);
                }
                $invoiceId = implode($invoiceArray);
            }else{
                if(!empty($membership)){
                    $membership_id = (int)$membership;
                    $membership_id++;
                    $invoiceId = sprintf("%'.06d", $membership_id);
                }
                else{
                    $invoiceId = 000001;
                }
            }        
            return $invoiceId; //Invoice ID formed return it to another controller
        }

        public function paymentProcess() {

            $invoiceNumber = $this->session->userdata('invoiceNumber');
            $price         = $this->session->userdata('price');

            $parkSpace     = $this->session->userdata('parkSpaceSelect');
            $parking       = $this->session->userdata('parkingSelect');

            if($parkSpace != null && $parkSpace != '' && $parking != null && $parking != ''){
                $data['parkingLot']    = $parking;
                $data['parkSpace']     = $parkSpace;
            }
            else{
                $data['parkingLot']    = $this->session->userdata('parkingLot');
                $data['parkSpace']     = $this->session->userdata('parkSpace');
            }
            
            $data['invoiceNumber'] = $invoiceNumber;
                
            $data['session_id']  = $this->curlCrediMax($invoiceNumber, $price);
            $this->load->view('payTest', $data);
        }

        public function curlCrediMax($orderId = 'TES123', $orderAmount = '0.000') {
            $operation            = 'CREATE_CHECKOUT_SESSION';
            $password             = '73ed433cfcb390ce623bf20b9a2789e5';
            $interaction          = 'https://park-pass.com/paymentSuccess';
            $username             = 'merchant.E10862951';
            $merchant             = 'E10862951';
            $orderCurrency        = 'BHD';
            $interactionOperation = 'PURCHASE';

            $fields = array('apiOperation' => $operation, 'apiPassword' => $password, 'interaction.returnUrl' => $interaction, 'apiUsername' => $username, 'merchant' => $merchant, 'order.id' => $orderId, 'order.amount' => $orderAmount, 'order.currency' => $orderCurrency, 'interaction.operation' => $interactionOperation);

            $fields_string = Null;

            foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }

            rtrim($fields_string, '&');
            //open connection
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, "https://credimax.gateway.mastercard.com/api/nvp/version/53");
            // curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //execute post
            $result = curl_exec($ch);
            //close connection
            curl_close($ch);
            $resultArray    = explode('&', $result);
            $sessionId      = explode('=', $resultArray[2]);
            $sessionVersion = explode('=', $resultArray[4]);
            array_push($sessionId, $sessionVersion[1]);
            $this->session->set_userdata('sessionVersion', $sessionVersion[1]);
            // Final result
            return $sessionId;

        }

        public function paymentSuccess() {
            
            $location_id = $this->session->userdata('location_id');
            $sessionType = $this->session->userdata('renewalOfApplication');


            $location_name  = $this->BDF_model->get_location_name($location_id);
            $data['location_name']  = $location_name['name'];

            if($sessionType == '1') {
                if($location_id == 19){
                    redirect(base_url('BDF/submitBDFRenewal'));
                }else{
                    //show error
                    redirect(base_url('BDF/BDFApply/submitNonBDFRenewal'));
                }
            }
        }

        public function submitBDFRenewal() {

            $sessionInfo = $this->session->userdata('account_id');
            $membershipGenerated = $this->session->userdata('account_id');

            if(!isset($sessionInfo)) {
                redirect('renew/'.$membershipGenerated);
            }

            $parkTypeSelect = $this->session->userdata('parkTypeSelect');

            $customers_info['first_name']    = $this->session->userdata('nameInput');
            $customers_info['last_name']     = $this->session->userdata('lastNameInput');
            $customers_info['email']         = $this->session->userdata('emailInput');
            $customers_info['mobile_number'] = $this->session->userdata('mobile');
            $customers_info['gender']        = $this->session->userdata('gender');
            $customers_info['client_id']	   = 16;

            $customers_info = $this->BDF_model->getCustomerInformation($membershipGenerated);

            $account['id']               = $membershipGenerated;
            $account['expiry_date']      = date('Y-m-d 23:59:59', strtotime($this->session->userdata('endDate')));
            $account['create_date']      = date('Y-m-d 23:59:59', strtotime($this->session->userdata('startDate')));

            $account['active']           = 1;

            $accountArray = array(	
                'create_date' => $account['create_date'],
                'expiry_date' => $account['expiry_date'],
                'active' => $account['active']
            );

            //Subs_transaction record
            $invoiceNumber = $this->session->userdata('invoiceNumber');

            $numbered_price = (double)$this->session->userdata('price');

            $subs_transactions['id']           = $invoiceNumber;
            $subs_transactions['invoice_date'] = date('Y-m-d H:i:s');
            $subs_transactions['customers_id'] = $account['id'];
            $subs_transactions['amount']       = $numbered_price;
            $subs_transactions['payment_mode'] = 'Online';
            $subs_transactions['filled_by']    = 'Member';		
            $subs_transactions['status']       = 'Renew';		

            //$parkSpace = $this->session->userdata('parkSpaceSelect');
            $parkSpace = $this->session->userdata('parkSpaceSelect');

            $parking_job_order = array(
                'account_id' => $account['id'],
                'job_order' => 'Renew membership',
                'subs_transactions_id' => $invoiceNumber ,
                'parkspace_id' => $parkSpace,
                'order_date' => date('Y-m-d H:i:s'), 
                'filled_by' => 'Member',
                'amount' => $numbered_price,
                'status' => 1
            );  

            $renewal_date = '2021-01-01 00:00:00';

            $renewal_sales_data = array(
                'invoice_date' => date('Y-m-d H:i:s'),
                'subs_transactions_id' => $subs_transactions['id'] ,
                'account_id' => $account['id'],
                'amount' => $subs_transactions['amount'],
                'filled_by' => 'Member',
                'status' => 'Online Renewal',
                'payment_mode' =>'Online',
                'renewal_type'=> $parkTypeSelect,
                'renewal_date'=> date('Y-m-d H:i:s', strtotime($renewal_date))
            );
            
            $this->db->trans_start();
                $this->db->set($accountArray);
                $this->db->where('id', $membershipGenerated);
                $this->db->update('account');
                
                $this->db->set($subs_transactions);
                $this->db->where('id', $invoiceNumber);
                $this->db->update('subs_transactions');

                if($this->session->userdata('reserved_space') != null && $this->session->userdata('reserved_space') != ''){
                    $reserved_space = $this->session->userdata('reserved_space');
                    $this->db->insert('reserved_space',$reserved_space);

                    $park_space_array = array('vacant'=>'busy','status'=>0);
                    $this->db->set($park_space_array);
                    $this->db->where('id',$parkSpace);
                    $this->db->update('park_space');
                }

                $this->db->insert('parking_job_orders',$parking_job_order);
                $this->db->insert('renewal_sales',$renewal_sales_data);

            $this->db->trans_complete();

        
            $reserved_space['parkspace_id'] = $this->db->get_where('reserved_space', array('account_id' => $account['id']));
            $reserved_spaceDetails = $reserved_space['parkspace_id']->result();
            
            $parkInfo = $this->BDF_model->getParkingLotNameAndParkingSpaceName($reserved_spaceDetails['0']->parkspace_id);

            $member_vehicle = $this->db->get_where('account_cars', array('account_id' => $account['id']))->result_array();

            //email configuration and design for member
                $this->load->library('email');              
                $admin_email = 'bdf.member@park-point.com';			                
                $accounts_email = 'accounts3@park-point.com';			                
                $this->load->library('mail');
                
                $customer_mail = $customers_info['email']; 

                $emails = ['members','admin','accounts'];
                
                $data['first_name']     = $customers_info['first_name'];
                $data['last_name']      = $customers_info['last_name'];
                $data['mobile_number']  = $customers_info['mobile_number'];
                $data['email']          = $customers_info['email'];
                $data['create_date']    = $account['create_date'];
                $data['expiry_date']    = $account['expiry_date'];
                $data['id']             = $account['id'];
                $data['cars']           = $member_vehicle;
                $data['price']          = $numbered_price;

                try{

                    $mail = new PHPMailer();					
                    $mail->isSMTP();					
                    $mail->Host       = "smtp.gmail.com";
                    $mail->Port       = 587; //you could use port 25, 587, 465 for googlemail					
                    $mail->SMTPAuth   = true; 
                    $mail->SMTPSecure = "tls";  //tls
                    
                    $mail->SMTPOptions = array(
                                            'ssl' => array(
                                                'verify_peer' => false,
                                                'verify_peer_name' => false,
                                                'allow_self_signed' => true
                                            )
                                        );

                    $mail->Username   = "info@park-pass.com";
                    $mail->Password   = "abcd@0987";

                    $mail->setFrom('info@park-pass.com','Park Point');
                    $mail->addReplyTo('no-reply@park-point.com');

                    $mail->AddBCC($admin_email,"Admin");
                    $mail->AddBCC('m.adil@park-point.com',"Admin");
                    $mail->AddBCC($accounts_email,"Accounts");
                    $mail->Subject = 'BDF Membership – Renewal Application';
                        
                    $mail->CharSet = 'UTF-8';
                    $mail->isHTML(true);
                    //$mail->SMTPDebug =2;

                    foreach($emails as $email){
                        $mail->addAddress($customer_mail, 'BDF Membership – Renewal Application');
                        $mail->Body = $this->load->view('BDF/renewal_mail',$data,true);				
                    }
                    
                    $mail_response = 1 ;
                    if(!$mail->send())
                    {
                        $errorMessage = $mail->ErrorInfo;
                        $mail_response = 0;
                    }
                    else
                    {
                        $msg=array('status'=>'success','msg'=>'Email has been sent successfully');
                        $mail_response = 1;
                    }
                    echo true;				
                }
                
                catch(phpmailerException $e)
                {
                    echo false;
                }
            //SMS send
            require APPPATH . 'api/smsScript.php';

            $member_number = $customers_info['mobile_number'] ;
            

            if (!empty($member_number) && $member_number != '0') {
                sendMsg($member_number, "Dear Member".",\n\n"."Welcome to ParkPoint."."\n\n"."your membership is now confirmed, we have sent you an email with details of the membership."."\n\n"."Thank you,"."\n"."ParkPoint");
            }
            $this->session->set_userdata('app_status', 'Renew');

            redirect(base_url('receipt'));
        }

        public function receipt() {

            $data['membershipId'] = $this->session->userdata('account_id');
            $data['location_id']  = $this->session->userdata('location_id');
            $data['renew'] 	      = $this->session->userdata('app_status');
            
            if($data['membershipId'] == "" || $data['membershipId'] == null){
                //show error
                $data['membershipId'] = 'BBD03210100';
                $data['location_id']  = 22;
                $data['renew'] 	      = 'Renew';
            }

            $location_id             = $this->session->userdata('location_id');
            
            $location_name           = $this->BDF_model->get_location_name($location_id);
            $data['location_name']   = $location_name['name'];
            
            if(!isset($data['membershipId'])) {
                //show error
                redirect('new/'.$location_id);
            };

            $membershipInfoResult       = $this->BDF_model->getReceiptInfo($data['membershipId'],$data['renew']);
            $data['receiptInformation'] = $membershipInfoResult->result();
            $receiptParkResult          = $this->BDF_model->getReceiptPark($data['membershipId'],$data['location_id']);
            $data['receiptPark']        = $receiptParkResult->result();

            $admin = $this->session->userdata('Admin');
            if($admin != 'AliX&^%$'){
                $this->session->sess_destroy();
            }
            $this->load->view('receipt', $data);
        }

}
?>