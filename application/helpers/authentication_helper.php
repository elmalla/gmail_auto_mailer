<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function _create_captcha()
{
     $CI = &get_instance();
     
     $CI->load->helper('captcha');
         $vals=array(
             'word'=>'',
             'img_path'=>'./captcha/',
             'img_url'=>base_url().'captcha/',
             'img_width'=>'200',
             'img_height'=>'30',
             'expiration'=>7200
         );
         $cap=  create_captcha($vals);
         $CI->session->set_userdata('captcha',$cap['word']);
         return $cap['image'];

}

//function captcha_form_validation()
//{
//    $CI = &get_instance();
//
//     $config= array(
//             array('field'=>'captcha','label'=>'captcha','rules'=>'trim|callback_captcha_input_check|required'),
//        );  
//        
//        $CI->load->library('form_validation');
//        $CI->form_validation->set_rules($config);
//        
//        if ($CI->form_validation->run() == FALSE)
//        {
//         return false;
//        }else {
//          return true;
//        }
//}


