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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['prefix' => 'v1','middleware'=>['guest']], function(){
		Route::post('login', 'API\UserController@login');
		Route::post('register', 'API\UserController@register');
		Route::post('getFacebookAuth', 'API\UserController@getFacebookAuth');	
		Route::get('getFacebookUser', 'API\UserController@get_user_data');	

		
});


Route::group(['prefix' => 'v1','middleware' => 'auth:api'], function(){
	Route::get('get_questions', 'API\UserController@getQuestion');
	Route::get('user', 'API\UserController@userData');
	Route::post('updateprofile', 'API\UserController@updateProfile');
	Route::post('uploadImage', 'API\UserController@edit_profile_picture');

});


