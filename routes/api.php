<?php

use Illuminate\Support\Facades\Route;
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

$middleware = array('log.route','api-log','json.response');
if(!in_array(Request::ip(), explode(',',env('API_IP_WHITELIST')))){
    $middleware[] = 'auth:sanctum';
}
/*
|--------------------------------------------------------------------------
| Authorisation routes for the API
|--------------------------------------------------------------------------
 */
Route::group(['prefix' => 'auth', 'middleware' => ['log.route','api-log','json.response']], function () {
    Route::post('signup', 'App\Http\Controllers\Api\AuthController@signup')->name('signup');
    Route::post('login', 'App\Http\Controllers\Api\AuthController@login')->name('login');
    Route::post('logout', 'App\Http\Controllers\Api\AuthController@logout')->middleware('auth:sanctum')->name('auth.logout');
    Route::post('forgotten', 'App\Http\Controllers\Api\AuthController@sendPasswordResetLinkEmail')->middleware('throttle:5,1')->name('password.email');
    Route::post('reset', 'App\Http\Controllers\Api\AuthController@resetPassword')->name('password.reset');
    Route::post('revoke', 'App\Http\Controllers\Api\AuthController@revokeTokens')->middleware('auth:sanctum')->name('revoke.tokens');
    Route::post('me', 'App\Http\Controllers\Api\AuthController@me')->middleware('auth:sanctum')->name('auth.user');
});
Route::get('/verify', 'App\Http\Controllers\Api\VerificationController@show')->name('verification.notice');
Route::post('/verify/{id}/{hash}', 'App\Http\Controllers\Api\VerificationController@verify')->name('verification.verify')->middleware(['signed']);
Route::post('/resend', 'App\Http\Controllers\Api\VerificationController@resend')->name('verification.resend');


/*
|--------------------------------------------------------------------------
| V1 home route for the API
|--------------------------------------------------------------------------
 */
Route::match(array('GET','POST'),'/v1', 'Api\IndexController@index')->name('api.home')->middleware('log.route','api-log','json.response');

/*
|--------------------------------------------------------------------------
| API V1 Authenticated routes
| All routes force json response, have logged and are authenticated with
| 'auth:sanctum' middleware
|--------------------------------------------------------------------------
 */
Route::group(['prefix' => 'v1', 'middleware' => $middleware], function () {
    Route::apiResource('objects', 'Api\ObjectsController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('departments', 'Api\DepartmentsController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('periods', 'Api\PeriodsController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('publications', 'Api\PublicationsController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('agents', 'Api\AgentsController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('terminology', 'Api\TerminologyController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('images', 'Api\ImagesController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('exhibitions', 'Api\ExhibitionsController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('institutions', 'Api\InstitutionsController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('makers', 'Api\MakersController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('places', 'Api\PlacesController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::apiResource('iiif', 'Api\IiifController', ['as' => 'api', 'only' => ['index', 'show']]);
    Route::fallback(function () {
        return response()->json(['error' => 'Nothing found with that query'], 404);
    });
});
