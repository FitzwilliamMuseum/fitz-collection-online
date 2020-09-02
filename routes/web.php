<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
* Basic search routes
*/
Route::get('/', 'indexController@search');
Route::get('/search', 'indexController@search');
Route::match(array('GET','POST'),'/search/results/', 'indexController@results');

/*
* Spelunker route for all records
*/
Route::get('/spelunker', 'indexController@index');

/*
* Object based routes
*/
Route::get('/id/object/{priref}', 'indexController@record');
Route::get('/id/object/{priref}/{format}', 'indexController@recordSwitch');
Route::get('/id/image/{id}/', 'indexController@image');

/*
* Publication routes
*/
Route::get('/id/publication/{id}', 'publicationsController@record');
Route::get('/id/publication/{id}/{format}', 'publicationsController@recordSwitch');

/*
* Terminology routes
*/
Route::get('/id/terminology/{id}', 'terminologyController@record');
Route::get('/id/terminology/{id}/{format}', 'terminologyController@recordSwitch');

/*
*  Agent based routes
*/
Route::get('/id/agent/{id}', 'agentsController@record');
Route::get('/id/agent/{id}/{format}', 'agentsController@recordSwitch');

/*
* Department page
*/
Route::get('/id/departments/{id}', 'departmentsController@record');

/*
* Cache clear route
*/
Route::get('/clear-cache', [
    'as' => 'cache-clear',
    'uses' => 'Controller@clearCache'
])->middleware('auth.very_basic', 'doNotCacheResponse');
