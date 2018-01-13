<?php  

return [

    'view' => [
        'public' => PUBLIC_DIR,
        'base' => ROOT_DIR.'/resources/view/',
        'template' => ROOT_DIR.'/resources/view/layouts/',
        'page' => ROOT_DIR.'/resources/view/page/',
        'css' => [
            'source' => ROOT_DIR.'/resources/assets/css/',
            'cache' => PUBLIC_DIR.'/cache/css/',
            'relative' => '/cache/css/',
        ],
        
        'js' => [
            'source' => ROOT_DIR.'/resources/assets/js/',
            'cache' => PUBLIC_DIR.'/cache/js/',
            'relative' => '/cache/js/'  
        ],  
        
        'font' => [
            'source' => ROOT_DIR.'/resources/font/',
            'cache' => PUBLIC_DIR.'/cache/font/',
        ],     
        
    ],
    
    
    'log_exception' => ROOT_DIR.'/log/errors/',
    
 
];

 