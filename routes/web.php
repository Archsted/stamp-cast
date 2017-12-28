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

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/listener', 'HomeController@listener')->name('listener');
Route::get('/broadcaster', 'HomeController@broadcaster')->name('broadcaster');

Route::get('/doStamp', function(){
    event(new \App\Events\StampEvent());
});