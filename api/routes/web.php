<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix'=>'v0'],function () use ($router){
    $router->get('orders/caja/{cajaid}',['uses'=>'OrderController@getCajaOrders']);

    $router->get('caja/{caja}',['uses' => 'CajaController@getCaja']);
    $router->get('caja/status/active',['uses'=>'CajaController@getActiveCaja']);
    $router->get('caja/retiros/{cajaid}',['uses'=>'CajaController@getRetirosFromCaja']);

    $router->get('products',['uses'=>'ProductController@getProducts']);
    $router->post('products',['uses'=>'ProductController@editProduct']);

    $router->get('users',['uses'=>'UserController@getUsers']);
});
