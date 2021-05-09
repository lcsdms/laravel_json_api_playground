<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//https://laraveljsonapi.io/docs/1.0/routing/
JsonApiRoute::server('v1')
    ->prefix('v1')
    ->namespace('App\Http\Controllers\Api\V1')
    ->resources(function ($server) {
        $server->resource('people')->relationships(function ($relationships) {
            $relationships->hasMany('emails');
        });
        $server->resource('emails');
    });
