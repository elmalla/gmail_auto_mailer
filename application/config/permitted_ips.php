<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Premitted IPs
*
* Version: 2.5.2
*
* Author: 
*
* Description:  Modified auth system based on redux_auth with extensive customization.  This is basically what Redux Auth 2 should be.
* Original Author name has been kept but that does not mean that the method has not been modified.
*
* Requirements: PHP5 or above
*
*/

/*
| -------------------------------------------------------------------------
| Tables.
| -------------------------------------------------------------------------
| Database table names.
*/
$config['tables']['users']           = 'users';
$config['tables']['groups']          = '';
$config['tables']['users_groups']    = '';
$config['tables']['login_attempts']  = '';


/*
 | -------------------------------------------------------------------------
 | Hash Method (sha1 or bcrypt)
 | -------------------------------------------------------------------------
 | Bcrypt is available in PHP 5.3+
 |
 | IMPORTANT: Based on the recommendation by many professionals, it is highly recommended to use
 | bcrypt instead of sha1.
 |
 | NOTE: If you use bcrypt you will need to increase your password column character limit to (80)
 |
 | Below there is "default_rounds" setting.  This defines how strong the encryption will be,
 | but remember the more rounds you set the longer it will take to hash (CPU usage) So adjust
 | this based on your server hardware.
 |
 | If you are using Bcrypt the Admin password field also needs to be changed in order to login as admin:
 | $2y$: $2y$08$200Z6ZZbp3RAEXoaWcMA6uJOFicwNZaqk4oDhqTUiFXFe63MG.Daa
 | $2a$: $2a$08$6TTcWD1CJ8pzDy.2U3mdi.tpl.nYOR1pwYXwblZdyQd9SL16B7Cqa
 |
 | Be careful how high you set max_rounds, I would do your own testing on how long it takes
 | to encrypt with x rounds.
 |
 | salt_prefix: Used for bcrypt. Versions of PHP before 5.3.7 only support "$2a$" as the salt prefix
 | Versions 5.3.7 or greater should use the default of "$2y$".
 */
	// sha1 or bcrypt, bcrypt is STRONGLY recommended
$config['vpn_ip']    = '';
$config['Home_ksa_ip']    = '';
$config['Home_msia_ip']    = '';
$config['ip_list'] = array(
	'hosting_ip' => '136.243.201.110',
        'vpn_ip'=>'',
        'home_ksa_ip'=>'95.185.198.167',
        'home_msia_ip'=>'',
        'local_ip'=>'127.0.0.1',
);

