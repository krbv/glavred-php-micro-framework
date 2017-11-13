<?php namespace Glavred\Core;

/*
Класс-маршрутизатор для определения запрашиваемой страницы.
> цепляет классы контроллеров
> создает экземпляры контролеров страниц и вызывает действия этих контроллеров.
*/

use Glavred\Singleton\Request;
use Glavred\Singleton\Config;
use Glavred\Core\Access\AccessController;

class Route
{     
    
        
       /*
       * [routemap]: if action is not set => index
       * [routemap]: allow post data => post = true
       * [routemap]: allow get data => get = true
       * [routemap]: attr "folder" for path to model and controller 
       */
        private static function routeFinder($check , $request){
          
                 foreach(Config::link()->route() as $config){
                     
                       $url = $config['path'];
                       
                       //GET Установлен по умолчанию
                       if(!isset($config['method'])){$config['method'] = "GET";}
                       //если методы не совпадают пропускаем
                       if($config['method'] !== $request->server('method')){                   
                            continue;
                       }

                       if (preg_match(  '|^'.$url.'$|'  , $check, $matches)){ 
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
                  Route::goto404('routeMapChecker cant find the way');
             }
             
            //запоминаем текущий url
            $request->url = $config["url"];            

             //check user type and rights
             $accessLevel = empty($config['route']['access']) ? 'Guest' 
                     : $config['route']['access']; 
             (new AccessController())->swticher($accessLevel);

             
             //если есть гет параметры но они не разрешены -> goto 4
             if(!empty($get) && ($config['route']["get"]!==true)) {Route::goto404(); }
                         
             //if JsonOnly
             if(!empty($config['route']["jsonOnly"]) && $config['route']["jsonOnly"] == true){
                 //print_r($request->server['']);
                 if(strpos($request->server('HTTP_ACCEPT'), 'application/json') === false){
                      self::methodNotAllowed();
                 }

             }
             
             // добавляем префиксы, находим пути
             $controllerName = $config['route']['controller'].'Controller';
             $actionName = 
                        empty($config['route']['action']) 
                        ? 'index' 
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
        
        

	static function goto404(){            
            if(DEBUG_MODE == true){
                die('goto404');
            }
            
            header("HTTP/1.1 404 Not Found"); 
            header("Status: 404 Not Found"); 
	    //header('Location: /404');
            exit;
       
         }

	static function methodNotAllowed(){            
            header("HTTP/1.0 405 Method Not Allowed"); 
            exit();
       
         } 
         
 	static function serverError($text = ''){ 
            header('X-Error-Message: '.$text, true, 500);
            die($text);
         } 
         
	static function AccessDenied(){            
            die('AccessDenied');
         }              
        
        
    	static function redirect($url, $code=302){
            header( "Location: $url", true, $code );
            exit;
        }        
        
}
