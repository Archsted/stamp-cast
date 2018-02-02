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

Route::get('/home', 'HomeController@index')
    ->middleware('auth')
    ->name('home');

Route::get('/{room}', 'HomeController@listener')
    ->name('listener')
    ->where('room', '^[\d]+$');

Route::get('/{room}/broadcaster', 'HomeController@broadcaster')
    ->name('broadcaster')
    ->where('room', '^[\d]+$');

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

Route::group(['prefix' => '/stamps'], function () {
    Route::get('/', 'StampController@uploadedIndex')->name('my_stamps');
});
