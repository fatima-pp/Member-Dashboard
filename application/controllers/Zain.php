<?php

require "vendor/autoload.php";
require "class.phpmailer.php";
require "class.smtp.php";

class Zain extends CI_Controller{

    public function __construct()
    {
        parent ::__construct();
        $this->load->model('Zain_model');
        $this->load->library('form_validation');
        $this->load->library('session');
    }

    public function sign_in($client_id = 0){

        $this->session->set_userdata('client_id',$client_id);
        $client_info  = $this->Zain_model->get_client_info($client_id);

        $data['client_info'] = $client_info;

        if($client_info['id'] == 1){
            $this->load->view('zain_registration',$data);
        }
    }

    public function sign_in_otp(){

        $client_id = $this->session->userdata('client_id');
        $mobile = $this->input->post('mobile');

        $this->form_validation->set_rules('mobile','Mobile Number','required|exact_length[8]',array('exact_length'=>'Please enter valid mobile number'));

        if($this->form_validation->run() !== FALSE){
            // make one db read,without condition just mobile number
            // if no row returned then mobile doesnot exists
            // else then check each value seperately and display errors accordingly.
            
            // mobile exists
            $is_zain = $this->Zain_model->verify_zain($mobile);
            if($is_zain){

                // active from zain
                $is_zain_actv = ($is_zain['clientStatus'] === 'active') ? true : false;
                if($is_zain_actv){

                    // is assigned parkpass
                    $is_ppass = ($is_zain['privilegeId'] !== '' && $is_zain['privilegeId'] !== null) ? true : false;
                    if($is_ppass){

                        // is parkpass account active and expired or not
                        $ppass_active = ($is_zain['status'] === 'active') ? true : false;
                        $ppass_expiry = ($is_zain['expiryDate'] === null || $is_zain['expiryDate'] >=  date("Y-m-d H:i:s")) ? true : false;
                        $is_ppass_valid = ($ppass_active && $ppass_expiry) ? true : false;
                        if($is_ppass_valid){

                            // is OTP sent successfully
                            $otp_sent = $this->generate_OTP($mobile);
                            if($otp_sent){
                                $data['zain_dtls'] = $is_zain;
                                $data['client_id'] = $client_id;
                                $data['mobile'] = $mobile;

                                $zain_dtls = $this->session->set_userdata('zain_dtls',$is_zain);
                                redirect('zain_verification');
                            }
                            else{
                                $this->session->set_flashdata('errors',"Didn't receive OTP ? Click Resend OTP");
                                $this->load->view('zain_registration_otp');
                            }
                        }
                        else{
                            $this->session->set_flashdata('errors','Please ensure your ParkPass membership is active');
                            $this->sign_in($client_id);
                        }         
                    }
                    else{
                        $this->session->set_flashdata('errors','Please ensure you are a ParkPass member');
                        $this->sign_in($client_id);
                    } 
                }
                else{
                    $this->session->set_flashdata('errors','Please ensure you are zain membership is active');
                    $this->sign_in($client_id);
                }            
            }
            else{
                $this->session->set_flashdata('errors','Please ensure you are entering a valid zain number');
                $this->sign_in($client_id);
            }            
        }
        else{
            $this->session->set_flashdata('errors',validation_errors());
            $this->sign_in($client_id);
        }
    }

    
    public function generate_OTP($mobile = 0){
        require APPPATH . 'api/smsScript.php';

        if($mobile != null && $mobile !==0){

            $member_number = $mobile;
            $otp = rand(1000, 9999);

            if (!empty($member_number) && $member_number != '0') {
                // sendMsg($member_number, "Your ParkPass membership activation PIN is "."$otp");
                $otp = 1234;
                // replace seconf line with line 1,for production
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

    public function zain_verification($zain_dtls = null){
        $data = null;
        
        $zain_dtls = $this->session->userdata('zain_dtls');
        $data['zain_dtls'] = $zain_dtls;

        $client_id = $this->session->userdata('client_id');
        $data['client_id'] = $client_id;

        $mobile = $this->session->userdata('mobile');
        $data['mobile'] = $mobile;

        $this->load->view('zain_registration_otp',$data);
    } 
    
    public function zain_verify_otp(){
        
        $otp = (int)$this->input->post('otp');
        $otp_sent = $this->session->userdata('code');

        $client_id = $this->session->userdata('client_id');
        $data['client_id'] = $client_id;

        $this->form_validation->set_rules('otp','OTP','required',array('required'=>'Please enter the OTP sent to provided mobile number'));
        if($this->form_validation->run() !== FALSE){
            if(isset($otp)){
                $is_otp_verified = $this->compare_otp($otp,$otp_sent);
                if($is_otp_verified){
                    $this->load->view('zain_reg_form',$data);
                }
                else{
                    $this->session->set_flashdata('errors',"Incorrect OTP! Enter the OTP sent or click 'Resend OTP' for new OTP");
                    $this->load->view('zain_registration_otp');
                }
            }
        }
        else{
            $this->session->set_flashdata('errors',validation_errors());
            $this->load->view('zain_registration_otp');
        }
    }

    public function compare_otp($user_otp = 0000,$otp_sent = 0000){
        return ($user_otp === $otp_sent) ;
    }

    public function zain_registration_form(){
        $this->load->view('zain_reg_form');
    }

    public function zain_resend_otp(){
        $mobile = $this->input->post('mobile');
        $otp_sent = $this->generate_OTP($mobile);

        echo ($otp_sent) ? json_encode(1) : json_encode(0);

    }

}

?>