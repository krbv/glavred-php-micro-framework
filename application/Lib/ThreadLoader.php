<?php  namespace Glavred\Lib;

class ThreadLoader {
    var $curl_for_url_parameters = array(); // for each url connection
    var $curl_parameters = array(); // for default connection
    var $curl_cookies = array();
    var $urls  = array();
    var $bacupfolder = false;
    
    function __construct() {
        $this->set_default_parameters();
    }

    
    function set_default_parameters(){

//          $ext_headers = array(
//
//            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
//            'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
//            'Accept-Charset: utf-8,windows-1251;q=0.7,*;q=0.5'
//           );        

           $this->curl_parameters = array(
                                   // CURLOPT_PORT               => 80, // возник баг с 89мв
                                    CURLOPT_RETURNTRANSFER     => 1,
                                    CURLOPT_BINARYTRANSFER     => 1, // передавать в binary-safe
                                    CURLOPT_CONNECTTIMEOUT     => 5, // таймаут соединения ( lookup + connect )
                                    CURLOPT_TIMEOUT            => 20, // таймаут на получение данных
                                    CURLOPT_USERAGENT          => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0',
                                    CURLOPT_VERBOSE            => 2, // уровень информирования
                                    CURLOPT_COOKIESESSION      => 1, // для указания текущему сеансу начать новую "сессию" cookies
                                    CURLOPT_HEADER             => 0, // заголовок не получаем
                                    CURLOPT_FOLLOWLOCATION     => 1, // следовать редиректам
                                    CURLOPT_SSL_VERIFYPEER     => 0, // не проверять ссл
                                    CURLOPT_MAXREDIRS          => 7, // максимальное число редиректов
                                    CURLOPT_AUTOREFERER        => 1, // при редиректе подставлять в «Referer:» значение из «Location:»
                                    CURLINFO_HEADER_OUT        => 1, // при редиректе подставлять в «Referer:» значение из «Location:»    
                                    CURLOPT_HTTPHEADER         => $ext_headers,
                           );
    }
    
    function clear_data(){
        $this->curl_for_url_parameters = array();
        $this->curl_cookies = array();
        $this->urls = array();
        $this->set_default_parameters();
    }
    
    function check_set_url($url)
    {
       if(!in_array($url,$this->urls,true)){
           return false;
       }else{return true;}
    }
    
    function bacup_activate($savingfolder){
        if(!empty($savingfolder)){
            $this->bacupfolder = $savingfolder;
        }
    }
    
   function set_url($url){
       if(!isset($url)){return false;}
       $url=(array)$url;
       for($i=0;$i<count($url);$i++)
        {
          if(!in_array($url[$i],$this->urls,TRUE))
           {
             $this->urls[]=$url[$i];
           } 
        }
   }
   
   function show_link(){
       return $this->urls;
   }
    
   function set_cookies($page, $cookies){
       if($this->check_set_url($page))
       {
            if(is_array($cookies)){
                foreach($cookies as $key => $value){
                   $this->curl_cookies[$page][$key]=$value;
                }
            }
            else{
                $cookies=str_replace(' ','',$cookies);
                if(strpos($cookies,':')===false){
                  $cookies=str_replace('=',':',$cookies);  
                }
                $cookies=str_replace(' ','',$cookies);
                
                $cookies_set=explode(';',$cookies);
                for($i=0;$i<count($cookies_set);$i++){
                    $cookies_couple=explode(':',$cookies_set[$i]);
                    $this->curl_cookies[$page][$cookies_couple[0]]=$cookies_couple[1];  
                }
                

            }
       }
    }
    
    function cookiesToStr($url){
       $cookies_str=false;
       if($this->check_set_url($url))
       {
           if(is_array($this->curl_cookies[$url]))
           {
                foreach($this->curl_cookies[$url] as $key => $value){
                    $cookies_str.="$key=$value; ";
                }
                if($cookies_str){$cookies_str=mb_substr($cookies_str,0,-2,"UTF-8");}
           }
       }
       return $cookies_str;
    }
    
