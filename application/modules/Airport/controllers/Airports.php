<?php
defined('BASEPATH') OR exit('No direct script access allowed');


require "vendor/autoload.php";
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
        $this->load->library('session');
    }
    
    public function customer_form($job_id = '',$token = ''){

        
        if(isset($job_id) && isset($token)){

            $is_parked = $this->Airport_model->check_car_parked($job_id);
            $is_created = $this->Airport_model->check_customer_create($job_id);
            

            if($is_parked->num_rows() > 0 ){//it's parked can continue further to display form
                if($is_created->num_rows() > 0){
                    
                    $seed = $this->Airport_model->get_updated_seed();
                    
                    if($seed->num_rows() > 0){
                        
                        $updated_seed_row = $seed->row_array();
                        $created_date_row = $is_created->row_array();

                        $new_token = $job_id . $created_date_row['createdAt'] . $updated_seed_row['seed'];
                        $token_generated = base64_encode(hash_hmac('sha256',$new_token,true));
                        
                        if($token_generated === $token){
                            $this->load->view('customer_form');
                        }
                        
                        else{
                            //print_r($token_generated .' - '. $token);
                        }
                    }
                    
                    else{
                        print_r($seed);
                    }
                }
            }
            else{
                //print_r('not parked');
            }
        }
        $qrcode = new QrCode("hello");
        //QRcode::png('code data text', 'filename.png'); 
        
        $data['ticket_qr'] = base64_encode($qrcode->writeString());
        $this->load->view('customer_form',$data);
    }

    public function submit_customer_form(){
        print_r('form submitted');       
        $this->load->view('welcome');
    }
}
