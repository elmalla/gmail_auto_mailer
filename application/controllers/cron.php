<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller {
 
    /**
    * Responsable for auto load the model
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
 
      // this controller can only be called from the command line
      if (!$this->input->is_cli_request()) show_error('Direct access is not allowed');
    
       $this->load->model('Emailv_model');
       $this->load->model('Emailntv_model');
       $this->load->model('Statistics_model');
       $this->load->model('Category_model');
       $this->load->model('Exports_model');
       $this->load->model('Company_model');
        
        
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
      if(!$this->input->is_cli_request())
      {
          echo "This script can only be accessed via the command line" . PHP_EOL;
          return;
      }
      
      
      $timestamp = strtotime("+1 days");
      $appointments = $this->Appointment_model->get_days_appointments($timestamp);
      if(!empty($appointments))
      {
          foreach($appointments as $appointment)
          {
              $this->email->set_newline("\r\n");
              $this->email->to($appointment->email);
              $this->email->from("youremail@example.com");
              $this->email->subject("Appointment Reminder");
              $this->email->message("You have an appointment tomorrow");
              $this->email->send();
              $this->Appointment_model->mark_reminded($appointment->id);
          }
      }
  
      

 }//index

 // spawn a process and do not wait for it to complete
    public function runci_nowait($controller, $method, $param)
    {
       $runit  = "php index.php {$controller} {$method} {$param}" ;
       pclose(popen("start \"{$controller} {$method}\" {$runit}", "r"));
       return;
    }

    // spawn a process and wait for the output.
    public function runci_wait($controller, $method, $param)
    {
       $runit  = "php index.php {$controller} {$method} {$param}";
       $output = exec("{$runit}");
       echo $output;
    } 
    //   
    //How to run them from the cli...
//To run the 'ci' 'nowait' routine then do:

//php index.php runtools runci_nowait <controller> <method> <param>

//where the parameters are the ci controller you want to run. Chnge to 'runci_wait' for the other one.

//'Hello World: 'wait for output' - (ci: tools message )

//codeigniter>php index.php runtools runci_wait tools message ryan3
//Hello ryan3!

//The waitMessage - 'do not wait for output' - (ci : tools waitMessage )

//codeigniter>php index.php runtools runci_nowait tools waitMessage ryan1

//codeigniter>php index.php runtools runci_nowait tools waitMessage ryan2

//These will start and run two separate 'ci' processes.
   

}