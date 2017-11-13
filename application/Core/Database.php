<?php namespace Glavred\Core;

use Glavred\Singleton\DBConnector;

class Database {
    
    private $mysqli, $tableName;
    private $filter = [];

    public function __construct($tableName, $tableExistsCheck = true){
            $this->tableName = $tableName;
            $this->mysqli = DBConnector::link()->getConnect(); 
            if($tableExistsCheck){ $this->checkTable();}
    }

        
        public function add(array $inpData){

          if(empty($inpData)){
               throw new \DatabaseException("Array is empty");
          }   
          
          $data = $result = [];
          $errors = 0;
      
          //array must start from [0]
          if(!isset($inpData[0])){ $data[] = $inpData;  }else{ $data = $inpData; }
          
          $stmt = $this->mysqli->stmt_init();
        
          foreach($data as $item){
              $keys = "`".implode('`,`', array_keys($item))."`";
              $val = implode(',', array_values(array_fill(0, count($item), '?')));
              
              $stmt->prepare("INSERT INTO `".$this->tableName."`(".$keys.") VALUES (".$val.")");
              if ($stmt->errno) {
                     throw new \DatabaseException("Cant' add (#{$stmt->errno}) {$stmt->error}");
              }      
              //sss generation
              $s = implode("", array_values(array_fill(0, count($item), 's')));
              $stmt->bind_param($s, ...array_values($item));
              $result[]= $stmt->execute();
              //counterros
              if($stmt->affected_rows !== 1){  $errors++;  }
          }
                    
          $stmt->close(); 
          $this->filter = []; //erase filters
          
          if($errors == 0) {  return true; }
          else{
                return !isset($result[1]) ? $result[0] : $result; 
          }
     }   
     
     public function update(array $inpData){
        if(empty($this->filter['where'])){
            throw new \DatabaseException("Where is not set");
        }        
        list($queryStr, $ssValue) = $this->build_update_query($inpData);
        return $this->prepareQuery($queryStr, $ssValue)['affected'];
     }    

     public function delete() {
        if(empty($this->filter['where'])){
            throw new \DatabaseException("Where is not set");
        }
        list($queryStr, $ssValue) = $this->build_delete_query();
        return $this->prepareQuery($queryStr, $ssValue)['affected'];
     }    

     public function first($key = null){
        $this->filter['limit'] = 1;
        $data = $this->get();
        if(!empty($data)){return false;}
        return ($key) ? $data[0]->$key : $data[0];
    }
    
    public function get(){
        list($queryStr, $ssValue) = $this->build_select_query();
        $result = $this->prepareQuery($queryStr, $ssValue);
        while ($row = $result['items']->fetch_object()) {
            $results[] = $row;
        }
        return $results ?? [];
    }
    

    
    
    public function prepareQuery($queryStr, $ssValue){
        
      
         $result = [];
         $stmt = $this->mysqli->stmt_init();
         $stmt->prepare($queryStr);
         if ($stmt->errno) {
                   throw new \DatabaseException("Cant' get: (#{$stmt->errno}) {$stmt->error}");
         }      
         //sss generation
         if(!empty($ssValue)){
            $s = implode("", array_values(array_fill(0, count($ssValue), 's')));
            $stmt->bind_param($s, ...array_values($ssValue));
         }

         $stmt->execute();
         $result['affected'] = $stmt->affected_rows;
         $result['items'] = $stmt->get_result();
         $stmt->close();  

         $this->filter = []; //erase filters
         return $result;
    }  
        
      

    public function raw($query){
        $result = $this->mysqli->query($query);
        if($this->mysqli->error){
            throw new \DatabaseException("(#{$this->mysqli->errno}) {$this->mysqli->error}");
        }
      
        if ($result->num_rows > 0) {
            $data = [];
            while($row = $result->fetch_assoc()) {
                $data[] =$row;
            }
        }
        return $data ?? false;
    }       
        

/* FILTERS */
        
    public function offset(int $numeric)
    {
       if(empty($this->filter['limit'])){
           throw new \DatabaseException("Set limit first");
       }
       $this->filter['offset'] = $numeric;
       return $this;
    }    
         
