<?php namespace Glavred\Core;


use Glavred\Singleton\Request;
use Glavred\Singleton\Config;
use Glavred\Core\Access\AccessController;

class Route
{     
   
        private static function routeFinder($check , $request){
          
                 foreach(Config::link()->route() as $config){
                     
                       $url = $config['path'];
                       
                       //GET Установлен по умолчанию
                       if(!isset($config['method'])){$config['method'] = "GET";}
                       //если методы не совпадают пропускаем
                       if($config['method'] !== $request->server('method')){                   
                            continue;
                       }

                       if (preg_match(  '%^'.$url.'$%'  , $check, $matches)){ 
                           if(!empty($config['host'])){
                               if(is_array($config['host'])){
                                   if(!in_array($request->server('host'), $config['host'])){
                                        continue;
                                   }
                               }elseif($config['host'] !== $request->server('host')){
                                        continue;
                               }
                           }

                           return  [
                              'route' => $config,
                              'url' => $matches
                           ];
                       }
                 }
                 return false;           
        }
        
        static function start()
	{   

            $request = Request::link();
            
            //если есть гет параметры отбрасываем от URL
            $urlToArray = parse_url($request->server('url'));
    
            //GET из строки в массив
            @parse_str($urlToArray["query"], $get);
            
            //проверяем есть ли URL В URLMAP
            if(!$config = self::routeFinder($urlToArray["path"], $request)) {    
                  Route::notFound('routeMapChecker cant find the way');
                  return false;
             }
             
            //запоминаем текущий url
            $request->path = $request->url = $config["url"];              

             //check user type and rights
             $accessLevel = empty($config['route']['access']) ? 'Guest' 
                     : $config['route']['access']; 
             (new AccessController())->swticher($accessLevel);

             
             //если есть гет параметры но они не разрешены -> goto 4
             if(!empty($get) && ($config['route']["get"]!==true)) {
                 Route::notFound();
                  return false;
             }
                         
             //if JsonOnly
             if(!empty($config['route']["jsonOnly"]) && $config['route']["jsonOnly"] == true){
                 //print_r($request->server['']);
                 if(strpos($request->server('HTTP_ACCEPT'), 'application/json') === false){
                      self::methodNotAllowed();
                      return false;
                 }

             }
             
             // добавляем префиксы, находим пути
             $controllerName = $config['route']['controller'].'Controller';
             $actionName = 
                        empty($config['route']['action']) 
                        ? 'action' 
                        : $config['route']['action'];
             

             $namespace = empty($config['route']['namespace']) 
                     ? 'Glavred\Controllers\\'
                     : $config['route']['namespace'];

             
             $controllerWNS = $namespace.$controllerName;

   
             $controller = new $controllerWNS();
             if(method_exists($controller, $actionName)){
		  // вызываем действие контроллера
		  $controller->$actionName($request);
             }else{
                   throw new \RouteException("$actionName is not found in $controllerName");
             }            
             
	}
        
        /* ERRORS */

	static public function notFound(){
            if(!self::constructErrorClass(__FUNCTION__)){
                header("HTTP/1.1 404 Not Found"); 
                header("Status: 404 Not Found"); 
                return false; 
            }
        }

	static public function methodNotAllowed(){
            if(!self::constructErrorClass(__FUNCTION__)){
                header("HTTP/1.0 405 Method Not Allowed"); 
                return false; 
            }       
        } 
         
        static public function serverError($text = ''){  
             if(!self::constructErrorClass(__FUNCTION__, $text)){
                header('X-Error-Message: '.$text, true, 500);
                return false; 
            }
         } 
         
	static public function accessDenied(){   
            if(!self::constructErrorClass(__FUNCTION__)){
                header("HTTP/1.1 401 Unauthorized"); 
                return false;
            }           
        }     
         
        /* END_ERRORS */  
         
      
    	static function redirect($url, $code=302){
            header( "Location: $url", true, $code );
            exit;
        }    
        
        
  
        
        static private function constructErrorClass(string $type, $tdata = ''){
            $data = Config::link()->error($type);
            if(!empty($data['class']) && !empty($data['action'])){
                    $class = new $data['class']();    
                    if(method_exists($class, $data['action'])){
                       $class->{$data['action']}($tdata);
                       return true;
                    }else{
                        throw new \LoaderException("{$data['class']}"
                        . " method {$data['action']} not found");
                    }    
            }        
        }      
        
        
        
        
}
