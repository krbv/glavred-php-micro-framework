<?php namespace Glavred\Singleton;

class Request extends AAASingletons
{
    public  $url;
    
    private $files;
    private $collection;


    protected function __construct() {
        parent::__construct();
        $this->collection = [
            'server' => $_SERVER,
            'get' => $_GET,
            'post' => $_POST,
            'cookie' => $_COOKIE,
            'all' => array_merge($_POST, $_GET)
        ];
        
        $this->files = $_FILES;
    }


    public function post($name = NULL) {
        return $name == null ? $this->collection['post'] :
            $this->collection['post'][$name];
    }
    
     public function get($name = NULL) {
        return $name == null ? $this->collection['get'] :
            $this->collection['get'][$name];
    }   
    
    public function server($name) {
        
        if($name == 'ip'){$name = 'REMOTE_ADDR';
        }elseif($name == 'path'){$name = 'DOCUMENT_ROOT';
        }elseif($name == 'url'){$name = 'REQUEST_URI';
        }elseif($name == 'browser'){$name = 'HTTP_USER_AGENT';
        }elseif($name == 'method'){$name = 'REQUEST_METHOD';
        }elseif($name == 'host'){$name = 'HTTP_HOST';}
        
        
        return empty($this->collection['server'][$name]) ? "" :
            $this->collection['server'][$name];
    }
    
    public function input($name = NULL) {

         if(!isset($name)){ return $this->collection['all'];}
        
         return empty($this->collection['all'][$name]) ? "" :
            $this->collection['all'][$name];       
    }
    
    public function cookie($name) {
         return empty($this->collection['cookie'][$name]) ? "" :
            $this->collection['cookie'][$name]; 
        
    }   
    
    public function file($name = null, $type = null){
        
        if(empty($name)){
            if(!empty($type)){
                throw new \ModelsException("You can't set type without file name");
            }
            return (count($this->files)) ? $this->files : []; 
        }else{
            if(!empty($type)){
                return $this->files[$name][$type];
            }
               
            if(!is_array($this->files[$name]['name'])){
                    return $this->files[$name];
            }

            //inpName multiple
            $output = [];
            foreach($this->files[$name] as $key => $values){
                  for($i=0;$i<count($values);$i++){
                      $output[$i][$key] = $values[$i];
                  }
            }
            return $output ?? null;
                   
        }
    }
    
    
    
}