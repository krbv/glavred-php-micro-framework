<?php namespace Glavred\Helpers;

class Converter {
    
    public static function objectToArray($array){
        return json_decode(json_encode($array), true);
    }
    
    /**
      * Переформатирует массив по указаному ключу
      *  [0][title] => Контакты
      *  [1][title] => Контакты
      *  [2][title] => Места
      *   || CONVERT BY TITLE ||
      *  [Контакты] => Array
      *           [0] [title] => Контакты
      *           [1] [title] => Контакты	
      *   [Места=> Array
      *           [0] [title] => Места
      *
      * @param array $data массив для обработки
      * @param string $key ключ по которому будем собирать
      * @return array
    */      
    public static function assembleArrayByKey(array $data, string $key) : array
    {
         foreach($data as $item){
             $item = (array) $item;
             $dir[$item[$key]][] = $item;
         }
         
         return empty($dir) ? [] : $dir;
    }

}
