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

Route::get('/', function() {
    return redirect()->to('chat');
});

Route::get('login', [ 'as' => 'login', 'uses' => 'SiteController@showLoginPage']);

Route::post('login', 'SiteController@login');

Route::get('logout', 'SiteController@logout');

Route::get('chat', 'ChatController@showChat');

Route::get('chat/get-messages/{limit?}/{offset?}', 'ChatController@getMessages');

Route::post('chat/post-message', 'ChatController@postMessage');

Route::delete('chat/delete-message/{message}', 'ChatController@deleteMessage');

Route::put('chat/like-message/{message}', 'ChatController@likeMessage');

Route::post('chat/post-attachment-image', 'ChatController@postAttachmentImage');
Route::post('chat/delete-attachment-image', 'ChatController@deleteTemporallyAttachmentImage');
