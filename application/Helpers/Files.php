<?php

namespace Glavred\Helpers;

class Files {
    

    public static function getFileContent($pathOrArray){
        
        $paths =  (array) $pathOrArray;
        $output = '';
        array_map(function($path) use (&$output){
            if(!file_exists($path)){
                return new \HelpersException("$path is not found");
            }
            $output .=file_get_contents($path);
        }, $paths);
        
        return $output;
    }
    
    
    public static function writeFile($path, $text, $rewrite = false, $TrowRewriteError = true){
            
            
            //если файл существует а перепизаписывать запрещено выход
            if($rewrite == false && file_exists($path)){
                if($TrowRewriteError){
                    throw new \HelpersException(__CLASS__." : ".__FUNCTION__." - can't rewrite $path");
                }else{
                    return false;
                }
            }
            //если нет файла создаем его
            fclose(fopen($path, "a+b"));
            //открываем
            if(!$f = fopen($path, "r+b")){
                throw new \HelpersException(__CLASS__." : ".__FUNCTION__." - can't open $path");               
            }
            //блокируем файл
            flock($f, LOCK_EX);
            //записываем в файл
            if (fwrite($f, $text) === FALSE) {fclose($f); throw new \HelpersException(__CLASS__." : ".__FUNCTION__." - can't write $path");   }
            fclose($f);

            return file_exists($path);
   }   
   
   
   public static function copyFolder($source, $destination) {
       
            if(!is_dir($source)){
                return new \HelpersException(__CLASS__." : ".__FUNCTION__." - $source source folder doesn't exist"); 
            }

            $dir = opendir($source);
            @mkdir($destination);
            while(false !== ( $file = readdir($dir)) ) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    if ( is_dir($source . '/' . $file) ) {
                        recurse_copy($source . '/' . $file,$destination . '/' . $file);
                    }
                    else {
                        copy($source . '/' . $file,$destination . '/' . $file);
                    }
                }
            }
            closedir($dir);
    }

    
    public static function gzCompressor($source, $saveTo = null, int $level = 9){ 

        if(is_array($source)){
            $output = [];
            foreach ($source as $path){
                $output[$path] = self::{__FUNCTION__}($path, $saveTo, $level);
            }
            return $output;
        }
               
        if('gz' === pathinfo($source, PATHINFO_EXTENSION)){ 
            return false; 
        } 

        $destination = ($saveTo) ? $saveTo : $source . '.gz';        
        if ($fp_out = gzopen($destination, 'wb'.$level)) { 
            if ($fp_in = fopen( $source  ,  'rb')) { 
                while (!feof($fp_in)){
                    gzwrite($fp_out, fread($fp_in, 1024 * 512));
                }
                fclose($fp_in); 
            } else {
                 gzclose($fp_out); 
                return false;
            }
            gzclose($fp_out); 
        } else {
            return false;
        }

        return $destination; 
    } 
    
    
}
