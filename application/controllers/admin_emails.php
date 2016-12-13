<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

define('EMAIL_PATTERN', '/([\s]*)([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*([ ]+|)@([ ]+|)([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,}))([\s]*)/i');
define('URL_PATTERN', '%^((https?://)|(www\.))([a-z0-9-].?)+(:[0-9]+)?(/.*)?$%i');

class Admin_emails extends MY_Controller {
 
  
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
       $this->load->model('Owner_model');//,'owner_m');
       $this->load->model('Source_model');
       $this->load->model('Company_model');//,'company_m');
       $this->load->model('Category_model');
       $this->load->model('Extraction_model');
       //$Emails = $this->Emailv_model->as_array()->set_cache('all_emails',3600)->get_all();
        
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
        
        
        //$u_path=dirname($_SERVER["SCRIPT_FILENAME"]).'/uploads/'; 
        //all the posts sent by the view
        //$value = ($condition) ? 'Truthy Value' : 'Falsey Value';
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        { 
            $owner_id = ($this->input->post('owner_id') !=null) ? $this->input->post('owner_id'): 0 ;        
            $company_id = ($this->input->post('company_id') !=null) ? $this->input->post('company_id'): 0 ;
            $source_id = ($this->input->post('source_id') !=null) ? $this->input->post('source_id'): 0 ;

            $search_string = $this->input->post('search_string');        
            $order = $this->input->post('order'); 
            $order_type = $this->input->post('order_type'); 
            
            }else {
                $company_id =false;
                $owner_id =false;
                $source_id = false;
                $search_string =false;
                $order =false;
                $order_type =false;
                $posted=false;
            }

        //pagination settings
        $config['per_page'] = 25;
        $config['base_url'] = base_url().'index.php/admin/Emails';
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        //limit end
        $page = $this->uri->segment(5);

