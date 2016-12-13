<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function get_smileys()
{
    $CI = &get_instance();
    
    $CI->load->helper('smiley');
    $CI->load->library('table');
    
    $image_array= get_clickable_smileys(base_url().'smileys/','comment');
    $col_array= $CI->table->make_columns($image_array,8);
    return $CI->table->generate($col_array);
}

/**
 * Open text file and get data to an array
 * @param 
 * @return array
 */
function fread_as_array($file)
{
    $data=array();
    $fp=fopen($file, 'r');
    while (!feof($fp))
    {
        $line=fgets($fp);

        //process line however you like
        $line=trim($line);

        //add to array
        $data[]=$line;

    }
    fclose($fp);

    return $data;
} 

/**
 * Open text file and get data to an array
 * @param 
 * @return string
 */
function fread_as_string($file)
{
   $data='';
    $fp=fopen($file, 'r');
    while (!feof($fp))
    {
        $line=fgets($fp);

        //process line however you like
        $line=$line;

        //add to array
        $data .=$line;

    }
    fclose($fp);

    return $data;
}

//File to keep spaces between lines after reading a text file
function format_text_array_to_string($arr)
{
 $string='';
 $prev_string='';
     foreach($arr as $line)
         {
             if ($line !=''){
                  $string .= $line;
                  $prev_string=$line;
             }else{
                 if ($prev_string != '<br/><br/>')
                 {  
                     $string .= '<br/><br/>';
                     $prev_string = '<br/><br/>';
                 }else
                     $prev_string ='';
             }
         }
    return $string;
}

// make important array element as key for easier array search
function restruct_array_key($arr,$str_key,$str_value)
{
      $result = array();
      foreach($arr as $row)
      {
        $result[strtolower($row[$str_key])] = $row[$str_value]; 
      }
      return $result;
}

function extract_email_name($address) {

        if (!is_array($address)) {

            $address = trim($address);

            if (preg_match('/(.*)\<(.*)\>/', $address, $match)) {
                return trim($match['1']);
            } else {
                return '';
            }
        }
}


function get_email_owner_name($email,$validate=false)
{    
    $full_name='';
    $first_name ='';
    $last_name='';
    $domain='';
    $valid = true;
     
     if ($validate)
       $valid = filter_var($email, FILTER_VALIDATE_EMAIL);
    
     if (strpos($email, '@') !== false && !$valid=== false)
     {         
             
        list($full_name, $domain) = explode('@', $email);

        if (strpos($full_name, '.') !== false)
        {    
            list($first_name, $last_name)= explode(".",$full_name);
        }else if (strpos($full_name, '_') !== false)
       {
         list($first_name, $last_name)= explode("_",$full_name);
       }else if (strpos($full_name, '-') !== false){
            list($first_name, $last_name)= explode("-",$full_name);    
       }else
           $first_name = $full_name;
       
           return $first_name;    
     }else
         return '';
}

function shuffle_assoc($list) {
  if (!is_array($list)) return $list;

  $keys = array_keys($list);
  shuffle($keys);
  $random = array_rand($keys,1);
  //foreach ($keys as $key)
    return $list[$keys[$random]];

  //return $random;
} 