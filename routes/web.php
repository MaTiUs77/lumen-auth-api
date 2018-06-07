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
	$service= 'lumen-auth-api';
	$status= 'online';

	$motor= app()->version();
	$server_time = \Carbon\Carbon::now();

	$tag = shell_exec('git describe --always --tags');
	$path = shell_exec('git remote -v');
	$path = explode(' ',preg_replace('/origin|\t/','',$path))[0];

	$github = [
		'url' => $path,
		'tag' => trim(preg_replace('/\s\s+/', ' ', $tag))
	];

	return compact('service','status','motor','github','server_time');
});

$router->post('/login', 'AuthController@postLogin');
$router->get('/password/{password}', 'AuthController@generatePassword');

$router->group(['middleware' => 'auth:api'], function($router)
{
	$router->get('/logout', 'AuthController@logout');
	$router->get('/refresh', 'AuthController@refresh');
	$router->get('/me', 'AuthController@me');
});
