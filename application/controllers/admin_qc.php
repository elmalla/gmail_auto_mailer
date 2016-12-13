<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

define('EMAIL_PATTERN', '/([\s]*)([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*([ ]+|)@([ ]+|)([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,}))([\s]*)/i');
define('URL_PATTERN', '%^((https?://)|(www\.))([a-z0-9-].?)+(:[0-9]+)?(/.*)?$%i');

class Admin_qc extends CI_Controller {
 
  
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
       $this->load->model('Extraction_model');
       $this->load->model('Source_model');
       $this->load->model('Category_model');
       $this->load->model('Company_model');//,'company_m');
       
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

    public function csv_extraction_report() {
        
            $_prefix='eExtract_';
            $this->output->enable_profiler(TRUE);
            set_time_limit(0);
            
            $source_id = 0;
            $transaction = null;

            //pagination settings
            $config['per_page'] = 200;
            $config['base_url'] = base_url().'admin/QC/summary';
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
            $limit_start = ($page * $config['per_page']) - $config['per_page'];
            if ($limit_start < 0){
                $limit_start = 0;
            } 
            $limit_end = $limit_start + $config['per_page'] ;
            
            $upload_path= dirname($_SERVER["SCRIPT_FILENAME"]).'/uploads/';
            
            $datafile_name = $this->uri->segment(4);
            
            $mapfile_path  = $upload_path . pathinfo($datafile_name, PATHINFO_FILENAME).'_map.txt';
            $datafile_path = $upload_path . $datafile_name;
            //$datafile_name = pathinfo($datafile, PATHINFO_FILENAME);
            
            
            
            $source = $this->Source_model->where(array('source LIKE'=>'%'.$datafile_name.'%'))->get();
            if (!$source) 
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
                'source_id'=>    $source_id,
                'page'=>    $page,
                'limit_start'=>    $limit_start,
                'limit_end'=>    $limit_end
            );
            
            if ($source_id != 0) 
            {
              $datafile_content_parsed = $this->parse_and_verify_companies_in_csv($data_to_function);
            }
            //$data = parse_complex_csv();
            
                    
                        
         set_time_limit(30);
         
        //initializate the panination helper
        $config['num_links'] = ceil($datafile_content_parsed['csv_count'] /$config['per_page']);
        $this->pagination->initialize($config);   
         
        $data['raw_data']= $datafile_content_parsed['rawdata'];
        
        $data['counts'] = $datafile_content_parsed['counters'];
        $data['statistics']= $datafile_content_parsed['Statistics'];
        //$data['companies'] = $datafile_content_parsed['companies'];
        //$data['companies_count'] = count($datafile_content_parsed['companies']);
        $data['DataFileName'] =$datafile_name;
        $data['source_id'] =$source_id;
        
        //$data['emails_in_DB']=$datafile_content_parsed['emails_in_DB'];
        //$data['emails_duplicate']=$datafile_content_parsed['emails_duplicate'];
        //$data['bformated_emails']= $datafile_content_parsed['bformated_emails'];
        //$data['emails_in_DB_count']=$emails_in_DB;
        //$data['emails_duplicate_count']=$emails_duplicate;
        
