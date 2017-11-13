<?php namespace Glavred\Controllers;

use  Glavred\Core\View;

class WelcomeController
{  
        /*
         * $request->url - has current url path
         * 
         */
        public function index($request){   
           
            View::$data = [
                'date'    => date('d.m.Y H:i'),
                'php'     => phpversion(),
                'version' => GLAVRED_VERSION
            ];     
      
            return View::render('welcome', 'main');
	}   
                
} 