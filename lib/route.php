<?php

class Route
{

    public bool $auth = false;
    public bool $logined = false;
    public bool $admin = false;

    public function Auth()
    {

        $this->auth = true;
        
    }
    public function LogedIn()
    {
        $this->logined = true;
        $this->auth = false;
    }

    public function Admin() {
        $this->admin = true;
    }

    public static function getBreadcrumbs()
    {
        // Get the current request URI
        $parsed_url = parse_url($_SERVER['REQUEST_URI']);
        $reqUri = isset($parsed_url['path']) ? trim($parsed_url['path'], '/') : '/';
        
        // Split the URI into parts
        $segments = explode('/', $reqUri);
        
        // Define the base URL
        $baseUrl = _BASE_DIR_;
        if ($baseUrl && strpos($reqUri, $baseUrl) === 0) {
            $segments = explode('/', substr($reqUri, strlen($baseUrl)));
        }

        return $segments;
    }

    public static function is($pattern)
    {
        // Get the current request URI
        $parsed_url = parse_url($_SERVER['REQUEST_URI']);
        $reqUri = isset($parsed_url['path']) ? $parsed_url['path'] : '/';

        // Remove base URL (if any) from request URI
        $baseUrl = _BASE_DIR_;
        if (strpos($reqUri, $baseUrl) === 0) {
            $reqUri = substr($reqUri, strlen($baseUrl));
        }
        
        // Match the current URI with the given pattern (basic matching, can be expanded)
        return fnmatch($pattern, $reqUri);
    }

    private function check_auth()
    {

        
        if ($this->admin) {
            if (!isset($_SESSION['user'])) {
                http_response_code(404);
                header('HTTP/1.0 404 Not Found');
                page::render('system/404');
                exit();
              
            }
            if (!in_array($_SESSION['user']['role'], ['Admin'])) {
                http_response_code(404);
                header('HTTP/1.0 404 Not Found');
                page::render('system/404');
                exit();
            }
        }
        if ($this->auth) {
            $headers = getallheaders();
            if (!array_key_exists('Authorization',$headers)) {
                http_response_code(403);
                header('HTTP/1.0 403 Forbidden');
                header('Content-Type: application/json');
                $result = [
                    'success' => false,
                    'message' => 'Unauthorized'
                ];
                echo json_encode($result);
                die;
            }
        }

        if ($this->logined) {
            
            if (!isset($_SESSION['user'])) {
                // redirect to login page
                header('Location: /login');
            }
        }
    }

    private function simpleRoute($method, $route)
    {


        $parsed_url = parse_url($_SERVER['REQUEST_URI']);

        if (isset($parsed_url['path'])) {
            $reqUri = $parsed_url['path'];
        } else {
            $reqUri = '/';
        }
        

        $baseUrl = _BASE_DIR_;
        if (strpos($reqUri, $baseUrl) === 0) {
            $reqUri = substr($reqUri, strlen($baseUrl));
        }


        // $R_method = $_SERVER['REQUEST_METHOD'];
        $method=$_SERVER['REQUEST_METHOD'];
        if ($reqUri == $route) {
            $params = [];
            $func = $this->_function($route) . $method;

            if ($func === '/GET') $func = "Index";
            if ($func === '/POST') $func = "Index";
            
         
            
            if (function_exists($func)) {
                $this->check_auth();
                call_user_func_array($func, $params);
            } else {
                !defined('_DEVMODE') or die("simpleroute : $func() not implemented!");
            }
            exit();
        }
    }

    protected static function _function($str)
    {
        return preg_replace_callback("~[/](\w)~", function ($m) {
            return strtoupper($m[1]);
        }, $str);
    }

    function add($route, $method)
    {
        
  
        //will store all the parameters value in this array
        $params = [];

        $function = '';

        //will store all the parameters names in this array
        $paramKey = [];

        //finding if there is any {?} parameter in $route
        preg_match_all("/(?<={).+?(?=})/", $route, $paramMatches);

        //if the route does not contain any param call simpleRoute();
        if (empty($paramMatches[0])) {
            $this->simpleRoute($method, $route);
            return;
        }

        //setting parameters names
        foreach ($paramMatches[0] as $key) {
            $paramKey[] = $key;
        }


        $parsed_url = parse_url($_SERVER['REQUEST_URI']);

        if (isset($parsed_url['path'])) {
            $reqUri = $parsed_url['path'];
        } else {
            $reqUri = '/';
        }

        //exploding route address
        $uri = explode("/", $route);

        //will store index number where {?} parameter is required in the $route 
        $indexNum = [];

        //storing index number, where {?} parameter is required with the help of regex
        foreach ($uri as $index => $param) {
            if (preg_match("/{.*}/", $param)) {
                $indexNum[] = $index;
            } else {
                if ($param !== '')
                    $function .= '/' . $param;
            }
        }

        //exploding request uri string to array to get
        //the exact index number value of parameter from $_REQUEST['uri']
        $reqUri = explode("/", $reqUri);

        //running for each loop to set the exact index number with reg expression
        //this will help in matching route
        foreach ($indexNum as $key => $index) {

            //in case if req uri with param index is empty then return
            //because url is not valid for this route
            if (empty($reqUri[$index])) {
                return;
            }

            //setting params with params names
            $params[$paramKey[$key]] = $reqUri[$index];


            //this is to create a regex for comparing route address
            $reqUri[$index] = "{.*}";
        }



        //converting array to sting
        $reqUri = implode("/", $reqUri);

        //replace all / with \/ for reg expression
        //regex to match route is ready !
        $reqUri = str_replace("/", '\\/', $reqUri);


        $R_method = $_SERVER['REQUEST_METHOD'];
        $R_method=strtolower($R_method);

        //now matching route with regex
        if (preg_match("/$reqUri/", $route)) {

            $func = $this->_function($function) . $R_method;
            if ($func === 'get') $func = "Index";
            if ($func === 'post') $func = "Index";
            
            if (function_exists($func)) {
                $this->check_auth();
                call_user_func_array($func, $params);
            } else {

                !defined('_DEVMODE') or die("$func not implemented!");
            }
            exit();
        }
    }

    function notFound()
    {
        http_response_code(404);
        header("HTTP/1.0 404 Not Found");
        page::renderBlade('system/404');
        exit();
    }
}