        $data['main_content'] = 'admin/QcReports/result';
        $this->load->view('includes/template', $data);     
           
    }
    
    private function parse_and_verify_companies_in_csv($data_to_function)
    {
        $data = array ();
       $companies_array_k_id =array();
        $verified_emails = array();
        $nt_verified_emails = array();
        $companies=array();
        $company = array();
        $emails_arr = array();
        $emails_w = null;
        $emails_b = null;
        $emails_w_validation=null;
        $emails_b_validation=null;
        $bformated_emails =array();
        $categories=array();
        $transaction =array();
        $email_duplicate_in_file_count =0;
        $emails_duplicate=array();
        $email_in_DB_count =0;
        $emails_in_DB=array();
        $rawdata=array();
        
        $preg_emails='';
        
        //counters
        $emails_wformated_count=0;
        $emails_bformated_count=0;
        $loop_count =0;
        $non_empty_lines_count =0;
        $processed_lines_count =0;
        $no_emails_detected_count =0;
        
        $companies_inserted_count=0;
        $emails_inserted_count=0;
        $emails_extracted_count=0;
        $email_with_w_company_id_count = 0;
        $email_with_b_company_id_count = 0;
        $email_without_w_company_id_count = 0;
        $email_without_b_company_id_count = 0;
        $vemail_w_newly_inserted_count = 0;
        $vemail_b_newly_inserted_count = 0;
        $nvemail_w_newly_inserted_count = 0;
        $nvemail_b_newly_inserted_count = 0;
        
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
        
        $email_with_w_id_count=0;
        $email_with_b_id_count=0;
        $no_wFormated_e_count =0;
        $no_bFormated_e_count =0;
        $Company_w_id_CDB =0;//In Company DB
        $Company_b_id_CDB =0;//In Company DB
        $vCompany_w_url_DB='';
        $vCompany_b_url_DB='';
        $company_w_url_match_status =''; 
        $company_b_url_match_status =''; 
        $vCompany_w_id_DB =0;  //In verfied Email DB
        $vCompany_b_id_DB =0;  //In verfied Email DB
        $vEmail_w_id_DB =0;
        $ntvEmail_w_id_DB =0;
        $vEmail_w_new ='';
        $ntvEmail_w_new ='';
        $vEmail_b_id_DB =0;
        $ntvEmail_b_id_DB =0;
        $vEmail_b_new ='';
        $ntvEmail_b_new ='';
        $category_id = 0;
        $company_id = 0;
        $email_id = 0;
        
        $country = '';
        $category_name = '';
        $address = '';
        $telephone = 0;
        $email = '';
        $url = '';
        $url_f = '';
        $city='';
        $csv_line_status='empty';
        $csv_first_section='empty';
        $company_info=array ();
        
        $J=0;

       
        $source_id = $data_to_function['source_id'];
        $page = $data_to_function['page'];
        $limit_start = $data_to_function['limit_start'];
        $limit_end = $data_to_function['limit_end'];
        
        $this->load->library('email',array('fromEmail'=>'ahmed.elmalla@linkedemails.com'));
        $validation_status = $this->_validate_email_domain('projects@i-awcs.com');
        
        $csv_data = $this->Emailv_model->fread_as_array($data_to_function['csv_path']);
        $map = $this->Emailv_model->fread_as_array($data_to_function['map_path']); 
            
        $transaction = $this->Extraction_model->where(array('source_id'=>$source_id))->get();
        
        //$companies_table= $this->Company_model->fields(array('company_name','url','company_id'))->where(array('company_id >='=>$transaction['company_start_id'],'company_id <='=>$transaction['company_end_id']))->get_all();
        $companies_table= $this->Company_model->fields(array('company_name','url','company_id'))->get_all();
        //$verfied_emails_table= $this->Emailv_model->fields(array('email','email_id'))->where(array('email_id >='=>$transaction['emailv_start_id'],'email_id <='=>$transaction['emailv_end_id']))->get_all();
        $verfied_emails_table= $this->Emailv_model->fields(array('email','email_id','company_id'))->get_all();
        //$ntverfied_emails_table= $this->Emailntv_model->fields(array('email','email_id'))->where(array('email_id >='=>$transaction['emailnv_start_id'],'email_id <='=>$transaction['emailnv_end_id']))->get_all();
        $ntverfied_emails_table= $this->Emailntv_model->fields(array('email','email_id'))->get_all();
            
        
        $vEmails_array_k_email = $this->restruct_array_key($verfied_emails_table,'email','email_id');
        $vEmails_array_k_company = $this->restruct_array_key($verfied_emails_table,'email','company_id');
        $ntvEmails_array_k_email = $this->restruct_array_key($ntverfied_emails_table,'email','email_id');
        
        $companies_array_k_url = $this->restruct_array_key($companies_table,'url','company_id');
        $companies_array_k_name  = $this->restruct_array_key($companies_table,'company_name','url');
        $companies_array_k_id = $this->restruct_array_key($companies_table,'company_id','url');
        
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
        
        if ($limit_end > $total_lines_csv)
            $limit_end = $total_lines_csv;
        
        //for($i = $limit_start; $i < $limit_end ; ++$i)
        for($i = 0; $i < $total_lines_csv; ++$i)
        {
            $loop_count +=1;
          if (!empty($csv_data[$i]))
          {  
               $non_empty_lines_count +=1;
               $csv_data[$i]=strtolower($csv_data[$i]);
               $line_data1 = explode(",",$csv_data[$i]);
               
               $line_data1 = $this->array_triming($line_data1);
              
              // check if the first section of data isn't empty or equal zero 
              if ($line_data1[0] != "" && $line_data1[0] != '0')
              {
                 $processed_lines_count +=1;
                 //get emails 
                 $emails_arr = $this->get_wformated_emails_from_array($line_data1);
                 
                 if ($validation_status)
                     $emails_w_validation = $this->_validate_email_domain($emails_w);

//Well Formated---->                 
                 //Process Well formated emails caputed
                 if (!empty($emails_arr['wformated'])){
                   if (count($emails_arr['wformated'])>1)  
                   {
                      $emails_w = implode(',',$emails_arr['wformated']);
                      //counter
                       $emails_wformated_count = count ($emails_arr['wformated']) + $emails_wformated_count;
                   }else{
                       
                       //counter
                       $emails_wformated_count += 1;
                       $emails_w = array_shift($emails_arr['wformated']);
                       
                       if (array_key_exists($emails_w,$vEmails_array_k_email))
                       {
                          $vEmail_w_id_DB = $vEmails_array_k_email[$emails_w];
                          
                          
                              
                          //Get company ID from verified emails DB
                          $vCompany_w_id_DB = $vEmails_array_k_company[$emails_w];
                          
                           //Extract URL to search URLin the Companies DB 
                          $vCompany_w_url_extracted = $this->get_url_from_email($emails_w);
                          
                          if (array_key_exists($vCompany_w_url_extracted,$companies_array_k_url))
                             $Company_w_id_CDB = $companies_array_k_url [$vCompany_w_url_extracted];
                          
                          //Get company URL from DB based on the ID extracted from Email DB                           
                          if ($vCompany_w_id_DB !=0 ){
                             $vCompany_w_url_DB = strtolower($companies_array_k_id [$vCompany_w_id_DB]);
                              //$vCompany_w_url_extracted = $this->get_url_from_email($emails_w);
                          
                            //Check if both extracted url from email match that stored in companies DB entry for the same company ID
                              if ($vCompany_w_url_DB == $vCompany_w_url_extracted)
                               $company_w_url_match_status ='OK';
                              else
                               $company_w_url_match_status ='NO';

                          
                              $email_with_w_company_id_count += 1;
                              
                              
                          }elseif ( $Company_w_id_CDB!=0 )
                              $email_without_w_company_id_count += 1;
                          
                          if ($vEmail_w_id_DB !=0 && $transaction['emailv_start_id']!=0 && $transaction['emailv_end_id']!=0 )
                          {     
                              $email_with_w_id_count += 1;
                          
                              if ($vEmail_w_id_DB >= $transaction['emailv_start_id'] || $vEmail_w_id_DB <= $transaction['emailv_end_id'])
                              {
                                 $vEmail_w_new ='Yes';
                                 $vemail_w_newly_inserted_count += 1;
                              }
                          }else if ( $transaction['emailv_start_id']!=0 || $transaction['emailv_end_id']!=0)
                              $vEmail_w_new ='Err';
                        }
                       }
                       
                       if (array_key_exists($emails_w,$ntvEmails_array_k_email))
                       {
                          $ntvEmail_w_id_DB = $ntvEmails_array_k_email[$emails_w];
                          
                          if ($ntvEmail_w_id_DB !=0 && $transaction['emailnv_start_id']!=0 && $transaction['emailnv_end_id']!=0)
                          {   
                              $email_with_w_id_count += 1;
                          
                              if ($ntvEmail_w_id_DB >= $transaction['emailnv_start_id'] || $ntvEmail_w_id_DB <= $transaction['emailnv_end_id'])
                              {
                                 $ntvEmail_w_new ='Yes';
                                 $nvemail_w_newly_inserted_count += 1;
                              }else if ( $transaction['emailnv_start_id']!=0 || $transaction['emailnv_end_id']!=0)
                                 $ntvEmail_w_new ='Err';
                          }
                       }

                   }else
                     $no_wFormated_e_count +=1;
                 
//bad Formated---->                 
                 //Process Bad formated emails caputed
                 if (!empty($emails_arr['bformated'])){
                     
                    if (count($emails_arr['bformated'])>1)  
                    {  
                       $emails_b = implode(',',$emails_arr['bformated']);
                       $emails_bformated_count = count ($emails_arr['bformated']) + $emails_bformated_count;
                   }else{
                       
                       //counter
                       $emails_bformated_count += 1;
                       $emails_b = array_shift($emails_arr['bformated']);
                       
                       //Do email validation only if the a test email validate successfully, to avoid delays if there is DNS issue
                       if ($validation_status)
                          $emails_b_validation = $this->_validate_email_domain($emails_b);
                       
                       if (array_key_exists($emails_b,$vEmails_array_k_email))
                       {
                          $vEmail_b_id_DB = $vEmails_array_k_email[$emails_b];
                          $vCompany_b_id_DB = $vEmails_array_k_company[$emails_b];
                          
                          //Extract URL to search URLin the Companies DB 
                          $vCompany_b_url_extracted = $this->get_url_from_email($emails_b);
                          
                          if (array_key_exists($vCompany_b_url_extracted,$companies_array_k_url))
                             $Company_b_id_CDB = $companies_array_k_url [$vCompany_b_url_extracted];
                          
                         //Get company URL from DB based on the ID extracted from Email DB                           
                          if ($vCompany_b_id_DB !=0 ){
                              $vCompany_b_url_DB = strtolower($companies_array_k_id [$vCompany_b_id_DB]);
           
                              $email_with_b_company_id_count += 1;
                              
                              //Check if both extracted url from email match that stored in companies DB entry for the same company ID
                              if ($vCompany_b_url_DB == $vCompany_b_url_extracted)
                               $company_b_url_match_status ='OK';
                              else
                               $company_b_url_match_status ='NO'; 
                          }elseif ($Company_b_id_CDB !=0 )
                              $email_without_b_company_id_count += 1;
                              
                          if ($vEmail_b_id_DB >= $transaction['emailv_start_id'] || $vEmail_b_id_DB <= $transaction['emailv_end_id'])
                          {
                             $vEmail_b_new ='Yes';
                             $vemail_b_newly_inserted_count += 1;
                          }  
                       }
                       
                       if (array_key_exists($emails_b,$ntvEmails_array_k_email))
                       {
                          $ntvEmail_b_id_DB = $ntvEmails_array_k_email[$emails_b];
                           
                          if ($ntvEmail_b_id_DB >= $transaction['emailnv_start_id'] || $ntvEmail_b_id_DB <= $transaction['emailnv_end_id'])
                          {
                              $ntvEmail_b_new ='Yes';
                              $nvemail_b_newly_inserted_count += 1;
                          }    
                       }
                   }  
                    
                 }else
                     $no_bFormated_e_count +=1;
                 
                 
                 if (empty( $emails_arr['wformated']) && empty( $emails_arr['bformated']))
                    $no_emails_detected_count +=1;
                 
                 //if (!empty($emails_b) && !is_array($emails_b))
                  //  $emails_b_validation = $this->_validate_email_domain($emails_b);
                 
                 
                // Get company information
                 $company_name =  $this->clean_data($line_data1[$company_index]);
                 $email =  $this->clean_data($line_data1[$email_index]);
                 $url_f= $this->get_url_from_array($line_data1, $url_index);
                 $url= $this->clean_data($line_data1[$url_index]);
                 
                 $country =  $this->clean_data($line_data1[$country_index]);
                 $city =  $this->clean_data($line_data1[$city_index]);
                 $address = $this->clean_data($line_data1[$address_index]);
                 $telephone = $this->get_telephone_from_string(($line_data1[$telephone_index]));

                 $category_name = $this->clean_data($line_data1[$category_index]); 
               

                
                $csv_first_section='OK';
                    
                    //$email=stripslashes($email);
                  
                    // $this->_validate_email($email)                 
              }
               $csv_line_status='OK';
            
          }
           $data_to_store = array(
                        'company_name' => $company_name,
                        'address' => $address.','.$city,
                        'country' => $country,
                        'telephone' => $telephone,
                        'category_name' => $category_name,
                        'url_f' => $url_f,
                        'url' => $url,
                        'email' => $email,
                        'email_w' => $emails_w,
                        'email_b' => $emails_b,
                        'source_id' => $source_id,
                        'csv_line' => $csv_data[$i],
                        'csv_line_status'=>$csv_line_status,
                        'csv_first_section'=>$csv_first_section,
                        'line_number'=>$i ,
                        'e_w_validation'=>$emails_w_validation,
                        'e_b_validation'=>$emails_b_validation,
                        'vEmail_w_id_DB'=>$vEmail_w_id_DB,
                        'ntvEmail_w_id_DB'=>$ntvEmail_w_id_DB,
                        'vEmail_w_new'=>$vEmail_w_new,
                        'ntvEmail_w_new'=>$ntvEmail_w_new,
                        'vEmail_b_id_DB'=>$vEmail_b_id_DB,
                        'ntvEmail_b_id_DB'=>$ntvEmail_b_id_DB,
                        'vEmail_b_new'=>$vEmail_b_new,
                        'ntvEmail_b_new'=>$ntvEmail_b_new,
                        'vCompany_w_id_DB'=>$vCompany_w_id_DB,
                        'vCompany_b_id_DB'=>$vCompany_b_id_DB,
                        'company_w_url_match_status'=>$company_w_url_match_status,
                        'company_b_url_match_status'=>$company_b_url_match_status,
                        'vCompany_w_url_DB'=> $vCompany_w_url_DB,
                        'vCompany_b_url_DB'=> $vCompany_b_url_DB,
                        'Company_w_id_CDB'=>$Company_w_id_CDB,
                        'Company_b_id_CDB'=>$Company_b_id_CDB
                    );
           
            $rawdata[$i]=$data_to_store;
           
            $country = '';
            $category_name = '';
            $address = '';
            $telephone = 0;
            $email = '';
            $url = '';
            $url_f = '';
            $city='';
            $csv_line_status='empty';
            $csv_first_section='empty';
            $emails_arr =array();
            $emails_w= null;
            $emails_b= null;
            $emails_w_validation= null;
            $emails_b_validation= null;    
            $vEmail_w_id_DB =0;
            $ntvEmail_w_id_DB =0;
            $vEmail_w_new ='';
            $ntvEmail_w_new ='';
            $vEmail_b_id_DB =0;
            $ntvEmail_b_id_DB =0;
            $vEmail_b_new ='';
            $ntvEmail_b_new ='';
            $vCompany_w_id_DB =0;
            $vCompany_b_id_DB =0;
            $company_w_url_match_status ='';
            $company_b_url_match_status ='';
            $vCompany_w_url_DB='';
            $vCompany_b_url_DB='';
            $Company_w_id_CDB =0;
            $Company_b_id_CDB =0;
    }  
        $data['rawdata']= $rawdata;  
        $data['csv_count']= $total_lines_csv;
        $data['counters']= array(
            'emails_bformated_count'=>$emails_bformated_count,
            'emails_wformated_count'=>$emails_wformated_count,
            'loop_count'=> $loop_count,
            'non_empty_lines_count'=>$non_empty_lines_count,
            'processed_lines_count'=>$processed_lines_count,
            'no_emails_detected_count'=>$no_emails_detected_count,
            'email_without_w_company_id_count'=>$email_without_w_company_id_count,
            'email_without_b_company_id_count'=>$email_without_b_company_id_count,
            '$nvemail_b_newly_inserted_count'=>$nvemail_b_newly_inserted_count
            );
        
        $data['Statistics']= array(
            'non_empty_data_percentage'=>($processed_lines_count/$total_lines_csv)*100,
            'valuable_data_percentage'=>(($processed_lines_count-$no_emails_detected_count)/$total_lines_csv)*100,
            'emails_wformated_percentage'=>($emails_wformated_count/($processed_lines_count-$no_emails_detected_count))*100,
            'emails_bformated_percentage'=>($emails_bformated_count/($processed_lines_count-$no_emails_detected_count))*100,
            'total_emails_percentage'=>(($emails_bformated_count+$emails_wformated_count)/($processed_lines_count-$no_emails_detected_count))*100,
            'read_csv_percentage'=> ($loop_count/$total_lines_csv)*100,
            'new_emails_inserted_percentage'=>(($vemail_b_newly_inserted_count+$nvemail_b_newly_inserted_count+$vemail_w_newly_inserted_count+$nvemail_w_newly_inserted_count)/($emails_bformated_count+$emails_wformated_count))*100,
            'new_validated_emails_inserted_percentage'=>(($vemail_b_newly_inserted_count+$vemail_w_newly_inserted_count)/($emails_bformated_count+$emails_wformated_count))*100,
            'new_nvalidated_emails_inserted_percentage'=>(($nvemail_b_newly_inserted_count+$nvemail_w_newly_inserted_count)/($emails_bformated_count+$emails_wformated_count))*100,
            'old_emails_percentage'=>((($emails_bformated_count+$emails_wformated_count)-($vemail_b_newly_inserted_count+$nvemail_b_newly_inserted_count+$vemail_w_newly_inserted_count+$nvemail_w_newly_inserted_count))/($emails_bformated_count+$emails_wformated_count))*100,
            
            'email_without_w_company_id_count'=>$email_without_w_company_id_count,
            'email_without_b_company_id_count'=>$email_without_b_company_id_count
            );
        
        return $data;
}//parse csv

    
    //Extract emails from an array
    private function get_wformated_emails_from_array($line)
    {
       $rank=100;
       $wf_emails = array(); 
       $bf_emails = array();
       $clean_line =$this->clean_data($line);
      //$sucess =preg_match(EMAIL_PATTERN, $clean_line,$result);
      $result = preg_grep(EMAIL_PATTERN, $clean_line);
       if ($result) 
      {
         //if (is_array($result))
         foreach($result as $key => $value)
         {
             $email = filter_var($value, FILTER_VALIDATE_EMAIL)? $value : '';
             if($email!='')
                 $wf_emails[$value]=strtolower($value);
             else
                 $bf_emails[$value]=strtolower($value);
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
      $free_emails=array('gmail','yahoo','hotmail','emirates');

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
       $free_emails=array('gmail','yahoo','hotmail','emirates');
       
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
      //$sucess = ;
       //$result = array();         
      if (is_array($data))
      {
        for ($i=0;$i<count($data);++$i)
        {
           $data[$i] = preg_replace('/\s+/', '', $data[$i]);
        } 
        $result = $data;
      }else {
        $result = preg_replace('/\s+/', '', $data);
        $result = trim($result);
      }
      
            
      return $result;
    }
    
    private function get_telephone_from_string($string)
    {
       $tel = '';
       //$result = array(); 
     if (strpos($string,':'))   
      list($str, $tel) = explode(':', $string);
      
      return $this->clean_data($tel);
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
         $domain_validated = $this->email->verify_domain($email);

         if ($domain_validated)
             return 1;
         else
             return 0;
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
        
        $free_emails=array('gmail','yahoo','hotmail','emirates');
       
        list($full_name, $url) = explode('@', $email);
        list($company, $url_ext) = explode('.', $url);
                
       if (!in_array($company,$free_emails))
          return strtolower($company);//","url"=>"$url");                         
       else
          return '';  
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
    
    private function get_company_name_from_url($url)
    {
        //$url='';
        $company='';
        $url_ext='';
        
        $free_emails=array('gmail','yahoo','hotmail','emirates');
       
        //list($full_name, $url) = explode('@', $email);
        list($company, $url_ext) = explode('.', $url);
         
       if (strpos('.',$company))        
         list($company, $url_ext) = explode('.', $company);
           
       
       if (!in_array($company,$free_emails))
          return strtolower($company);//","url"=>"$url");                         
       else
          return '';
       
    }
    
    private function calculate_email_rank($email,$rank)
    {
        
        $url='';
        $full_name='';
        
        list($full_name, $url) = explode('@', $email);
        
        if ($this->string_email_name_contains(array('job','jobs','hr','recruitment','cv'),$full_name)) {
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
    
    
    
    
}