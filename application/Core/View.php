<?php namespace Glavred\Core;


/*
 * СТРУКТУРА
 * html['title']
 * html['description']
 * css[] - array
 * js[] - array
 * data

 *  */
//use Glavred\Helpers\Network;
//use Glavred\Helpers\Password;
//use Glavred\Helpers\Files;
use Glavred\Singleton\Config;
use Less_Parser;
use ViewsException;
use Glavred\Helpers\{Files,Network};

class View
{
       static public $data = [];
       static private $page;
       static private $configSettings;

       /* TYPES OF VIEWS */
       static public function render(string $page, string $template = null, bool $compress = null) {
           
           self::$page = $page;
           self::$configSettings = $config = Config::link();
           $config = Config::link();
           if($template){
                $path = $config->path('view/template')."$template.php";
           }else{
               $path = $config->path('view/page')."$page.php";
           }
           echo self::cache($path, $compress);
       }           
       
       static public function renderCSS($text){
           header("HTTP/1.1 200 OK");
           header("Status: 200 OK");
           header("Content-Type: text/css"); 
           header("X-Content-Type-Options: nosniff");           
           echo $text;       
       }
        
       static public function renderJS($text){
            header("HTTP/1.1 200 OK");
            header("Status: 200 OK");
            header("Content-Type: application/javascript");        
            echo $text;       
       }  
       
       static public function renderJSON($arr){
            header('Content-Type: application/json; charset=utf8');
            echo json_encode($arr);     
       }    
       
       static public function renderHTML(string $text){
          echo $text; 
       }          
       
/* END */ 
       
       
       
       static public function assembleCSS(...$names) : string
       {
           $paths = self::checkMakePaths($names, 'css');

           $content = '';
           $less = new Less_Parser();
           foreach($paths as $path){
               try{
                  $content .= ($less->parseFile($path))->getCss(); 
               } catch (\Less_Exception_Parser $ex) {
                   ob_get_clean(); 
                   return new ViewsException("Can't parse less with message: "
                           . "\r\n\r\n".$ex->getMessage()."\r\n\r\n"); 
               }
           }
           
           $hash = self::hash($paths, $content);
           self::saveCacheFile($hash, 'css', $content);
           
           return self::$configSettings->path('view/css/relative')."$hash.css";
       }
              
       
    
       static public function assembleJS(...$names) : string
       {
           
           $paths = self::checkMakePaths($names, 'js');
           
           $jsText = Files::getFileContent($paths);
              
           $hash = self::hash($paths, $jsText);
           self::saveCacheFile($hash, 'js', $jsText);

           return self::$configSettings->path('view/js/relative')."$hash.js";
 
       }    
       
       
       static private function saveCacheFile(string $hash, string $type, string $code) : bool
       {
 
           if(!in_array($type, ['css','js'])){
                return new ViewsException("Allow only css and js types");  
           }
           
           $cacheFolder = self::$configSettings->path("view/$type/cache");

           if(file_exists($saveTo = "$cacheFolder$hash.$type")){
               return 0;
           }
           
           $compressed = (self::$configSettings->view("$type/compress") == true) 
                            ? self::{$type."Compress"}($code) 
                            : $code;
               
            if(!Files::writeFile($saveTo, $compressed)){
                      return new ViewsException("Can't write cache file");  
            }else{
                if(self::$configSettings->view("$type/gzip") == true){
                    Files::gzCompressor($saveTo);
                }
                self::copyFonts();
                self::cleanup();
            }  

             return 1;           
       }
       
       
       static private function copyFonts():int
       {

           $moved = 0;
           $sourceFolder = self::$configSettings->path("view/font/source");
           $destinationFolder = self::$configSettings->path("view/font/cache");
           if(!is_dir($sourceFolder) || !is_dir($destinationFolder)){
               throw new ViewsException("Can't copy fonts dirs are no exists");
           }
           
            $fonts = self::$configSettings->view("fonts");
            
            foreach($fonts as $font){
                $copied = $destinationFolder.$font;
                if(is_dir($copied)){ continue; }
                $source = $sourceFolder.$font;
                if(!is_dir($source)){ 
                    throw new ViewsException("Folder $source doesnt exist");
                }
                Files::copyFolder($source, $copied);
                $moved++;
            }
           
           return $moved;
       } 
       
       
       static private function cleanup() : ?array
       {
           $cleanTypes = ['css','js'];
           $deleted = [];
           if(self::$configSettings->view("cleanup/active") !== true){
               return null;
           }
           
           foreach($cleanTypes as $type){
               $cacheFolder = self::$configSettings->path("view/$type/cache");
                foreach(glob($cacheFolder.'*.css') as $path){
                       if(time()- filemtime($path) > self::$configSettings->view("cleanup/after")){
                           unlink($path);
                           $deleted[] = $path;
                       }
                }              
               
           }     
           return $deleted;
       }      
       
