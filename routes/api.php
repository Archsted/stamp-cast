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
            Route::post('/{room}/stamps/guest', 'StampController@create');
            Route::post('/{room}/imprints/guest', 'ImprintController@guestCreate');
        });
        Route::group(['middleware' => ['throttle:40,1']], function () {
            Route::post('/{room}/stamps', 'StampController@create')->middleware('auth:api');
            Route::post('/{room}/imprints', 'ImprintController@create')->middleware('auth:api');
        });

        // スタンプのタグ
        Route::get('/{room}/stamps/{stamp}/tags', 'StampController@indexTags');

        Route::put('/{room}/stamps/{stamp}/tags', 'StampController@updateTags');

        // ルーム内のタグ
        // 名称一覧（タグ入力画面のオートコンプリート用）
        Route::get('/{room}/tags/names', 'RoomController@indexTagNames');

        // タグ名と件数
        Route::get('/{room}/tags', 'RoomController@indexTagNamesWithCount');

        // スタンプ削除
        Route::delete('/{room}/stamps/{stamp}', 'StampController@uploadedDelete')
            ->middleware('auth:api');
    });

    // Stamp
    Route::group(['prefix' => '/stamps'], function () {
        Route::get('/samples', 'StampController@sample');

//        Route::delete('/{stamp}', 'StampController@uploadedDelete')
//            ->middleware('auth:api');
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
        Route::delete('/{blackList}', 'BlackListController@destroy');

        // 送信一覧から送信者をブラックリストに入れる
        Route::post('/imprints/{imprint}', 'BlackListController@storeByImprint');
    });

    // Book
    Route::group(['prefix' => '/books', 'middleware' => ['auth:api']], function () {
        // 一覧取得
        Route::get('/', 'BookController@indexApi');

        // 新規作成
        Route::post('/{book}/stamps', 'BookController@storeStampApi');

        // 詳細とスタンプ取得
        Route::get('/{book}', 'BookController@showApi');

        // Bookの並び順変更
        Route::put('/{book}/order', 'BookController@updateOrderApi');

        // Bookの削除
        Route::delete('/{book}', 'BookController@destroyApi');

        // Bookに登録中のスタンプの並び順変更
        Route::put('/{book}/stamps/{stampId}/order', 'BookController@updateStampOrderApi');

        // Bookに登録中のスタンプを一括削除
        Route::put('/{book}/stamps', 'BookController@destroyStampsApi');

        // Bookに登録中のスタンプを別のBookに移動
        Route::put('/{book}/stamps/move', 'BookController@moveStampsApi');

        // Bookに登録中のスタンプを別のBookにコピー
        Route::put('/{book}/stamps/copy', 'BookController@copyStampsApi');
    });

    // Twitter
    Route::post('/apps/twitter/send', 'TwitterTokenController@send')
        ->middleware(['auth:api']);

});
