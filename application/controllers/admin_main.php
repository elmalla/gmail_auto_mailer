<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_main extends CI_Controller {
 
    /**
    * Responsable for auto load the model
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
          $this->load->model('Emailv_model');
       $this->load->model('Emailntv_model');
       $this->load->model('Owner_model');//,'owner_m');
       $this->load->model('Mailer_log_model');
       $this->load->model('Company_model');//,'company_m');
       $this->load->model('Category_model');
       $this->load->model('Cron_log_model');
       $this->load->model('Mailer_schedule_log_model');
       $this->load->model('Source_model');
       $this->load->model('Extraction_model');
       $this->load->model('Mailer_scheduled_model'); 
        
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
    public function index($type='view')
    {


        //initializate the panination helper 
        //Setup Pagination
        //$this->load->library('pagination');
        //$this->pagination->initialize($config);
        $data =array();

        $data['count_ntvemails']= $this->Emailntv_model->count_rows();
        //$data['emails'] =$this->Emailntv_model->fields($field_data)->get_all();//->order_by($order_type);
       
        $data['count_companies']= $this->Company_model->count_rows();
        //$data['count_categories']= $this->Category_model->count_rows();
        $data['count_sources']= $this->Source_model->count_rows();
        //$data['count_edetails']= $this->Category_model->count_rows();
        $data['count_vemails']= $this->Emailv_model->count_rows();
        $data['count_ntvemails']= $this->Emailntv_model->count_rows();
        $data['count_mailer_log']= $this->Mailer_log_model->count_rows();
        $data['count_cron_log']= $this->Cron_log_model->count_rows();
        $data['count_mailer_schedule_log']= $this->Mailer_schedule_log_model->count_rows();
        $data['count_extraction']= $this->Extraction_model->count_rows();
        $data['count_Mailer_scheduled'] = $this->Mailer_scheduled_model->where(array('review_status'=>'1'))->count_rows();
        
        $data['count_emails_no_company_id']= $this->Emailv_model->count_rows(array('company_id'=>0));
        
        $data['count_scheculed_sa']= $this->Mailer_scheduled_model->count_rows(array('email LIKE'=>'%.sa','sending_status'=>''));
        $data['count_sent_sa']= $this->Mailer_scheduled_model->count_rows(array('email LIKE'=>'%.sa','sending_status'=>'OK'));
        
        $data['count_scheculed_kw']= $this->Mailer_scheduled_model->count_rows(array('email LIKE'=>'%.kw','sending_status'=>''));
        $data['count_sent_kw']= $this->Mailer_scheduled_model->count_rows(array('email LIKE'=>'%.kw','sending_status'=>'OK'));
        
        $data['count_scheculed_qa']= $this->Mailer_scheduled_model->count_rows(array('email LIKE'=>'%.qa','sending_status'=>''));
        $data['count_sent_qa']= $this->Mailer_scheduled_model->count_rows(array('email LIKE'=>'%.qa','sending_status'=>'OK'));
        
        $data['count_scheculed_ae']= $this->Mailer_scheduled_model->count_rows(array('email LIKE'=>'%.ae','sending_status'=>''));
        $data['count_sent_ae']= $this->Mailer_scheduled_model->count_rows(array('email LIKE'=>'%.ae','sending_status'=>'OK'));
        //$data['count_emails_source2']= $this->Emailv_model->count_rows(array('source_id'=>2));
        //$data['count_emails_source4']= $this->Emailv_model->count_rows(array('source_id'=>4));
        //$data['count_emails_source5']= $this->Emailv_model->count_rows(array('source_id'=>5));
        //$data['count_emails_source6']= $this->Emailv_model->count_rows(array('source_id'=>6));
        //$data['count_emails_source7']= $this->Emailv_model->count_rows(array('source_id'=>7));
        
       $data['mailer_schedule_log'] =$this->Mailer_schedule_log_model->limit(20,$data['count_mailer_schedule_log']-20)->get_all();//->order_by($order_type);
       $data['cron_log'] =$this->Cron_log_model->limit(4,$data['count_cron_log']-4)->get_all();
       $data['Extraction'] =$this->Extraction_model->limit(4,$data['count_extraction']-4)->get_all();   
       
       
       //$date = '2011-04-8 08:29:49';
       //$today = strtotime(date("Y-m-d"));
       
       $currentDate = strtotime(date("Y-m-d H:i:s"));
       $oldDate = $currentDate-(60*60);
       $formatDate = date("Y-m-d H:i:s", $oldDate);//array('updated_at >= '=>date("Y-m-d"))
     
      //$data['email_sent_today'] = $this->Mailer_scheduled_model->fields('emails_sent')->where(array('updated_at >= '=>date("Y-m-d")))->get_all();
       
       $check_count = $this->Mailer_scheduled_model->where(array('updated_at >= '=>$formatDate))->count_rows();
       if ($check_count >0)
         $data['mailer_scheduled'] = $this->Mailer_scheduled_model->where(array('updated_at >= '=>$formatDate))->get_all();
       else{
           $check_count = $this->Mailer_scheduled_model->where(array('review_status'=>'0','sending_status'=>'OK'))->count_rows();
           $data['mailer_scheduled'] = $this->Mailer_scheduled_model->limit(20,$check_count-20)->where(array('review_status'=>'0','sending_status'=>'OK'))->get_all(); 
       }
          
       $table_fields=array();
       
       
       $res =$this->Mailer_scheduled_model->_get_table_fields();
       $table_fields= $this->Mailer_scheduled_model->table_fields;
       $data['mailer_scheduled_table_fields'] = $table_fields;
       $table_fields=array();
       
       $res =$this->Mailer_schedule_log_model->_get_table_fields();
       $table_fields= $this->Mailer_schedule_log_model->table_fields;
       $data['mailer_schedule_table_fields'] = $table_fields;
       $table_fields=array();  
         
       
       $res = $this->Cron_log_model->_get_table_fields();
       $table_fields =$this->Cron_log_model->table_fields;
       $data['cron_table_fields'] =$table_fields;
       $table_fields=array();
       
       
       $res = $this->Extraction_model->_get_table_fields();
       $table_fields = $this->Extraction_model->table_fields;
       $data['Extraction_table_fields'] =$table_fields;
       $table_fields=array();
               
    
    //load the view
    $data['main_content'] = 'admin/main/list';
    $file=dirname($_SERVER["SCRIPT_FILENAME"]).'/output/main/'.date('H_d_m_Y').'.html';
    
    $this->store_html($file,'includes/template',$data);
    
    if($type == 'view')
    {
        return $this->load->view('includes/template', $data);
    }
    elseif($type == 'refresh')
    {
        //not working properly
        return $this->load->view('admin/main/list', $data);
    }
          
 }//index

    public function add()
    {
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {

            //form validation
            $this->form_validation->set_rules('description', 'description', 'required');
            $this->form_validation->set_rules('stock', 'stock', 'required|numeric');
            $this->form_validation->set_rules('cost_price', 'cost_price', 'required|numeric');
            $this->form_validation->set_rules('sell_price', 'sell_price', 'required|numeric');
            $this->form_validation->set_rules('manufacture_id', 'manufacture_id', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');

            //if the form has passed through the validation
            if ($this->form_validation->run())
            {
                $data_to_store = array(
                    'description' => $this->input->post('description'),
                    'stock' => $this->input->post('stock'),
                    'cost_price' => $this->input->post('cost_price'),
                    'sell_price' => $this->input->post('sell_price'),          
                    'manufacture_id' => $this->input->post('manufacture_id')
                );
                //if the insert has returned true then we show the flash message
                if($this->admin_model->store_product($data_to_store)){
                    $data['flash_message'] = TRUE; 
                }else{
                    $data['flash_message'] = FALSE; 
                }

            }

        }
        //fetch manufactures data to populate the select field
        $data['manufactures'] = $this->manufacturers_model->get_manufacturers();
        //load the view
        $data['main_content'] = 'admin/main/add';
        $this->load->view('includes/template', $data);  
    }       

    /**
    * Update item by his id
    * @return void
    */
    public function update()
    {
        //product id 
        $id = $this->uri->segment(4);
  
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {
            //form validation
            $this->form_validation->set_rules('description', 'description', 'required');
            $this->form_validation->set_rules('stock', 'stock', 'required|numeric');
            $this->form_validation->set_rules('cost_price', 'cost_price', 'required|numeric');
            $this->form_validation->set_rules('sell_price', 'sell_price', 'required|numeric');
            $this->form_validation->set_rules('manufacture_id', 'manufacture_id', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');
            //if the form has passed through the validation
            if ($this->form_validation->run())
            {
    
                $data_to_store = array(
                    'description' => $this->input->post('description'),
                    'stock' => $this->input->post('stock'),
                    'cost_price' => $this->input->post('cost_price'),
                    'sell_price' => $this->input->post('sell_price'),          
                    'manufacture_id' => $this->input->post('manufacture_id')
                );
                //if the insert has returned true then we show the flash message
                if($this->admin_model->update_product($id, $data_to_store) == TRUE){
                    $this->session->set_flashdata('flash_message', 'updated');
                }else{
                    $this->session->set_flashdata('flash_message', 'not_updated');
                }
                redirect('admin/main/update/'.$id.'');

            }//validation run

        }

        //if we are updating, and the data did not pass trough the validation
        //the code below wel reload the current data

        //product data 
        $data['product'] = $this->admin_model->get_product_by_id($id);
        //fetch manufactures data to populate the select field
        $data['manufactures'] = $this->manufacturers_model->get_manufacturers();
        //load the view
        $data['main_content'] = 'admin/main/edit';
        $this->load->view('includes/template', $data);            

    }//update

    /**
    * Delete product by his id
    * @return void
    */
    public function delete()
    {
        //product id 
        $id = $this->uri->segment(4);
        $this->admin_model->delete_product($id);
        redirect('admin/main');
    }//edit
    
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

}