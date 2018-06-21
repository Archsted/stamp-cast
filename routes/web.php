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
Route::group(['prefix' => '/broadcaster'], function () {
    // ツールの初期表示
    Route::get('/', 'ToolController@toolIndex')->name('tool_login');

    // ツール上で受信ページ移動
    Route::post('/receiver', 'ToolController@receiver')->name('receiver');
});


// ログイン後のホーム画面
Route::get('/home', 'HomeController@index')
    ->middleware('auth')
    ->name('home');

// ルーム関連
Route::group(['prefix' => '/rooms', 'middleware' => ['auth']], function () {
    // ルーム新規作成画面
    Route::get('/create', 'RoomController@create')
        ->name('room_create');

    // ルーム新規作成処理
    Route::post('/', 'RoomController@store')
        ->name('room_store');

    // ルーム編集画面
    Route::get('/{room}/edit', 'RoomController@edit')
        ->name('room_edit')
        ->where('room', '^[\d]+$');

    // ルーム編集処理
    Route::put('/{room}', 'RoomController@update')
        ->name('room_update')
        ->where('room', '^[\d]+$');

    // スタンプ送信一覧画面
    Route::get('/{room}/imprints', 'ImprintController@index')
        ->name('room_imprint')
        ->where('room', '^[\d]+$');
});

// リスナー用ルームURL
Route::get('/{room}', 'HomeController@listener')
    ->name('listener')
    ->where('room', '^[\d]+$');

Route::get('/{room}/tag/{tag}', 'HomeController@listener')
    ->where('room', '^[\d]+$');

Route::get('/{room}/notags', 'HomeController@listenerNoTags')
    ->where('room', '^[\d]+$');

// 配信者ツール用のスタンプ待機URL
Route::get('/{room}/broadcaster', 'HomeController@broadcaster')
    ->name('broadcaster')
    ->where('room', '^[\d]+$');

Route::get('/{room}/broadcasterBeta', 'HomeController@broadcasterBeta')
    ->name('broadcasterBeta')
    ->where('room', '^[\d]+$');

Route::group(['prefix' => '/stamps'], function () {
    Route::get('/', 'StampController@uploadedIndex')->name('my_stamps');
});

// ツール
Route::group(['prefix' => '/tools', 'middleware' => ['auth']], function () {
    Route::get('/', 'ToolController@index')->name('tool_top');
    Route::get('/download/{platform}', 'ToolController@download')->name('tool_download');
});

// スタンプ帳関連
Route::group(['prefix' => '/books', 'middleware' => ['auth']], function () {
    Route::get('/', 'BookController@index')->name('book_index');

    Route::get('/create', 'BookController@create')->name('book_create');

    Route::post('/', 'BookController@store')->name('book_store');

    Route::get('/{book}', 'BookController@show')
        ->where('book', '^[\d]+$')
        ->name('book_show');

    Route::get('/{book}/edit', 'BookController@edit')
        ->where('book', '^[\d]+$')
        ->name('book_edit');

    Route::put('/{book}', 'BookController@update')
        ->where('book', '^[\d]+$')
        ->name('book_update');
});

Route::group(['prefix' => '/blackLists', 'middleware' => ['auth']], function (){
   Route::get('/', 'BlackListController@index')->name('blackList_index');
});
