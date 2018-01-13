<?php namespace Glavred\Helpers;

use DateTime;

class Date {

    public static function daysLeft($timeOrData, $fullData = false){
                
        if(( is_numeric($timeOrData) && (int)$timeOrData == $timeOrData )){
            $need = new DateTime(date('00:00 d-m-Y', $timeOrData));
        }else{
            $need = new DateTime(date('00:00 d-m-Y', strtotime($timeOrData)));
        }
        
        $current = (new DateTime('now'))->modify('00:00');
        
        $diff = $current->diff($need);

        return $fullData ? $diff : $diff->days;
    } 
    

}