    public function limit(int $numeric)
    {
       $this->filter['limit'] = $numeric;
       return $this;
    }    
    
    public function orderBy(string $name, $countdown = false)
    {
        $this->filter['orderBy'][$name] = ($countdown == true) ? "DESC" : "ASC";
        return $this;
    }       
    
    public function where($name, $type, $value = null, $and = true)
    {
         if(!isset($value)){ $value = $type;  $type = '=';}
         $this->filter['where'][] = [
             'name' => $name,
             'type' => $type,
             'value' => $value,
             'bridge' => !$and ? "OR" : "AND",
         ];
         return $this;
    }  
    
    public function whereOR($name, $type, $value = null)
    {
        if(!isset($this->filter['where'][0]) 
                || $this->filter['where'][0]['bridge'] !== 'AND'){
            throw new \DatabaseException("whereOR without where");
        }
        return $this->where($name, $type, $value, false);
    }          
            
    public function select( $rowName)
    {
       $this->filter['select'] = (array) $rowName;
       return $this;
    }         
 
/* FILTERS_END */
        
    
/* QUERY BILDS */

    private function build_select_query(){
        
        $queryStr = 'SELECT';
        $filter = $this->filter;
        $ssValue = [];
        
       /* SELECT */
        if(empty($filter['select'])){$queryStr .= ' *';}
        else{ $queryStr .= " ".implode(',',array_values($filter['select']));}
        /* FROM */
        $queryStr .= " FROM `{$this->tableName}`";
        
        $this->addWhereToQuery($queryStr, $ssValue);
        
        /* ORDER */
        if(!empty($filter['orderBy'])){ 
             $queryStr .= " ORDER BY";
             foreach($filter['orderBy'] as $key => $value){
                 $queryStr .= " `{$key}` {$value},";
             }
             $queryStr = substr($queryStr, 0, -1);
        } 
        $this->addLimitToQuery($queryStr, $ssValue);
        return [ $queryStr, $ssValue ];
    }
    
    
    
    private function build_delete_query(){
        $ssValue = [];
        $queryStr = "DELETE FROM `{$this->tableName}`";  
        $this->addWhereToQuery($queryStr, $ssValue);
        $this->addLimitToQuery($queryStr, $ssValue);
        return [ $queryStr, $ssValue ];
    }
    
    
     private function build_update_query($inpData){
        $ssValue = [];
        $queryStr = "UPDATE `{$this->tableName}` SET";
        
        foreach($inpData as $key => $value){
            $queryStr .= '`'.$key.'`=?,';
            $ssValue[] = $value;
        }
        $queryStr = substr($queryStr, 0, -1);
        $this->addWhereToQuery($queryStr, $ssValue);
        $this->addLimitToQuery($queryStr, $ssValue);
        return [ $queryStr, $ssValue ];
    }   
    
/* END_QUERY BILDS */     

    
    
    private function addWhereToQuery(string &$queryStr, array &$ssValue){
         /* WHERE */
        if(!empty($this->filter['where'])){
            $queryStr .= " WHERE";
            foreach($this->filter['where'] as $where){
                $queryStr .= " `{$where['name']}` {$where['type']} ? {$where['bridge']}";
                $ssValue[] =  $where['value'];
                $lastBride = $where['bridge'];
            }
            $queryStr = substr($queryStr, 0, -1*strlen($lastBride));
        }
    }
    
    
    
    private function addLimitToQuery(string &$queryStr, array &$ssValue){
        /* LIMIT, OFFSET */
         foreach (['limit','offset'] as $one){
           if(!empty($this->filter[$one])){
                $queryStr .= " ".strtoupper($one)." ?";
                $ssValue[] =  $this->filter[$one];
            }        
         }
    }  
        
        
        
     private function checkTable(){
           $res = $this->mysqli->query("SHOW TABLES LIKE '$this->tableName'");
           if(!$res->num_rows){
                throw new \DatabaseException("Table '$this->tableName' doesnt exists");
           }
            
     }
          
  
}