<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */

/** Authentication & Authorization */

$router->get('/products', 'ProductController@index');
$router->get('/product/{product}', 'ProductController@show');
$router->post('/products', 'ProductController@store');
$router->put('/product/{product}', 'ProductController@update');
$router->delete('/product/{product}', 'ProductController@destroy');

