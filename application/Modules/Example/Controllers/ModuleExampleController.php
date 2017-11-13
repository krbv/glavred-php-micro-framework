<?php namespace Glavred\Modules\Example\Controllers;

use Glavred\Core\View;

class ModuleExampleController 
{  
        public function index($request){               
            return View::renderHTML( "MODULE EXAMPLE");
	}  
} 