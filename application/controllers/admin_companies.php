<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_companies extends CI_Controller {
 
    /**
    * Responsable for auto load the model
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
       //we will use this when create an upload files table 
       $this->load->model('Emailv_model');
       $this->load->model('Category_model');
       $this->load->model('Exports_model');
       
       $this->load->model('Company_model');
       //$Emails = $this->Email_model->as_array()->set_cache('all_emails',3600)->get_all();
        
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
        
        $company_prefix='company_';
        $this->output->enable_profiler(TRUE);
        
        
        //$u_path=dirname($_SERVER["SCRIPT_FILENAME"]).'/uploads/'; 
        //all the posts sent by the view
        //$value = ($condition) ? 'Truthy Value' : 'Falsey Value';
        
        //$source_id = ($this->input->post('source_id') !=null) ? $this->input->post('source_id'): 0 ;
          
        
        
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {  
            $category_id = ($this->input->post('category_id') !=null) ? $this->input->post('category_id'): 0 ;        
            $country = ($this->input->post('country') !=null) ? $this->input->post('country'): '' ;
            $search_string = $this->input->post('search_string');        
            $order = $this->input->post('order'); 
            $order_type = $this->input->post('order_type'); 
            $posted=true;
        }else {
            $category_id =false;
            $country =false;
            $search_string =false;
            $order =false;
            $order_type =false;
            $posted=false;
        }
            
        
        //pagination settings
        $config['per_page'] = 25;
        $config['base_url'] = base_url().'index.php/admin/Companies';
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        //limit end
        $page = $this->uri->segment(4);

        //math to get the initial record to be select in the database
        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0){
            $limit_end = 0;
        } 

        //if order type was changed
        if($order_type){
            $filter_session_data[$company_prefix.'order_type'] = $order_type;
        }
        else{
            //we have something stored in the session? 
            if($this->session->userdata($company_prefix.'order_type')){
                $order_type = $this->session->userdata($company_prefix.'order_type');    
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
if( $posted!==false && $country !== false && $category_id !== false && $search_string !== false && $order !== false || $this->uri->segment(4) == true){ 
           
            /*
            The comments here are the same for line 79 until 99

            if post is not null, we store it in session data array
            if is null, we use the session data already stored
            we save order into the the var to load the view with the param already selected       
            */

           
           
           if($category_id !== 0){
                $filter_session_data[$company_prefix.'category_selected'] = $category_id;
            }else{
                $category_id = ($this->session->userdata($company_prefix.'category_selected')!= null) ? $this->session->userdata($company_prefix.'category_selected') : 1;
            }
            $data['category_selected'] = $category_id; 

            if($country !== ''){
                $filter_session_data[$company_prefix.'country_selected'] = $country;
            }else{
                $country = ($this->session->userdata($company_prefix.'country_selected')!= '') ? $this->session->userdata($company_prefix.'country_selected') : '';
            }
            $data['country_selected'] = $country; 
            
           if($search_string){
                $filter_session_data[$company_prefix.'search_string_selected'] = $search_string;
            }else{
                $search_string = $this->session->userdata($company_prefix.'search_string_selected');
            }
            $data['search_string_selected'] = $search_string;

            if($order){
                $filter_session_data[$company_prefix.'order'] = $order;
            }
            else{
                $order = $this->session->userdata($company_prefix.'order');
            }
            $data['order'] = $order;

           
            //save session data into the session
            if (isset($filter_session_data))
            {
             $this->session->set_userdata($filter_session_data);
            }
            

            //fetch manufacturers data into arrays
            //$data['Email_Owner'] = $this->Owner_model->as_dropdown('category')->get_all();
            $data['Countries'] = array_unique($this->Company_model->as_dropdown('country')->get_all());
            //array_unshift($data['Countries'],'');
            $data['Categories'] = $this->Category_model->as_dropdown('category')->get_all();
            //array_unshift($data['Categories'],'');
            //TO DO: nneed to activate search and order
             
                    //$this->_model->count_files($u_path, $search_string, $order);
            

            //fetch sql data into arrays
            if($search_string){
                $data['count_companies']= $this->Company_model->where(array('company_name LIKE'=>'%'.$search_string.'%','category_id'=>$category_id))->count_rows();
                if($order){
                    $data['Companies'] = $this->Company_model->where(array('company_name LIKE'=>'%'.$search_string.'%','category_id'=>$category_id))->order_by($order,$order_type)->paginate($config['per_page'],$data['count_companies']);
                    
                         //get_files($u_path, $search_string, $order, $order_type,$limit_end ,$config['per_page']);        
                }else{
                    $data['Companies'] = $this->Company_model->where(array('company_name LIKE'=>'%'.$search_string.'%','category_id'=>$category_id))->paginate($config['per_page'],$data['count_companies']);
                              //get_files($u_path, $search_string, '', $order_type, $limit_end,$config['per_page']);           
                }
                
            }else{
                $data['count_companies']= $this->Company_model->where(array('category_id'=>$category_id,'country'=>$country))->count_rows();
               if($order){
                    $data['Companies'] = $this->Company_model->where(array('category_id'=>$category_id,'country'=>$country))->order_by($order,$order_type)->paginate($config['per_page'],$data['count_companies']);//->order_by($order_type));
                              //get_files($u_path, '', $order, $order_type, $limit_end,$config['per_page']); 
                    //get_products($manufacture_id, $search_string, $order, $order_type, $config['per_page'],$limit_end); 
                }else{
                    $data['Companies'] = $this->Company_model->where(array('category_id'=>$category_id,'country'=>$country))->paginate($config['per_page'],$data['count_companies']);
                            //get_files($u_path, '', '', $order_type, $limit_end,$config['per_page']);           
                }
                
            }
            
            $config['total_rows'] = $data['count_companies'];

        }else{

            //clean filter data inside section
            $filter_session_data[$company_prefix.'category_selected'] = null;
            //$filter_session_data['source_selected'] = null;
            $filter_session_data[$company_prefix.'counrty_selected'] = null;
            $filter_session_data[$company_prefix.'search_string_selected'] = null;
            $filter_session_data[$company_prefix.'order'] = null;
            $filter_session_data[$company_prefix.'order_type'] = null;
            
            $filter_session_data[$company_prefix.'country'] = null;
            $filter_session_data[$company_prefix.'category_id'] = null;
            
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['category_selected'] = 0;
            //$data['source_selected'] = 0;
            $data['country_selected'] = 0;
            $data['order'] = 'id';

            //fetch sql data into arrays
            //$data['manufactures'] = $this->manufacturers_model->get_manufacturers();
            
            $data['Countries'] = array_unique($this->Company_model->as_dropdown('country')->get_all());
            $data['Categories'] = $this->Category_model->as_dropdown('category')->get_all();
            
            $data['count_companies']= $this->Company_model->count_rows();
            $data['Companies'] =$this->Company_model->paginate($config['per_page'],$data['count_companies']);//->order_by($order_type); 
                    //$this->eextract_model->get_files('', '', '', $order_type ,$limit_end,$config['per_page']);        
            $config['total_rows'] = $data['count_companies'];

        }//!isset($manufacture_id) && !isset($search_string) && !isset($order)

        //initializate the panination helper 
        $this->pagination->initialize($config);   

        //load the view
        $data['all_pages'] = $this->Company_model->all_pages; // will output links to all pages like this model: "< 1 2 3 4 5 >". It will put a link if the page number is not the "current page"
        $data['previous_page'] =         $this->Company_model->previous_page; // will output link to the previous page like this model: "<". It will only put a link if there is a "previous page"
        $data['next_page'] =         $this->Company_model->next_page;
        $data['page']=$page;
        $data['page_count'] =$config['per_page'];
        $data['main_content'] = 'admin/Companies/list';
        $this->load->view('includes/template', $data);  

    }//index

    
    //clear selections
    public function clear()
    {
        /*
         * TODO:
         * must check agin bugs when owner_id, and other ids become zero.
         */
  
            //clean filter data inside section
          //clean filter data inside section
        
            $company_prefix='company_';
            $filter_session_data[$company_prefix.'category_selected'] = null;
            //$filter_session_data['source_selected'] = null;
            $filter_session_data[$company_prefix.'country_selected'] = null;
            $filter_session_data[$company_prefix.'search_string_selected'] = null;
            $filter_session_data[$company_prefix.'order'] = null;
            $filter_session_data[$company_prefix.'order_type'] = null;

            
            $filter_session_data[$company_prefix.'country'] = null;
            $filter_session_data[$company_prefix.'category_id'] = null;
            
            $this->session->set_userdata($filter_session_data);

            redirect(base_url().'index.php/admin/Companies/index');


    }//clear
    
    public function add()
    {
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {

            //form validation
            $this->form_validation->set_rules('company_name', 'company_name', 'required');
            //$this->form_validation->set_rules('owner_id', 'owner_id', 'required|numeric');
            $this->form_validation->set_rules('country', 'country', 'required');
            $this->form_validation->set_rules('url', 'url', 'required');
            $this->form_validation->set_rules('address', 'address', 'required');
             $this->form_validation->set_rules('telephone', 'telephone', 'required');
              $this->form_validation->set_rules('activities', 'activities', 'required');
            $this->form_validation->set_rules('category_id', 'category_id', 'required|numeric');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');

            //if the form has passed through the validation
            if ($this->form_validation->run())
            {
                $data_to_store = array(
                    'company_name' => $this->input->post('company_name'),
                    'country' => $this->input->post('country'),
                    'url' => $this->input->post('url'),
                    'address' => $this->input->post('address'),
                    'telephone' => $this->input->post('telephone'),
                    'activities' => $this->input->post('activities'),
                    'category_id' => $this->input->post('category_id')
                   
                );
                //if the insert has returned true then we show the flash message
                if($this->Company_model->insert($data_to_store)=== 0){
                    $data['flash_message'] = TRUE;
                    $this->session->set_flashdata('flash_message','sucess');
                    
                }else{
                    $data['flash_message'] = FALSE;
                    $this->session->set_flashdata('flash_message','error');
                }
                
                
                
                redirect(base_url().'index.php/admin/Company/add');
            }

        }
        //fetch manufactures data to populate the select field
        $data['Categories'] = $this->Category_model->as_dropdown('category')->get_all();
        //load the view
        $data['main_content'] = 'admin/Companies/add';
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
    
    
    /**
    * Export db field to text file
    * @return void
    */
    public function export_url_from_DB()
    {
        
        $f_data = 'url';
        $fh = 0;
        $count =0;
        $export_dir = dirname($_SERVER["SCRIPT_FILENAME"]).'/exports/';
        $file_name =$f_data.'_'.date("Ymd").'.txt';
        $data['Companies_url'] = array();
        $data['error'] = '';
        
        $data['db_count_urls']= $this->Company_model->count_rows();
        $data['Companies_url'] =$this->Company_model->fields($f_data)->get_all();//->order_by($order_type); 
        $data['file']=$export_dir.$file_name;
        
        if (($fh = fopen($data['file'], "wb")) !== false) { 
            foreach($data['Companies_url'] as $key => $value)
            {
                if ($value[$f_data] !== '' && $value[$f_data] !== '0')
                {
                 $value[$f_data] = trim(preg_replace('/\s+/', ' ', $value[$f_data])) ;
                 fwrite($fh, $value[$f_data]."\n");
                 $count +=1;
                }
                
                //fwrite($fh, $value[$f_data].PHP_EOL);
            }
             fclose($fh); 
        }
        
        
        $data['count_urls'] = $count;
        $data['main_content'] = 'admin/exports/list';
                
        $this->load->view('includes/template', $data); 
    }//edit

}