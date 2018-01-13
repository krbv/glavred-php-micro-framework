<?php namespace Glavred\Modules\Example;

use Glavred\Core\Module\StarterInterface;
use Glavred\Singleton\Config;

class Starter implements StarterInterface 
{  
    
    public function boot(){

  
        $currentFolder = realpath(dirname(__FILE__));
        Config::link()->appendSettings($currentFolder.'/Config/route.php' , 'route');
        
    }

               
} 