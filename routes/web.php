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

Route::get('/', 'indexController@search');
Route::get('/search', 'indexController@search');
Route::get('/object/id/{priref}', 'indexController@record');
Route::match(array('GET','POST'),'/search/results/', 'indexController@results');