       static private function checkMakePaths(array $names, string $type) : array
       {
           if(!in_array($type, ['css','js'])){
                return new ViewsException("Allow only css and js types");  
           }elseif(empty($names)){
               return new ViewsException("nothing to do, values are empty");  
           }
           
           return preg_filter('/^/',  
                   self::$configSettings->path("view/$type/source"), $names);       
       }
       
       
       
       static private function hash(array $paths, string $text) : string{
            return hash("crc32", implode('', $paths).md5($text));
        }       
       
       
       static public function page(){
           return self::render(self::$page); 
       }
       
       
       static public function join(string $path){
           return self::cache((Config::link())->path("view/base")."$path.php");
       }
       
       
       static public function safe(string $value ){
           return htmlspecialchars(htmlspecialchars_decode($value));
       }

       
       static public function htmlCompress(string $code){

                  $code = preg_replace('/[\s]{2,}/', ' ', $code); 
                  $code = str_replace(['class=""'], "", $code);
                  $code = str_replace(["\r\n","\t"], "", $code);
                  
                  $code = str_replace('" >', '">', $code);
                  $code = str_replace('> <', "><", $code);
                  $code = str_replace('<div  ', "<div ", $code);
                  
                  return $code;
        }
        
       static public function jsCompress(string $code){
           
           $answer = Network::curl('https://closure-compiler.appspot.com/compile', [
                                          'compilation_level' => 'SIMPLE_OPTIMIZATIONS',
                                          'output_format' => 'text',
                                          'output_info' => 'compiled_code',
                                          'js_code' => $code,
                                    ],[],true);
           
           if($answer['code'] == 200 && !empty($answer['output'])){
               return $answer['output'];
           }else{
               return $code;
           }
         
        }
                
        public static function cssCompress(string $style) : string
        {
                   /* удалить табуляции, пробелы, символы новой строки */
                   /* удалить комментарии */
                   /* удалить двойные пробелы */
                   $textReplaced = preg_replace('/[\s]{2,}/', ' ', 
                           preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', 
                           str_replace(["\r", "\n", "\t"], '', $style)));

                   //массив замен
                   $replace = [
                           chr( 194 ) => ' ',
                           chr( 160 ) => ' ',                
                           '; ' => ';',
                           ' ;' => ';',
                           '} ' => '}',
                           ' }' => '}',
                           '{ ' => '{',
                           ' {' => '{',               
                           ': ' => ':',
                           ' :' => ':',
                           ', ' => ',',
                           ' ,' => ',',
                           ";}" => '}',
                       ];

                   return str_replace(array_keys($replace), array_values($replace), $textReplaced); 
       }
        
      static public function cache(string $path, bool $compress = null){
           
          if(!file_exists($path)){
               throw new ViewsException("Path is not found: $path");
          }
          
          if(is_null($compress)){
              $compress = (Config::link())->view("html/compress");
          }          
          
          ob_start();
          extract(self::$data);
          require $path;
          $content = ob_get_clean(); 
          return ($compress) ? self::htmlCompress($content) : $content;          
       }
       
 
}
