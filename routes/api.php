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

/**
  Public API Routes
*/
Route::group([
    'middleware' => 'api'
], function() {

    Route::get('/load/{language}', 'LanguageController@copies')
        ->name('api.load')
        ->where(['language' => '[a-z]{2}']);

    Route::get('/question/{language}/{topic?}/{question?}', 'QuestionController@question')
        ->name('api.question')
        ->where([
            'language' => '[a-z]{2}',
            'topic' => '[0-9]+',
            'question' => '[0-9]+',
        ]);
});
