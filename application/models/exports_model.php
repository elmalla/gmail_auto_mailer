<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Email Extract
 *
 * @author ahmed.elmalla
 */

class Exports_model extends CI_Model {
 
    /**
    * Responsable for auto load the database
    * @return void
    */
    public function __construct()
    {
        //$this->load->database();
    }

  

  
   

        
    /**
    * Fetch files data from the upload folder
    * possibility to mix search, filter and order
    * @param int $manufacuture_id 
    * @param string $search_string 
    * @param strong $order
    * @param string $order_type 
    * @param int $limit_start
    * @param int $limit_end
    * @return array
    */
    public function get_files($path, $search_string=null, $order=null, $order_type='SORT_ASC', $limit_start, $limit_end)
    {
        
        
        /*To Do :
         * 1) ASC and DESC doesn't work 
         * 2) sorting selection doesn't work
         * 30 check the values stored in the the session
         */
        $s_files1 = array();
        //$files_info=array();
        
        $this->load->helper('file');

        $files_info=get_dir_file_info($path);


        if($search_string){
                $input = preg_quote($search_string, '~'); // don't forget to quote input string!
                //$data = array('orange', 'blue', 'green', 'red', 'pink', 'brown', 'black');

                $result = preg_grep('~' . $input . '~', $files_info);
        }


        if($order){
                //$this->db->order_by($order, $order_type);

            foreach ($files_info as $key => $row)
            {
                $s_files1[$key] = $row[$order];
            }
            if ($order_type=="SORT_ASC")
             array_multisort($s_files1, SORT_ASC, $files_info);
            else
             array_multisort($s_files1, SORT_DESC, $files_info);   
        }else{
            //$this->db->order_by('id', $order_type);
            foreach ($files_info as $key => $row)
            {
                $s_files1[$key] = $row['date'];
            }
            if ($order_type=="SORT_ASC")
             array_multisort($s_files1, SORT_ASC, $files_info);
            else
             array_multisort($s_files1, SORT_DESC, $files_info);  
        }


        //$this->db->limit($limit_start, $limit_end);
        //$this->db->limit('4', '4');


        //$query = $this->db->get();

        return array_slice($files_info,$limit_start, $limit_end); 	
    }
     
     /**
    * Count the number of rows
    * @param int $manufacture_id
    * @param int $search_string
    * @param int $order
    * @return int
    */
    function count_files($path, $search_string=null, $order=null)
    {
        
        $this->load->helper('file');

        $files_info=get_dir_file_info($path);
        
        return count($files_info);
    }
}
?>	