    function change_parameters($set_parameter, $set_value=null){
         if(!isset($set_parameter)){return false;}
         if((is_array($set_parameter)) && ($set_value==null))
         {
             foreach ($set_parameter as $key => $value) {
                 $this->curl_parameters[$key]=$value; 
             }
             return true;
         }elseif((!is_array($set_parameter)) && (!is_array($set_value))){
           $this->curl_parameters[$set_parameter]=$set_value;  
           return true;
         }     
     }
      //$curl_for_url_parameters
     function change_url_parameters($url, $set_parameter, $set_value=null){
       if($this->check_set_url($url))
       {
           if(!isset($set_parameter)){return false;}
            if((is_array($set_parameter)) && ($set_value==null))
            {
                foreach ($set_parameter as $key => $value) {
                    $this->curl_for_url_parameters[$url][$key]=$value; 
                }
                return true;
            }elseif((!is_array($set_parameter)) && (!is_array($set_value))){
              $this->curl_for_url_parameters[$url][$set_parameter]=$set_value;  
              return true;
            }     
       }
     }     
     
   function get_cookies($url){
    $ch = curl_init($url);
    curl_setopt_array( $ch, $this->curl_parameters );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // get headers too with this line
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $content = curl_exec($ch);
    // get cookies
    $cookies = array();
    preg_match_all('/Set-Cookie:(?<cookie>\s{0,}.*)$/im', $content, $cookies);
    return $cookies['cookie']; 
   }
     
     
    function get_data($callback=false){

        if(count($this->urls)===0){return false;}
        
	 $mh = curl_multi_init();
	 $chs = array();
	 foreach ( $this->urls as $url ) {
            $chs[] = ($ch = curl_init());
            curl_setopt_array( $ch, $this->curl_parameters ); // устанавливаем параметры
            
            if($this->curl_for_url_parameters[$url]){
                curl_setopt_array( $ch, $this->curl_for_url_parameters[$url]); // устанавливаем параметры персональные
            }
            
            curl_setopt( $ch, CURLOPT_URL, $url );
            
            //set cookies
            if($this->cookiesToStr($url)){
              curl_setopt( $ch, CURLOPT_COOKIE, $this->cookiesToStr($url) );
            }
            curl_multi_add_handle( $mh, $ch );
	 }
			
	// если $callback задан как false, то функция должна не вызывать $callback, а выдать страницы как результат работы

        if ( $callback === false ) {
                $results = array();
        }

       $running = null;

        do {
             curl_multi_exec( $mh, $running );
             do {
                // получаю информацию о текущих соединениях
                $info = curl_multi_info_read($mh);
                if ( is_array( $info ) && ( $ch = $info['handle'] ) ) {
                    // получаю содержимое загруженной страницы
                    $content = curl_multi_getcontent( $ch );
                    // скаченная ссылка
                    $url = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL );
                    if ( $callback !== false ) {
                      //$url, $content, $info['result'], $ch
                          // вызов callback-обработчика            

                          $callback(array('url'=>$url,'content'=>$content)  );
                    }
                    else {
                       // добавление в хеш результатов
                       $results[ $url ] = array( 'content' => $content, 'status' => $info['result'], 'status_text' => curl_error( $ch ) );
                    }
                    //make bacup
                    if(($this->bacupfolder) && (!empty($content))){
                        $gzdata = gzencode($content, 9);
                        $fp = fopen($this->bacupfolder.time().'_'.md5($url).".gz", "w");
                        fwrite($fp, $gzdata);
                        fclose($fp);
                    }
                }

             } while ($info);


            } while ( $running > 0 );

            foreach ( $chs as $ch ) {
                    curl_multi_remove_handle( $mh, $ch );

                    curl_close( $ch );
            }
            curl_multi_close( $mh );

            // результаты
            return ( $callback !== false ) ? true : $results;
    
     }
    
   
}
