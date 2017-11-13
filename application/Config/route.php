<?php 

/* START PAGE 
    [
            'controller' => "Welcome",
            'get' =>true, // default false
            'action' => 'store' // default index
            'access' => 'User',  // default 'Guest',
			
            'path' => "/",
            'method' => "GET",  
            'method' => "GET", 
            'jsonOnly' => true, 
            'namespace' => "Some\Name\Space",

           'host' => 'domain.zone',    
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

 