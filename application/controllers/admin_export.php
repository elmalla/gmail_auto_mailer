<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_export extends CI_Controller {
 
    /**
    * Responsable for auto load the model
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Exports_model');
        
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
       $files_prefix='efile_';
       $u_path=dirname($_SERVER["SCRIPT_FILENAME"]).'/exports/'; 
        //all the posts sent by the view
        //$manufacture_id = $this->input->post('manufacture_id');        
        $search_string = $this->input->post('search_string');        
        $order = $this->input->post('order'); 
        $order_type = $this->input->post('order_type'); 

        //pagination settings
        $config['per_page'] = 20;
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
        $page = $this->uri->segment(4);

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

            $data['count_files']= $this->Exports_model->count_files($u_path, $search_string, $order);
            $config['total_rows'] = $data['count_files'];

            //fetch sql data into arrays
            if($search_string){
                if($order){
                    $data['ExportedFiles'] = $this->Exports_model->get_files($u_path, $search_string, $order, $order_type,$limit_end ,$config['per_page']);        
                }else{
                    $data['ExportedFiles'] = $this->Exports_model->get_files($u_path, $search_string, '', $order_type, $limit_end,$config['per_page']);           
                }
            }else{
               if($order){
                    $data['ExportedFiles'] = $this->Exports_model->get_files($u_path, '', $order, $order_type, $limit_end,$config['per_page']);        
                }else{
                    $data['ExportedFiles'] = $this->Exports_model->get_files($u_path, '', '', $order_type, $limit_end,$config['per_page']);           
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
            $data['count_files']= $this->Exports_model->count_files();
            $data['ExportedFiles'] = $this->Exports_model->get_files('', '', '', $order_type ,$limit_end,$config['per_page']);        
            $config['total_rows'] = $data['count_files'];

        }//!isset($manufacture_id) && !isset($search_string) && !isset($order)

        //initializate the panination helper 
        $this->pagination->initialize($config);   

        //load the view
        $data['main_content'] = 'admin/exports/show';
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
    
    //Download an exported fie on computer
    public function download_file()
    {
      $name = $this->uri->segment(4);  
      $path=  dirname($_SERVER["SCRIPT_FILENAME"]).'/exports/';;
      // make sure it's a file before doing anything!
      
      $fullname = $path.$name; 
      if(is_file($fullname))
      {
        // required for IE
        //if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off'); }

        // get the file mime type using the file extension
        $this->load->helper('file');

        $mime = get_mime_by_extension($path);

        $fd = fopen($fullname, "rb");
        if ($fd) {
            $fsize = filesize($fullname);
            
            // Build the headers to push out the file properly.
            header('Pragma: public');     // required
            header('Expires: 0');         // no cache
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($path)).' GMT');
            header('Cache-Control: private',false);
            header('Content-Type: '.$mime);  // Add the mime type from Code igniter.
            header('Content-Disposition: attachment; filename="'.basename($name).'"');  // Add the file name
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: '.filesize($fullname)); // provide file size
            header('Connection: close');
            //header("Content-length: $fsize");
            //header("Cache-control: private"); //use this to open files directly
            
            while(!feof($fd)) {
                $buffer = fread($fd, 1*(1024*1024));
                echo $buffer;
                ob_flush();
                flush();    //These two flush commands seem to have helped with performance
            }
        }
        else {
            echo "Error opening file";
        }
        fclose($fd);
        
        /*
        $fd = fopen($fullname, "rb");
        if ($fd) {
            $fsize = filesize($fullname);
            $path_parts = pathinfo($fullname);
            $ext = strtolower($path_parts["extension"]);
            switch ($ext) {
                case "pdf":
                header("Content-type: application/pdf");
                break;
                case "zip":
                header("Content-type: application/zip");
                break;
                default:
                header("Content-type: application/octet-stream");
                break;
            }
            header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\"");
            header("Content-length: $fsize");
            header("Cache-control: private"); //use this to open files directly
            while(!feof($fd)) {
                $buffer = fread($fd, 1*(1024*1024));
                echo $buffer;
                ob_flush();
                flush();    //These two flush commands seem to have helped with performance
            }
        }
        else {
            echo "Error opening file";
        }
        fclose($fd);
       */ 
        
       
    }
  }

}