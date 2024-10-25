<?php
    
    // include '.env';
    define('_DEVMODE',true);
    
    

    if(file_exists('config.php')){
        include 'config.php';
    }

    include 'init.php';



    
   

    $route = new Route();


    //** Public routes */
    $route->add('/','get');
    $route->add('/login','get');
    $route->add('/logout','get');
    $route->add('/signout','post');

    $route->add('/about','get');
    $route->add('/contact','get');

    
    
    /**
     * Routes for API that needs api authentication by token
     */
    $route->Auth();
    $route->add('/api/v1','post');

    /**
     * Routes for logined users with login sessions
     */
    
    // $route->LogedIn();
    // $route->add('/dashboard','get');


    
    /**
     * Routes for admin users with adnin sessions
     */    
    // $route->Admin();
    // $route->add('/ad','get');
    // $route->add('/empty','get');  

    //User managment routes
    // $route->add('/api/user/register','post');
    


    //Parameters in route Test!
    /*
    $route->add("/","get");
    $route->add("/{slug}","get");
    $route->Auth();
    $route->add('/slug/{name}/{tel}','post');
    // $route->add('/api/login/{username}/{pass}','get');
    // $route->add("/api/trades/{id}","get");
    */
    $route->notFound();
    

    
?>