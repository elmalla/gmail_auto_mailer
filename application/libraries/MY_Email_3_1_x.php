<?php defined('BASEPATH') OR exit('No direct script access allowed.');

/**
 * CodeIgniter compatible email-library powered by PHPMailer.
 *
 * @author Ivan Tcholakov <ivantcholakov@gmail.com>, 2012-2017.
 * @license The MIT License (MIT), http://opensource.org/licenses/MIT
 * @link https://github.com/ivantcholakov/codeigniter-phpmailer
 *
 * This class is intended to be compatible with CI 3.1.x.
 */

class MY_Email extends CI_Email {

    public $phpmailer;  // This property has been made public for testing purposes.

    protected static $default_properties = array(
        'useragent' => 'CodeIgniter',
        'mailpath' => '/usr/sbin/sendmail',
        'protocol' => 'smtp',
        'smtp_host' => '',
        'smtp_user' => '',
        'smtp_pass' => '',
        'smtp_port' => 465,
        'smtp_timeout' => 5,
        'smtp_keepalive' => FALSE,
        'smtp_crypto' => '',
        'wordwrap' => TRUE,
        'wrapchars' => 76,
        'mailtype' => 'text',
        'charset' => 'UTF-8',
        'multipart' => 'mixed',
        'alt_message' => '',
        'validate' => FALSE,
        'priority' => 3,
        'newline' => "\n",
        'crlf' => "\n",
        'dsn' => FALSE,
        'send_multipart' => TRUE,
        'bcc_batch_mode' => FALSE,
        'bcc_batch_size' => 200,
        'smtp_debug' => 0,
        'encoding' => '8bit',
        'smtp_auto_tls' => true,
        'smtp_conn_options' => array(),
        'dkim_domain' => '',
        'dkim_private' => '',
        'dkim_private_string' => '',
        'dkim_selector' => '',
        'dkim_passphrase' => '',
        'dkim_identity' => '',
    );

    protected $properties = array();

    protected $mailer_engine = 'codeigniter';
    protected $CI;
    protected $_is_ci_3 = NULL;

    protected static $protocols = array('mail', 'sendmail', 'smtp');
    protected static $mailtypes = array('html', 'text');
    protected static $encodings_ci = array('8bit', '7bit');
    protected static $encodings_phpmailer = array('8bit', '7bit', 'binary', 'base64', 'quoted-printable');


    // The Constructor ---------------------------------------------------------

