 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Crypto encrypt/decrypt Class it uses AES by $secret_key
 *
 * @access    public
 * @param     array/value
 * @return    array/value
 */
   
class Crypto {

    public function encrypt($data,$secret_key){
        $array = array();

        if(is_array($data)){
            foreach($data as $key=>$value){
                 $array[$key] = trim(
            base64_encode(
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_256,
                    $secret_key, $value, 
                    MCRYPT_MODE_ECB, 
                    mcrypt_create_iv(
                        mcrypt_get_iv_size(
                            MCRYPT_RIJNDAEL_256, 
                            MCRYPT_MODE_ECB
                            ), 
                        MCRYPT_RAND)
                    )
                )
            );
            }
            return $array;

        }else{

           return trim(
            base64_encode(
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_256,
                    $secret_key, $data, 
                    MCRYPT_MODE_ECB, 
                    mcrypt_create_iv(
                        mcrypt_get_iv_size(
                            MCRYPT_RIJNDAEL_256, 
                            MCRYPT_MODE_ECB
                            ), 
                        MCRYPT_RAND)
                    )
                )
            );
       }
   }



   public function decrypt($data,$secret_key)
   {
    $array = array();

        if(is_array($data)){
            foreach($data as $key=>$value){
                 $array[$key] = trim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256, 
                $secret_key, 
                base64_decode($value), 
                MCRYPT_MODE_ECB,
                mcrypt_create_iv(
                    mcrypt_get_iv_size(
                        MCRYPT_RIJNDAEL_256,
                        MCRYPT_MODE_ECB
                        ), 
                    MCRYPT_RAND
                    )
                )
            );
            }
            return $array;
        }else{
        return trim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256, 
                $secret_key, 
                base64_decode($data), 
                MCRYPT_MODE_ECB,
                mcrypt_create_iv(
                    mcrypt_get_iv_size(
                        MCRYPT_RIJNDAEL_256,
                        MCRYPT_MODE_ECB
                        ), 
                    MCRYPT_RAND
                    )
                )
            );
    }
}


}
//end class Crypto

