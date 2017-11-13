<?php namespace Glavred\Core;

use \Glavred\config\{ViewValues,Paths};


class Model
{
    protected $db;
    
    public function __construct() {
          if(isset($this->table)){
              $this->db = new Database($this->table);
          }

    }
    
    public function defaultConfig(){
        return ViewValues::$defaultSetting;
    }
    
        
    public function paths($query){
        return Paths::giveMeWay($query);
    }

}
