<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Admin_statistics extends CI_Controller {
 

  
    /**
    * Responsable for auto load the model
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
       //we will use this when create an upload files table 
       $this->load->model('Emailv_model');
       $this->load->model('Emailntv_model');
       $this->load->model('Statistics_model');
    
       $this->load->helper('common');
    
        
         $this->load->library(array('ion_auth'));
     $this->load->helper(array('language'));
                
      if(!$this->input->is_cli_request())
      {  
        if (!$this->ion_auth->logged_in())
        {
                // redirect them to the login page
                redirect('auth/login', 'refresh');
        }
      }
    }
 
    
     /**
    * Load the main view with all the current model model's data.
    * @return void
    */
    public function index()
    {
        /*
         * TODO:
         * must check agin bugs when owner_id, and other ids become zero.
         */
        
        $email_prefix='emailv_';
        $this->output->enable_profiler(TRUE);
        
        
        
         $data['counters']= array(
            'name_nt_extracted_nv_count'=>$name_nt_extracted_nv_count,
            'name_nt_extracted_v_count'=>$name_nt_extracted_v_count,
            'name_extracted_nv_count'=>$name_extracted_nv_count,
            'name_extracted_v_count'=>$name_extracted_v_count,
            'name_extracted_repeated_nv_count'=>$name_extracted_repeated_nv_count,
            'name_extracted_repeated_v_count'=>$name_extracted_repeated_v_count,
            'name_extracted_unique_nv_count'=>$name_extracted_unique_nv_count,
            'name_extracted_unique_v_count'=>$name_extracted_unique_v_count,
            'insert_count'=>$insert_count,
            'insert_loop'=>$insert_loop,
            'unique_first_v_names_count'=>$unique_first_v_names_count,
            'unique_first_nv_names_count'=>$unique_first_nv_names_count   
            );
           
           $data['unique_first_names'] = $unique_first_names;
           $data['unique_emails_wout_names'] =$unique_emails_wout_names;
           $data['unique_first_names_count'] = count($unique_first_names);
          
     
        $data['main_content'] = 'admin/Statistics/list';
        $this->load->view('includes/template', $data);  

    }//index

   
   
    
    public function collect_email_names()
    {
        set_time_limit (3000);
        $this->output->enable_profiler(TRUE);
        
        $verfied_emails_table=array();
        $ntverfied_emails_table=array();
        
        $vEmails_array_k_email =array();
        $ntvEmails_array_k_email = array();
        
        $name='';
        $unique_first_names=array();
        $unique_emails_wout_names=array();
        
        $insert_count =0;
        $update_count=0;
        $insert_loop =0;
        $name_nt_extracted_nv_count=0;
        $name_nt_extracted_v_count=0;
        $name_extracted_nv_count=0;
        $name_extracted_v_count=0;
        $name_extracted_repeated_nv_count=0;
        $name_extracted_repeated_v_count=0;
        $name_extracted_unique_nv_count=0;
        $name_extracted_unique_v_count=0;
        
        $verfied_emails_table= $this->Emailv_model->fields(array('email','email_id'))->get_all(array('email_id <='=>10000));
        $ntverfied_emails_table= $this->Emailntv_model->fields(array('email','email_id'))->get_all(array('email_id <='=>2000));
        $mailer_names_table= $this->Statistics_model->fields(array('name','count','id'))->get_all();    
        
        $vEmails_array_k_email = restruct_array_key($verfied_emails_table,'email','email_id');
        $ntvEmails_array_k_email = restruct_array_key($ntverfied_emails_table,'email','email_id');
        $mailer_names_k_name= restruct_array_key($mailer_names_table,'name','count'); 

        //Loop for the verfied Email Array
         foreach($vEmails_array_k_email as $key => $id)
          { 
           if  ( $name = get_email_owner_name($key)) 
           {    
               if (!array_key_exists($name, $unique_first_names)&& !array_key_exists($name,$mailer_names_k_name))
               {   
                   $unique_first_names[$name]=1;
                   $name_extracted_unique_v_count++;
               
               }else{
                   if(!array_key_exists($name,$mailer_names_k_name))
                   {
                       $count=$unique_first_names[$name]['count'];
                       $count++;
                       $unique_first_names[$name] = array('count'=>$count,'email'=>$key);
                       $name_extracted_repeated_v_count++;
                   }else{
                        $count = $mailer_names_k_name[$name];
                        $count++;
                        $mailer_names_k_name[$name] = $count;
                        
                         $stat_data_to_store =array(
                            'name' => $name,
                            'count' =>$count
                        );
                        if( $this->Statistics_model->update($stat_data_to_store,'name'))
                                $update_count++;
                   }
                   
               }
               
               $name_extracted_v_count++;
           }else{
               $unique_emails_wout_names[$key]=$key;
               $name_nt_extracted_v_count++;
           }   
               
          }
          
          $unique_first_v_names_count = count($unique_first_names);
          
          foreach($ntvEmails_array_k_email as $key=>$id)
          { 
           if  ( $name = get_email_owner_name($key,true)) 
           {    
               if (!array_key_exists($name, $unique_first_names)&& !array_key_exists($name,$mailer_names_k_name))
               {   
                   $unique_first_names[$name]=1;
                   $name_extracted_unique_nv_count++;
               
               }else{
                   if(!array_key_exists($name,$mailer_names_k_name))
                   {
                       $count=$unique_first_names[$name]['count'];
                       $count++;
                       $unique_first_names[$name] = array('count'=>$count,'email'=>$key);;
                       $name_extracted_repeated_nv_count++;
                   }else{
                        $count = $mailer_names_k_name[$name];
                        $count++;
                        $mailer_names_k_name[$name] = $count;
                        
                         $stat_data_to_store =array(
                            'name' => $name,
                            'count' =>$count
                        );
                        if( $this->Statistics_model->update($stat_data_to_store,'name'))
                                $update_count++;
                   }
                   
               }
               
               $name_extracted_nv_count++;
           }else{
              $unique_emails_wout_names[$key]=$key; 
              $name_nt_extracted_nv_count++;
           }
          }
          
          $unique_first_nv_names_count = count($unique_first_names);
          
          if (is_array($unique_first_names)&& !empty($unique_first_names))
          {
              foreach($unique_first_names as $key=>$value)
              {
                $stat_data_to_store =array(
                'name' => $key,
                'count' =>$value['count']
                );
                //Insert non verfied email data to DB
                if( $this->Statistics_model->insert($stat_data_to_store))
                  $insert_count++;
                
                $insert_loop++;
              }
          }
            
           $data['counters']= array(
            'name_nt_extracted_nv_count'=>$name_nt_extracted_nv_count,
            'name_nt_extracted_v_count'=>$name_nt_extracted_v_count,
            'name_extracted_nv_count'=>$name_extracted_nv_count,
            'name_extracted_v_count'=>$name_extracted_v_count,
            'name_extracted_repeated_nv_count'=>$name_extracted_repeated_nv_count,
            'name_extracted_repeated_v_count'=>$name_extracted_repeated_v_count,
            'name_extracted_unique_nv_count'=>$name_extracted_unique_nv_count,
            'name_extracted_unique_v_count'=>$name_extracted_unique_v_count,
            'insert_count'=>$insert_count,
            'insert_loop'=>$insert_loop,
            'unique_first_v_names_count'=>$unique_first_v_names_count,
            'unique_first_nv_names_count'=>$unique_first_nv_names_count   
            );
           
           $data['unique_first_names'] = $unique_first_names;
           $data['unique_emails_wout_names'] =$unique_emails_wout_names;
           $data['unique_first_names_count'] = count($unique_first_names);
          
           
           //$data['debug']=$this->email->print_debugger();
           
           $data['main_content'] = 'admin/Mailer/result';
           $this->load->view('includes/template', $data);
           
           //$this->email->clear();
    }
    
   
   
    
}