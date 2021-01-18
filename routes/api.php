<?php

use App\Http\Controllers\API\V1\StatusApiController;
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

Route::group(['prefix' => 'v1', 'as' => 'api.', 'middleware' => ['auth:api']], function () {
    Route::group(['prefix' => 'statuses', 'as' => 'statuses.'], function () {
        Route::get('/list', [StatusApiController::class, 'list'])->name('list');
    });
});


Route::get('profile', 'API\V1\ProfileController@profile');
Route::put('profile', 'API\V1\ProfileController@updateProfile');
Route::post('change-password', 'API\V1\ProfileController@changePassword');
Route::get('tag/list', 'API\V1\TagController@list');
Route::get('category/list', 'API\V1\CategoryController@list');
Route::post('product/upload', 'API\V1\ProductController@upload');

Route::get('stores/list', 'API\V1\StoreController@list');
Route::get('stores/search', 'API\V1\StoreController@search');

Route::get('channels/list', 'API\V1\ChannelController@list');
Route::get('positions/list/v2', 'API\V1\PositionController@list_v2');
Route::get('positions/list', 'API\V1\PositionController@list');
Route::get('positions/statuses', 'API\V1\PositionController@getStatuses');
Route::post('positions/import', 'API\V1\PositionController@import');
Route::post('positions/export', 'API\V1\PositionController@export');


Route::get('provinces/list', 'API\V1\ProvinceController@list');
Route::get('districts/list', 'API\V1\DistrictController@list');
Route::get('wards/list', 'API\V1\WardController@list');
Route::get('brands/list', 'API\V1\BrandController@list');
Route::get('company/list', 'API\V1\CompanyController@list');

Route::apiResources([
    'user' => 'API\V1\UserController',
    'product' => 'API\V1\ProductController',
    'category' => 'API\V1\CategoryController',
    'tag' => 'API\V1\TagController',
    'stores' => 'API\V1\StoreController',
    'channels' => 'API\V1\ChannelController',
    'positions' => 'API\V1\PositionController',
    'campaigns' => 'API\V1\CampaignController',
    'provinces' => 'API\V1\ProvinceController',
    'districts' => 'API\V1\DistrictController',
    'wards' => 'API\V1\WardController',
    'company' => 'API\V1\CompanyController',
    'brands' => 'API\V1\BrandController',
    'roles' => 'API\V1\RolesController',
]);
