<?php defined('BASEPATH') OR exit('No direct script access allowed.');



class MY_E {

    public $phpmailer;  // This property has been made public for testing purposes.

    protected static $default_properties = array(
        'useragent' => 'CodeIgniter',
        'mailpath' => '/usr/sbin/sendmail',
        'protocol' => 'smtp',
        'smtp_host' => 'node01.facesharedeu1.com',
        'smtp_user' => 'ahmed.elmalla@linkedemails.com',
        'smtp_pass' => '',
        'wordwrap' => TRUE,
        'alt_message' => '',
        'validate' => FALSE,
        'priority' => 3,
        'newline' => "\n",
        'crlf' => "\n",
        'dsn' => FALSE,
        'send_multipart' => TRUE,
        'smtp_auth' => TRUE,
        'smtp_conn_options' => array()

    );

    protected $properties = array();

    protected $mailer_engine = 'codeigniter';
    protected $CI;
    protected $_is_ci_3 = NULL;

    protected static $protocols = array('mail', 'sendmail', 'smtp');
    protected static $mailtypes = array('html', 'text');
    

    //Added by Ahmed
    //
    //                        
    private $fromEmail;
   private $htmlBody = '';
   private $textBody = '';
    // The Constructor ---------------------------------------------------------

    public function __construct($config = array()) {

        $this->_is_ci_3 = (bool) ((int) CI_VERSION >= 3);

        $this->CI = get_instance();
        
        $this->CI->load->helper('email');
        $this->CI->load->helper('html');
        
       //Added by Ahmed,to to be corrected
        $this->fromEmail ='Ahmed.elmalla@linkedemails.com';
        $this->CI->load->library('Smtp_validation');

        if (!is_array($config)) {
            $config = array();
        }

        // Wipe out certain properties that are declared within the parent class.
        // These properties would be accessed by magic.
        foreach (array_keys(self::$default_properties) as $name) {

            if (property_exists($this, $name)) {
                unset($this->{$name});
            }
        }

        $this->properties = self::$default_properties;
        $this->refresh_properties();

        $this->_safe_mode = (!is_php('5.4') && ini_get('safe_mode'));

        if (!isset($config['charset'])) {
            $config['charset'] = config_item('charset');
        }

        $this->initialize($config);

        log_message('info', 'Email Class Initialized (Engine: '.$this->mailer_engine.')');
    }

    // Triggers the setter functions to do their job.
    protected function refresh_properties() {

        foreach (array_keys(self::$default_properties) as $name) {
            $this->{$name} = $this->{$name};
        }
    }


    // The Destructor ----------------------------------------------------------

    public function __destruct() {

        if (is_callable('parent::__destruct')) {
            parent::__destruct();
        }
    }


    // Magic -------------------------------------------------------------------

    function __set($name, $value) {

        $method = 'set_'.$name;

        if (is_callable(array($this, $method))) {
            $this->$method($value);
        } else {
            $this->properties[$name] = $value;
        }
    }

    function __get($name) {

        if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        } else {
            throw new OutOfBoundsException('The property '.$name.' does not exists.');
        }
    }

    public function __isset($name) {

        return isset($this->properties[$name]);
    }

    public function __unset($name) {

        $this->$name = null;

        if (array_key_exists($name, $this->properties)) {
            unset($this->properties[$name]);
        } else {
            unset($this->$name);
        }
    }


    // Keep the API Fluent -----------------------------------------------------

    /**
     * An empty method that keeps chaining, the parameter does the desired operation as a side-effect.
     *
     * @param   mixed   $expression     A (conditional) expression that is to be executed.
     * @return  object                  Returns a reference to the created library instance.
     */
    public function that($expression = NULL) {

        return $this;
    }


    // Initialization & Clearing -----------------------------------------------

    public function initialize($config = array()) {

        if (!is_array($config)) {
            $config = array();
        }

        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }

        $this->clear();

        return $this;
    }

    public function clear($clear_attachments = false) {

        $clear_attachments = !empty($clear_attachments);

        parent::clear($clear_attachments);

        if ($this->mailer_engine == 'phpmailer') {

            $this->phpmailer->clearAllRecipients();
            $this->phpmailer->clearReplyTos();
            if ($clear_attachments) {
                $this->phpmailer->clearAttachments();
            }

            $this->phpmailer->clearCustomHeaders();

            $this->phpmailer->Subject = '';
            $this->phpmailer->Body = '';
            $this->phpmailer->AltBody = '';
        }

        return $this;
    }


   
    
}
