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

if (!function_exists('addResourceRoutes')) {
    function addResourceRoutes($app, $resource, $controller)
    {
        $app->get(sprintf('/%s', $resource), sprintf('%s@index', $controller));
        $app->get(sprintf('/%s/{id}', $resource), sprintf('%s@show', $controller));
        $app->post(sprintf('/%s', $resource), sprintf('%s@create', $controller));
        $app->put(sprintf('/%s/{id}', $resource), sprintf('%s@update', $controller));
        $app->delete(sprintf('/%s/{id}', $resource), sprintf('%s@delete', $controller));
    }
}

$groupAttributes = [
    'namespace' => 'App\Http\Controllers\V1',
    'prefix' => 'v1',
];
$app->group($groupAttributes, function ($app)
{
    $app->post('/auth/login', 'AuthController@login');
    $app->post('/auth/token', 'AuthController@tokenRefresh');
});

$groupAttributes['middleware'] = ['jwt-auth'];
$app->group($groupAttributes, function ($app) use ($groupAttributes) {
    $app->get('/auth/token', 'AuthController@token');

    addResourceRoutes($app, 'users', 'UserController');
});
