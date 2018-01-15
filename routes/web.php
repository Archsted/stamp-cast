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

Route::get('/{room}', 'HomeController@listener');
Route::get('/{room}/broadcaster', 'HomeController@broadcaster');


Route::get('/rooms/create', 'RoomController@create')
    ->middleware('auth')
    ->name('room_create');

Route::post('/rooms', 'RoomController@store')
    ->middleware('auth')
    ->name('room_store');

//Route::get('/{code}/listener', 'HomeController@listener');
//Route::get('/{code}/broadcaster', 'HomeController@broadcaster');


