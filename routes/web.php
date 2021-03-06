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

Route::namespace('Home')->group(function (){

    Route::get('/', function () {
        return redirect('https://klinson.com');
    })->name('index');
    Route::get('/nlp/simple', 'NlpController@simple')->name('nlp.simple');
    Route::post('/nlp/simple', 'NlpController@simple');
//    Route::get('categories/{category}', 'ArticlesController@categories')->where('category', '[0-9]+')->name('articles.categories');
//    Route::get('categories/{category}/articles/{article}', 'ArticlesController@show')->where('category', '[0-9]+')->where('category', '[0-9]+')->name('articles.show');
//    Route::get('/contactUs', 'SystemController@contactUs')->name('system.contactUs');
//    Route::post('/contactUs', 'SystemController@storeContactUs')->name('system.contactUs.store');
});
Route::any('/wechat-serve', 'WechatController@serve');
