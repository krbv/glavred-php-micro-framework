<?php namespace Glavred\Singleton;

class Config extends AAASingletons
{
     
     private $folder = APP_DIR.'/Config/';
     private $settings = [];
     
     protected function __construct() {
        parent::__construct();
    }           
          
     public function __call($name, $arguments) {
        
        $foundData = [];   

        
        foreach($arguments as $one){
            $bits = explode('/', $one);
            $foundData[$one] = $this->getSettings($name);
            foreach ($bits as $bit){
              if(empty($foundData[$one][$bit])){
                        return false;
               }               
                $foundData[$one] = $foundData[$one][$bit];
            }

        }
        
        /*
         * if no $arguments return all file
         * if 1 = value
         * if more = array
         */
        switch (count($arguments)){
             case 0:
                 return $this->getSettings($name);
             case 1:
                 return $foundData[$arguments[0]];
            default:
                $array  = [];
                array_walk($arguments, function($item) use (&$array, $foundData){
                   $array[$item] = $foundData[$item];
                }); 
                return $array;
        }

    }

    
    public function mergeSettings($path, $name){
 
        $data = $this->getSettingsFromFile($path, true);
        $this->settings[$name] = array_merge($this->getSettings($name), $data);
    }
    
    
    
    
    
    private function getSettings($name){

        if(!isset($this->settings[$name])){
           $this->settings[$name] = $this->getSettingsFromFile($name);
        }
        return $this->settings[$name];
    }
    
    private function getSettingsFromFile($name, $fullPath = false){
        
       
        if($fullPath){ $path = $name;
        }else{         $path = $this->folder.$name.'.php';  }


        if(!file_exists($path)){
            return new \ConfigException("$path is not found");
        } return include $path;
        
          
    }

}