<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Admin_mailer extends CI_Controller {
 
    
     private $upload_path;
     private $template_path;
     private $subject;
     private $attachment_name ;
     private $Letter_name ;
     private $template_html;
     private $from_email;
     private $my_name;
     private $reply_to ;
     private $MY_NAME ;
     private $HP;
  
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
       //$this->load->model('Owner_model');//,'owner_m');
       $this->load->model('Mailer_log_model');
       //$this->load->model('Company_model');//,'company_m');
       $this->load->model('Category_model');
       $this->load->model('Cron_log_model');
       $this->load->model('Mailer_schedule_log_model');
       $this->load->model('Mailer_scheduled_model');
       $this->load->model('Users_model');
       $this->load->helper('common_helper');
       
       $this->load->model('Mailing_servents_model');
       
       
       
       //Mailer_scheduled_model
      
       $this->load->helper('common');
       
     //Default data varables
     $this->upload_path= dirname($_SERVER["SCRIPT_FILENAME"]).'/uploads/attachments/Technical/';
     $this->template_path= dirname($_SERVER["SCRIPT_FILENAME"]).'/application/views/cover_letters/';

     $this->subject =  "Technical Engineer Resume - didn't like it ? drop m......";
     $this->attachment_name = 'resume_1.pdf';
     $this->Letter_name = 'Tcover_letter.txt';
     $this->template_html = 'cover_letter_4.html';
     $this->from_email ='any@linkedemails.com';
     $this->my_name='Your name';
     $this->reply_to ='any@gmail.com';
     $this->MY_NAME = "Your name".'<br/>';
     $this->HP = "H/P   : 0060000000";
         
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
     
       
        
        $data['main_content'] = 'admin/Mailer/longrun_c';
        $this->load->view('includes/template', $data);  

    }//index

    public function loaddata()
    {
     header("Content-Type: text/event-stream");
     header("Cache-Control: no-cache");
     header("Connection: keep-alive");

     for($i = 1; $i <= 10; $i++)
     {
        $this->send_message($i, 'on iteration ' . $i . ' of 10' , $i *10); 

        $data['main_content'] = 'admin/Mailer/longrun_c';
        $this->load->view('includes/template', $data);
        sleep(1);
     } 

        $this->send_message('CLOSE', 'Process complete');
         
    }
    
 //testing long run operations
    private function send_message($id, $message, $progress) 
    {
        $d = array('message' => $message , 'progress' => $progress);

        echo "id: $id" . PHP_EOL;
        echo "data: " . json_encode($d) . PHP_EOL;
        echo PHP_EOL;

        //PUSH THE data out by all FORCE POSSIBLE
        ob_flush();
        flush();
    }
 
 
    
    //cron mail ----Task schedular entrance
    public function cron_mail($user,$category,$units,$smtp,$tiggered_by='UI')
    {
        
        $simulation_test=false;
                    
        $is_cli =true;
        set_time_limit (1500);
        $this->output->enable_profiler(TRUE);
//check if the jobs isn't called from command line ->
        if(!$this->input->is_cli_request())
        {
              //echo "This script can only be accessed via the command line" . PHP_EOL;
              //return;
            $is_cli = false;      
            
        }
//varaible definations       
        $data= array();
        
        $this->benchmark->mark('start');
        
//Insert a log into cron log   
        //Cron_scheduler_log_model');
       //$this->load->model('Mailer_schedule_log
//Check if the last cron mail run ended by quota error        
        //$mailer_log_count = $this->Mailer_schedule_log_model->count_rows();
        
        $mailer_log_last_id = $this->Mailer_schedule_log_model->max_value('id');
        $mailer_log_last_row = $this->Mailer_schedule_log_model->fields(array('emails_sent','debug_info','created_at'))->where(array('id'=>$mailer_log_last_id))->get();
        
        $error_to_watch = 'Daily user sending quota exceeded';
        $debug_info = $mailer_log_last_row['debug_info'];
        $must_exit_flag = false;

        
        
        //create a random time delay to
        $delay_list =array(
                '1'=> 'no_delay' ,
                '2'=>'delay'
                
                
                //'3'=>'Machinery'
                //'4'=>'Latina'

            );

         $delay = shuffle_assoc($delay_list);
        
        
        
       //$oldDate = $currentDate-(60*60);
       //$formatDate = date("Y-m-d H:i:s", $oldDate);//array('updated_at >= '=>date("Y-m-d"))
     
      //$data['email_sent_today'] = $this->Mailer_scheduled_model->fields('emails_sent')->where(array('updated_at >= '=>date("Y-m-d")))->get_all();
       //$check_count = $this->Mailer_scheduled_model->where(array('updated_at >= '=>$formatDate))->count_rows();

        if (strpos( $debug_info,$error_to_watch) !== false) {
            
            $current_date = strtotime(date("Y-m-d H:i:s"));
            $error_date= strtotime($mailer_log_last_row['created_at']);
            $time_elapsed = $current_date - $error_date;
            
            if ($time_elapsed <(210*60) )
                $must_exit_flag ='true';
        } 
          
        //$delay = 'delay';
        if (!$must_exit_flag)
        { 
            if ($delay == 'delay')
            {
                 //$this->load->library('log');
                 $must_exit_flag ='true';
                 log_message('info','CRON_INFO '.' cron_mail delayed on '.date('Y-m-d H:i'));
            }else
                 log_message('info','CRON_INFO '.' cron_mail executed on '.date('Y-m-d H:i'));
               
        }
        
        if (!$must_exit_flag)
        {    
            $data_to_store =array(
                        'user_id'=>    $user,
                        'job_category'=>  $category,
                        'tiggered_by'=>$tiggered_by,
                        'unit_count'=>   $units,
                        'job_started'=>  date('Y-m-d H:i:s')  
                        );
            //Update the mailer_log with all the emails
            if (!$simulation_test)
            {  
              $cron_id = $this->Cron_log_model->insert($data_to_store);
            }
            //$jobs=array(
            //    'sa'=>array('email LIKE'=>'%.sa','sending_status !='=>'OK','review_status'=>'0','email NOT REGEXP'=> 'gov|stc|mobily'),
            //    'qa'=>array('email LIKE'=>'%.qa','sending_status !='=>'OK','review_status'=>'0','email NOT REGEXP'=> 'gov|stc|mobily'),
            //    'ae'=>array('email LIKE'=>'%.ae','sending_status !='=>'OK','review_status'=>'0','email NOT REGEXP'=> 'gov|stc|mobily')
            //);

            //$multiple_jobs=false;
            //if ($multiple_jobs==true)
            //    $data = $this->run_multiple_jobs($jobs,$units,$smtp,$cron_id,$is_cli);
            //else

            $region_list =array(
                '1'=>'Europe',
                '2'=>'GCC'
                //'3'=>'Machinery'
                //'4'=>'Latina'

            );

            $region = shuffle_assoc($region_list);

            if ($region == 'Europe')
            {
                $mailTo_list =array(
                '1'=>'%.se',
                '2'=>'%.ie',
                '3'=>'%.ch',
                '4'=>'%.be',
                '5'=>'%.ca',
                '6'=>'%.de'
                );

                $mailBcc_list =array(
                '1'=>'%.ca',
                '2'=>'%.uk',
                '3'=>'%.de',
                //'4'=>'%.nl',
                '4'=>'%.tr',
                //'6'=>'%.za',
                '5'=>'%.jp',
                '6'=>'%.ca',
                '7'=>'%.de',    
                );

                 $this->upload_path= dirname($_SERVER["SCRIPT_FILENAME"]).'/uploads/attachments/General_Europe/';

                 $this->subject =  "Resume Projects - didn't like it ? drop m......";
                 $this->attachment_name = 'A_K_Elmalla_PM_SAC_EU_R1_0.pdf';
                 $this->Letter_name = 'PM_cover_letter.txt';

            }else if ($region == 'GCC')
            {
                  $mailTo_list =array(
                    //'1'=>'%.kw',
                    //'1'=>'%.sa',
                    '1'=>'%.eg',
                     '2'=>'%.ae' 
                    //'3'=>'%.om'  
                      
                   );

                  $mailBcc_list =array(
                    //'1'=>'%.sa',
                    '1'=>'%.ae',
                    '2'=>'%.eg'
                   );

                  $this->upload_path= dirname($_SERVER["SCRIPT_FILENAME"]).'/uploads/attachments/Technical/';
                  $this->subject =  "Technical Engineer Resume - didn't like it ? drop m......";
                  $this->attachment_name = 'resume_1.pdf';
                  $this->Letter_name = 'Technical_cover_letter.txt';
                }

                $mailTo_clause = shuffle_assoc($mailTo_list);
                $mailBcc_clause = shuffle_assoc($mailBcc_list);

                $data_to_function =array(
                    'mailTo_clause'=>$mailTo_clause,
                    'mailBcc_clause'=>$mailBcc_clause,
                    'user'=>$user
                );

            $data = $this->run_single_fixed_job($data_to_function,$units,$smtp,$cron_id,$is_cli);


             $this->benchmark->mark('end');
           if (isset($data))
           {

              //Insert a log into cron log        
                $data_to_store =array(
                            'elapsed'=>    $this->benchmark->elapsed_time('start', 'end'),
                            'mailer_schedule_log_id'=>  $data['m_schedule_id']
                            );
         //Update the mailer_log with all the emails
                if (!$simulation_test)
                { 
                   $_cron_id = $this->Cron_log_model->update($data_to_store,$cron_id);
                }
                 $result_path = dirname($_SERVER["SCRIPT_FILENAME"]).'/output/cron_mail/';
                 $result_file = date('H_d_m_Y').'cron_'.$cron_id.'_mslog_'.$data['m_schedule_id'].'.html';

                 $file=dirname($_SERVER["SCRIPT_FILENAME"]).'/output/cron_mail/'.date('H_d_m_Y').'cron_'.$cron_id.'_mslog_'.$data['m_schedule_id'].'.html';
                 $data['main_content'] = 'admin/Mailer/Mail_jobs/result';
               $is_testing=false; 
               if (!$is_testing && ($data['counts']['emails_scheduled_next_run']==0 ||!$is_cli))
               {   
                   //show view
                    //$data['main_content'] = 'admin/Mailer/Mail_jobs/result';
                    $this->store_html($file,'includes/template',$data);
                    $this->load->view('includes/template', $data);
               }else if ($is_cli || $is_testing){
                     $this->store_html($file,'includes/template',$data);

                     //Send email summary to linkedemails

                 $summary_email_subject='HeartBeat_summary_'.date('H_d_m_Y').'cron_'.$cron_id.'_mslog_'.$data['m_schedule_id'];        
                 if (file_exists($result_path.$result_file))
                 {
                   $data_to_function =array(

                        'subject'=>    $summary_email_subject,
                        'attachment_name'=>   $result_file,
                        'from_email'=>  'any@gmail.com',  //$this->from_email,
                        'reply_to'=>    'any@gmail.com',
                        'emails'=>    array('0'=>'any@linkedemails.com'),
                        'upload_path'=>    $result_path,
                        'my_name'=>'ahmed elmalla',
                        'summary'=> true    

                        );

                      //run resume mailer
                     $data_from_mailer = $this->resume_Mailer($data_to_function);
                      if (count($data_from_mailer['emails_sent']) > 0)
                      {
                        $sucsess=true;
                      }
                   }
              }      
            }
      }
        set_time_limit(30);       
      
    }
    //cron_mail
       
    

    //run multiple mixed jobs
   // delected on 20 Nov
    //run a multiple job
    
    
    
     //run a single scheduled job
    public function run_single_fixed_job($data,$units=1000,$smtp='gmail',$cron_id=0,$is_cli=false)
    {
        //set_time_limit (3000);
        //$this->output->enable_profiler(TRUE);
        
        //TODO:
        //
        //
        //
        //
        //
        
        //Variable definations
        //$subject =  "Technical Engineer Resume - didn't like it ? I will......";
        $emails = array();
        $data_from_mailer = array ();
        $emails_sent =array();
        $emails_nt_sent=array();
        $emails_sent_earlier =array();
        $emails_debug_info = array();
        $email_sent_status_arr=array();
        $mail_log_ids =array();
        $errors = array();
        $debug = array();
        $email_to_mailer_count = 0;
        $email_v_count = 0;
        $email_nv_count = 0;
        $email_sent_count =0;
        $email_nt_sent_count =0;
        $email_sent_earlier_count =0;
        $debug_flag = false;
        $debug_exit_count = 4;
        $debug_count = 0;
        $HR = 2;
        $owner_id= $HR; 
        $simulation_test= false;
        $must_exit_flag = false;
        //get password for stmp
        $this->load->library('crypto');
       
        
        $mailing_details_main= $this->Mailing_servents_model->fields(array('username','password','email'))->where(array('users_id'=>$data['user']))->get();
        $mailing_details_secondry = $this->Users_model->fields(array('remember_code'))->where(array('id'=>$data['user']))->get();

        if (is_array($mailing_details_main))
        {
           $smtp_user_main=$mailing_details_main['username'];
           $smtp_pass=$mailing_details_main['password'];
           $smtp_user_sec = $mailing_details_main['email'];
           
           if (is_array($mailing_details_secondry))
           {   
             
             $code = $this->config->item('encryption_key');//$mailing_details_secondry['remember_code'];
             $smtp_pass = $this->crypto->decrypt($smtp_pass,$code);
           }
           
           
        //Check which mailerver to use
       //
       // 
       if ($smtp == 'gmail')
       {
           $config = Array(
                'useragent' => 'PHPMailer', 
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://smtp.gmail.com',
                'smtp_port' => '465',
                'smtp_user' => $smtp_user_main,
                'smtp_pass' => $smtp_pass,
                'smtp_crypto'=> 'tls',
                'smtp_auth' => true,
                'mailtype'  => 'html', 
                'charset'   => 'UTF-8',
                //'charset'=> null,                     // 'UTF-8', 'ISO-8859-15', ..., NULL (preferable) means config_item('charset'), i.e. the character set of the site.
                'validate'=> true,
                'priority'=> 3,                        // 1, 2, 3, 4, 5, on PHPMailer useragent NULL is a possible option, it means that X-priority header is not set at all, see https://github.com/PHPMailer/PHPMailer/issues/449
                'crlf'          => "\n",                     // "\r\n" or "\n" or "\r"                   // "\r\n" or "\n" or "\r"
                'bcc_batch_mode'=> false,
                'bcc_batch_size'=> 200,
                'encoding'=> '8bit',
                'newline'  => "\r\n"
          
            );
       }else{
            $config = Array(
                'useragent' => 'PHPMailer',
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://node01.facesharedeu1.com',
                'smtp_port' => '465',
                'smtp_user' => $smtp_user_sec,
                'smtp_pass' => $smtp_pass,
                'mailtype'  => 'html', 
                //'charset'   => 'UTF-8',
                'smtp_crypto'=> 'tls',
                'smtp_auto_tls' => true, 
                'smtp_auth' => true,
                'wrapchars'      => 76,
                'mailtype'=> 'html',                   // 'text' or 'html'
                'charset'=> null,                     // 'UTF-8', 'ISO-8859-15', ..., NULL (preferable) means config_item('charset'), i.e. the character set of the site.
                'validate'=> true,
                'priority'=> 3,                        // 1, 2, 3, 4, 5, on PHPMailer useragent NULL is a possible option, it means that X-priority header is not set at all, see https://github.com/PHPMailer/PHPMailer/issues/449
                'crlf'          => "\n",                     // "\r\n" or "\n" or "\r"
                'newline'=> "\n",                     // "\r\n" or "\n" or "\r"
                'bcc_batch_mode'=> false,
                'bcc_batch_size'=> 200,
                'encoding'=> '8bit'
            );    
       }
       $this->email->initialize($config);
       
//        if ($_SERVER["HTTP_HOST"] !='www.linkedemails.com')
//        {
            $mail_options= array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );

            $this->email->set_smtp_conn_options($mail_options);
//        }
       //$this->load->library('email', $config);
       //$this->load->library('email');//, $config);
       //$this->email->set_newline("\r\n");
        
       //Read the mailer log
        $mailer_log_table= $this->Mailer_log_model->fields(array('id','email_id'))->get_all();
        $mailer_log_array_k_email_id =$this->restruct_array_key($mailer_log_table,'email_id','id');
        
       //Check the validity of the avilable job
       $test_upgrade = true;
        if ($test_upgrade){
       
            // $countries=array('sa');
            $clauses = $data['mailTo_clause'].','.$data['mailBcc_clause'];
             $main_clause = array('email LIKE'=>$data['mailTo_clause'],'sending_status'=>'');
             $bcc_clause = array('email LIKE'=>$data['mailBcc_clause'],'sending_status'=>'');
             
             $mailer_scheduled_table_count =$this->Mailer_scheduled_model->where($main_clause)->count_rows();
             $bcc_mailer_scheduled_table_count =$this->Mailer_scheduled_model->where($bcc_clause)->count_rows();
             
            //A new table was created for sending out specfic reviewed emails
            $mailer_scheduled_table= $this->Mailer_scheduled_model->fields(array('email','email_id','country'))->where($main_clause)->limit(50,0)->get_all();
            //where(array('email LIKE'=>'%'.$search_string.'%','owner_id'=>$owner_id))
            $mailer_scheduled_k_email_id = $this->restruct_array_key($mailer_scheduled_table,'email_id','email');
            $mailer_scheduled_k_id_country = $this->restruct_array_key($mailer_scheduled_table,'email_id','country'); 
            
            
            $bcc_mailer_scheduled_table= $this->Mailer_scheduled_model->fields(array('email','email_id','country'))->where($bcc_clause)->limit(250,0)->get_all();
            //where(array('email LIKE'=>'%'.$search_string.'%','owner_id'=>$owner_id))
            $bcc_mailer_scheduled_k_email_id = $this->restruct_array_key($bcc_mailer_scheduled_table,'email_id','email');
            $bcc_mailer_scheduled_k_id_country = $this->restruct_array_key($bcc_mailer_scheduled_table,'email_id','country');
            
            
            
                     //where(array('email LIKE'=>'%'.$search_string.'%','owner_id'=>$owner_id))
           
      
           if ($mailer_log_table != false)
           {    
             $emails_merged =array_diff($mailer_scheduled_k_email_id,$mailer_log_array_k_email_id);
             $bcc_emails_merged =array_diff($bcc_mailer_scheduled_k_email_id,$mailer_log_array_k_email_id);
           }else{
             $emails_merged =$mailer_scheduled_k_email_id;  
           //$emails_merged = $mailer_scheduled_k_email_id;
           }
       }
          
            if ($mailer_log_table == false) 
                $mailer_log_table= array();

            
            
           $email_v_count = count($mailer_scheduled_table);
           $email_merged_count = count($emails_merged);
           $bcc_email_merged_count = count($bcc_emails_merged);
           $mailer_log_count = count($mailer_log_array_k_email_id);

        $bcc_counter =0;
        $bcc_count_per_email = 3; 
        $bcc_keys =array_keys($bcc_emails_merged);
        
        foreach ($emails_merged as $id=>$email)
        {
            
//Bcc emails loop
            
          $bcc_ids=array();
          $bcc_emails=array();  
          
          if ($bcc_email_merged_count >= ($bcc_counter + $bcc_count_per_email))
          {
                  
            //$test=$bcc_emails_merged[0];
              for ($i=$bcc_counter;$i< ($bcc_counter + $bcc_count_per_email);++$i)
              {
                 $bcc_id =$bcc_keys[$i];
                 $bcc_ids[]= $bcc_id; 
                 $bcc_email_sent_status= array_key_exists($bcc_id,$mailer_log_array_k_email_id);

                 if (!$bcc_email_sent_status)
                 {
                    $bcc_emails[] = $bcc_emails_merged[$bcc_id]; 
                 }

              }
              
              if (count( $bcc_emails)>1)
                  $bcc_email = implode (", ",  $bcc_emails);
          }else
             $bcc_email ='';
           
          
//check if email was sent before ->
           $email_sent_status= array_key_exists($id,$mailer_log_array_k_email_id);

           $email_sent_status_arr[$id]=$email_sent_status;
           
          
           
           if ($debug_flag || !array_key_exists($id,$mailer_log_array_k_email_id) )
           {
               //$bcc_email='';
               //if (!$bcc_email_sent_status)
               //       $bcc_email=$bcc_emails_merged[$bcc_id];
                   
                $data_to_function =array(
                'letter_name'=>    $this->Letter_name,
                'subject'=>    $this->subject,
                'attachment_name'=>   $this->attachment_name,
                'from_email'=>  $config['smtp_user'],  //$this->from_email,
                'reply_to'=>    $this->reply_to,
                'my_HP'=>    $this->HP,
                'emails'=>    array($id=>$email),
                 'bcc'=>$bcc_email,
                'my_name'=>    $this->my_name,
                'upload_path'=>    $this->upload_path,
                'template_html'=>$this->template_html,
                'template_path'=>$this->template_path,    
                'summary'=> false
                );
          
                
              //record all suspected variables into debug
               
               $debug[]=array('id'=>$id,'email'=>$email,'email sent counter'=>$email_sent_count);
               $debug[]=array('bcc counter'=>$bcc_counter,'bcc emails'=>$bcc_email);
               
                
              //run resume mailer
              $data_from_mailer = $this->resume_Mailer($data_to_function);
              $data_to_function =array();
              
               $debug[]=$data_from_mailer;  
           
//Check if any of the emails was sent out ->
//
//Check the debug info
            $error_to_watch = 'Daily user sending quota exceeded';
            
            if (strpos($data_from_mailer['debug'],$error_to_watch ) !== false) {
                $must_exit_flag ='true';
            } 
            //check data returned from resume amiler is an array before procedding 
            if (isset($data_from_mailer) && is_array($data_from_mailer))
            {   
              //Update the mailer log table
             if ((count($data_from_mailer['emails_sent']) > 0) && (!$must_exit_flag))
             {
//confirm that emails was sent before recording them into mailer_log table               
               
               $emails_sent_values = array_values($data_from_mailer['emails_sent']);  
               $emails_sent_values = $this->clean_emails_before($emails_sent_values); 
              
               if (in_array($email,$emails_sent_values))
               {
                   $emails_sent[$id] =$email;//array_shift($data_from_mailer['emails_sent']);  

                   $data_to_store =array(
                        'email_id'=>    $id,
                        'scheduler_id'=>    1,
                         'country'=> $mailer_scheduled_k_id_country[$id],
                        'letter'=>   $this->Letter_name,
                        'attachment'=>    $this->attachment_name
                    );
                    if (!$simulation_test)
                    {//Update the mailer_log with all the emails
                        $mailer_id = $this->Mailer_log_model->insert($data_to_store);
                        $mail_log_ids[]=$mailer_id;

                        $mail_scheduled_id = $this->Mailer_scheduled_model->where('email_id', $id)->update(array('sending_status'=> 'OK') );
                        $mail_scheduled_ids[] = $mail_scheduled_id;
                    }
               }
                
        //bcc loop
              if (count($bcc_ids)>0)
              {
                foreach($bcc_ids as $bcc_id)
                {
                   $bcc_e=$bcc_emails_merged[$bcc_id]; 
                   if (in_array($bcc_e,$emails_sent_values))
                   { 
                       $emails_sent[$bcc_id] =$bcc_emails_merged[$bcc_id];

                            $data_to_store =array(
                            'email_id'=>    $bcc_id,
                            'scheduler_id'=>    1,
                             'country'=> $bcc_mailer_scheduled_k_id_country[$bcc_id],
                            'letter'=>   $this->Letter_name,
                            'attachment'=>    $this->attachment_name
                            );

                        if (!$simulation_test)
                        {    
                            //Update the mailer_log with all the emails
                            $mailer_id = $this->Mailer_log_model->insert($data_to_store);
                            $mail_log_ids[]=$mailer_id;

                            $mail_scheduled_id = $this->Mailer_scheduled_model->where('email_id', $bcc_id)->update(array('sending_status'=> 'OK') );
                            $mail_scheduled_ids[] = $mail_scheduled_id;
                        }
                    }
                 }
              }else{
                  $errors[]="count(bcc_ids):".count($bcc_ids);
                  $errors[]="debug_flag :".$debug_flag;
                  $errors[]="debug_count :".$debug_count;
                  $errors[]="bcc_counter :".$bcc_counter;
                  $errors[]="bcc_count_per_email :".$bcc_count_per_email;
                  $errors[]="email_sent_count :".$email_sent_count;
                  $errors[]="units :".$units;
                  
              }
                
                //update sending status on mailer_scheduled
                //$mail_scheduled_id = $this->Mailer_scheduled_model->fields(array('id'))->where(array('email_id'=> $id))->get(1);
                //$this->Mailer_scheduled_model->fields(array('email','email_id','country'))->where(array('review_status'=>'1','review_status'=>'1'))->get_all();
             
                $email_sent_count +=  1;
                $bcc_counter +=$data_from_mailer['emails_count'] - 1;
             }else
             {
              //   
              $emails_nt_sent_values = array_values($data_from_mailer['emails_nt_sent']);
              $emails_nt_sent_values = $this->clean_emails_before($emails_nt_sent_values);
              
              if (in_array($email,$emails_nt_sent_values))
               {
                   $emails_nt_sent[$id] =$email;//array_shift($data_from_mailer['emails_sent']);  
               }
//record id and email for emails not sent                 
              if (count($bcc_ids)>0)
              {
                foreach($bcc_ids as $bcc_id)
                {
                   if (in_array($bcc_emails_merged[$bcc_id],$emails_nt_sent_values))
                   { 
                       $emails_nt_sent[$bcc_id] =$bcc_emails_merged[$bcc_id];
                   }
                }
              }
               //$emails_nt_sent[$id]= array_shift($data_from_mailer['emails_nt_sent']);
               $email_nt_sent_count += count($data_from_mailer['emails_nt_sent']);
             }
             
               $email_to_mailer_count += $data_from_mailer['emails_count'];
               $emails_debug_info[$id]= $data_from_mailer['debug'];
           }   
               if ($debug_flag)
                 $debug_count += 1;
               
               
                       
           }else{
// Emails sent before ->               
               $emails_nt_sent[$id]= $email;
               $emails_sent_earlier[$id] = $email;
               $email_sent_earlier_count += 1;
               
               //if ($bcc_counter > 0 && count($data_from_mailer['emails_nt_sent']) >0 )
                 //$bcc_counter -=count($data_from_mailer['emails_nt_sent']) - 1 ;
           }
                  
           
           
           if ($debug_flag &&  $debug_count >= $debug_exit_count)
              break;
           
           if(($email_sent_count >= $units) || ($must_exit_flag) )
               break;
           
           //too much failure
           if ($email_nt_sent_count > (2*$units + $units *$bcc_count_per_email ))
               break;
           
        }     
//update mailer_schedular_log
       $_count_sent =count($mail_log_ids);
         $data_to_store =array(
                    'cron_scheduler_id'=>    $cron_id,
                    'total_scheduled_emails'=>  ($email_merged_count+ $bcc_email_merged_count),
                    'emails_sent'=>   $_count_sent,
                    'emails_nt_sent'=>  $email_nt_sent_count,
                    'emails_sent_earlier'=>  $email_sent_earlier_count ,
                    'clauses'=>$clauses,
                    'debug_info'=>$data_from_mailer['debug'],
                    'emails_scheduled_next_run'=> ($bcc_mailer_scheduled_table_count +$bcc_mailer_scheduled_table_count - $_count_sent ) ,
                    'smtp_host'=> $smtp 
              
                    );
        if (!$simulation_test)
        {   
        //Update the mailer_log with all the emails
          $data['m_schedule_id'] = $this->Mailer_schedule_log_model->insert($data_to_store);        
        
        
//Update the mailer_log with the correct id for scheduler
           foreach($mail_log_ids as $log_id)
           {
            // $data_to_store =array(
              //          'scheduler_id'=>  $id
                //   );
               $mailer_id = $this->Mailer_log_model->update(array('scheduler_id'=>  $data['m_schedule_id']),$log_id);
                    //$mail_log_ids[]=$mailer_id;
           }
        }
//prepare data to send to viewer
        
        //set_time_limit(30);               
        $data['emails_sent']=$emails_sent;
        $data['emails_nt_sent']=$emails_nt_sent;
        $data['emails_sent_earlier']=$emails_sent_earlier;
        //$data['email_to_mailer_count']=$companies;
        $data['emails_debug_info']=$emails_debug_info;
        $data['error']=$errors;
        $data['debug']=$debug;
        //$data['m_schedule_id']=$m_schedule_id;
        $data['counts']= array(
            'email_to_mailer_count'=>$email_to_mailer_count,
            'email_v_count'=>$email_v_count,
            'email_nv_count'=>$email_nv_count,
            'email_merged_count'=>$email_merged_count,
            'email_sent_count'=>$_count_sent,
            'emails_sent_earlier'=>  $email_sent_earlier_count,
            'emails_scheduled_next_run'=> ($bcc_mailer_scheduled_table_count +$bcc_mailer_scheduled_table_count- $_count_sent)
            );
        
//detect if jobs is called from cli
        
        if($debug_flag && $cron_id == 0 && !$this->input->is_cli_request())
        {
          $data['main_content'] = 'admin/Mailer/Mail_jobs/result';
          $this->load->view('includes/template', $data);
        }else
            return $data;
      }   
    }
    //run a single fixed job
    
     //Resume mailer
    private function resume_Mailer($data_to_function)
    {
        //TODO:
        //the ALT message need to be complete with Dear statement + Siguture my name and phone
        //Need to investigate the usage of other HTML templates
        //Need to create at least another 2 templates.
        //
        //
        //variables Definations
        $emails_sent= array();
        $emails_nt_sent= array();
        $emails = array();
        $bybass_name_extraction=true;
        $data = array();
        $testing_flag=false;
        $cc_email ='';
        $bcc_email='';
        $simulation_test= false;
       //$email_owner='Sir';
        
        //extract data passed from the array
        $summary_email = $data_to_function['summary'];
        
        $subject = $data_to_function['subject'];
        $my_name = $data_to_function['my_name'];
        $attachment_name = $data_to_function['attachment_name'];
        $from_email = $data_to_function['from_email'];
        $emails=  $data_to_function['emails'];
        $reply_to = $data_to_function['reply_to'];
        $upload_path = $data_to_function['upload_path'];
        
        
        if (!$summary_email)
        {
            
           $letter_name = $data_to_function['letter_name'];
           $my_HP=  $data_to_function['my_HP'];
        
        
          $template_html= $data_to_function['template_html'];
          $template_path = $data_to_function['template_path'];
          $bcc_email = $data_to_function['bcc'];
        }    
             
        
        //check first if the file exists
        if ($summary_email || file_exists($upload_path.$letter_name)) 
        {
            if (!$summary_email)
            {    
                 $clfile_content = fread_as_array($upload_path.$letter_name);
                 $letter = format_text_array_to_string($clfile_content);

                 $alt_file=  basename($letter_name, ".txt").'_alt.txt';

                 if (file_exists($upload_path.$alt_file)) 
                 {
                   $this->email->set_alt_message($upload_path.$alt_file);
                 }
            }
             

             if (!$testing_flag && file_exists($upload_path.$attachment_name))
             {
                 if (!$summary_email)
                     $this->email->attach($upload_path.$attachment_name);
                 else{
                     $this->email->clear(true);
                     $this->email->attach($upload_path.$attachment_name);
                 }
                     
             }
                     
             $this->email->from($from_email, $my_name);
             $this->email->subject($subject);

             $this->email->reply_to($reply_to, $my_name);
             
             foreach($emails as $id=>$to_email)
             { 
                 //for testing only
                 if ($testing_flag)
                   $to_email = 'any@linkedemails.com';
                  else{
                  
                  }
                 
               if (!$summary_email)
               {     
                 $email_owner = $this->get_email_owner_name($to_email,$bybass_name_extraction);      

                  $data_e = array(
                    "MESSAGE"    => $letter,
                    "DATE"       => date('Y-m-d'),
                    "EMAIL_NAME" => ucfirst($email_owner),
                    "MY_NAME" => $my_name,
                    "HP" => $my_HP,
                    "EMAIL_ID"=>$id      
                    );
                }

                   if ($summary_email ||file_exists($template_path.$template_html)) 
                   {
                     if (!$summary_email)
                     {    
                       $body = $this->email->set_Mail_Body($data_e, $template_html,$template_path);
                       $this->email->message($body);
                     }else
                     {
                         $body= 'summary Report';
                         $this->email->message($body);
                     }
                         
                     
                      $this->email->to($to_email);
                      //$this->email->to($to_email);
                      
                      if($cc_email !='')
                          $this->email->cc($cc_email);
                      
                      if ($bcc_email)
                          $this->email->bcc($bcc_email);
                      
                      $this->email->subject($subject);
                      
                      if (!$simulation_test)
                        $data['result'] = $this->email->send();
                      else
                        $data['result'] ='';  
    
                      if(($data['result']&& $body !=''))
                      {
                           $emails_sent[]=$to_email;
                           if (strpos($bcc_email, ',') !== false)
                           {
                              $arr = explode(',',$bcc_email); 
                              foreach( $arr as $e)
                              {
                                  $emails_sent[]= $e;
                              }
                           }else
                               $emails_sent[]=$bcc_email;
                      }else{
                          $emails_nt_sent[]=$to_email;
                          if (strpos($bcc_email, ',') !== false)
                           {
                              $arr = explode(',',$bcc_email); 
                              foreach( $arr as $e)
                              {
                                  $emails_nt_sent[]= $e;
                              }
                           }else
                               $emails_nt_sent[]=$bcc_email;
                      }    
                          
                   } 
              }
          
               $data['emails_sent'] = $emails_sent;
               $data['emails_nt_sent'] = $emails_nt_sent;
               $data['emails_count'] = count($emails_sent);

               if (!$simulation_test) 
                 $data['debug']=$this->email->print_debugger();
               else
                 $data['debug']='';  
               //else
                //$data['debug']='SMTP Error: data not accepted.SMTP server error: DATA command failed Detail: Daily user sending quota exceeded. p144sm6451619wme.23 - gsmtp SMTP code: 550 Additional SMTP info: 5.4.5';   
            }     
           //$data['main_content'] = 'admin/Mailer/result';
           //$this->load->view('includes/template', $data);
           $this->email->clear(); 
           return $data;
           
    }
    //auto resume mailer
    
    private function get_email_owner_name($to_email,$bybass)
    {
         $email_owner='Sir';
         if(!$bybass)
         {
             //Should Add smart email extraction or detection
             $name=extract_email_name($to_email);
             list($email_owner,$domain) = explode('@',$to_email);

              if (strpos($email_owner,'.'))
                list($email_owner,$last_name) = explode('.',$email_owner);
         }
         
         return $email_owner;
    }
    
    private function restruct_array_key($arr,$str_key,$str_value)
    {
      $result = array();
      foreach($arr as $row)
      {
        $result[strtolower($row[$str_key])] = $row[$str_value]; 
      }
      return $result;
    }
    
    
   
    //LONG RUNNING TASK

 
   
    //Auto Mailing Code
    
    //Scheduler
    public function run_mailing_scheduler()
    {
        set_time_limit (300);
        $this->output->enable_profiler(TRUE);
        
        //check avilable jobs at job schedular table
        $this->check_mailing_jobs();
        
        
        
        //check smart matcher table for chosing the correct letter & attachement
        $this->check_smart_matcher();
        
        //Loop through scheduled Jobs
        for($i = 0; $i < count($jobs_count); ++$i) 
        {
          //run a single scheduled job
          $this->run_single_job();  
        }
        
        
       //store data 
       //$data['result']=$;
       //$data['debug']=$;

       
       //show view
       $data['main_content'] = 'admin/Mailer/scheduler';
       $this->load->view('includes/template', $data);
    }
    
    //check avilable jobs at job schedular table
    private function check_mailing_jobs()
    {
        //SELECT * FROM `emails_master` WHERE `email` LIKE '%@%.sa'
        //SELECT * FROM `emails_master` WHERE `email` LIKE 'hr%@%.ae'
        //SELECT * FROM `emails_master` WHERE `email` LIKE 'hr@%'
        //SELECT * FROM `emails_master` WHERE `email` LIKE 'rec%@%'
        //SELECT * FROM `emails_master` WHERE `email` LIKE 'job%@%'
        //SELECT * FROM `emails_master` WHERE `email` LIKE 'ce%@%'
        
        
    }
    //check avilable jobs at job schedular table
    
     //check smart matcher table for chosing the correct letter & attachement
    private function check_smart_matcher()
    {
        
    }
    //check smart matcher table for chosing the correct letter & attachement
    
    
    public function sendTestEmail()
    {

          //$this->load->library('email');
          $this->email->from('any@linkedemails.com', 'Ahmed Elmalla');
          $this->email->to('any@i-awcs.com');
          //$this->mail->SMTPAuth = true;
          $this->email->subject('This is my subject');
          $this->email->message('This is my message');
          
          
          $this->email->reply_to('any@any.fi', 'Ahmed');
          
           
            $this->email->send();
           $data['result']=$this->email->print_debugger();
           
           $data['main_content'] = 'admin/Mailer/list';
           $this->load->view('includes/template', $data);
    }
    
    //sendEmail_list_with_attachment
    public function sendEmail_list_with_attachment()
    {
        set_time_limit (300);
        $this->output->enable_profiler(TRUE);
        
        $emails_sent= array();
        $emails_nt_sent= array();
        
         //$to_email ='projects@i-awcs.com';
        $emails = array('any@i-awcs.com','any@gmail.com','any@linkedemails.com');
       
        $subject = 'CV - Professional construction Manager';
                  
         $clfile_content = fread_as_array($this->upload_path.$this->Letter_name);
         $message = format_text_array_to_string($clfile_content);
            
          
         $this->email->from($this->from_email, $this->my_name);
         $this->email->subject($subject);
          
          //$this->email->set_alt('This is my message');

         $this->email->reply_to($this->reply_to, $this->my_name);
          //$this->email->attach($upload_path.$attachment_name);
 
         foreach($emails as $key=>$to_email)
          { 
             
             $name=extract_email_name($to_email);
              list($email_owner,$domain) = explode('@',$to_email);
              
              if (strpos($email_name,'.'))
                list($email_name,$last_name) = explode('.',$email_name);      
              
              $data_e = array(
                "MESSAGE"    => $message,
                "DATE"       => date('Y-m-d'),
                "EMAIL_NAME" => ucfirst($email_name),
                "MY_NAME" => $this->MY_NAME,
                "HP" => $this->HP
                );

              $body = $this->email->set_Mail_Body($data_e, $this->template_html,$this->template_path);
              $this->email->message($body);
              $this->email->to($to_email);
              $this->email->subject($subject);
              $data['result'] = $this->email->send();
              
              if($data['result']&& $body !='')
                  $emails_sent[$key]=$to_email;
              else    
                  $emails_nt_sent[$key]=$to_email;
          }
          
           $data['emails_sent'] = $emails_sent;
           $data['emails_nt_sent'] = $emails_nt_sent;
           $data['emails_count'] = count($emails);
           
           $data['debug']=$this->email->print_debugger();
           
           $data['main_content'] = 'admin/Mailer/result';
           $this->load->view('includes/template', $data);
           
           //$this->email->clear();
    }
    //sendEmail_list_with_attachment
    
   
    
    
    public function sendTestEmail_with_attachment()
    {
         $this->output->enable_profiler(TRUE);

         $to_email ='any@i-awcs.com';
         $subject = 'CV - Professional construction Manager';
                  
         $clfile_content = fread_as_array($upload_path.$Letter_name);
         $message = format_text_array_to_string($clfile_content);
            
          
          $this->email->from($this->from_email, $this->my_name);
          $this->email->to($this->to_email);
          $this->email->subject($this->subject);
          
          list($email_name,$domain)=explode('@',$to_email);
         
          $data_e = array(
            "MESSAGE"    => $message,
            "DATE"       => date('Y-m-d'),
            "EMAIL_NAME" => ucfirst($email_name),
            "MY_NAME" => $MY_NAME,
            "HP" => $HP
            );

          $body = $this->email->set_Mail_Body($data_e, $template_html,$template_path);
          $this->email->message($body);
          //$this->email->set_alt('This is my message');

          $this->email->reply_to($this->reply_to, $this->my_name);
          //$this->email->attach($upload_path.$attachment_name);
 
           $data['result']=$this->email->send();
           $data['debug']=$this->email->print_debugger();
           
           $data['main_content'] = 'admin/Mailer/list';
           $this->load->view('includes/template', $data);
    }
    
    
    
    public function send_mail()
    { 
 // 
        $this->mail->isSMTP();
        $this->mail->Host = 'ssl://domain email *****:465';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'any@linkedemails.com';
        $this->mail->Password = '';
         
         //old code
          $this->mail->setFrom('any@domain.fi', 'Ahmed');
          $this->mail->addReplyTo('any@domain.fi', 'Ahmed');

        $this->mail->Subject = $arg['subject'];//"PHPMailer Simple database mailing list test";

        //Same body for all messages, so set this before the sending loop
        //If you generate a different body for each recipient (e.g. you're using a templating system),
        //set it inside the loop
        $this->mail->msgHTML($this->body);
        
        //msgHTML also sets AltBody, but if you want a custom one, set it afterwards
        $this->mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';

        //send the message, check for errors
        if (!$this->mail->send()) {
            echo "Mailer Error: " . $this->mail->ErrorInfo;
        } else {
            echo "Message sent!";
        }
        
        foreach ($this->mailing_list as $row) { //This iterator syntax only works in PHP 5.4+
            
            $this->mail->addAddress($row['email'], $row['full_name']);
            $this->mail->addAttachment($arg['attachment']);

            if (!$this->mail->send()) {
                save_statistics(array($row['email_id'],$arg['subject'],$arg['attachment'],$this->mail->ErrorInfo)); 
                //break; //Abandon sending
            } else {
                save_statistics(array($row['email_id'],$arg['subject'],$arg['attachment'],'sucess')); 
                //Mark it as sent in the DB
                
                
            }
            // Clear all addresses and attachments for next loop
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
        }

         
         
      } 
    

    
    private function arr_subtraction($arr1, $arr2) 
    {
     $ret = array();
     foreach ($arr1 as $key => $value) {
       $ret[$key] = $arr2[$key] - $arr1[$key];
       }
       return $ret;
    }
    
    private function store_html($file,$template,$data)
    {
        //$file = '/var/www/whatever/upload_dir/file.html';
        $string = $this->load->view($template, $data, true);
        // Write the contents back to the file
        file_put_contents($file, $string );
        
         if (file_exists($file)) 
         {
           return true;
         }else
             return false;
    }
    
     private function clean_emails_before($data)
    {
      //$sucess = ;
       //$result = array();
        
      if (is_array($data))
      {
        for ($i=0;$i<count($data);++$i)
        {
           $data[$i] = preg_replace('/\s+/', '', $data[$i]);
           $data[$i] = trim($data[$i], '.');
           //$data[$i] = trim($data[$i]);
           $data[$i] = str_replace(";"," ",$data[$i]);
           $data[$i] = str_replace("\r\n","",$data[$i]);
        } 
        $result = $data;
      }else {
          if ($data !=''){
            $result = preg_replace('/\s+/', '', $data);
            //$result = trim($result);
            $result  = str_replace(";"," ",$result );
            $result = trim($result, '.');
            $result  = str_replace("\r\n","",$result );
          }else 
              return '';
      }
      
      return $result;
    }
    
}