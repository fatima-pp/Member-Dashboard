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

    public function sign_in(){
        $this->load->view('zain_registration');
    }

    public function sign_in_otp(){

        $mobile = $this->input->post('mobile');

        $this->form_validation->set_rules('mobile','Mobile Number','required|min_length[8]',array('min_length'=>'Please enter valid mobile number'));

        if($this->form_validation->run() !== FALSE){
            // make one db read,without condition just mobile number
            // if no row returned then mobile doesnot exists
            // else then check each value seperately and display errors accordingly.
            $is_zain = $this->Zain_model->verify_zain($mobile);
            if($is_zain){
                $is_ppass = $this->Zain_model->verify_ppass($mobile);
                if($is_ppass){
                    $data['zain_dtls'] = $is_zain;
                    $data['ppass_dtls'] = $is_ppass;
                    $this->load->view('zain_registration_otp',$data);             
                }
            }
        }
        else{
            $this->sign_in();
        }
    }

}

?>