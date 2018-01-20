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
    ->name('listener');

Route::get('/{room}/broadcaster', 'HomeController@broadcaster')
    ->name('broadcaster');


Route::group(['prefix' => '/rooms', 'middleware' => ['auth']], function () {
    Route::get('/create', 'RoomController@create')->name('room_create');
    Route::post('/', 'RoomController@store')->name('room_store');

    Route::get('/{room}/edit', 'RoomController@edit')->name('room_edit');
    Route::put('/{room}', 'RoomController@update')->name('room_update');

});


//Route::get('/{code}/listener', 'HomeController@listener');
//Route::get('/{code}/broadcaster', 'HomeController@broadcaster');


