<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
class Extraction_model extends MY_Model
{
	public function __construct()
	{
        $this->table = 'emails_upload_log';
        $this->primary_key = 'id';
        $this->has_one['source'] = array('local_key'=>'source_id', 'foreign_key'=>'id', 'foreign_model'=>'Source_model');
        $this->timestamps = TRUE;
        $this->return_as =  'array';
        
        $this->protected = array('id');
        $this->cache_driver = 'redis';
        //By default, MY_Model uses the files (CodeIgniter's file driver) to cache result. If you want to change the way it stores the cache, you can change the $cache_driver property to whatever CodeIgniter cache driver you want to use.

        $this->cache_prefix = 'mm';
        //$this->has_one['author'] = 'User_model';
        
        // // $this->has_one['details'] = array('User_details_model','user_id','id');
        // $this->has_one['details'] = array('model'=>'User_details_model','foreign_key'=>'user_id','local_key'=>'id');

		parent::__construct();
	}

    public function insert_dummy()
    {
        $insert_data = array(
            array(
                'user_id' => '1',
                'title' => 'First title',
                'content' => 'This is content for first title'
            ),
            array(
                'user_id' => '3',
                'title' => 'Another title',
                'content' => 'This is content for another title'
            ),
            array(
                'user_id' => '3',
                'title' => 'One more title',
                'content' => 'This is content for one more title'
            ),
            array(
                'user_id' => '5',
                'title' => 'This one has a title too',
                'content' => 'This is content for this title too'
            ),
            array(
                'user_id' => '5',
                'title' => 'How about this title',
                'content' => 'This is content for how about this title'
            ),
        );
        $this->db->insert_batch($this->table, $insert_data);
    }
	

}
/* End of file '/User_model.php' */
/* Location: ./application/models//User_model.php */