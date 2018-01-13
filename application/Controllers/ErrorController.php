<?php namespace Glavred\Controllers;

use  Glavred\Core\View;

class ErrorController
{  
    
        // 401 - Access denied. 
        public function error401(){   
           View::setHeader('HTTP/1.1 401 Unauthorized');
           
           return View::renderHTML('Access denied');   
	}   
        
        
        // 404 - Not found.
        public function error404(){  
            View::setHeader([
                'HTTP/1.1 404 Not Found',
                'Status: 404 Not Found']);
            
            return View::renderHTML('Page not found');
	}   
        
        
        //method not allowed.
        public function error405(){  
            View::setHeader('HTTP/1.0 405 Method Not Allowed');
            
            return View::renderHTML('Method Not Allowed');  
	}          
        
        
        //Internal server error.
        public function error500(string $message = ''){   
             View::setHeader([
                    'HTTP/1.1 500 Internal Server Error',
                    "X-Error-Message: $message"
             ]); 
             
            return View::renderHTML($message);
	}
        
                
} 