        //math to get the initial record to be select in the database
        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0){
            $limit_end = 0;
        } 

        //if order type was changed
        if($order_type){
            $filter_session_data[$email_prefix.'order_type'] = $order_type;
        }
        else{
            //we have something stored in the session? 
            if($this->session->userdata($email_prefix.'order_type')){
                $order_type = $this->session->userdata($email_prefix.'order_type');    
            }else{
                //if we have nothing inside session, so it's the default "Asc"
                $order_type = 'Asc';    
            }
        }
 
        //make the data type var avaible to our view
        $data['order_type_selected'] = $order_type;        


        //we must avoid a page reload with the previous session data
        //if any filter post was sent, then it's the first time we load the content
        //in this case we clean the session filter data
        //if any filter post was sent but we are in some page, we must load the session data

        //filtered && || paginated
        if( $posted!==false && $company_id != false && $owner_id != false && $search_string !== false && $order !== false || $this->uri->segment(4) == true){ 
           
            /*
            The comments here are the same for line 79 until 99

            if post is not null, we store it in session data array
            if is null, we use the session data already stored
            we save order into the the var to load the view with the param already selected       
            */

           
            if($company_id !== 0){
                $filter_session_data[$email_prefix.'company_selected'] = $company_id;
            }else{
                $company_id = $this->session->userdata($email_prefix.'company_selected');
            }
            $data['company_selected'] = $company_id; 
            
           if($owner_id !== 0){
                $filter_session_data[$email_prefix.'owner_selected'] = $owner_id;
            }else{
                $owner_id = ($this->session->userdata($email_prefix.'owner_selected')!= null) ? $this->session->userdata($email_prefix.'owner_selected') : 1;
            }
            $data['owner_selected'] = $owner_id; 

             if($source_id !== null){
                $filter_session_data[$email_prefix.'source_selected'] = $source_id;
            }else{
                $source_id = $this->session->userdata($email_prefix.'source_selected');
            }
            $data['source_selected'] = $source_id;
            
           if($search_string){
                $filter_session_data[$email_prefix.'search_string_selected'] = $search_string;
            }else{
                $search_string = $this->session->userdata($email_prefix.'search_string_selected');
            }
            $data['search_string_selected'] = $search_string;

            if($order){
                $filter_session_data[$email_prefix.'order'] = $order;
            }
            else{
                $order = $this->session->userdata($email_prefix.'order');
            }
            $data['order'] = $order;

           
            //save session data into the session
            if (isset($filter_session_data))
            {
             $this->session->set_userdata($filter_session_data);
            }
            

            //fetch manufacturers data into arrays
            $data['Email_Owner'] = $this->Owner_model->as_dropdown('owner')->get_all();
            $data['Email_Source'] = array_unique($this->Source_model->as_dropdown('source')->get_all());
            
            $data['Companies'] = $this->Company_model->as_dropdown('company_name')->get_all();
            
            //TO DO: nneed to activate search and order
             
                    //$this->_model->count_files($u_path, $search_string, $order);
            

            //fetch sql data into arrays
            if($search_string){
                $data['count_emails']= $this->Emailv_model->where(array('email LIKE'=>'%'.$search_string.'%','owner_id'=>$owner_id))->count_rows();
                if($order){
                    $data['Emails'] = $this->Emailv_model->where(array('email LIKE'=>'%'.$search_string.'%','owner_id'=>$owner_id))->order_by($order,$order_type)->paginate($config['per_page'],$data['count_emails']);
                    
                         //get_files($u_path, $search_string, $order, $order_type,$limit_end ,$config['per_page']);        
                }else{
                    $data['Emails'] = $this->Emailv_model->where(array('email LIKE'=>'%'.$search_string.'%','owner_id'=>$owner_id))->paginate($config['per_page'],$data['count_emails']);
                              //get_files($u_path, $search_string, '', $order_type, $limit_end,$config['per_page']);           
                }
                
            }else{
                $data['count_emails']= $this->Emailv_model->count_rows(array('owner_id'=>(int)$owner_id,'company_id'=>(int)$company_id));
               if($order){
                    $data['Emails'] = $this->Emailv_model->where(array('owner_id'=>(int)$owner_id,'company_id'=>(int)$company_id))->order_by($order,$order_type)->paginate($config['per_page'],$data['count_emails']);//->order_by($order_type));
                              //get_files($u_path, '', $order, $order_type, $limit_end,$config['per_page']); 
                    //get_products($manufacture_id, $search_string, $order, $order_type, $config['per_page'],$limit_end); 
                }else{
                    $data['Emails'] = $this->Emailv_model->where(array('owner_id'=>(int)$owner_id,'company_id'=>(int)$company_id))->paginate($config['per_page'],$data['count_emails']);
                            //get_files($u_path, '', '', $order_type, $limit_end,$config['per_page']);           
                }
                
            }
            
            $config['total_rows'] = $data['count_emails'];

        }else{

            //clean filter data inside section
            $filter_session_data[$email_prefix.'owner_selected'] = null;
            $filter_session_data[$email_prefix.'source_selected'] = null;
            $filter_session_data[$email_prefix.'company_selected'] = null;
            $filter_session_data[$email_prefix.'search_string_selected'] = null;
            $filter_session_data[$email_prefix.'order'] = null;
            $filter_session_data[$email_prefix.'order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['owner_selected'] = 0;
            $data['source_selected'] = 0;
            $data['company_selected'] = 0;
            $data['order'] = 'id';

            //fetch sql data into arrays
            //$data['manufactures'] = $this->manufacturers_model->get_manufacturers();
            
            $data['Email_Owner'] = $this->Owner_model->as_dropdown('owner')->get_all();
            $data['Email_Source'] = array_unique($this->Source_model->as_dropdown('source')->get_all());
            $data['Companies'] = $this->Company_model->as_dropdown('company_name')->get_all();
            
            $data['count_emails']= $this->Emailv_model->count_rows();
            $data['Emails'] =$this->Emailv_model->paginate($config['per_page'],$data['count_emails']);//->order_by($order_type); 
                    //$this->eextract_model->get_files('', '', '', $order_type ,$limit_end,$config['per_page']);        
            $config['total_rows'] = $data['count_emails'];

        }//!isset($manufacture_id) && !isset($search_string) && !isset($order)

        //initializate the panination helper 
        $this->pagination->initialize($config);   

        //load the view
        $data['all_pages'] = $this->Emailv_model->all_pages; // will output links to all pages like this model: "< 1 2 3 4 5 >". It will put a link if the page number is not the "current page"
        $data['previous_page'] =         $this->Emailv_model->previous_page; // will output link to the previous page like this model: "<". It will only put a link if there is a "previous page"
        $data['next_page'] =         $this->Emailv_model->next_page;
        $data['page']=$page;
        $data['page_count'] =$config['per_page'];
        
        
        $data['unVerfied_Emails']= false;
        $data['main_content'] = 'admin/Emails/list';
        $this->load->view('includes/template', $data);  

    }//index

    public function get_unverfied_emails()
    {
         /*
         * TODO:
         * must check agin bugs when owner_id, and other ids become zero.
         */
        
        $email_prefix='emailntv_';
        $this->output->enable_profiler(TRUE);
        
        
        //$u_path=dirname($_SERVER["SCRIPT_FILENAME"]).'/uploads/'; 
        //all the posts sent by the view
        //$value = ($condition) ? 'Truthy Value' : 'Falsey Value';
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        { 
            $owner_id = ($this->input->post('owner_id') !=null) ? $this->input->post('owner_id'): 0 ;        
            $company_id = ($this->input->post('company_id') !=null) ? $this->input->post('company_id'): 0 ;
            $source_id = ($this->input->post('source_id') !=null) ? $this->input->post('source_id'): 0 ;

            $search_string = $this->input->post('search_string');        
            $order = $this->input->post('order'); 
            $order_type = $this->input->post('order_type'); 
            
            }else {
                $company_id =false;
                $owner_id =false;
                $source_id = false;
                $search_string =false;
                $order =false;
                $order_type =false;
                $posted=false;
            }

        //pagination settings
        $config['per_page'] = 25;
        $config['base_url'] = base_url().'index.php/admin/Emails';
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        //limit end
        $page = $this->uri->segment(5);

        //math to get the initial record to be select in the database
        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0){
            $limit_end = 0;
        } 

        //if order type was changed
        if($order_type){
            $filter_session_data[$email_prefix.'order_type'] = $order_type;
        }
        else{
            //we have something stored in the session? 
            if($this->session->userdata($email_prefix.'order_type')){
                $order_type = $this->session->userdata($email_prefix.'order_type');    
            }else{
                //if we have nothing inside session, so it's the default "Asc"
                $order_type = 'Asc';    
            }
        }
 
        //make the data type var avaible to our view
        $data['order_type_selected'] = $order_type;        


        //we must avoid a page reload with the previous session data
        //if any filter post was sent, then it's the first time we load the content
        //in this case we clean the session filter data
        //if any filter post was sent but we are in some page, we must load the session data

        //filtered && || paginated
        if( $posted!==false && $company_id != false && $owner_id != false && $search_string !== false && $order !== false || $this->uri->segment(4) == true){ 
           
            /*
            The comments here are the same for line 79 until 99

            if post is not null, we store it in session data array
            if is null, we use the session data already stored
            we save order into the the var to load the view with the param already selected       
            */

           
            if($company_id !== 0){
                $filter_session_data[$email_prefix.'company_selected'] = $company_id;
            }else{
                $company_id = $this->session->userdata($email_prefix.'company_selected');
            }
            $data['company_selected'] = $company_id; 
            
           if($owner_id !== 0){
                $filter_session_data[$email_prefix.'owner_selected'] = $owner_id;
            }else{
                $owner_id = ($this->session->userdata($email_prefix.'owner_selected')!= null) ? $this->session->userdata($email_prefix.'owner_selected') : 1;
            }
            $data['owner_selected'] = $owner_id; 

             if($source_id !== null){
                $filter_session_data[$email_prefix.'source_selected'] = $source_id;
            }else{
                $source_id = $this->session->userdata($email_prefix.'source_selected');
            }
            $data['source_selected'] = $source_id;
            
           if($search_string){
                $filter_session_data[$email_prefix.'search_string_selected'] = $search_string;
            }else{
                $search_string = $this->session->userdata($email_prefix.'search_string_selected');
            }
            $data['search_string_selected'] = $search_string;

            if($order){
                $filter_session_data[$email_prefix.'order'] = $order;
            }
            else{
                $order = $this->session->userdata($email_prefix.'order');
            }
            $data['order'] = $order;

           
            //save session data into the session
            if (isset($filter_session_data))
            {
             $this->session->set_userdata($filter_session_data);
            }
            

            //fetch manufacturers data into arrays
            $data['Email_Owner'] = $this->Owner_model->as_dropdown('owner')->get_all();
            $data['Email_Source'] = array_unique($this->Source_model->as_dropdown('source')->get_all());
            
            $data['Companies'] = $this->Company_model->as_dropdown('company_name')->get_all();
            
            //TO DO: nneed to activate search and order
             
                    //$this->_model->count_files($u_path, $search_string, $order);
            

            //fetch sql data into arrays
            if($search_string){
                $data['count_emails']= $this->Emailntv_model->where(array('email LIKE'=>'%'.$search_string.'%','owner_id'=>$owner_id))->count_rows();
                if($order){
                    $data['Emails'] = $this->Emailntv_model->where(array('email LIKE'=>'%'.$search_string.'%','owner_id'=>$owner_id))->order_by($order,$order_type)->paginate($config['per_page'],$data['count_emails']);
                    
                         //get_files($u_path, $search_string, $order, $order_type,$limit_end ,$config['per_page']);        
                }else{
                    $data['Emails'] = $this->Emailntv_model->where(array('email LIKE'=>'%'.$search_string.'%','owner_id'=>$owner_id))->paginate($config['per_page'],$data['count_emails']);
                              //get_files($u_path, $search_string, '', $order_type, $limit_end,$config['per_page']);           
                }
                
            }else{
                $data['count_emails']= $this->Emailntv_model->count_rows(array('owner_id'=>(int)$owner_id,'company_id'=>(int)$company_id));
               if($order){
                    $data['Emails'] = $this->Emailntv_model->where(array('owner_id'=>(int)$owner_id,'company_id'=>(int)$company_id))->order_by($order,$order_type)->paginate($config['per_page'],$data['count_emails']);//->order_by($order_type));
                              //get_files($u_path, '', $order, $order_type, $limit_end,$config['per_page']); 
                    //get_products($manufacture_id, $search_string, $order, $order_type, $config['per_page'],$limit_end); 
                }else{
                    $data['Emails'] = $this->Emailntv_model->where(array('owner_id'=>(int)$owner_id,'company_id'=>(int)$company_id))->paginate($config['per_page'],$data['count_emails']);
                            //get_files($u_path, '', '', $order_type, $limit_end,$config['per_page']);           
                }
                
            }
            
            $config['total_rows'] = $data['count_emails'];

        }else{

            //clean filter data inside section
            $filter_session_data[$email_prefix.'owner_selected'] = null;
            $filter_session_data[$email_prefix.'source_selected'] = null;
            $filter_session_data[$email_prefix.'company_selected'] = null;
            $filter_session_data[$email_prefix.'search_string_selected'] = null;
            $filter_session_data[$email_prefix.'order'] = null;
            $filter_session_data[$email_prefix.'order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['owner_selected'] = 0;
            $data['source_selected'] = 0;
            $data['company_selected'] = 0;
            $data['order'] = 'id';

            //fetch sql data into arrays
            //$data['manufactures'] = $this->manufacturers_model->get_manufacturers();
            
            $data['Email_Owner'] = $this->Owner_model->as_dropdown('owner')->get_all();
            $data['Email_Source'] = array_unique($this->Source_model->as_dropdown('source')->get_all());
            $data['Companies'] = $this->Company_model->as_dropdown('company_name')->get_all();
            
            $data['count_emails']= $this->Emailntv_model->count_rows();
            $data['Emails'] =$this->Emailntv_model->paginate($config['per_page'],$data['count_emails']);//->order_by($order_type); 
                    //$this->eextract_model->get_files('', '', '', $order_type ,$limit_end,$config['per_page']);        
            $config['total_rows'] = $data['count_emails'];

        }//!isset($manufacture_id) && !isset($search_string) && !isset($order)

        //initializate the panination helper 
        $this->pagination->initialize($config);   

        //load the view
        /*
        $data['all_pages'] = $this->Emailntv_model->all_pages; // will output links to all pages like this model: "< 1 2 3 4 5 >". It will put a link if the page number is not the "current page"
        $data['previous_page'] =         $this->Emailntv_model->previous_page; // will output link to the previous page like this model: "<". It will only put a link if there is a "previous page"
        $data['next_page'] =         $this->Emailntv_model->next_page;
        $data['page']=$page;
        */
        
        $data['unVerfied_Emails'] = TRUE;
        $data['page_count'] =$config['per_page'];
        $data['main_content'] = 'admin/Emails/list';
        $this->load->view('includes/template', $data);  

    }//get unverfied emails
    
    //Export verfied emails
    public function export_emails_from_DB()
    {
        
        $field_data = 'email';
        $first_name='';
        $url ='';
        $fh = 0;
        $export_dir = dirname($_SERVER["SCRIPT_FILENAME"]).'/exports/';
        
        $data['emails'] = array();
        $data['error'] = '';
        
        $emails_table = $this->uri->segment(4);
        $url = $this->uri->segment(5);
        
        if ($emails_table == 'unverfied')
        {
           $data['count_emails']= $this->Emailntv_model->count_rows();
           $data['emails'] =$this->Emailntv_model->fields($field_data)->get_all();//->order_by($order_type);
           $first_name = 'unverfied_';
 
        }else
        {
            $data['count_emails']= $this->Emailv_model->count_rows();
            $data['emails'] =$this->Emailv_model->fields($field_data)->get_all();//->order_by($order_type);
            $first_name = 'verfied_';
        }
        
        if ($url == 'url')
            $first_name = $first_name.'urls';
        else
            $first_name = $first_name.'emails';
        
        
        $file_name =$first_name.'_'.date("Ymd").'.txt';
        $data['file']=$export_dir.$file_name;
        
        if (($fh = fopen($data['file'], "wb")) !== false) { 
            foreach($data['emails'] as $key => $value)
            {
                if ($value[$field_data] !== '' && $value[$field_data] !== '0')
                {
                 $value[$field_data] = trim(preg_replace('/\s+/', ' ', $value[$field_data])) ;
                if ($url != 'url')
                  fwrite($fh, $value[$field_data]."\n");
                else{
                    $domain = $this->get_url_from_email($value[$field_data]);
                    if (!empty($domain))
                      fwrite($fh, $domain."\n");
                  }
                    
                }
                //fwrite($fh, $value[$f_data].PHP_EOL);
            }
             fclose($fh); 
        }
        
        
        
        $data['main_content'] = 'admin/exports/list';          
        $this->load->view('includes/template', $data); 
    }//export verfied emails to file
    
      //clear selections
    public function clear()
    {
        /*
         * TODO:
         * must check agin bugs when owner_id, and other ids become zero.
         */

            $company_prefix='email_';
            $filter_session_data[$email_prefix.'owner_selected'] = null;
            $filter_session_data[$email_prefix.'source_selected'] = null;
            $filter_session_data[$email_prefix.'company_selected'] = null;
            $filter_session_data[$email_prefix.'search_string_selected'] = null;
            $filter_session_data[$email_prefix.'order'] = null;
            $filter_session_data[$email_prefix.'order_type'] = null;
            
            $filter_session_data[$email_prefix.'owner_id'] = null;
            $filter_session_data[$email_prefix.'source_id'] = null;
            $filter_session_data[$email_prefix.'company_id'] = null;
            
            $this->session->set_userdata($filter_session_data);

            redirect(base_url().'index.php/admin/Emails/index');


    }//clear
    
    
    public function add()
    {
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {

            //form validation
            $this->form_validation->set_rules('owner_name', 'owner_name', 'required');
            $this->form_validation->set_rules('owner_id', 'owner_id', 'required|numeric');
            //$this->form_validation->set_rules('cost_price', 'cost_price', 'required|numeric');
            //$this->form_validation->set_rules('sell_price', 'sell_price', 'required|numeric');
            //$this->form_validation->set_rules('manufacture_id', 'manufacture_id', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');

            //if the form has passed through the validation
            if ($this->form_validation->run())
            {
                $data_to_store = array(
                    'id' => $this->input->post('owner_id'),
                    'owner' => $this->input->post('owner_name')
                   
                );
                //if the insert has returned true then we show the flash message
                if($this->Owner_model->insert($data_to_store)=== 0){
                    $data['flash_message'] = TRUE;
                    $this->session->set_flashdata('flash_message','sucess');
                    
                }else{
                    $data['flash_message'] = FALSE;
                    $this->session->set_flashdata('flash_message','error');
                }
                
                
                
                redirect(base_url().'index.php/admin/Owner/add');
            }

        }
        //fetch manufactures data to populate the select field
        //$data['manufactures'] = $this->manufacturers_model->get_manufacturers();
        //load the view
        $data['main_content'] = 'admin/Companies/owner/add';
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
            $this->form_validation->set_rules('owner_name', 'owner_name', 'required');
            $this->form_validation->set_rules('owner_id', 'owner_id', 'required|numeric');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');
            //if the form has passed through the validation
            if ($this->form_validation->run())
            {
    
                $data_to_store = array(
                    'id' => $this->input->post('owner_id'),
                    'owner' => $this->input->post('owner_name')
                   
                );
                
                //if the insert has returned true then we show the flash message
                if($this->Owner_model->update($data_to_store,'id') == TRUE){
                    $this->session->set_flashdata('flash_message', 'updated');
                }else{
                    $this->session->set_flashdata('flash_message', 'not_updated');
                }
                redirect('admin/Owner/edit'.$id.'');

            }//validation run

        }

        //if we are updating, and the data did not pass trough the validation
        //the code below wel reload the current data

        //product data 
        $data['owner'] = $this->Owner_model->where('id',$id)->get();
        $data['count_owner']= $this->Owner_model->count_rows();
        //fetch manufactures data to populate the select field
        //$data['manufactures'] = $this->manufacturers_model->get_manufacturers();
        //load the view
        $data['main_content'] = 'admin/Emails/owner/edit';
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
        $this->Owner_model->delete($id);
        redirect('admin/Owner');
    }//delete

   public function cron_db_backup($download=false)
   {
     $this->benchmark->mark('start');  
     $backup_path = dirname($_SERVER["SCRIPT_FILENAME"]).'/backup/db/';
     $backup_file= 'ci_linked_e_m_'.date('d_m_Y').'.gz';
    
     
    $this->Emailv_model->database_backup($backup_file,$backup_path,$download);
     
     $this->benchmark->mark('end');
     $elapsed = $this->benchmark->elapsed_time('start', 'end');
   }
 
    public function extract_data_from_file($csv_file='') {
        
    $email_prefix='eExtract_';
    $this->output->enable_profiler(TRUE);
    $datafile_content_parsed= array();
    $errors = array();
    $source_id = 0;
    $source=array();
    $error =true;

    set_time_limit(0);

    $upload_path= dirname($_SERVER["SCRIPT_FILENAME"]).'/uploads/';
   
    $backup_path = dirname($_SERVER["SCRIPT_FILENAME"]).'/backup/db/';
    $backup_file= 'ci_linked_e_m_'.date('d_m_Y').'.gz';
    
    $bybass_backup=true;
    if (!$bybass_backup)
       $this->Emailv_model->database_backup($backup_file,$backup_path);
    
    if ($csv_file =='')
    {
        $datafile_name = $this->uri->segment(4);
    }else
       $datafile_name = csv_file;

    $ext = pathinfo($datafile_name, PATHINFO_EXTENSION);

    if ($ext == 'csv')
    {    
      $mapfile_path  = $upload_path . pathinfo($datafile_name, PATHINFO_FILENAME).'_map.txt';
      $datafile_path = $upload_path . $datafile_name;
            
//check first if file exists
            
         if (file_exists($mapfile_path) && file_exists($datafile_path)) 
         {
            
            //$datafile_name = pathinfo($datafile, PATHINFO_FILENAME);
            
            
            
            $source = $this->Source_model->where(array('source LIKE'=>'%'.$datafile_name.'%'))->get();
            //if (is_array($source))
            if ($source==false && $source['id'] ==0) 
            {
                $data_to_store = array(
                    'source' => $datafile_name,
                    'link' => 'NA',
                    'user_id'=>1);
                
                $source = $this->Source_model->insert($data_to_store);                
            }
            
            $source_id = $source['id'];
            
            $data_to_function =array(
                'csv_path'=>    $datafile_path,
                'map_path'=>    $mapfile_path,
                'source_id'=>    $source_id
            );
            
            if ($source_id != 0) 
            {
              $datafile_content_parsed = $this->parse_companies_in_csv($data_to_function);
              //$datafile_content_parsed = $this->parse_companies_in_csv($datafile_content,$mapfile_content,$source_id); 
            
               $data['counts'] = $datafile_content_parsed['counters'];               
               $data['companies'] = $datafile_content_parsed['companies'];
               $data['companies_count'] = count($datafile_content_parsed['companies']);
               $data['DataFileName'] =$datafile_name;
               $data['source_id'] =$source_id;
        
               $data['emails_in_DB']=$datafile_content_parsed['emails_in_DB'];
               $data['emails_duplicate']=$datafile_content_parsed['emails_duplicate'];
               $data['bformated_emails']= $datafile_content_parsed['bformated_emails'];
               
               $data['urls_more_than_one'] = $datafile_content_parsed['urls_more_than_one'] ;
               $data['free_email_list_no_c_id'] = $datafile_content_parsed['free_email_list_no_c_id'];
               $data['email_list_no_c_id'] = $datafile_content_parsed['email_list_no_c_id']; 
            }else{
                $error =true;
                $errors[]= "source id =0";
                
            }
            //$data = parse_complex_csv();
            
                    
                        
            
         }else{
                $error =true;
                $errors[]= "file doesn't exist";
         }
         
    }else{
          $error =true;      
          $errors[]= "wrong extension file";
    }
    
    if ($error)
    {
       $errors[]= 'source_id='.$source_id;
       $errors[]= 'datafile_path='.$datafile_path;
       $errors[]= 'mapfile_path='.$mapfile_path;
       $errors[]= 'upload_path='.$upload_path; 
       $data['errors']=$errors;
    }
        
       set_time_limit(30);
        //$data['emails_in_DB_count']=$emails_in_DB;
        //$data['emails_duplicate_count']=$emails_duplicate;
       
       $data['main_content'] = 'admin/Emails/ExtractEmails/result';
        
       $test_file_save =true; 
       if ($test_file_save)
       {
         $file=dirname($_SERVER["SCRIPT_FILENAME"]).'/output/email_extractions/'.$datafile_name.'_'.date('H_d_m_Y').'.html';
         $this->store_html($file,'includes/template',$data);
       }
       
        $this->load->view('includes/template', $data);     
           
    }
    
    private function parse_companies_in_csv($data_to_function)
    {
        $data = array ();
        $emails = array ();
        $urls=array();
        $verified_emails = array();
        $nt_verified_emails = array();
        $companies=array();
        $company = array();
        $bformated_emails =array();
        $categories=array();
        
        $email_duplicate_in_file_count =0;
        $emails_duplicate=array();
          $email_in_DB_count =0;
          $emails_in_DB=array();
          $free_email_list_no_c_id =array();
          $email_list_no_c_id =array();

        $preg_emails='';
        //counters
        $companies_inserted_count=0;
        $emails_inserted_count=0;
        $emails_extracted_count=0;
        $emails_bformated_count=0;
        $emails_verified_count=0;
        $emails_ntverified_count=0;
        $companies_url_updated_count =0;
        $companies_without_url_count =0;
        $companies_without_emails_count =0;
        $companies_updated_count =0;
        //$companies_updated_count =0;
        $company_name_fr_email_count =0;
        $companies_no_name_count=0;
        $companies_no_id_count=0;
        $category_inserted_count=0;
        $companies_nt_inserted_count=0;
        $emails_bformated_count =0;
        $shared_url_count = 0;
        $free_emails_companies_count=0;
        
        $category_name = '';
        $category_id = 0;
        $company_id = 0;
        $emailv_id = 0;
         $emailnv_id = 0;
        $lines_count =0;
        $non_empty_lines_count =0;
        //$source_id = 0;
        $country = '';
        $address = '';
        $telephone = 0;
        $email = '';
        $url = '';
        $city='';
        $company_info=array ();
        $rank =100;
        $J=0;

        
        //email_upload log table
        $company_start_id = 0;
        $company_end_id = 0;
        $emailv_start_id = 0;
        $emailv_end_id = 0;
        $emailnv_start_id = 0;
        $emailnv_end_id = 0;
        $source_lines=0;
        $company_inserts=0;
        $emailv_inserts =0;
        $emailnv_inserts =0;
        
        $source_id = $data_to_function['source_id'];
        //$page = $data_to_function['page'];
        //$limit_start = $data_to_function['limit_start'];
        //$limit_end = $data_to_function['limit_end'];
        
        $this->load->library('email',array('fromEmail'=>'ahmed.elmalla@linkedemails.com'));
        $validation_status = $this->_validate_email_domain('projects@i-awcs.com');
        
        $csv_data = $this->Emailv_model->fread_as_array($data_to_function['csv_path']);
        $map = $this->Emailv_model->fread_as_array($data_to_function['map_path']);
        
        $company_name_extracted = false;
        $url_extracted=false;
        
        $companies_table= $this->Company_model->fields(array('company_name','url','company_id'))->get_all();
        $verfied_emails_table= $this->Emailv_model->fields(array('email','email_id'))->get_all();
        $ntverfied_emails_table= $this->Emailntv_model->fields(array('email','email_id'))->get_all();
            
        
        $vEmails_array_k_email = $this->restruct_array_key($verfied_emails_table,'email','email_id');
        $ntvEmails_array_k_email = $this->restruct_array_key($ntverfied_emails_table,'email','email_id');
        
        $companies_array_k_url = $this->restruct_array_key($companies_table,'url','company_id');
        $companies_array_k_name  = $this->restruct_array_key($companies_table,'company_name','company_id');
        $companies_array_k_id = $this->restruct_array_key($companies_table,'company_id','url');
        
        //$url_counts = array_count_values($array);
        
        $category_table= $this->Category_model->fields(array('category','id'))->get_all();
        $category_array_k_category = $this->restruct_array_key($category_table,'category','id');
        
        $map_data = explode(",",$map[0]);
        
        $company_index = array_search('Company',$map_data);
        $category_index = array_search('Category',$map_data);
        $country_index = array_search('Country',$map_data);
        $city_index = array_search('City',$map_data); 
        $address_index = array_search('Address',$map_data);
        $telephone_index = array_search('Tel',$map_data);
        $email_index = array_search('Email',$map_data);
        $url_index = array_search('Url',$map_data);

        $total_lines_csv =count($csv_data);


        for($i = 0; $i < count($csv_data); ++$i) 
        {
            $lines_count +=1;
            $company_name_extracted = false;
            $url_extracted=false;
            
          if (!empty($csv_data[$i]))
          {  
              //counter to check proper processing for all file entries
               $non_empty_lines_count +=1;
               
               //make sure all data is lowercase for equal checks == 
               $csv_data[$i]=strtolower($csv_data[$i]);
               $line_data1 = explode(",",$csv_data[$i]);
               
               $line_data1 = $this->array_triming($line_data1);
              
              // check if the first section of data isn't empty or equal zero 
              if ($line_data1[0] != "" && $line_data1[0] != '0')
              {
                 $emailv_id = 0;
                 $emailnv_id = 0;
                 $emails = array();
                 
                 
                 $stop=false;
                 if ($line_data1[$company_index] =='HAMID FALLAH TRADING CO. L.L.C.')
                     $stop=true;
                 
                 if ($stop==true)
                     $pppp=1;
                 //get emails 
                 $emails_arr = $this->get_wformated_emails_from_array($line_data1);
                 $emails = $emails_arr['wformated'];
                  
                 if (!empty($emails_arr['bformated'])){
                    $bformated_emails[$lines_count] = $emails_arr['bformated'];
                    $emails_bformated_count+=1;
                 }
                                
                // Get company information
                 
                 $company_name =  strtolower($line_data1[$company_index]);
                 $company_name =  $this->clean_company($company_name);
                 
                 $url= $this->get_url_from_array($line_data1, $url_index);
                 $url = $this->condition_url($url);
                 
                 
                 
                //If no company name in csv file, get it from the url or get it from the domain name 
                if ($company_name == '')
                {   
                    // Flag to know if the company was provided in the csv
                    $company_name_extracted = true ;    
                
                    if ($url !='')
                    {
                        $company_name = $this->get_company_name_from_url($url,false);
                    }    
                    
                    //If the extracted email array not empty 
                    if (!empty($emails) && $url =='' && $company_name != 'free_email')
                    {
                        list($key,$value)= each($emails);
                        if ($value !='')
                        {
                          $company_name = $this->get_company_name_from_email($value);
                        //$company = $company_info['company'];
                          if ($company_name != 'free_email'){ 
                            $url = $this->get_url_from_email($value);
                            $url_extracted=true;
                            $company_name_fr_email_count +=1;
                          }
                        }
                    }
                }

                if ($company_name != '' && $company_name != 'free_email')
                {
                    //Get info about company category
                    if ($country_index)
                       $country =  $this->clean_data($line_data1[$country_index]);
                    
                    if ($city_index)
                         $city =  $this->clean_data($line_data1[$city_index]);
                    
                    if ($address_index)
                       $address = $this->clean_only_trailing($line_data1[$address_index]);
                    
                    if ($telephone_index)
                        $telephone = $this->get_telephone_from_string(($line_data1[$telephone_index]));
                    
                    if ($category_index)
                       $category_name = strtolower($this->clean_data($line_data1[$category_index]));
                    if ($category_name !='')
                    {
                       if (!array_key_exists($category_name, $category_array_k_category)){
                          if (!array_key_exists($category_name,$categories))
                          {
                            //$category_id = $this->Category_model->where(array('category LIKE'=>'%'.$category_name.'%'))->get()['id']; 
                            $category_id = $this->Category_model->where(array('category'=>$category_name))->get()['id'];   
                            if ($category_id == 0)
                            {
                              $data_to_store = array(
                                'category' => $category_name 
                                );

                              $category_id = $this->Category_model->insert($data_to_store);
                              $categories[$category_name]=$category_id;
                              $category_inserted_count +=1;
                            }
                          
                          }else{
                              $category_id = $categories[$category_name];
                          }
                       }else
                           $category_id = $category_array_k_category[$category_name];
                    }
                    
                }else if ($company_name == 'free_email'){
                    $free_emails_companies_count += 1;
                }
                    
                 

                

                //confirm DB does't have same url or same company_name
                 $company_id= 0;
                if ($company_name != '' && $company_name != 'free_email')
                {   //check if company name is not in our companies table
                    
                      $url_count = 0;
                      if ($url !='')
                      {   if(array_key_exists($url,$companies_array_k_url))
                         {
                             //Need toupdate the DB info about the company details
                             //
            //Get url count ->
                            $url_count = $this->Company_model->where(array('url LIKE'=>$url))->count_rows() ;  
            
                            
                            if($company_name_extracted)
                            {
                                if ($url_count >1)
                                {   
                                   $shared_url_count += 1;   
                                   $urls[$url] =$url_count;
                                   //$many_url_exist
                                }else
                                   $company_id = $companies_array_k_url[$url];     
                            } 
                            
                          }
                       }
                    
                    if (!array_key_exists($company_name,$companies_array_k_name) &&!array_key_exists($company_name,$companies) )
                    {

                       //Insert data to company
                        $data_to_store = array(
                        'company_name' => $company_name,
                        'address' => $address.','.$city,
                         'country' => $country,
                         'telephone' => $telephone,
                         'category_id' => $category_id,
                         'url' => $url,
                         'source_id'=>$source_id 
                          );
  
                       //$url=strtolower($url);
                        //check if company data wasn't updated
                        if (($company_name_extracted ==false && $url_count >0)&& ($company_id == 0 && $company_name != '' && $company_name != 'free_email'))
                        {
                          $company_id = $this->Company_model->insert($data_to_store);
                          if (is_array($company_id))
                             $companies[$company_name] = $company_id['company_id'];
                          else
                             $companies[$company_name] = $company_id;
                          
                          if ($company_id !=0)
                             $companies_inserted_count+=1;
                        }else if (($company_name_extracted && $url_count ==0) && ($company_id == 0 && $company_name != '' && $company_name != 'free_email') )
                        {
                          $company_id = $this->Company_model->insert($data_to_store);
                          
                          if (is_array($company_id))
                             $companies[$company_name] = $company_id['company_id'];
                          else
                             $companies[$company_name] = $company_id;
                          
                          if ($company_id !=0)
                             $companies_inserted_count+=1;
                        }else if (!$company_name_extracted  && ($company_id == 0 && $company_name != '' && $company_name != 'free_email') )
                        {
                            $company_id = $this->Company_model->insert($data_to_store);
                          
                          if (is_array($company_id))
                             $companies[$company_name] = $company_id['company_id'];
                          else
                             $companies[$company_name] = $company_id;
                          
                          if ($company_id !=0)
                             $companies_inserted_count+=1;
                        }
                     
                    }else{
//company name exist either in the same file or even in DB->
                        //if ($company_name_extracted ==false)
                         if (array_key_exists($company_name,$companies_array_k_name))
                             $company_id = $companies_array_k_name[$company_name];
                         else if (array_key_exists($company_name,$companies))  
                             $company_id =$companies[$company_name];
                         
                         if(($company_id ==0) )  
                         {  
                            if ($url != '') 
                            {
                               $company_id_arr =$this->Company_model->get(array('url LIKE'=>'%'.$url.'%'));
                               $company_id = $company_id_arr['company_id'];
                            }else{
                               $company_id_arr =$this->Company_model->get(array('company_name LIKE'=>'%'.$company_name.'%'));
                               $company_id = $company_id_arr['company_id'];
                            }      
                         }
                         //$company_id_arr =$this->Company_model->get(array('company_name LIKE'=>'%'.$company_name.'%','OR url'=>$url));
                         
                         $companies_nt_inserted_count+=1;
                    }
                   
                } else
                      $companies_no_name_count+=1;
                
                
                If ($company_id == 0)
                {
                    $companies_no_id_count+=1;
                }else{
                    if ($company_start_id == 0 )
                    {    $company_start_id = $company_id;
                        $company_end_id = $company_id;
                    }else
                       $company_end_id = $company_id; 
                }
                    
                
                
    //checkif there is emails
                
                if (!empty($emails) )
                {
                  foreach($emails as $email)
                  { 
                    
                    //Make an intial validation test 
                 
                 
                    $email=strtolower($email);
                    $email=stripslashes($email);
                    $email_data_to_store =array();
                    
                    if ((!array_key_exists($email,$vEmails_array_k_email) ))
                        {
                          if ((!array_key_exists($email,$verified_emails))  )
                          {
                            
                              $rank =0;
                              $owner_id = 1000;
                               //If category name & comapny name avialble,emailmust be in Email_Master Table, else make the rank 1000
                               // And if validation not working rank 100 or if not validated rank become 200   
                               if ($category_name !='' || !$company_name_extracted )
                               {

                                    if ( $validation_status) 
                                    {
                                        if ($this->_validate_email_domain($email))
                                            $rank = 90; 
                                        else
                                            $rank = 200;
                                    }else    
                                       $rank = 100;  
                               }else if( $validation_status){
                                      if ($this->_validate_email_domain($email))
                                            $rank = 90;
                                      else
                                          $rank = 1000; 
                               }else if( !$validation_status)    
                                        $rank = 1000;
                                
                               
                                //Only the above checked conditions match them insert into Email_MASTER Table  
                                if ( $rank < 1000 ) 
                                {    

                                     $rank = $this->calculate_email_rank($email,$rank);
                                     
                                     $owner_id =$this->detect_owner($email);
                                     
                                     $email_data_to_store =array(
                                        'email' => $email,
                                        'country' => $country,
                                        'rank' => $rank,
                                        'owner_id' => $owner_id ,
                                        'rank' => $rank,
                                        'source_id' => $source_id,
                                        'company_id' => $company_id
                                        );

                                       
                                             //Insert verified email data to DB
                                           $emailv_id = $this->Emailv_model->insert($email_data_to_store);
                                           $verified_emails[$email]= $emailv_id;


                                           if ($emailv_id != 0)
                                           {
                                             $emails_verified_count +=1;

                                             if ($emailv_start_id == 0)
                                             {    $emailv_start_id =$emailv_id;
                                                 $emailv_end_id =$emailv_id;
                                             }else
                                                 $emailv_end_id =$emailv_id;
                                           }
                                     

                                             //Update url from email domain if not found earlier
                                             If ($url == '' && $company_id != 0)
                                             {
                                                 $url = $this->get_url_from_email($email);
                                                 if ($url != '')
                                                   $this->Company_model->update(array('url'=> $url),$company_id);
                                                 $companies_url_updated_count +=1;
                                             }else
                                                 $companies_without_url_count +=1;
                                         
                                       If ( $company_name == 'free_email')
                                       {
                                          $free_email_list_no_c_id[]=$email; 
                                       }else if ($company_id == 0)    
                                           $email_list_no_c_id[]=$email; ;

                                }else if( (!array_key_exists($email,$ntvEmails_array_k_email))){
                                    
                                    if (!array_key_exists($email,$nt_verified_emails)){
                                       
                                        $owner_id = 1000;
                                        $owner_id =$this->detect_owner($email);
                                       
                                        $email_data_to_store =array(
                                            'email' => $email,
                                            'country' => $country,
                                            'rank' => $rank,
                                            'owner_id' => $owner_id,
                                            'rank' => $rank,
                                            'source_id' => $source_id,
                                            'company_id' => $company_id
                                            );
                                       //Insert non verfied email data to DB
                                         $emailnv_id = $this->Emailntv_model->insert($email_data_to_store);
                                         $nt_verified_emails[$email]= $email_data_to_store;
                                     
                                     
                                     
                                     if ($emailnv_id != 0)
                                     {
                                         $emails_ntverified_count +=1;
                                         
                                         if ($emailnv_start_id == 0)
                                         {    $emailnv_start_id =$emailnv_id;
                                             $emailnv_end_id =$emailnv_id;
                                         }else
                                             $emailnv_end_id =$emailnv_id;
                                     }
                                  }else{
                                      $email_duplicate_in_file_count +=1;
                                      $emails_duplicate[$lines_count]=$email;
                                  } 
                                }else{
                                    $email_in_DB_count +=1;
                                    $emails_in_DB[$lines_count]=$email;
                                }
                          }else{
                              $email_duplicate_in_file_count +=1;
                              $emails_duplicate[$lines_count]=$email;
                          }
                          
                        }else{  
                          $email_in_DB_count +=1;
                          $emails_in_DB[$lines_count]=$email;
                        }   
                      $emails_extracted_count +=1;
                  }
                }else{
                    
                    $companies_without_emails_count +=1;
                    
                    if ($url == '')
                     $companies_without_url_count +=1;
                }
                 
              }
          }
  }
  
   //
   If ($company_id != 0)
    {
      $company_end_id = $company_id;
    }
    
    if ($emailv_id != 0)
    {
         $emailv_end_id =$emailv_id;
    }
    
    if ($emailnv_id != 0)
    {
         $emailnv_end_id =$emailnv_id;
    }
    
    
          $data_to_store=array(
                'source_id' => $source_id,
                'company_start_id' => $company_start_id,
                'company_end_id' => $company_end_id, 
                'emailv_start_id' => $emailv_start_id,
                'emailv_end_id' => $emailv_end_id,
                'emailnv_start_id' => $emailnv_start_id,
                'emailnv_end_id' => $emailnv_end_id,
                'emailv_inserts' => $emails_verified_count,
                'company_inserts'=>$companies_inserted_count,
                'emailnv_inserts' => $emails_ntverified_count
                );
        
       $data['email_log_id'] = $this->Extraction_model->insert($data_to_store); 
      
      
 
        $data['bformated_emails']= $bformated_emails;
        $data['emails_in_DB']=$emails_in_DB;
        $data['emails_duplicate']=$emails_duplicate;
        $data['emailsv']=$verified_emails;
        $data['emailsntv']=$nt_verified_emails;
        $data['companies']=$companies;
        $data['urls_more_than_one'] = $urls;
        $data['free_email_list_no_c_id'] = $free_email_list_no_c_id;
        $data['email_list_no_c_id'] = $email_list_no_c_id;        
        
        $data['counters']= array(
            'emails_verified_count'=>$emails_verified_count,
            'emails_ntverified_count'=>$emails_ntverified_count,
            'emails_extracted_count'=>$emails_extracted_count,
            'non_empty_lines_count'=>$non_empty_lines_count,
            'lines_count'=>$lines_count,
            'companies_without_url_count'=>$companies_without_url_count,
            'companies_without_emails_count'=>$companies_without_emails_count,
            'companies_url_updated_count'=>$companies_updated_count,             
            'company_name_fr_email_count'=>$company_name_fr_email_count,
            'category_inserted_count'=>$category_inserted_count,
            'companies_nt_inserted_count'=>$companies_nt_inserted_count,
            'companies_inserted_count'=>$companies_inserted_count,
            'email_duplicate_in_file_count'=>$email_duplicate_in_file_count,
            'email_in_DB_count'=>$email_in_DB_count,
            'emails_bformated_count'=>$emails_bformated_count,
            'shared_url_count'=>$shared_url_count,
            'free_emails_companies_count'=>$free_emails_companies_count
            );
        
        return $data;
}//parse csv

    
    //Extract emails from an array
    private function get_wformated_emails_from_array($line)
    {
       $rank=100;
       $wf_emails = array(); 
       $bf_emails = array();
       $clean_line =$this->clean_emails_before($line);
      //$sucess =preg_match(EMAIL_PATTERN, $clean_line,$result);
      $result = preg_grep(EMAIL_PATTERN, $clean_line);
       if ($result) 
      {
         //if (is_array($result))
         foreach($result as $key => $value)
         {
             if ($value =='fallah77@emirates.net.aehftsale7@emirates.net.ae')
                 $stop =true;
             $e= $this->clean_emails_after($value);
             if ($e =='fallah77@emirates.net.aehftsale7@emirates.net.ae')
                 $stop =true;
             $email = filter_var($value, FILTER_VALIDATE_EMAIL)? $e : '';
             if($email!='')
                 $wf_emails[$e]=strtolower($e);
             else
                 $bf_emails[$e]=strtolower($e);
         }
          
      } 
         return array('wformated'=>$wf_emails,'bformated'=>$bf_emails);
    }
    
    private function get_emails_from_array($line)
    {
      //$sucess = ;
       $emails = array(); 
       $clean_line =$this->clean_data($line);
      //$sucess =preg_match(EMAIL_PATTERN, $clean_line,$result);
      $result = preg_grep(EMAIL_PATTERN, $clean_line);
       if ($result) 
         return strtolower($result); 
      else
         return ''; 
    }

    private function get_url_from_array($line,$url_index)
    {
        $url = '';
        $_element='';
        $arr_count =count($line);
       //$result = array(); 
      $free_emails=array('gmail','yahoo','hotmail','emirates','aol','live');

      $clean_line =$this->clean_data($line);
      //$sucess =preg_match(EMAIL_PATTERN, $clean_line,$result);
     
      $result = preg_grep(URL_PATTERN, $clean_line);
      if ($result) 
      {
         //if (is_array($result))
        list($key, $value) = each($result);  
         
        if (!in_array($value,$free_emails))
            return strtolower($value);
      } 
         return '';    
    }
    
 
    
    private function get_url_from_email($email)
    {  
       $url='';
       $company='';
       $url_ext='';
       $free_emails=array('gmail','yahoo','hotmail','emirates','aol','live');
       
        list($full_name, $url) = explode('@', $email);
        list($company, $url_ext) = explode('.', $url);
                
       if (!in_array($company,$free_emails))
       {
        //list($full_name, $url) = explode('@', $email);
        //list($company, $url_ext) = explode('.', $url);
           return strtolower($url); 
       }else
           return '';       
    }

    //remove whitespace(space, tab or newline)
    private function clean_data($data)
    {
      //$sucess = ;compan
       //$result = array();
        
      if (is_array($data))
      {
        for ($i=0;$i<count($data);++$i)
        {
           $data[$i] = preg_replace('/\s+/', '', $data[$i]);
           $data[$i] = preg_replace('/[?]/', '', $data[$i]);
           $data[$i] = trim($data[$i], '.');
           $data[$i] = trim($data[$i]);
           $data[$i] = str_replace("\r\n","",$data[$i]);
        } 
        $result = $data;
      }else {
          if ($data !=''){
            $result = preg_replace('/\s+/', '', $data);
            $result = preg_replace('/[?]/', '', $result);
            $result = trim($result);
            $result = trim($result, '.');
            $result  = str_replace("\r\n","",$result );
          }else 
              return '';
      }
      
      return $result;
    }
    
     private function clean_company($data)
    {
      //$sucess = ;compan
       //$result = array();
        
      if (is_array($data))
      {
        for ($i=0;$i<count($data);++$i)
        {
           //$data[$i] = preg_replace('/\s+/', '', $data[$i]);
           $data[$i] = preg_replace('/[?]/', '', $data[$i]);
           //$data[$i] = trim($data[$i], '.');
           $data[$i] = trim($data[$i]);
           $data[$i] = str_replace("\r\n","",$data[$i]);
        } 
        $result = $data;
      }else {
          if ($data !=''){
            //$result = preg_replace('/\s+/', '', $data);
            $result = preg_replace('/[?]/', '', $data);
            $result = trim($result);
            //$result = trim($result, '.');
            $result  = str_replace("\r\n","",$result );
          }else 
              return '';
      }
      
      return $result;
    }
    
    //clean emails only
    
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
    
    private function clean_emails_after($data)
    {
      //$sucess = ;
       //$result = array();
        
      if (is_array($data))
      {
        for ($i=0;$i<count($data);++$i)
        {
           $data[$i] = preg_replace('/\s+/', '', $data[$i]);
           //$data[$i] = trim($data[$i], '.');
           $data[$i] = trim($data[$i]);
           $data[$i] = str_replace(";","",$data[$i]);
           $data[$i] = str_replace("\r\n","",$data[$i]);
        } 
        $result = $data;
      }else {
          if ($data !=''){
            $result = preg_replace('/\s+/', '', $data);
            //$result = trim($result);
            $result  = str_replace(";","",$result );
            //$result = trim($result, '.');
            $result  = str_replace("\r\n","",$result );
          }else 
              return '';
      }
      
      return $result;
    }
    //clean emails only
    //
    
     //remove whitespace(space, tab or newline)
    private function clean_only_trailing($data)
    {
      //$sucess = ;
       //$result = array();
        
      if (is_array($data))
      {
        for ($i=0;$i<count($data);++$i)
        {
           //$data[$i] = preg_replace('/\s+/', '', $data[$i]);
           $data[$i] = trim($data[$i], '.');
           $data[$i] = trim($data[$i]);
           $data[$i] = str_replace("\r\n","",$data[$i]);
        } 
        $result = $data;
      }else {
          if ($data !=''){
            //$result = preg_replace('/\s+/', '', $data);
            $result = trim($data);
            $result = trim($result, '.');
            $result = trim($result, ',');
            $result  = str_replace("\r\n","",$result );
          }else 
              return '';
      }
      
      return $result;
    }
    
    private function get_telephone_from_string($string)
    {
       $tel = '';
       //$result = array(); 
     if (strpos($string,':'))
     {
      list($str, $tel) = explode(':', $string);
      return $this->clean_only_trailing($tel);
     }else
        return $this->clean_only_trailing($string); 
         
      
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

    
    
    protected function _validate_email_domain($email)
    { 
        $url ='';
        $company='';
        $free_emails=array('gmail','yahoo','hotmail','emirates','aol','live');
       
        list($full_name, $url) = explode('@', $email);
        list($company, $url_ext) = explode('.', $url);
                
       if (!in_array($company,$free_emails))
       {        
         $domain_validated = $this->email->verify_domain($email);

         if ($domain_validated)
             return 1;
         else
             return 0;
       }else
           return 1;
    }
    
    protected function check_domain_in_email($email)
    { 
        
         $this->load->library('email',array('fromEmail'=>'ahmed.elmalla@linkedemails.com'));
  
         $domain_validated = $this->email->verify_domain($email);
          
         if ($domain_validated)
             return 1;
         else
             return 0;
    }//validate email domain
    
    protected function check_format_of_email($email)
    { 
        $email_format = filter_var($email, FILTER_VALIDATE_EMAIL)? true : false;
          
        return $email_format;
    }//validate email format
    
            
    private function get_email_owner_name($email)
    {    
        $full_name='';
        $first_name ='';
        $last_name='';
        $domain='';
         
                list($full_name, $domain) = explode('@', $email);
                
                if (strpos($full_name, '.') !== false)
                {    
                    list($first_name, $last_name)= explode(".",$full_name);
                }else {       
                   if (strpos($full_name, '_') !== false)
                   {
                     list($first_name, $last_name)= explode("_",$full_name);
                   }else{ 
                    if ((strlen($full_name)>3) && ($full_name!== 'mail'))
                     $first_name= $full_name;
                   else
                     $first_name="Sir";  
                   }
                }
                return array("first_name"=>"$first_name","last_name"=>"$last_name");
    }
    
    private function get_company_name_from_email($email)
    {
        $url='';
        $company='';
        $url_ext='';
        
        $free_emails=array('gmail','yahoo','hotmail','emirates','aol','live');
       
        list($full_name, $url) = explode('@', $email);
        list($company, $url_ext) = explode('.', $url);
                
       if (!in_array($company,$free_emails))
          return strtolower($company);//","url"=>"$url");                         
       else
          return 'free_email';  
    }
    
    
    private function array_triming($arr)
    {
     $result=array();   
     if (is_array($arr))
     {
       
        for ($i=0;$i<count($arr);++$i)
        {
           $result[$i] = trim($arr[$i]);
        } 
         
     }
     return $result;
    }
    
    private function condition_url($url)
    {
        $domain='';
        $input = trim($url, '/');

      // If scheme not included, prepend it
        if (!preg_match('#^http(s)?://#', $input)) {
            $input = 'http://' . $input;
        }

         $urlParts = parse_url($input);

        // remove www
         $domain = preg_replace('/^www\./', '', $urlParts['host']);
         
         return $domain;

    }
    
    private function get_company_name_from_url($url,$condition=false)
    {
        //$url='';
        $company='';
        $url_ext='';
        
        $free_emails=array('gmail','yahoo','hotmail','emirates','aol','live');
       
        
        if($condition)
          $domain = $this->condition_url($url);
        else
          $domain =$url;  
        
        //list($full_name, $url) = explode('@', $email);
        list($company, $url_ext) = explode('.', $domain);
         
       if (strpos('.',$company))        
         list($company, $url_ext) = explode('.', $company);
           
       
       if (!in_array($company,$free_emails))
          return strtolower($company);//","url"=>"$url");                         
       else
          return 'free_email';
       
    }
    
    private function detect_owner($email)
    {
         $url='';
        $full_name='';
        $owner=1000;
        
        list($full_name, $url) = explode('@', $email);
        
        if ($this->string_email_name_contains(array('job','jobs','hr','recruitment','cv','career','recruit','rec','hrdept','globalstaffing','hire'),$full_name)) 
          return 2;
        else
            return $owner;
    }
    
    private function calculate_email_rank($email,$rank)
    {
        
        $url='';
        $full_name='';
        
        list($full_name, $url) = explode('@', $email);
        
        if ($this->string_email_name_contains(array('job','jobs','hr','recruitment','cv','career'),$full_name)) {
          $rank -= 10;
        }else if ($this->string_email_name_contains(array('secretary','pa','assistant'),$full_name))
        {
           $rank -= 20; 
        }else if ($this->string_email_name_contains(array('manager','hiring'),$full_name)){
            
           $rank -= 30;
        }else if ($this->string_email_name_contains(array('md','ceo','coo'),$full_name)){
            $rank -= 40;
        }
                
         return $rank;                         
                
    }
    
    private function string_email_name_contains($needles, $haystack)
    {  
        if (strpos('.',$haystack))
           $arr = explode(".", preg_replace("/[^A-Za-z0-9' -]/", "", $haystack));
        else if (strpos('_',$haystack))
           $arr = explode("_", preg_replace("/[^A-Za-z0-9' -]/", "", $haystack));
        else if (strpos('-',$haystack))
           $arr = explode("-", preg_replace("/[^A-Za-z0-9' -]/", "", $haystack));
        else
           $arr = array(preg_replace("/[^A-Za-z0-9' -]/", "", $haystack));
        
        $arr_inter= array_intersect($needles,$arr );
        return count($arr_inter);
    }
    
    private function string_contains($needles, $haystack)
    {
        
        return count(array_intersect($needles, explode(" ", preg_replace("/[^A-Za-z0-9' -]/", "", $haystack))));
    }
    
    //May need to done in MY_Model
    private function clean_column()
    {
        //UPDATE `table` SET `col_name` = REPLACE(`col_name`, '\t', '' )
        //UPDATE `table` SET `col_name` = REPLACE(`col_name`, '\n', '')
    }
    
    
    protected function is_email_in_DB($email)
    {
        $this->result = mysql_query("select email_id from emails_txt where email='$email'");//"select exists(select id from emails_txt where email='$email'";
        $error= mysql_error();
        if (mysql_num_rows($this->result) > 0)
            return true;
        else
            return false;
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
    
}