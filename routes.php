<?php

use Controllers\RouteController;
use Controllers\HomeController;

$route = new RouteController();
// add new routes here
$route->add('/','GET', 'Controllers\HomeController','home');
$route->add('/register','POST','Controllers\UserController','register');
$route->auth()->add('/quotes','GET','Controllers\QuoteController','index');
$route->auth()->add('/quotes/{id}','GET','Controllers\QuoteController','getQuote');
$route->auth()->add('/quotes', 'POST' ,'Controllers\QuoteController','store');
$route->auth()->add('/quotes/{id}','PUT','Controllers\QuoteController','update');
$route->auth()->add('/quotes/{id}','DELETE','Controllers\QuoteController','delete');
$route->auth()->add('/quotes/author/{author}','GET','Controllers\QuoteController','getQuoteByAuthor');
$route->auth()->add('/quotes/user/{id}','GET','Controllers\QuoteController','getQuotesByUserId');
$route->auth()->add('/quotes/page/{num}','GET','Controllers\QuoteController','pagelimetation');
 