<?php

class BDF extends CI_Controller{

    public function __construct()
    {
        parent ::__construct();
        $this->load->model('BDF_model');
    }

    public function sign_in(){
        print_r('function sign_in');
        // $this->load->
    }

    public function index()
    {
        print_r('index function here');
    }
}
?>