<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_upload extends CI_Controller {
 
    /**
    * Responsable for auto load the model
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('upload_model');
        
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
        //load the view
        $data['error'] = '';
        $data['main_content'] = 'admin/upload/list';
        $this->load->view('includes/template', $data);  

    }//index

  
    function do_uploads()
    {
        //check 
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {
          
          
                
        $config['upload_path']= dirname($_SERVER["SCRIPT_FILENAME"]).'/uploads/';
        $config['allowed_types']='rb|pdf|txt|csv';
        $config['max_size']='10000000';
        $config['overwrite']= TRUE;
        
        $this->load->library('upload',$config);
        
        if(!$this->upload->do_upload())
        {
            $data['errors']= $this->upload->display_errors();
            
        }else{
            $data['uploaded_data']=  $this->upload->data();
            //$result=$this->resize($data['upload_data']['full_path'],$data['upload_data']['file_name']);
            //if ($result != true){
            //    $data['errors']=$result ;
           //}else {$data['error']=null;}
           
             
        
        }
        
       }
       
       $data['main_content'] = 'admin/upload/list';
       $this->load->view('includes/template', $data);
    }
    
   
   public function show_Files()
   {
       $files_prefix='ufile_';
               $u_path=dirname($_SERVER["SCRIPT_FILENAME"]).'/uploads/'; 
        //all the posts sent by the view
        //$manufacture_id = $this->input->post('manufacture_id');        
        $search_string = $this->input->post('search_string');        
        $order = $this->input->post('order'); 
        $order_type = $this->input->post('order_type'); 

        //pagination settings
        $config['per_page'] = 59;
        $config['base_url'] = base_url().'admin/upload';
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        //limit end
        $page = $this->uri->segment(3);

        //math to get the initial record to be select in the database
        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0){
            $limit_end = 0;
        } 

        //if order type was changed
        if($order_type){
            $filter_session_data[$files_prefix.'order_type'] = 'SORT_'.strtoupper($order_type);
        }
        else{
            //we have something stored in the session? 
            if($this->session->userdata($files_prefix.'order_type')){
                $order_type = $this->session->userdata($files_prefix.'order_type');    
            }else{
                //if we have nothing inside session, so it's the default "Asc"
                $order_type = 'SORT_ASC';    
            }
        }
        //make the data type var avaible to our view
        $data['order_type_selected'] = $order_type;        


        //we must avoid a page reload with the previous session data
        //if any filter post was sent, then it's the first time we load the content
        //in this case we clean the session filter data
        //if any filter post was sent but we are in some page, we must load the session data

        //filtered && || paginated
        if( $search_string !== false && $order !== false || $this->uri->segment(3) == true){ 
           
            /*
            The comments here are the same for line 79 until 99

            if post is not null, we store it in session data array
            if is null, we use the session data already stored
            we save order into the the var to load the view with the param already selected       
            */

            

            if($search_string){
                $filter_session_data[$files_prefix.'search_string_selected'] = $search_string;
            }else{
                $search_string = $this->session->userdata($files_prefix.'search_string_selected');
            }
            $data['search_string_selected'] = $search_string;

            if($order){
                $filter_session_data[$files_prefix.'order'] = $order;
            }
            else{
                $order = $this->session->userdata($files_prefix.'order');
            }
            $data['order'] = $order;

            //save session data into the session
            if (isset($filter_session_data))
            {
             $this->session->set_userdata($filter_session_data);
            }
            

            //fetch manufacturers data into arrays
            //$data['manufactures'] = $this->manufacturers_model->get_manufacturers();

            $data['count_files']= $this->upload_model->count_files($u_path, $search_string, $order);
            $config['total_rows'] = $data['count_files'];

            //fetch sql data into arrays
            if($search_string){
                if($order){
                    $data['UploadedFiles'] = $this->upload_model->get_files($u_path, $search_string, $order, $order_type,$limit_end ,$config['per_page']);        
                }else{
                    $data['UploadedFiles'] = $this->upload_model->get_files($u_path, $search_string, '', $order_type, $limit_end,$config['per_page']);           
                }
            }else{
               if($order){
                    $data['UploadedFiles'] = $this->upload_model->get_files($u_path, '', $order, $order_type, $limit_end,$config['per_page']);        
                }else{
                    $data['UploadedFiles'] = $this->upload_model->get_files($u_path, '', '', $order_type, $limit_end,$config['per_page']);           
                }
            }

        }else{

            //clean filter data inside section
            //$filter_session_data['manufacture_selected'] = null;
            $filter_session_data[$files_prefix.'search_string_selected'] = null;
            $filter_session_data[$files_prefix.'order'] = null;
            $filter_session_data[$files_prefix.'order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            //$data['manufacture_selected'] = 0;
            $data['order'] = 'id';

            //fetch sql data into arrays
            //$data['manufactures'] = $this->manufacturers_model->get_manufacturers();
            $data['count_files']= $this->upload_model->count_files();
            $data['UploadedFiles'] = $this->upload_model->get_files('', '', '', $order_type ,$limit_end,$config['per_page']);        
            $config['total_rows'] = $data['count_files'];

        }//!isset($manufacture_id) && !isset($search_string) && !isset($order)

        //initializate the panination helper 
        $this->pagination->initialize($config);   

        //load the view
        $data['main_content'] = 'admin/upload/show';
        $this->load->view('includes/template', $data);  
   }


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

   
    public function open($file)
    {
        //product id 
        $id = $this->uri->segment(4);
        //$this->admin_model->delete_product($id);
        redirect('admin/main');
    }
    
    public function delete($file)
    {
        //product id 
        $id = $this->uri->segment(4);
        //$this->admin_model->delete_product($id);
        redirect('admin/main');
    }//edit

}