    public function __construct(array $config = array()) {

        $this->_is_ci_3 = (bool) ((int) CI_VERSION >= 3);

        $this->CI = get_instance();
        $this->CI->load->helper('email');
        $this->CI->load->helper('html');

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

    public function initialize(array $config = array()) {

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


    // Prepare & Send a Message ------------------------------------------------

    public function from($from, $name = '', $return_path = NULL) {

        $from = (string) $from;
        $name = (string) $name;
        $return_path = (string) $return_path;

        if ($this->mailer_engine == 'phpmailer') {

            if (preg_match( '/\<(.*)\>/', $from, $match)) {
                $from = $match['1'];
            }

            if ($this->validate) {

                $this->validate_email($this->_str_to_array($from));

                if ($return_path) {
                    $this->validate_email($this->_str_to_array($return_path));
                }
            }

            $this->phpmailer->setFrom($from, $name, 0);

            if (!$return_path) {
                $return_path = $from;
            }

            $this->phpmailer->Sender = $return_path;

        } else {

            parent::from($from, $name, $return_path);
        }

        return $this;
    }

    public function reply_to($replyto, $name = '') {

        $replyto = (string) $replyto;
        $name = (string) $name;

        if ($this->mailer_engine == 'phpmailer') {

            if (preg_match( '/\<(.*)\>/', $replyto, $match)) {
                $replyto = $match['1'];
            }

            if ($this->validate) {
                $this->validate_email($this->_str_to_array($replyto));
            }

            if ($name == '') {
                $name = $replyto;
            }

            $this->phpmailer->addReplyTo($replyto, $name);

            $this->_replyto_flag = TRUE;

        } else {

            parent::reply_to($replyto, $name);
        }

        return $this;
    }

    public function to($to) {

        if ($this->mailer_engine == 'phpmailer') {

            $to = $this->_str_to_array($to);
            $names = $this->_extract_name($to);
            $to = $this->clean_email($to);

            if ($this->validate) {
                $this->validate_email($to);
            }

            $i = 0;

            foreach ($to as $address) {

                $this->phpmailer->addAddress($address, $names[$i]);

                $i++;
            }

        } else {

            parent::to($to);
        }

        return $this;
    }

    public function cc($cc) {

        if ($this->mailer_engine == 'phpmailer') {

            $cc = $this->_str_to_array($cc);
            $names = $this->_extract_name($cc);
            $cc = $this->clean_email($cc);

            if ($this->validate) {
                $this->validate_email($cc);
            }

            $i = 0;

            foreach ($cc as $address) {

                $this->phpmailer->addCC($address, $names[$i]);

                $i++;
            }

        } else {

            parent::cc($cc);
        }

        return $this;
    }

    public function bcc($bcc, $limit = '') {

        if ($this->mailer_engine == 'phpmailer') {

            $bcc = $this->_str_to_array($bcc);
            $names = $this->_extract_name($bcc);
            $bcc = $this->clean_email($bcc);

            if ($this->validate) {
                $this->validate_email($bcc);
            }

            $i = 0;

            foreach ($bcc as $address) {

                $this->phpmailer->addBCC($address, $names[$i]);

                $i++;
            }

        } else {

            parent::bcc($bcc, $limit);
        }

        return $this;
    }

    public function subject($subject) {

        $subject = (string) $subject;

        if ($this->mailer_engine == 'phpmailer') {

            // Modified by Ivan Tcholakov, 01-AUG-2015.
            // See https://github.com/ivantcholakov/codeigniter-phpmailer/issues/8
            // This change probably is not needed, done anyway.
            //$this->phpmailer->Subject = $subject;
            $this->phpmailer->Subject = str_replace(array('{unwrap}', '{/unwrap}'), '', $subject);
            //

        } else {

            parent::subject($subject);
        }

        return $this;
    }

    public function message($body) {

        $body = (string) $body;

        if ($this->mailer_engine == 'phpmailer') {

            // Modified by Ivan Tcholakov, 01-AUG-2015.
            // See https://github.com/ivantcholakov/codeigniter-phpmailer/issues/8
            //$this->phpmailer->Body = $body;
            $this->phpmailer->Body = str_replace(array('{unwrap}', '{/unwrap}'), '', $body);
            //
        }

        parent::message($body);

        return $this;
    }

    // Modified by Ivan Tcholakov, 16-JAN-2014.
    //public function attach($file, $disposition = '', $newname = NULL, $mime = '') {
    public function attach($file, $disposition = '', $newname = NULL, $mime = '', $embedded_image = false) {
    //

        $file = (string) $file;

        $disposition = (string) $disposition;

        if ($disposition == '') {
            $disposition ='attachment';
        }

        $newname = (string) $newname;

        if ($newname == '') {
            // For making strict NULL checks happy.
            $newname = NULL;
        }

        $mime = (string) $mime;

        if ($this->mailer_engine == 'phpmailer') {

            if ($mime == '') {

                if (strpos($file, '://') === FALSE && ! file_exists($file)) {

                    $this->_set_error_message('lang:email_attachment_missing', $file);
                    // Modified by Ivan Tcholakov, 14-JAN-2014.
                    //return FALSE;
                    return $this;
                    //
                }

                if (!$fp = @fopen($file, FOPEN_READ)) {

                    $this->_set_error_message('lang:email_attachment_unreadable', $file);
                    // Modified by Ivan Tcholakov, 14-JAN-2014.
                    //return FALSE;
                    return $this;
                    //
                }

                $file_content = stream_get_contents($fp);
                $mime = $this->_mime_types(pathinfo($file, PATHINFO_EXTENSION));
                fclose($fp);

                $this->_attachments[] = array(
                    'name' => array($file, $newname),
                    'disposition' => $disposition,
                    'type' => $mime,
                );

                $newname = $newname === NULL ? basename($file) : $newname;
                $cid = $this->attachment_cid($file);

            } else {

                // A buffered file, in this case make sure that $newname has been set.

                $file_content =& $file;

                $this->_attachments[] = array(
                    'name' => array($newname, $newname),
                    'disposition' => $disposition,
                    'type' => $mime,
                );

                $cid = $this->attachment_cid($newname);
            }

            if (empty($embedded_image)) {
                $this->phpmailer->addStringAttachment($file_content, $newname, 'base64', $mime, $disposition);
            } else {
                $this->phpmailer->addStringEmbeddedImage($file_content, $cid, $newname, 'base64', $mime, $disposition);
            }

        } else {

            parent::attach($file, $disposition, $newname, $mime);
        }

        return $this;
    }

    public function attachment_cid($filename) {

        if ($this->mailer_engine == 'phpmailer') {

            for ($i = 0, $c = count($this->_attachments); $i < $c; $i++) {

                if ($this->_attachments[$i]['name'][0] === $filename) {

                    $this->_attachments[$i]['cid'] = uniqid(basename($this->_attachments[$i]['name'][0]).'@');
                    return $this->_attachments[$i]['cid'];
                }
            }

        } else {

            return parent::attachment_cid($filename);
        }

        return FALSE;
    }

    // Added by Ivan Tcholakov, 16-JAN-2014.
    public function get_attachment_cid($filename) {

        for ($i = 0, $c = count($this->_attachments); $i < $c; $i++) {

            if ($this->_attachments[$i]['name'][0] === $filename) {
                return empty($this->_attachments[$i]['cid']) ? FALSE : $this->_attachments[$i]['cid'];
            }
        }

        return FALSE;
    }

    public function set_header($header, $value) {

        $header = (string) $header;
        $value = (string) $value;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->addCustomHeader($header, str_replace(array("\n", "\r"), '', $value));
        }

        parent::set_header($header, $value);

        return $this;
    }

    public function send($auto_clear = true) {

        $auto_clear = !empty($auto_clear);

        if ($this->mailer_engine == 'phpmailer') {

            if ($this->mailtype == 'html') {

                // Modified by Ivan Tcholakov, 01-AUG-2015.
                // See https://github.com/ivantcholakov/codeigniter-phpmailer/issues/8
                //$this->phpmailer->AltBody = $this->_get_alt_message();
                $this->phpmailer->AltBody = str_replace(array('{unwrap}', '{/unwrap}'), '', $this->_get_alt_message());
                //
            }

            $result = (bool) $this->phpmailer->send();

            if ($result) {

                $this->_set_error_message('lang:email_sent', $this->_get_protocol());

                if ($auto_clear) {
                    $this->clear();
                }

            } else {

                $this->_set_error_message($this->phpmailer->ErrorInfo);
            }

        } else {

            $result = parent::send($auto_clear);
        }

        return $result;
    }


    // Methods for setting configuration options -------------------------------

    public function set_mailer_engine($mailer_engine) {

        $mailer_engine = strpos(strtolower($mailer_engine), 'phpmailer') !== false ? 'phpmailer' : 'codeigniter';

        if ($this->mailer_engine == $mailer_engine) {
            return $this;
        }

        $this->mailer_engine = $mailer_engine;

        if ($mailer_engine == 'phpmailer') {

            if (!is_object($this->phpmailer)) {

                // Try to autoload the PHPMailer if there is already a registered autoloader.
                $phpmailer_class_exists = class_exists('PHPMailer', true);

                // No? Search for autoloader at some fixed places.
                if (!$phpmailer_class_exists && defined('COMMONPATH')) {

                    $autoloader = COMMONPATH.'third_party/phpmailer/PHPMailerAutoload.php';
                    @ include_once $autoloader;
                    $phpmailer_class_exists = class_exists('PHPMailer', true);
                }

                if (!$phpmailer_class_exists) {

                    $autoloader = APPPATH.'third_party/phpmailer/PHPMailerAutoload.php';
                    @ include_once $autoloader;
                    $phpmailer_class_exists = class_exists('PHPMailer', true);
                }

                if (!$phpmailer_class_exists) {
                    throw new Exception('The file PHPMailerAutoload.php can not be found.');
                }

                $this->phpmailer = new PHPMailer();

                // The property PluginDir seems to be useless, setting it just in case.
                if (property_exists($this->phpmailer, 'PluginDir')) {

                    $phpmailer_reflection = new ReflectionClass($this->phpmailer);
                    $this->phpmailer->PluginDir = dirname($phpmailer_reflection->getFileName()).DIRECTORY_SEPARATOR;
                    unset($phpmailer_reflection);
                }
            }
        }

        $this->refresh_properties();
        $this->clear(true);

        return $this;
    }

    public function set_useragent($useragent) {

        $useragent = (string) $useragent;

        $this->properties['useragent'] = $useragent;

        $this->set_mailer_engine($useragent);

        return $this;
    }

    public function set_mailpath($value) {

        $value = (string) $value;

        $this->properties['mailpath'] = $value;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->Sendmail = $value;
        }

        return $this;
    }

