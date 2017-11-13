<?php namespace Glavred\Helpers;

class Network {
    
    

     public static  function curl($url, $post = [], array $config = [], $bildQuery = true): array
     {
         
       $ch = curl_init();
     
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,3);
       curl_setopt($ch, CURLOPT_TIMEOUT, 10);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

       if(!count($post)){
          curl_setopt($ch, CURLOPT_POST, 0); 
       }else{
           curl_setopt($ch, CURLOPT_POST, 1); 
           if(!$bildQuery){
               //multipart/form-data
               curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
           }else{
               //application/x-www-form-urlencoded
               curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
           }
          
       }
      
       curl_setopt_array($ch, $config);
       
       $answer = [
           'output' => curl_exec($ch),
           'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
       ];      
      
       curl_close ($ch);
       return $answer;
     }
}
