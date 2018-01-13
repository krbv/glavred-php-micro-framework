<?php 

return [
   
    /* Какие использовать шрифты */
    'font' => [
        'copy' => ['fontawesome'],
        'gzip' => true,
    ],
 
    'html' => [
        'compress' => false,
    ],    
    
    'css' => [
        'compress' => true,
        'gzip' => true
    ],

   'js' => [
        'compress' => true,
        'gzip' => true,
    ],
    
    //чистка js и css
   'cleanup' => [
        'active' => true,
        'after' => 3600*24*31*3,
    ],
    
 
];

 