    public function set_protocol($protocol = 'mail') {

        $protocol = in_array($protocol, self::$protocols, TRUE) ? strtolower($protocol) : 'mail';

        $this->properties['protocol'] = $protocol;

        if ($this->mailer_engine == 'phpmailer') {

            switch ($protocol) {

                case 'mail':
                    $this->phpmailer->isMail();
                    break;

                case 'sendmail':
                    $this->phpmailer->isSendmail();
                    break;

                case 'smtp':
                    $this->phpmailer->isSMTP();
                    break;
            }
        }

        return $this;
    }

    public function set_smtp_host($value) {

        $value = (string) $value;

        $this->properties['smtp_host'] = $value;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->Host = $value;
        }

        return $this;
    }

    public function set_smtp_user($value) {

        $value = (string) $value;

        $this->properties['smtp_user'] = $value;
        $this->_smtp_auth = !($value == '' && $this->smtp_pass == '');

        if ($this->mailer_engine == 'phpmailer') {

            $this->phpmailer->Username = $value;
            $this->phpmailer->SMTPAuth = $this->_smtp_auth;
        }

        return $this;
    }

    public function set_smtp_pass($value) {

        $value = (string) $value;

        $this->properties['smtp_pass'] = $value;
        $this->_smtp_auth = !($this->smtp_user == '' && $value == '');

        if ($this->mailer_engine == 'phpmailer') {

            $this->phpmailer->Password = $value;
            $this->phpmailer->SMTPAuth = $this->_smtp_auth;
        }

        return $this;
    }

    public function set_smtp_port($value) {

        $value = (int) $value;

        $this->properties['smtp_port'] = $value;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->Port = $value;
        }

        return $this;
    }

    public function set_smtp_timeout($value) {

        $value = (int) $value;

        $this->properties['smtp_timeout'] = $value;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->Timeout = $value;
        }

        return $this;
    }

    public function set_smtp_keepalive($value) {

        $value = !empty($value);

        $this->properties['smtp_keepalive'] = $value;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->SMTPKeepAlive = $value;
        }

        return $this;
    }

    public function set_smtp_crypto($smtp_crypto = '') {

        $smtp_crypto = trim(strtolower($smtp_crypto));

        if ($smtp_crypto != 'tls' && $smtp_crypto != 'ssl') {
            $smtp_crypto = '';
        }

        $this->properties['smtp_crypto'] = $smtp_crypto;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->SMTPSecure = $smtp_crypto;
        }

        return $this;
    }

    public function set_wordwrap($wordwrap = TRUE) {

        $wordwrap = !empty($wordwrap);

        $this->properties['wordwrap'] = $wordwrap;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->WordWrap = $wordwrap ? (int) $this->wrapchars : 0;
        }

        return $this;
    }

    public function set_wrapchars($wrapchars) {

        $wrapchars = (int) $wrapchars;

        $this->properties['wrapchars'] = $wrapchars;

        if ($this->mailer_engine == 'phpmailer') {

            if (!$this->wordwrap) {

                $this->phpmailer->WordWrap = 0;

            } else {

                if (empty($wrapchars)) {
                    $wrapchars = 76;
                }

                $this->phpmailer->WordWrap = (int) $wrapchars;
            }
        }

        return $this;
    }

    public function set_mailtype($type = 'text') {

        $type = trim(strtolower($type));
        $type = in_array($type, self::$mailtypes) ? $type : 'text';

        $this->properties['mailtype'] = $type;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->isHTML($type == 'html');
        }

        return $this;
    }

    public function set_charset($charset) {

        if ($charset == '') {
            $charset = config_item('charset');
        }

        $charset = strtoupper($charset);

        $this->properties['charset'] = $charset;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->CharSet = $charset;
        }

        return $this;
    }

    // Not used by PHPMailer.
    public function set_multipart($value) {

        $this->properties['multipart'] = (string) $value;

        return $this;
    }

    public function set_alt_message($str) {

        $this->properties['alt_message'] = (string) $str;

        return $this;
    }

    public function set_validate($value) {

        $this->properties['validate'] = !empty($value);

        return $this;
    }

    public function set_priority($n = 3) {

        $n = preg_match('/^[1-5]$/', $n) ? (int) $n : 3;

        $this->properties['priority'] = $n;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->Priority = $n;
        }

        return $this;
    }

    public function set_newline($newline = "\n") {

        $newline = in_array($newline, array("\n", "\r\n", "\r")) ? $newline : "\n";

        $this->properties['newline'] = $newline;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->LE = $newline;
        }

        return $this;
    }

    // A CodeIgniter specific option, PHPMailer uses the standard value "\r\n" only.
    public function set_crlf($crlf = "\n") {

        $crlf = ($crlf !== "\n" && $crlf !== "\r\n" && $crlf !== "\r") ? "\n" : $crlf;

        $this->properties['crlf'] = $crlf;

        return $this;
    }

    // Not used by PHPMailer.
    public function set_dsn($value) {

        $this->properties['dsn'] = !empty($value);

        return $this;
    }

    // Not used by PHPMailer.
    public function set_send_multipart($value) {

        $this->properties['send_multipart'] = !empty($value);

        return $this;
    }

    // Not used by PHPMailer.
    public function set_bcc_batch_mode($value) {

        $this->properties['bcc_batch_mode'] = !empty($value);

        return $this;
    }

    // Not used by PHPMailer.
    public function set_bcc_batch_size($value) {

        $this->properties['bcc_batch_size'] = (int) $value;

        return $this;
    }

    // PHPMailer's SMTP debug info level.
    // 0 = off, 1 = commands, 2 = commands and data, 3 = as 2 plus connection status, 4 = low level data output.
    public function set_smtp_debug($level) {

        $level = (int) $level;

        if ($level < 0) {
            $level = 0;
        }

        $this->properties['smtp_debug'] = $level;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->SMTPDebug = $level;
        }

        return $this;
    }

    // Setting explicitly the body encoding.
    // See https://github.com/ivantcholakov/codeigniter-phpmailer/issues/3
    public function set_encoding($encoding) {

        $encoding = (string) $encoding;

        if (!in_array($encoding, $this->mailer_engine == 'phpmailer' ? self::$encodings_phpmailer : self::$encodings_ci)) {
            $encoding = '8bit';
        }

        $this->properties['encoding'] = $encoding;
        $this->_encoding = $encoding;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->Encoding = $encoding;
        }

        return $this;
    }

    // PHPMailer: Whether to enable TLS encryption automatically if a server supports it,
    // even if `SMTPSecure` is not set to 'tls'.
    // Be aware that in PHP >= 5.6 this requires that the server's certificates are valid.
    public function set_smtp_auto_tls($value) {

        $value = !empty($value);

        $this->properties['smtp_auto_tls'] = $value;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->SMTPAutoTLS = $value;
        }

        return $this;
    }

    // PHPMailer: Options array passed to stream_context_create when connecting via SMTP.
    // See https://github.com/ivantcholakov/codeigniter-phpmailer/issues/12
    public function set_smtp_conn_options($value) {

        if (!is_array($value)) {
            $value = array();
        }

        $this->properties['smtp_conn_options'] = $value;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->SMTPOptions = $value;
        }

        return $this;
    }

    // DKIM signing, see https://github.com/ivantcholakov/codeigniter-phpmailer/issues/11

    // PHPMailer: DKIM signing domain name, for exmple 'example.com'.
    public function set_dkim_domain($value) {

        $value = (string) $value;

        $this->properties['dkim_domain'] = $value;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->DKIM_domain = $value;
        }

        return $this;
    }

    // PHPMailer: DKIM private key, set as a file path.
    public function set_dkim_private($value) {

        $value = (string) $value;

        $this->properties['dkim_private'] = $value;

        // Parse the provided path seek for constant and translate it.
        // For example the path to the private key could be set as follows:
        // {APPPATH}config/rsa.private
        $value_parsed = str_replace(array_keys(self::_get_file_name_variables()), array_values(self::_get_file_name_variables()), $value);

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->DKIM_private = $value_parsed;
        }

        if ($value != '') {

            // Reset the alternative setting.
            $this->set_dkim_private_string('');
        }

        return $this;
    }

    // PHPMailer: DKIM private key, set directly from a string.
    public function set_dkim_private_string($value) {

        $value = (string) $value;

        $this->properties['dkim_private_string'] = $value;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->DKIM_private_string = $value;
        }

        if ($value != '') {

            // Reset the alternative setting.
            $this->set_dkim_private('');
        }

        return $this;
    }

    // PHPMailer: DKIM selector.
    public function set_dkim_selector($value) {

        $value = (string) $value;

        $this->properties['dkim_selector'] = $value;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->DKIM_selector = $value;
        }

        return $this;
    }

    // PHPMailer: DKIM passphrase, used if your key is encrypted.
    public function set_dkim_passphrase($value) {

        $value = (string) $value;

        $this->properties['dkim_passphrase'] = $value;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->DKIM_passphrase = $value;
        }

        return $this;
    }

    // PHPMailer: DKIM Identity, usually the email address used as the source of the email.
    public function set_dkim_identity($value) {

        $value = (string) $value;

        $this->properties['dkim_identity'] = $value;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->DKIM_identity = $value;
        }

        return $this;
    }


    // Overridden public methods -----------------------------------------------

    public function valid_email($email) {

        return valid_email($email);
    }


    // Custom public methods ---------------------------------------------------

    public function full_html($subject, $message) {

        $full_html =
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset='.strtolower($this->charset).'" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>'.htmlspecialchars($subject, ENT_QUOTES, $this->charset).'</title>

    <style type="text/css">

        /* See http://htmlemailboilerplate.com/ */

        /* Based on The MailChimp Reset INLINE: Yes. */
        /* Client-specific Styles */
        #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
        body {
            width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:40px;
            font-family: Arial, Verdana, Helvetica, sans-serif; font-size: 16px;
        }
        /* End reset */

        /* Some sensible defaults for images
        Bring inline: Yes. */
        img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;}
        a img {border:none;}

        /* Yahoo paragraph fix
        Bring inline: Yes. */
        p {margin: 1em 0;}

        /* Hotmail header color reset
        Bring inline: Yes. */
        h1, h2, h3, h4, h5, h6 {color: black !important;}

        h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {color: blue !important;}

        h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active {
        color: red !important; /* Preferably not the same color as the normal header link color.  There is limited support for psuedo classes in email clients, this was added just for good measure. */
        }

        h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
        color: purple !important; /* Preferably not the same color as the normal header link color. There is limited support for psuedo classes in email clients, this was added just for good measure. */
        }

        /* Outlook 07, 10 Padding issue fix
        Bring inline: No.*/
        table td {border-collapse: collapse;}

        /* Remove spacing around Outlook 07, 10 tables
        Bring inline: Yes */
        table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }

        /* Styling your links has become much simpler with the new Yahoo.  In fact, it falls in line with the main credo of styling in email and make sure to bring your styles inline.  Your link colors will be uniform across clients when brought inline.
        Bring inline: Yes. */
        a {color: blue;}

    </style>

