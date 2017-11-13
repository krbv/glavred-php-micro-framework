<?php

namespace Glavred\Helpers;

class Password {
    
    private static $method = 'AES-128-CTR'; 
    private static $key = 'GlavredFMKey0';
    
    
    public static function shortHash($string){
        return hash( "crc32", $string );
    }
    
    public static function shortRandomString(){
        return base_convert(
                   mt_rand(1000,9999).time()
                , 10, 36);
    }    
    
    public static function makePasswordHash($string){
        return password_hash($string, PASSWORD_DEFAULT);
    }    
    
    public static function checkPasswordHash($password, $hash){
       return password_verify($password, $hash);
    }   
    
    
 /* CRYPT */   
    private static function getCryptConfig(){
      return [
          'key' => openssl_digest(self::$key, 'SHA256', true),
          'ivb' => openssl_cipher_iv_length(self::$method),
      ];
    }   
    public static function encrypt($data){
      $conf = self::getCryptConfig();
      $iv = openssl_random_pseudo_bytes($conf['ivb']);
      return bin2hex($iv) . openssl_encrypt($data, self::$method, $conf['key'], 0, $iv);
    }
    public static function decrypt($data){
       $conf = self::getCryptConfig();
       $iv_strlen = 2  * $conf['ivb'];
       if(preg_match("/^(.{" . $iv_strlen . "})(.+)$/", $data, $regs)) {
            list(, $iv, $crypted_string) = $regs;
             $decrypted_string = openssl_decrypt($crypted_string, self::$method, $conf['key'], 0, hex2bin($iv));
             return $decrypted_string;
        } 
        return false;
    } 
 /* END_CRYPT */   
    
}
