<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Home routes
|--------------------------------------------------------------------------
|
| Here you can see home routes.
|
*/

Route::group(['prefix' => 'panel', 'middleware' => 'auth'], static function ($router) {
    $router->patch('categories/{id}/active', ['uses' => 'CategoryController@active', 'as' => 'categories.change.status.active']);
    $router->patch('categories/{id}/inactive', ['uses' => 'CategoryController@inactive', 'as' => 'categories.change.status.inactive']);
    $router->resource('categories', 'CategoryController');
});
