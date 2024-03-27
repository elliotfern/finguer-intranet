<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$url_root = $_SERVER['DOCUMENT_ROOT'];
$url_server = $_SERVER['HTTP_HOST'];
$base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];

define("APP_SERVER", $url_server); 
define("APP_ROOT", $url_root);
define("APP_WEB",$base_url);

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

class Route {
    private function simpleRoute($file, $route){
        //replacing first and last forward slashes
        //$_REQUEST['uri'] will be empty if req uri is /

        if(!empty($_REQUEST['uri'])){
            $route = preg_replace("/(^\/)|(\/$)/","",$route);
            $reqUri =  preg_replace("/(^\/)|(\/$)/","",$_REQUEST['uri']);
        }else{
            $reqUri = "/";
        }

        if($reqUri == $route){
            $params = [];
            include($file);
            exit();

        }

    }

    function add($route,$file){

        //will store all the parameters value in this array
        $params = [];

        //will store all the parameters names in this array
        $paramKey = [];

        //finding if there is any {?} parameter in $route
        preg_match_all("/(?<={).+?(?=})/", $route, $paramMatches);

        //if the route does not contain any param call simpleRoute();
        if(empty($paramMatches[0])){
            $this->simpleRoute($file,$route);
            return;
        }

        //setting parameters names
        foreach($paramMatches[0] as $key){
            $paramKey[] = $key;
        }

       
        //replacing first and last forward slashes
        //$_REQUEST['uri'] will be empty if req uri is /

        if(!empty($_REQUEST['uri'])){
            $route = preg_replace("/(^\/)|(\/$)/","",$route);
            $reqUri =  preg_replace("/(^\/)|(\/$)/","",$_REQUEST['uri']);
        }else{
            $reqUri = "/";
        }

        //exploding route address
        $uri = explode("/", $route);

        //will store index number where {?} parameter is required in the $route 
        $indexNum = []; 

        //storing index number, where {?} parameter is required with the help of regex
        foreach($uri as $index => $param){
            if(preg_match("/{.*}/", $param)){
                $indexNum[] = $index;
            }
        }

        //exploding request uri string to array to get
        //the exact index number value of parameter from $_REQUEST['uri']
        $reqUri = explode("/", $reqUri);

        //running for each loop to set the exact index number with reg expression
        //this will help in matching route
        foreach($indexNum as $key => $index){

             //in case if req uri with param index is empty then return
            //because url is not valid for this route
            if(empty($reqUri[$index])){
                return;
            }

            //setting params with params names
            $params[$paramKey[$key]] = $reqUri[$index];

            //this is to create a regex for comparing route address
            $reqUri[$index] = "{.*}";
        }

        //converting array to sting
        $reqUri = implode("/",$reqUri);

        //replace all / with \/ for reg expression
        //regex to match route is ready !
        $reqUri = str_replace("/", '\\/', $reqUri);

        //now matching route with regex
        if(preg_match("/$reqUri/", $route))
        {
            include($file);
            exit();

        }
    }

    function notFound($file){
        include($file);
        exit();
    }
}

$route = new Route(); 

// Route for paths containing '/control/'
require_once(APP_ROOT . '/connection.php');

 // API SERVER 
 $route->add("/api/reserves/get","api/get-reserves.php");
 
 // aqui comença la lògica del sistema
 
        // Pàgines que no han de tenir header
        $route->add("/accounting/invoice/pdf/{id}", "php-forms/accounting/generate_pdf.php");
        $route->add("/accounting/invoice-customer/new","php-forms/accounting/invoice-customer-add.php");
        $route->add("/accounting/process/invoice-customer/new","php-process/accounting/customer-invoice-insert.php");

        // CARREGAR HEADER
        require_once(APP_ROOT . '/public/inc/header.php');

        // 01. Inici
        $route->add("/","public/index.php");
        $route->add("/inici","public/index.php");

        $route->add("/reserves-parking","public/reserves-parking.php");
        $route->add("/reserves-completades","public/reserves-completades.php");

        // Manejar todas las demás rutas (404)
        $route->notFound("public/404.php");

?>