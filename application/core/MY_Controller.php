<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MY_Controller
 *
 * @author Ahmed elmalla
 */
class MY_Controller extends CI_Controller{
    //put your code here
    public function __construct()
    {
        parent::__construct();
        //$this->load->config('ion_auth', TRUE);
        $this->load->config('permitted_ips', TRUE);
        $ips_list = $this->config->item('ip_list', 'permitted_ips');
        $ips = array_values($ips_list);
        
        //$vistor_ip= $_SERVER['REMOTE_ADDR'];
        $vistor_ip='';
        $vistor_ip_sec= $this->input->ip_address();
        
        if (in_array($vistor_ip,$ips) || in_array($vistor_ip_sec,$ips))
            die();
        // check visitor IP against $config['ips'] array, redirect as needed
        //get info about an IP
        //http://geobytes.com/
        //http://ipinfodb.com/ip_location_api.php
        //http://www.ip2nation.com/
    }
}
