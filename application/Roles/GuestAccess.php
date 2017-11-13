<?php namespace Glavred\Roles;

use Glavred\Core\Access\{
    AccessController,
    AccessInterface
};


class GuestAccess extends AccessController implements AccessInterface{
	
    public function check($request) : bool 
    {      
        return true;
    }
    
    public function ifSucceed() 
    {

    }

    public function ifFail() 
    {
      
    }   

}
