<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Bahrain');
require "vendor/autoload.php";

//require "PHPMailer.php";
require "class.phpmailer.php";
require "class.smtp.php";
//require "PHPMailerAutoload.php";

use Endroid\QrCode\QrCode;

class Airports extends CI_Controller {

	public function index()
	{
		print_r('hello');
    }

    public function __construct(){
        parent ::__construct();
        $this->load->model('Airport_model');
        $this->load->library('form_validation');
    }
    public function php_ver(){
        echo 'Current PHP version: ' . phpversion();
    }

    // $airlines = [
    //     'AEROLOGIC GMBH','AIR ARABIA',	
    //     'Air India','AIR INDIA EXPRESS',	
    //     'Al Naser Airlines','ARKE FLY',	
    //     'Atlas Global', 'Azerbaijan Airlines',	
    //     'BRITISH AIRWAYS','CATHAY PACIFIC',	
    //     'DHL International','EGYPT AIR',	
    //     'EMIRATES AIRLINES','ETHIOPIAN AIRLINES',	
    //     'ETIHAD AIRWAYS','FLY DUBAI',	
    //     'Georgian Airways','GULF AIR',	
    //     'IRAQI AIRWAYS','JAZEERA AIRWAYS',		
    //     'KLM ROYAL DUTCH AIRLINES',	
    //     'KUWAIT AIRWAYS','LUFTHANSA',	
    //     'OMAN AIR','Pegasus Airlines',		
    //     'SAUDI ARABIAN AIRLINES','SRILANKAN AIRLINES',	
    //     'SunExpress Airline','SYRIAN ARAB AIRLINES',	
    //     'TRAVEL SERVICES','TURKISH AIRLINES'	
    // ];
    
    
    public function customer_form($j_id = '',$tok = '',$response = false){

        $data['is_response'] = $response;      

        if($j_id != '' && $j_id != null && $tok != '' && $tok != null){
            $job_id = $j_id;
            $token  = $tok;
        }
        else{
            $url = $_SERVER['REQUEST_URI'];
            $parts = parse_url($url);
            $output = [];
            parse_str($parts['query'], $output); //converts + sign to spaces,can't use urlencode bcz that converts the other nonalphanumberic to hexcodes
        
            $job_id = $output['id'];
            $token  = str_replace(' ', '+',$output['token']);//str_replace to convert space to +
        }

    
        //print_r($job_id.'  -  '.$token);
        if(isset($job_id) && isset($token)){

            $is_parked = $this->Airport_model->check_car_parked($job_id);
            $is_created = $this->Airport_model->check_customer_create($job_id);
            $flight_details = $this->Airport_model->get_flight_details($job_id);

            if($is_parked->num_rows() > 0 ){//it's parked can continue further to display form
                if($is_created->num_rows() > 0){
                    
                    $seed = $this->Airport_model->get_updated_seed();
                    
                    if($seed->num_rows() > 0){
                        
                        $updated_seed_row = $seed->row_array();
                        $created_date_row = $is_created->row_array();

                        $new_token = $job_id . $created_date_row['timestamp']; 
                                                
                        $token_generated = base64_encode(hash_hmac('sha256', $new_token, $updated_seed_row['seed'],true));

                        if($token_generated === $token){
                            $all_images = [];

                            $image_query  = $this->Airport_model->get_car_images($job_id);
                            
                            $new_img_urls = json_decode($image_query['url']);

                            // foreach($new_img_urls as $image){
                                
                            //     $image_exploded = substr($image, 0, strpos($image, "?alt"));
                            //     $all_images = $image_exploded;
                            //     print_r($all_images);
                            // }

                            $data['job_id'] = $job_id;
                            $data['token'] = $token;
                            
                            $data['customer_details'] = $created_date_row;
                            $data['flight_details']   = $flight_details;
                            $data['images'] = $new_img_urls;
                            $qrcode = new QrCode($job_id);        
                            $data['ticket_qr'] = base64_encode($qrcode->writeString());
                            // $data['airlines'] = $airlines;
                            $this->load->view('customer_form',$data);
                        }
                        
                        else{
                            show_404();
                        }
                    }
                    
                    else{
                        show_404();
                    }
                }
            }
            else{
                show_404();
            }
        }
    }

