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
    return $router->app->version();
});


$router->group(['prefix' => 'admin', 'middleware' => 'auth:api'], function ($router) {
    $router->group(['prefix' => 'training'], function ($router) {
        //
        $router->get('index[/{offset}]', 'AdminTrainingController@index');
        $router->post('create', 'AdminTrainingController@create');
        $router->post('edit', 'AdminTrainingController@edit');
        $router->get('show/{id}', 'AdminTrainingController@show');
        $router->get('search', 'AdminTrainingController@search');
        $router->post('delete', 'AdminTrainingController@delete');
        $router->get('frequency', 'AdminTrainingController@frequency');
        $router->post('trainee/add', 'AdminTrainingController@addTrainee');
        $router->post('trainee/delete', 'AdminTrainingController@deleteTrainee');
        $router->get('trainee/{id}/search', 'AdminTrainingController@searchTrainee');
        $router->get('trainee/{id}[/{offset}]', 'AdminTrainingController@trainee');
    });

});


$router->group(['prefix' => 'staff', 'middleware' => 'auth:staff'], function ($router) {
    $router->group(['prefix' => 'training'], function ($router) {
        $router->get('index[/{offset}]', 'StaffTrainingController@index');
    });
});
