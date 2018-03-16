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
        Route::get('/{room}/stamps', 'StampController@index')->middleware('auth:api');
        Route::get('/{room}/stamps/guest', 'StampController@index');

        Route::group(['middleware' => ['throttle:20,1']], function () {
            Route::post('/{room}/stamps', 'StampController@create')->middleware('auth:api');
            Route::post('/{room}/stamps/guest', 'StampController@create');

            Route::post('/{room}/imprints', 'ImprintController@create')->middleware('auth:api');
            Route::post('/{room}/imprints/guest', 'ImprintController@guestCreate');
        });

        // スタンプのタグ
        Route::get('/{room}/stamps/{stamp}/tags', 'StampController@indexTags');

        Route::put('/{room}/stamps/{stamp}/tags', 'StampController@updateTags');

        // ルーム内のタグ
        // 名称一覧（タグ入力画面のオートコンプリート用）
        Route::get('/{room}/tags/names', 'RoomController@indexTagNames');

        // タグ名と件数
        Route::get('/{room}/tags', 'RoomController@indexTagNamesWithCount');
    });

    // Stamp
    Route::group(['prefix' => '/stamps'], function () {
        Route::get('/samples', 'StampController@sample');

        Route::delete('/{stamp}', 'StampController@uploadedDelete')
            ->middleware('auth:api');
    });

    // Favorite
    Route::group(['prefix' => '/favorites', 'middleware' => ['auth:api']], function () {
        Route::get('/', 'FavoriteController@index');
        Route::post('/', 'FavoriteController@store');
        Route::delete('/', 'FavoriteController@destroy');
    });

    // BlackList
    Route::group(['prefix' => '/blackLists', 'middleware' => ['auth:api']], function () {
        Route::post('/', 'BlackListController@store');
        Route::delete('/', 'BlackListController@destroy');

        // 送信一覧から送信者をブラックリストに入れる
        Route::post('/imprints/{imprint}', 'BlackListController@storeByImprint');
    });
});
