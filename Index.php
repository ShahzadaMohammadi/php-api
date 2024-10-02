<?php

// set header
header('Content-Type: application/json');


use Controllers\AuthController;
// include file
require_once 'autoloader.php';
require_once 'routes.php';

// $auth = new AuthController();

// $result = $auth->validataToken();
// GET URL & REQUSET TYPE
// if($result){
$requestUrl = parse_url(htmlspecialchars($_SERVER['REQUEST_URI']), PHP_URL_PATH);
$requestMethod = htmlspecialchars($_SERVER['REQUEST_METHOD']);
$route->match($requestUrl,$requestMethod);
// }else{
//     $auth->unAuthorizeUser();
// }
// load routes here