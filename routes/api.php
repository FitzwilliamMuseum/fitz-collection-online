<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
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

Route::prefix('auth')->group(function () {
    Route::post('signup', 'App\Http\Controllers\Api\AuthController@signup')->name('auth.signup');
    Route::post('login', 'App\Http\Controllers\Api\AuthController@login')->name('auth.login');
    Route::post('logout', 'App\Http\Controllers\Api\AuthController@logout')->middleware('auth:sanctum')->name('auth.logout');
    Route::get('user', 'App\Http\Controllers\Api\AuthController@getAuthenticatedUser')->middleware('auth:sanctum')->name('auth.user');

    Route::post('/password/email', 'App\Http\Controllers\Api\AuthController@sendPasswordResetLinkEmail')->middleware('throttle:5,1')->name('password.email');
    Route::post('/password/reset', 'App\Http\Controllers\Api\AuthController@resetPassword')->name('password.reset');
});

Route::match(array('GET','POST'),'/', 'Api\IndexController@index')->middleware(['log.route']);
Route::apiResource('objects', 'Api\ObjectsController', ['as' => 'api', 'only' => ['index', 'show']])->middleware(['log.route']);
Route::apiResource('departments', 'Api\DepartmentsController', ['as' => 'api', 'only' => ['index', 'show']])->middleware(['log.route']);
Route::apiResource('periods', 'Api\PeriodsController', ['as' => 'api', 'only' => ['index', 'show']])->middleware(['log.route']);
Route::apiResource('publications', 'Api\PublicationsController', ['as' => 'api', 'only' => ['index', 'show']])->middleware(['log.route']);
Route::apiResource('agents', 'Api\AgentsController', ['as' => 'api', 'only' => ['index', 'show']])->middleware(['log.route']);
Route::apiResource('terminology', 'Api\TerminologyController', ['as' => 'api', 'only' => ['index', 'show']])->middleware(['log.route']);
Route::apiResource('images', 'Api\ImagesController', ['as' => 'api', 'only' => ['index', 'show']])->middleware(['log.route']);
Route::apiResource('exhibitions', 'Api\ExhibitionsController', ['as' => 'api', 'only' => ['index', 'show']])->middleware(['log.route']);
Route::apiResource('institutions', 'Api\InstitutionsController', ['as' => 'api', 'only' => ['index', 'show']])->middleware(['log.route']);
Route::apiResource('makers', 'Api\MakersController', ['as' => 'api', 'only' => ['index', 'show']])->middleware(['log.route']);
Route::apiResource('places', 'Api\PlacesController', ['as' => 'api', 'only' => ['index', 'show']])->middleware(['log.route']);
Route::fallback(function () {
    return response()->json(['error' => 'Nothing found with that query'], 404);
});
