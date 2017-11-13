<?php namespace Glavred\Singleton;

use \DatabaseException;

class DBConnector extends AAASingletons
{
    private $mysqli, $connected = false;

    protected function __construct() {
        parent::__construct();
        $this->connect();
    }
    function __destruct() { $this->close(); }
      
     
    
    public function getConnect(){
        return $this->mysqli;
    }

    public function close(){
         if($this->connected){ $this->mysqli->close();  $this->connected = false; }
    }

    public function connect(){
        $config = Config::link();
        $this->mysqli = $mysqli = new \mysqli($config->bd('host'), 
               $config->bd('login'), $config->bd('password'),  $config->bd('bd'));

        if ($this->mysqli->connect_errno){
             return new DatabaseException("#$mysqli->connect_errno | $mysqli->connect_error");
        }

        $this->mysqli->set_charset("utf8");
        $this->connected = true;     
      
    }
    
}