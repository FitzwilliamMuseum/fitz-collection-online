<?php

use Illuminate\Support\Facades\Route;
use App\Models\Api\IpAddress;
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


/*
|--------------------------------------------------------------------------
| Authorisation routes for the API
|--------------------------------------------------------------------------
 */
Route::group(['prefix' => 'auth', 'middleware' => ['log.route','api-log','json.response']], function () {
    Route::post('signup', 'App\Http\Controllers\Api\AuthController@signup')->name('api.signup');
    Route::post('login', 'App\Http\Controllers\Api\AuthController@login')->name('api.login');
    Route::post('logout', 'App\Http\Controllers\Api\AuthController@logout')->middleware('auth:sanctum')->name('api.logout');
    Route::post('me', 'App\Http\Controllers\Api\AuthController@me')->middleware('auth:sanctum')->name('api.user');

});


/*
|--------------------------------------------------------------------------
| V1 home route for the API
|--------------------------------------------------------------------------
 */
Route::match(
    array('GET','POST'),'/v1', 'Api\IndexController@index')->name('api.home')->middleware(
    'log.route','json.response'
);
$middleware = array('log.route','json.response');

if(!in_array(Request::ip(), IpAddress::whitelist())){
    $middleware[] = 'auth:sanctum';
}

/*
|--------------------------------------------------------------------------
| API V1 Authenticated routes
| All routes force json response, have logged and are authenticated with
| 'auth:sanctum' middleware
|--------------------------------------------------------------------------
 */
Route::group(['prefix' => 'v1', 'middleware' => $middleware], function () {
    Route::apiResource('ids', 'Api\ObjectNumbersController', ['as' => 'api.ids', 'only' => ['index']]);
    Route::apiResource('objects', 'Api\ObjectsController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('linked-art', 'Api\LinkedArtController', ['as' => 'api', 'only' => ['index', 'show']]);

    Route::apiResource('departments', 'Api\DepartmentsController', ['as' => 'api', 'only' => ['index', 'show']]);

    Route::apiResource('periods', 'Api\PeriodsController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('ids/periods', 'Api\PeriodsNumbersController', ['as' => 'api.ids', 'only' => ['index']]);

    Route::apiResource('publications', 'Api\PublicationsController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('ids/publications', 'Api\PublicationsNumbersController', ['as' => 'api.ids', 'only' => ['index']]);

    Route::apiResource('agents', 'Api\AgentsController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('ids/agents', 'Api\AgentsNumbersController', ['as' => 'api.ids', 'only' => ['index']]);

    Route::apiResource('terminology', 'Api\TerminologyController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('ids/terminology', 'Api\TerminologyNumbersController', ['as' => 'api.ids', 'only' => ['index']]);

    Route::apiResource('images', 'Api\ImagesController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('ids/images', 'Api\ImagesNumbersController', ['as' => 'api.ids', 'only' => ['index']]);

    Route::apiResource('exhibitions', 'Api\ExhibitionsController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('ids/exhibitions', 'Api\ExhibitionsNumbersController', ['as' => 'api.ids', 'only' => ['index']]);

    Route::apiResource('institutions', 'Api\InstitutionsController', ['as' => 'api', 'only' => ['index', 'show']]);

    Route::apiResource('makers', 'Api\MakersController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('ids/makers', 'Api\MakersNumbersController', ['as' => 'api.ids', 'only' => ['index']]);

    Route::apiResource('places', 'Api\PlacesController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('ids/places', 'Api\PlacesNumbersController', ['as' => 'api.ids', 'only' => ['index', 'show']]);

    Route::apiResource('iiif', 'Api\IiifController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('ids/iiif', 'Api\IiifNumbersController', ['as' => 'api.ids', 'only' => ['index']]);

    Route::fallback(function () {
        return response()->json(['error' => 'Nothing found with that query'], 404);
    });
});
