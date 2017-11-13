<?php 

class MyExceptions extends \Exception{
    
   
    public function __construct($message=null) {
        @ob_end_clean(); // во view есть буферизация, закрываем если возникла ошибка
        $this->logging($message);
        parent::__construct($message);
        $this->showError();
        //$this->gotoface();
        die();
    }  
    
    public function logging($message){
        
        $ind = date("Y-m-d H:i")." [".get_called_class()."] [".$message."] [".$_SERVER['REQUEST_URI']."]\r\n";
        $ind.="Class: [".debug_backtrace()[3]['class'].'] action: ['.debug_backtrace()[3]['function']."]\r\n";
        $ind.=debug_backtrace()[2]['line'].': '.debug_backtrace()[2]['file']."\r\n";
        if(!empty(debug_backtrace()[3]['line'])){
             $ind.=debug_backtrace()[3]['line'].': '.debug_backtrace()[3]['file']."\r\n\r\n";
        }
       
        
        //ADMIN: SHOW ERROR MESSAGE
        if(defined('DEBUG_MODE')  && DEBUG_MODE == 1) {
            echo "<pre>";
            echo htmlspecialchars($ind);
            echo "</pre>";
        }
        
        $path = Glavred\Singleton\Config::link()->path('log_exception').date('d-m-Y').'.txt';
        if($fp = fopen($path, 'a')){
            fwrite($fp, $ind);
            fclose($fp);
        }else{
            //send email
        }
    } 
    
    public function showError(){
        echo "Something went wrong";
    }
    
    
    public function gotoface(){
        if($_SERVER['REQUEST_URI'] != '/'){
            Route::ErrorPage404();
        }
    }  

}




class RouteException extends MyExceptions {
    public function __construct($message=null) {
        parent::__construct($message);
    }
}


class ControllersException extends MyExceptions {
    public function __construct($message=null) {
  
        parent::__construct($message);
        
       
    }
}

class LoaderException extends MyExceptions {
    public function __construct($message=null) {
        parent::__construct($message);
    }
}


class PathsException extends MyExceptions {
    public function __construct($message=null) {
        parent::__construct($message);
    }

}


class ViewsException extends MyExceptions {
    public function __construct($message=null) {
        parent::__construct($message);
    }
    
}

class ModelsException extends MyExceptions {
    public function __construct($message=null) {
        parent::__construct($message);
    }
    
}

class SingeltonException extends MyExceptions {
    public function __construct($message=null) {
        parent::__construct($message);
    }
    
}

class ConfigException extends MyExceptions {
    public function __construct($message=null) {
        parent::__construct($message);
    } 
}

class HelpersException extends MyExceptions {
    public function __construct($message=null) {
        parent::__construct($message);
    } 
}

class DatabaseException extends MyExceptions {
    public function __construct($message=null) {
        parent::__construct($message);
    } 
}


