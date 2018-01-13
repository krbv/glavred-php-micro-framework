<?php namespace Glavred\Modules\Example\Controllers;

use Glavred\Core\View;

class ModuleExampleController 
{  
        public function action($request){               
            return View::renderHTML( "MODULE EXAMPLE");
	}  
} 