</head>

<body>

'.$message.'

</body>
</html>';

        return $full_html;
    }


    // Protected methods -------------------------------------------------------

    protected function _get_alt_message() {

        $alt_message = (string) $this->alt_message;

        if ($alt_message == '') {
            $alt_message = $this->_plain_text($this->_body);
        }

        if ($this->mailer_engine == 'phpmailer') {
            // PHPMailer would do the word wrapping.
            return $alt_message;
        }

        return ($this->wordwrap)
            ? $this->word_wrap($alt_message, 76)
            : $alt_message;
    }

    protected function _plain_text($html) {

        if (!function_exists('html_to_text')) {

            $body = @ html_entity_decode($html, ENT_QUOTES, $this->charset); // Added by Ivan Tcholakov, 28-JUL-2013.

            $body = preg_match('/\<body.*?\>(.*)\<\/body\>/si', $body, $match) ? $match[1] : $body;
            $body = str_replace("\t", '', preg_replace('#<!--(.*)--\>#', '', trim(strip_tags($body))));

            for ($i = 20; $i >= 3; $i--) {
                $body = str_replace(str_repeat("\n", $i), "\n\n", $body);
            }

            // Reduce multiple spaces
            $body = preg_replace('| +|', ' ', $body);

            return $body;
        }

        // You can implement your own helper function html_to_text().
        //
        // An example of Markdownify-based implementation, see https://github.com/Elephant418/Markdownify
        //
        // Install using Composer the following package: pixel418/markdownify
        // Place in MY_html_helper.php the following function:
        //
        // function html_to_text($html) {
        //
        //     static $parser;
        //
        //     if (!isset($parser)) {
        //         $parser = new \Markdownify\ConverterExtra();
        //     }
        //
        //     $parser->setKeepHTML(false);
        //
        //     return @ $parser->parseString($html);
        // }
        //

        return html_to_text($html);
    }

    protected function _extract_name($address) {

        if (!is_array($address)) {

            $address = trim($address);

            if (preg_match('/(.*)\<(.*)\>/', $address, $match)) {
                return trim($match['1']);
            } else {
                return '';
            }
        }

        $result = array();

        foreach ($address as $addr) {

            $addr = trim($addr);

            if (preg_match('/(.*)\<(.*)\>/', $addr, $match)) {
                $result[] = trim($match['1']);
            } else {
                $result[] = '';
            }
        }

        return $result;
    }

    protected static function _get_file_name_variables() {

        static $result = null;

        if ($result === null) {

            $result = array('{APPPATH}' => APPPATH);

            if (defined('COMMONPATH')) {
                $result['{COMMONPATH}'] = COMMONPATH;
            }

            if (defined('PLATFORMPATH')) {
                $result['{PLATFORMPATH}'] = PLATFORMPATH;
            }
        }

        return $result;
    }
    
    
       //Added by Ahmed, need some more studying
    public function verify_domain($address_to_verify, $verbose=FALSE){
            //TODO:
            // must figure out ho to get config setting of codeigniter
            $current_platform='win';
            $record = 'ANY'; # <-- Can be changed to check for other records like A records or CNAME records as well
            list($user, $domain) = explode('@', $address_to_verify);
            
                if ($current_platform == 'win')
                    return $this->checkdnsrr_win($domain);
                else
                    return checkdnsrr($domain, $record);
	}
               
        public function checkDomainAvailability($domain_name){

            $server = 'whois.crsnic.net';

            // Open a socket connection to the whois server
            $connection = fsockopen($server, 43);
            if (!$connection) return false;

            // Send the requested doman name
            fputs($connection, $domain_name."\r\n");

            // Read and store the server response
            $response_text = ' :';
            while(!feof($connection)) {
            $response_text .= fgets($connection,128);
            }

            // Close the connection
            fclose($connection);

            // Check the response stream whether the domain is available
            if (strpos($response_text, 'No match for')) return true;
            else return false;
    }
        
         public function checkdnsrr_win($hostname, $recType = '')
        {

         if(!empty($hostname)) {

           if( $recType == '' ) $recType = "MX";

           exec("nslookup -type=$recType $hostname", $result);

           // check each line to find the one that starts with the host

           // name. If it exists then the function succeeded.
           $hostname=  trim($hostname);
           foreach ($result as $line) {

             if(preg_match("/^$hostname/",$line)) {
               return true;
             }

           }
           // otherwise there was no mail handler for the domain

           return false;

         }

         return false;

        }

	# Verify that the email address is formatted as an email address should be
	public function verify_formatting($address_to_verify, $verbose=FALSE){
		
		# Check to make sure the @ symbol is included
		if(strstr($address_to_verify, "@") == FALSE){
			if($verbose){
				return 'Ampersand not present.';
			}else{
				return false;
			}
		}else{
			
			# Bust up the address so that we have the name and the domain name
			list($user, $domain) = explode('@', $address_to_verify);
			
			# Verify the domain name has a period like all good domain names should
			if(strstr($domain, '.') == FALSE){
				if($verbose){
					return 'Period not present.';
				}else{
					return false;
				}
			}else{
				
				# Bust up the domain name
				$domain_check = explode(".", $domain);
				$domain_extension = end($domain_check);
				
				if(strlen($domain_extension) < 2){
					if($verbose){
						return 'Domain name extension is too short.';
					}else{
						return false;
					}
				}else{
					if(!in_array($domain_extension, $this->list_domain_extensions)){
						if($verbose){
							return 'Domain name extension could not be verified.';
						}else{
							return false;
						}
					}else{
						return true;
					}
				}
			}
		}
	}
	# Take the code from an HTML email and convert it to plain text
	# This is commonly used when sending HTML emails as a backup for email clients who can only view, or who choose to only view, 
	#	plain text emails
	public function convert_html_to_plain_txt($content, $remove_links=FALSE){
		# Replace HTML line breaks with text line breaks
		$plain_text = str_ireplace(array("<br>","<br />"), "\n\r", $content);
		
		# Remove the content between the tags that wouldn't normally get removed with the strip_tags function
		$plain_text = preg_replace(array('@<head[^>]*?>.*?</head>@siu',
							            '@<style[^>]*?>.*?</style>@siu',
							            '@<script[^>]*?.*?</script>@siu',
							            '@<noscript[^>]*?.*?</noscript>@siu',
							        ), "", $plain_text); # Remove everything from between the tags that doesn't get removed with strip_tags function
		
		# If the user has chosen to preserve the addresses from links
		if(!$remove_links){
			$plain_text = strip_tags(preg_replace('/<a href="(.*)">/', ' $1 ', $plain_text));
		}
		
		# Remove HTML spaces
		$plain_text = str_replace("&nbsp;", "", $plain_text);
		
		# Replace multiple line breaks with a single line break
		$plain_text = preg_replace("/(\s){3,}/","\r\n\r\n",trim($plain_text));
		
		return $plain_text;
	}

        # verify emails using STMP 
	# This , 
	#	p
        public function verify_by_Stmp($toemail, $getdetails = false){
            $details='';
            $fromemail=$this->fromEmail;
            $email_arr = explode("@", $toemail);
            $domain = array_slice($email_arr, -1);
            $domain = $domain[0];
            // Trim [ and ] from beginning and end of domain string, respectively
            $domain = ltrim($domain, "[");
            $domain = rtrim($domain, "]");
            if( "IPv6:" == substr($domain, 0, strlen("IPv6:")) ) {
                    $domain = substr($domain, strlen("IPv6") + 1);
            }
            $mxhosts = array();
            if( filter_var($domain, FILTER_VALIDATE_IP) )
                    $mx_ip = $domain;
            else
                    getmxrr($domain, $mxhosts, $mxweight);
            if(!empty($mxhosts) )
                    $mx_ip = $mxhosts[array_search(min($mxweight), $mxhosts)];
            else {
                    if( filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {
                            $record_a = dns_get_record($domain, DNS_A);
                    }
                    elseif( filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
                            $record_a = dns_get_record($domain, DNS_AAAA);
                    }
                    if( !empty($record_a) )
                            $mx_ip = $record_a[0]['ip'];
                    else {
                            $result   = "invalid";
                            $details .= "No suitable MX records found.";
                            return ( (true == $getdetails) ? array($result, $details) : $result );
                    }
            }

            $connect = @fsockopen($mx_ip, 25); 
            if($connect){ 
                    if(preg_match("/^220/i", $out = fgets($connect, 1024))){
                            fputs ($connect , "HELO $mx_ip\r\n"); 
                            $out = fgets ($connect, 1024);
                            $details .= $out."\n";

                            fputs ($connect , "MAIL FROM: <$fromemail>\r\n"); 
                            $from = fgets ($connect, 1024); 
                            $details .= $from."\n";
                            fputs ($connect , "RCPT TO: <$toemail>\r\n"); 
                            $to = fgets ($connect, 1024);
                            $details .= $to."\n";
                            fputs ($connect , "QUIT"); 
                            fclose($connect);
                            if(!preg_match("/^250/i", $from) || !preg_match("/^250/i", $to)){
                                    $result = "invalid"; 
                            }
                            else{
                                    $result = "valid";
                            }
                    } 
            }
            else{
                    $result = "invalid";
                    $details .= "Could not connect to server";
            }
            if($getdetails){
                    return array($result, $details);
            }
            else{
                    return $result;
            }
      }
      
      public function verify_emailby_Stmp($email)
      {
          $validator = new Smtp_Validation($email, $this->fromEmail);
          $smtp_results = $validator->validate();

          //var_dump($smtp_results);
      } 
      
         /**
   * Set Mail Body configuration
   *
   * Format email message Body, this can be an external template html file with a copy
   * of a plain-text like template.txt or HTML/plain-text string.
   * This method can be used by passing a template file HTML name and an associative array
   * with the values that can be parsed into the file HTML by the key KEY_NAME found in your
   * array to your HTML {KEY_NAME}.
   * Other optional ways to format the mail body is available like instead of a template the
   * param $data can be set as an array or string, but param $template_html must be equal to null
   *
   * @update 2014-04-01 01:46
   * @author Adriano Rosa (http://adrianorosa.com)
   * @param mixed  $data [array|string] that contain the values to be parsed in mail body
   * @param string $template_html the external html template filename , OR the message as HMTL string
   * @param string $format [HTML|TEXT]
   * @return string
   */
    //TODO this function need modfication
   public function set_Mail_Body($data, $template_html = null,$path, $format = 'HTML')
   {
      $TemplateFolder = $path;
      $textBody='';
      $htmlBody='';
      
      if ( !is_array($data) && $template_html == null ) {
         if ( $format == 'TEXT' ) {
            $this->isHTML = false;
            return $textBody = $data;
         }
         return $htmlBody = $data;
      } elseif ( is_array($data) && $template_html == null ) {
         return $htmlBody = implode('<br>  ', $data);
      } else {
         $templatePath = ($TemplateFolder)
               ? $TemplateFolder . $template_html
               : $template_html;
        // Support load different path to views available in CI v3.0
         if ( defined('VIEWPATH') ) {
            $views_path = VIEWPATH;
         } else {
            $views_path = APPPATH .'views/';
         }
         // $templatePath =$templatePath.$template_html;
         if ( !file_exists( $templatePath ) ) {
            log_message('error','setEmailBody() HTML template file not found: ' . $template_html);
            return $htmlBody = 'Template ' . ($template_html) . ' not found.'; //'none template message found in: ' .$template_html;
         } else {
            $htmlBody = $this->CI->load->view('templates/'.$template_html, '', true);
            //$htmlBody = $this->CI->load->view($TemplateFolder . $template_html, '', true);
            if ( preg_match('/\.txt$/', $template_html) ) {
               $textBody = $htmlBody;
            } else {
               $templateTextPath = preg_replace('/\.[html|php|htm]+$/', '.txt', $templatePath);
               if ( file_exists( $views_path . $templateTextPath ) ) {
                  $textBody = $this->CI->load->view($templateTextPath, '', true);
               }
            }
         }
         $data = (is_array($data)) ? $data : array($data);
         //$data = array_merge($data, $this->TemplateOptions);
         if ( $format == 'HTML' ) {
            foreach ($data as $key => $value) {
               $htmlBody = str_replace("{".$key."}", "".$value."", $htmlBody);
               $textBody = str_replace("{".$key."}", "".$value."", $textBody);
            }
         } elseif ( $format == 'TEXT' ) {
            $this->isHTML = false;
            $textBody = @vsprintf($textBody, $data);
         }
         
         return $htmlBody;
      }
   }

}
