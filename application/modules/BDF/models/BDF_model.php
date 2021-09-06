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


}
?>