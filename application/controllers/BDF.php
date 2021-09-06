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
                    $is_verified = $this->BDF_model->verify_email($email_phone);
                    if($is_verified){
                        // authenticate email and password 
                        $is_auth = $this->auth_crd($email_phone,$password,'email');
                        if($is_auth){
                            // print_r('success');
                            //get account data and show dashboard
                            $data['customer'] = $is_registered;
                            $data['dept'] = $this->get_dept($email_phone,'email');
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
                    // authenticate email and password 
                    $is_auth = $this->auth_crd($email_phone,$password,'mobile_number');
                    if($is_auth){
                        // print_r('success');   
                        //get account data and show dashboard
                        $data['customer'] = $is_mob_reg;
                        $data['dept'] = $this->get_dept($email_phone,'mobile_number');
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
        print_r($account_id);
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

    public function auth_crd($email = '',$password = '',$id_type ='email'){
        $account_info = $this->BDF_model->authenticate($email,$password,$id_type);
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
}
?>