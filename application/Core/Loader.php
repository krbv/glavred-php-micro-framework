<?php namespace Glavred\Core;

use  Glavred\Singleton\Config;

class Loader
{
   public $autoladed = [];
       
   public function loadClass($class){
  
        $path = explode('\\', $class);

        $prefix = array_shift($path);
        
        if($prefix == 'Glavred'){
            $className = array_pop($path);
            $file =  APP_DIR.'/'.implode('/', $path).'/'.$className.'.php';
  

        }elseif($prefix=='vendor'){
        }else{
            throw new \LoaderException("Unknown prefix: $prefix");
        }
        
        //check file
        if(is_file($file) && is_readable($file)){
            require($file);
            $this->autoladed[] = $file;
        }else{
            throw new \LoaderException("File  '$file' doesn't exists or not readable");
        }
    }
    
    
    //Производит загрузку модулей
    public static function loadModules(){
        

        foreach (Config::link()->modules() as $starterClass){
            $class = new $starterClass();
            if(method_exists($class, 'boot')){
                $class->{'boot'}();
            }else{
                throw new \LoaderException("$starterClass method boot not found");
            }
        }
        
    }
    
}


