<?php 
return [
    
    'accessDenied' => [
            'action' => 'error401',
            'class'  => Glavred\Controllers\ErrorController::class,
    ],     
   
    'notFound' => [
            'action' => 'error404',
            'class'  => Glavred\Controllers\ErrorController::class,
    ],  
    
    'methodNotAllowed' => [
            'action' => 'error405',
            'class'  => Glavred\Controllers\ErrorController::class,
    ], 

    'serverError' => [
            'action' => 'error500',
            'class'  => Glavred\Controllers\ErrorController::class,
    ], 
     
];

 