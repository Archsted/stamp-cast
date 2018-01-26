<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

Route::group(['prefix' => '/v1'], function () {

    // Room
    Route::group(['prefix' => '/rooms'], function () {
        Route::get('/{room}/stamps', 'StampController@index');

        Route::post('/{room}/stamps', 'StampController@create')->middleware('auth:api');
        Route::post('/{room}/stamps/guest', 'StampController@guestCreate');

        Route::post('/{room}/imprints', 'ImprintController@create')->middleware('auth:api');
        Route::post('/{room}/imprints/guest', 'ImprintController@guestCreate');
    });

    // Stamp
    Route::group(['prefix' => '/stamps'], function () {
//        Route::get('/', 'StampController@index');
//        Route::post('/', 'StampController@create');
        Route::get('/samples', 'StampController@sample');
    });

    // Imprint
    Route::group(['prefix' => '/imprints'], function () {
//        Route::post('/', 'ImprintController@create');
    });
});