    public function submit_customer_form(){

        //insertion in v_customer_details
        $firstname = $this->input->post('first_name');      
        //$lastname  = $this->input->post('last_name');      
        //$cpr       = $this->input->post('cpr');      
        $email     = $this->input->post('email');
        
        $mobile    = $this->input->post('mobile');
        $whatsapp  = $this->input->post('whatsapp');
        
        $updatedAt = date('Y-m-d H:i:s');
        $updateStatus = 'updated';



        $customer_details = array(
            'firstname' => $firstname,      
            //'lastname'  => $lastname,      
            //'cpr'       => $cpr,      
            'email'     => $email,
            'updatedAt' => $updatedAt,
            'status'    => $updateStatus,
            //'mobile'    => $mobile,
            'whatsapp'  => $whatsapp
        );
        
        //insertion in v_flight_details
        $job_id          = $this->input->post('job_id'); 
        $token          = $this->input->post('token'); 
        $customer_id     = $this->input->post('customer_id'); 
        //$airline         = $this->input->post('airline'); 
        $flight_no       = $this->input->post('flight_no'); 
        $arrival_date    = $this->input->post('arrival_date'); 
        $createdAT       = date('Y-m-d H:i:s'); 


        $flight_details = array(
            'job_id'          => $job_id, 
            'customer_id'     => $customer_id, 
            //'airline'         => $airline, 
            'flight_no'       => $flight_no, 
            'arrival_date'    => $arrival_date, 
            'createdAT'       => $createdAT,
            'status'          => 'created'  
        );

        
        // ask about validation and what should be shown in case of success and error          
        $a = $this->form_validation->set_rules('first_name','Name','required');
        $this->form_validation->set_rules('email','Email','required|valid_email');
        $this->form_validation->set_rules('confirm_email','Confirm Email','required|valid_email|matches[email]');
        $this->form_validation->set_rules('flight_no','Flight Number','required');
        $this->form_validation->set_rules('arrival_date','Return Date','required');
        $this->form_validation->set_rules('terms','Terms  Conditions','required');
    
        if($this->form_validation->run() === FALSE){
          $this->session->set_flashdata('error',validation_errors());
          $this->customer_form($job_id,$token);
        }
        else{	
        
            $insertion_response = $this->Airport_model->update_customer_flight_details($customer_details,$flight_details);
            if($insertion_response){
                $mail_response = $this->send_success_mail($email,$mobile,$customer_details,$flight_details,$token);
                $this->customer_form($job_id,$token,$insertion_response);

            }else{
                print_r($insertion_reponse);
            }
        }
        
    }

    function send_success_mail($email="",$mobile=000000000,$customer_details,$flight_details,$token = ''){
        
        if($email != '' && $email != null){

            $admin_mail = 'bdf.member@park-point.com';	
            
            $data['customer_details'] = $customer_details;
            $data['flight_details'] = $flight_details;
            $data['mobile'] = $mobile;

            $job_id = $flight_details['job_id'];
            $data['url'] = 'https://park-pass.com/airport/airports?id='.$job_id .'&token='.$token;

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
        
              
                $mail->AddBCC($admin_mail,"Admin");
                //$mail->AddBCC($accounts_email,"Accounts");
        
                $mail->Subject = 'ParkPoint Valet Service - Bahrain International Airport';
                  
                $mail->CharSet = 'UTF-8';
                $mail->isHTML(true);
                // $mail->SMTPDebug =2;
        
                $mail->addAddress($email, 'ParkPoint Valet Service - Bahrain International Airport');
                $mail->Body = $this->load->view('sucess_mail',$data,true);				
          
                
                $mail_response ;
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
                return  true;				
              }
              
            catch(phpmailerException $e){
                return  false;
            }
        }
        
    }

    function php_info(){
        echo (phpinfo());
    }

    public function ticket_info($j_id = '',$tok = '',$response = false){

        $data['is_response'] = $response;      

        if($j_id != '' && $j_id != null && $tok != '' && $tok != null){
            $job_id = $j_id;
            $token  = $tok;
        }
        else{
            $url = $_SERVER['REQUEST_URI'];
            $parts = parse_url($url);
            $output = [];
            parse_str($parts['query'], $output); //converts + sign to spaces,can't use urlencode bcz that converts the other nonalphanumberic to hexcodes
        
            $job_id = $output['id'];
            $token  = str_replace(' ', '+',$output['token']);//str_replace to convert space to +
        }
    
        //print_r($job_id.'  -  '.$token);
        if(isset($job_id) && isset($token)){

            $is_parked = $this->Airport_model->check_car_parked($job_id);
            $is_created = $this->Airport_model->check_customer_create($job_id);
            $flight_details = $this->Airport_model->get_flight_details($job_id);

            if($is_parked->num_rows() > 0 ){//it's parked can continue further to display form
                if($is_created->num_rows() > 0){
                    
                    $seed = $this->Airport_model->get_updated_seed();
                    
                    if($seed->num_rows() > 0){
                        
                        $updated_seed_row = $seed->row_array();
                        $created_date_row = $is_created->row_array();

                        $new_token = $job_id . $created_date_row['timestamp']; 
                                                
                        $token_generated = base64_encode(hash_hmac('sha256', $new_token, $updated_seed_row['seed'],true));

                        if($token_generated === $token){
                            $all_images = [];

                            $image_query  = $this->Airport_model->get_car_images($job_id);
                            
                            $new_img_urls = json_decode($image_query['url']);

                            // foreach($new_img_urls as $image){
                                
                            //     $image_exploded = substr($image, 0, strpos($image, "?alt"));
                            //     $all_images = $image_exploded;
                            //     print_r($all_images);
                            // }

                            $data['job_id'] = $job_id;
                            $data['token'] = $token;
                            
                            $data['customer_details'] = $created_date_row;
                            $data['flight_details']   = $flight_details;
                            $data['images'] = $new_img_urls;
                            $qrcode = new QrCode($job_id);        
                            $data['ticket_qr'] = base64_encode($qrcode->writeString());
                            // $data['airlines'] = $airlines;
                            $this->load->view('customer_ticket',$data);
                        }
                        
                        else{
                            show_404();
                        }
                    }
                    
                    else{
                        show_404();
                    }
                }
            }
            else{
                show_404();
            }
        }
    }
}
