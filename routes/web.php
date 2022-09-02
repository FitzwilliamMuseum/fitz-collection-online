<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

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
Route::middleware(['auth', 'verified', 'doNotCacheResponse'])->group(function () {
    Route::get('api/activity', 'HomeController@activity')->name('home');
    Route::view('password/update', 'auth.passwords.update')->name('passwords.update');
});
Route::get('/', 'indexController@search')->name('data.home');
Route::get('/search', 'indexController@search')->name('search');
Route::match(array('GET', 'POST'), '/search/results/', 'indexController@results')->name('results');;

/*
* Spelunker route for all records
*/
Route::get('/random', ['middleware' => 'doNotCacheResponse', 'uses' => 'indexController@randomsearch'])->name('random');
Route::get('/random/app', ['middleware' => 'doNotCacheResponse', 'uses' => 'indexController@randomsearchapp'])->name('random.app');

/*
* ObjectOrArtwork based routes
*/
Route::middleware([])->group(function () {
    Route::get('/id/object/{priref}', 'indexController@record')->name('record');
//    Route::get('/id/object/{priref}/{format}', 'indexController@recordSwitch')->name('record.context');
});
Route::get('/id/image/{id}/', 'imagesController@image')->name('image.single');
Route::get('/id/image/3d/{id}/', 'imagesController@sketchfab')->name('sketchfab');
Route::get('/id/image/iiif/{id}/', 'imagesController@iiif')->name('image.iiif');
Route::get('/id/image/flutter/iiif/{id}/', 'imagesController@flutteriiif')->name('image.iiif.flutter');
Route::get('/id/image/slow/iiif/', 'imagesController@slowiiif')->name('slow.iiif');
Route::get('/id/image/mirador/{id}/', 'imagesController@mirador')->name('image.mirador');
Route::match(array('GET', 'POST'), '/images/id/{priref}/', 'imagesController@images')->name('images.multiple');
/*
* Publication routes
*/
Route::get('/id/publication/', 'publicationsController@index')->name('publications');
Route::get('/id/publication/{id}', 'publicationsController@record')->name('publication.record');
Route::get('/id/publication/{id}/{format}', 'publicationsController@recordSwitch')->name('publication.context');

/*
* Publication routes
*/
Route::get('/id/exhibition/', 'exhibitionsController@index')->name('exhibitions');
Route::get('/id/exhibition/{id}', 'exhibitionsController@exhibition')->name('exhibition.record');


/*
* Terminology routes
*/
Route::get('/id/terminology/', 'terminologyController@index')->name('terminologies');
Route::get('/id/terminology/{id}', 'terminologyController@record')->name('terminology');

/*
 * Place based routes
 */
Route::get('/id/places/', 'placesController@index')->name('places');

/*
 * Period based routes
 */
Route::get('/id/periods/', 'periodsController@index')->name('periods');
/*
*  Agent based routes
*/
Route::get('/id/agent/', 'agentsController@index')->name('agents');
Route::get('/id/agent/{id}', 'agentsController@record')->name('agent');

/*
* Department page
*/
Route::get('/id/departments', 'departmentsController@index')->name('departments');
Route::get('/id/departments/{id}', 'departmentsController@record')->name('department');

/*
* Cache clear route
*/
Route::get('/clear-cache', [
    'as' => 'cache-clear',
    'uses' => 'Controller@clearCache'
])->middleware('auth.very_basic', 'doNotCacheResponse');

Route::get("/api", function () {
    return View::make("api.index");
})->name('api.index');

