<?php namespace Glavred\Helpers;


class String {

    public static function arrayToString($str){
        return base64_encode(serialize($str));
    } 
    
    
    public static function stringToArray($str){
        return unserialize(base64_decode($str));
    }     
    
    
}
