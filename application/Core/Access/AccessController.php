<?php namespace Glavred\Core\Access;

use Glavred\Singleton\Request;

class AccessController{


    public function swticher($type){
       
       $className = "\Glavred\Roles\\".$type."Access";
       $accessClass = new $className();
     
       return $accessClass->check(Request::link())  
                       ? $accessClass->ifSucceed() 
                       : $accessClass->ifFail();
       
    }
    
}
