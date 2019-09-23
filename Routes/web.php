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

Route::get('login', 'LoginController@showLoginForm')->name('login');
Route::post('login', 'LoginController@login');
Route::post('logout', 'LoginController@logout')->name('logout');

//  Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
//  Route::post('register', 'Auth\RegisterController@register');

//Email Verification routes
Route::get('email/verify', 'VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}', 'VerificationController@verify')->name('verification.verify');
Route::get('email/resend', 'VerificationController@resend')->name('verification.resend');

//Password Reset routes
Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');
