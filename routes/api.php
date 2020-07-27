<?php

use Illuminate\Http\Request;

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

Route::get('version', function () {
    return response()->json(['version' => config('app.version')]);
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    Log::debug('User:' . serialize($request->user()));
    return $request->user();
});


Route::get('profile', 'API\V1\ProfileController@profile');
Route::put('profile', 'API\V1\ProfileController@updateProfile');
Route::post('change-password', 'API\V1\ProfileController@changePassword');
Route::get('tag/list', 'API\V1\TagController@list');
Route::get('category/list', 'API\V1\CategoryController@list');
Route::post('product/upload', 'API\V1\ProductController@upload');

Route::get('stores/list', 'API\V1\StoreController@list');
Route::get('channels/list', 'API\V1\ChannelController@list');

Route::get('provinces/list', 'API\V1\ProvinceController@list');
Route::get('districts/list', 'API\V1\DistrictController@list');
Route::get('wards/list', 'API\V1\WardController@list');


Route::apiResources([
    'user' => 'API\V1\UserController',
    'product' => 'API\V1\ProductController',
    'category' => 'API\V1\CategoryController',
    'tag' => 'API\V1\TagController',
    'stores' => 'API\V1\StoreController',
    'channels' => 'API\V1\ChannelController',
    'positions' => 'API\V1\PositionController',
    'provinces' => 'API\V1\ProvinceController',
    'districts' => 'API\V1\DistrictController',
    'wards' => 'API\V1\WardController',
]);
