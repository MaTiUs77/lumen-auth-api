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

$router->get('/', function () use ($router) {
	return [
		'auth_server'=>'online',
	];
});

$router->post('/auth/login', 'AuthController@postLogin');
$router->get('/password', 'AuthController@generatePassword');

$router->group(['prefix'=>'auth','middleware' => 'auth:api'], function($router)
{
	$router->get('/logout', 'AuthController@logout');
	$router->get('/refresh', 'AuthController@refresh');
	$router->get('/me', 'AuthController@me');
});