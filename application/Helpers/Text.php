<?php namespace Glavred\Helpers;


class Text {

    public static function arrayToString($str){
        return base64_encode(serialize($str));
    } 
    
    
    public static function stringToArray($str){
        return unserialize(base64_decode($str));
    }   
    
    public static function smartcut($string, $length){
        $text = mb_substr($string,0,mb_strrpos(mb_substr($string,0,$length,'utf-8'),' ','utf-8'),'utf-8');

        return empty($text) ? mb_substr($string, 0, $length, 'utf-8') : $text;
    }   
    
}
