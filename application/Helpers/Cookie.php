<?php namespace Glavred\Helpers;

use Glavred\Helpers\Password;
use Glavred\Singleton\Request;

class Cookie {
    
    
    public static function set($name, $value, $minToLive, $decrypt = true){
        if($decrypt == true){ $value = Password::encrypt($value);}       
        return setcookie($name, $value, time() + (60 * $minToLive), "/");
    }
   
    
    public static function get($name, $encrypt = true){
        if($encrypt == true){
              return Password::decrypt(Request::link()->cookie($name));
        }else{return Request::link()->cookie($name);}
    }    
    
    
    public static function delete($name){
         return setcookie($name, '', time() - 3600000, '/');
    }       
    
    
}
