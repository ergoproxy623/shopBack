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

Route::post('register', 'Auth\RegisterController@register');// register user
Route::post('login', 'Auth\LoginController@login'); //login user
Route::post('loginAdmin', 'Auth\LoginController@loginAdmin'); //login for admin panel
Route::post('socialLogin', 'Auth\LoginController@socialLogin'); //login and register via Facebook
Route::post('password/reset','UserController@reset'); // reset user's password
Route::get('forLogin','UserController@forLogin'); // checking data of the entered in Login fields
//preview
Route::get('preview','PreviewController@show'); // View preview
//image
Route::get('image','ImageController@index'); // View all images

Route::post('payment/success','VideoController@paymentSuccess'); // Change status user if payment is success
Route::post('feedback','UserController@Feedback'); // Feedback

Route::group(['middleware' => ['jwt.auth']], function () {

    //user
    Route::post('update/user','UserController@update'); // User update him profile
    Route::get('updatePage','UserController@updatePage'); // Get user's data by token
    Route::post('updatePassword','UserController@updatePassword'); // User update him password

    //video
    Route::get('showVideo1','VideoController@showVideo1'); // get the list video of the first block
    Route::get('showVideo2','VideoController@showVideo2'); // get the list video of the second block
    Route::post('buying','VideoController@purchase'); // user buy video

    Route::group(['middleware' => ['role:admin']], function () {

        //user for admin
        Route::get('user','Admin\UserController@index');   //list all users with them purchase
        Route::get('user/{user}','Admin\UserController@show'); // all user's data
        Route::get('usersWhoBought','Admin\UserController@usersWhoBought'); // List of user who bought any block

        //video for admin
        Route::post('video', 'Admin\VideoController@store'); // create video
        Route::post('video/{video}','Admin\VideoController@update'); // update video
        Route::delete('video/{video}', 'Admin\VideoController@destroy'); // delete video

        //image for admin
        Route::post('image', 'Admin\ImageController@store'); // create image
        Route::post('image/{image}','Admin\ImageController@update'); // update image
        Route::delete('image/{image}', 'Admin\ImageController@destroy'); // delete image

        //preview for admin
        Route::post('preview','Admin\PreviewController@store'); // write Youtube list in database
        Route::put('preview','Admin\PreviewController@update'); // update Youtube list
    });
});
