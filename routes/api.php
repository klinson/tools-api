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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array', 'bindings'],
], function ($api) {
    // 登录验证相关路由
    $api->group([
        'prefix' => 'auth'
    ], function ($api) {
        $api->post('login', 'AuthorizationsController@login');
        $api->post('wxappLogin', 'AuthorizationsController@wxappLogin');
        $api->post('logout', 'AuthorizationsController@logout');
    });

    //不需要登录的路由
    $api->group([

    ], function ($api) {
        $api->post('characterRecognition/general', 'CharacterRecognitionController@general');

        $api->post('portrait/{type}', 'PortraitController@index')->where('type', '^(score|pk|cp|who_treat)$');;

        $api->get('configs/{key}', 'SystemController@getConfig')->where('key', '^(weapp_contact_information|wxapp_about_us)$');

        $api->get('weather', 'SystemController@weather');

        $api->post('files/uploadImage', 'FilesController@image');

        // 论坛
        $api->get('postCategories', 'PostsController@categories');
        $api->get('posts', 'PostsController@index');
        $api->get('posts/{post}', 'PostsController@show')->where('post', '[0-9]+');
        $api->get('postComments', 'PostCommentsController@index');

        $api->get('nearbyUsers', 'FavoursController@nearbyUsers');
        $api->get('users/{user}', 'UsersController@show')->where('user', '[0-9]+');

    });

    // 需要登录的路由
    $api->group([
        'middleware' => 'refresh.token'
    ], function ($api) {
        $api->put('user', 'UserController@update');
        $api->post('user/updateLocation', 'UserController@updateLocation');

        // 论坛
        $api->post('posts', 'PostsController@store');
        $api->put('posts/{post}', 'PostsController@update')->where('post', '[0-9]+');
        $api->delete('posts/{post}', 'PostsController@destroy')->where('post', '[0-9]+');

        $api->post('postComments/{post}', 'PostCommentsController@store')->where('post', '[0-9]+');
        $api->delete('postComments/{comment}', 'PostCommentsController@destroy')->where('post', '[0-9]+');

        $api->delete('messages/clearCount', 'MessagesController@clearMessageCount');

        $api->get('messages/count', 'MessagesController@count');

        $api->post('favour/{user}', 'FavoursController@favour');
        $api->post('unfavour/{user}', 'FavoursController@unfavour');

        $api->get('friends', 'FriendsController@index');

    });
});