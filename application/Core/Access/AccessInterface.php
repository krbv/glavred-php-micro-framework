<?php  namespace Glavred\Core\Access;


interface AccessInterface
{
    public function check($request): bool;
        
    public function ifSucceed();
    
    public function ifFail();
    
}