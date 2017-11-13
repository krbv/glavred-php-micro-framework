<?php namespace Glavred\Models;

use Glavred\Core\Model;

class DbExample extends Model {
    
/*
    protected $table = "test"; // name of table
   
    public function get(){
        //if not found get return [], first returns false
         $userInfo = $this->db
                        ->select(['id','text']) // array or single value
                        ->where('num', '>', '10')
                        ->where('text','hi!')
                        ->orderBy('id') // array or single value
                        ->limit(2)
                        ->offset(10)
                        ->get(); // first() for one value, first('text') pluck text
         

    }
    

    public function add(){
        
        //returns true or info true/false about each one
         $this->db->add(['text' => 'hi!']); 
         $this->db->add(
                 [
                  ['text' => 'hi!'],
                  ['text' => 'good morning!']
                 ]
         ); 
    }    
    
    public function update(){
        //returns how many rows was updated, -1 wrong query
        $this->db
                        ->where('text','hi!')
                        ->limit(2) // limit also work
                        ->update(['text' => 'hello!']);
         

    }   
    
    public function delete(){
         //returns how many rows was deleted, -1 wrong query
         $this->db
                        ->select(['id','text']) // array or single value
                        ->where('num', '>', '10')
                        ->where('text','hi!')
                        ->orderBy('id') // array or single value
                        ->limit(2)
                        ->offset(10)
                        ->delte(); // first() for one value, first('text') pluck text
         

    } 
 * 
 * 
 */
}
