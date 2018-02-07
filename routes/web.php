<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// 配信者用ツールの初期表示ページ
Route::get('/broadcaster', function () {
    return redirect('/1/broadcaster');
});

// ログイン後のホーム画面
Route::get('/home', 'HomeController@index')
    ->middleware('auth')
    ->name('home');

// ルーム関連
Route::group(['prefix' => '/rooms', 'middleware' => ['auth']], function () {
    Route::get('/create', 'RoomController@create')->name('room_create');
    Route::post('/', 'RoomController@store')->name('room_store');

    Route::get('/{room}/edit', 'RoomController@edit')
        ->name('room_edit')
        ->where('room', '^[\d]+$');

    Route::put('/{room}', 'RoomController@update')
        ->name('room_update')
        ->where('room', '^[\d]+$');
});

// リスナー用ルームURL
Route::get('/{room}', 'HomeController@listener')
    ->name('listener')
    ->where('room', '^[\d]+$');

// 配信者ツール用のスタンプ待機URL
Route::get('/{room}/broadcaster', 'HomeController@broadcaster')
    ->name('broadcaster')
    ->where('room', '^[\d]+$');

Route::group(['prefix' => '/stamps'], function () {
    Route::get('/', 'StampController@uploadedIndex')->name('my_stamps');
});
