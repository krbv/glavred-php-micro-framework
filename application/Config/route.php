<?php 

/* START PAGE 
    [
            'controller' => "Welcome",
            'get' =>true, // default false
            'action' => 'store' // default action
            'access' => 'User',  // default 'Guest',
			
            'path' => "/",
            'method' => "GET",  
            'method' => "GET", 
            'jsonOnly' => true, 
            'namespace' => "Some\Name\Space",

           'host' => 'domain.zone', or array   ['doment1','domen2'] 
    ],
*/

return [
    
/* START PAGE */
    [
            'path' => "/",
            'method' => "GET",  
            'controller' => "Welcome",

    ],
    
/* END_START PAGE */
    
];

 