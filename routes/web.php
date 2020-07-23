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

Auth::routes(['verify' => true]);

Route::get('/pmc/login', 'Auth\PMCLoginController@showLoginForm')->name('get.pmc_login');
Route::post('/pmc/login', 'Auth\PMCLoginController@login')->name('post.pmc_login');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('home', function () {
    return redirect('/dashboard');
});

Route::post('/file/upload', 'FileController@upload')->name('fileUpload');


Route::get('/{vue_capture?}', function () {
    return view('home');
})->where('vue_capture', '[\/\w\.-]*')->middleware('auth');
