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

/**
 * Misc
 */
Route::redirect('/', '/topics');

/**
 * Widget
 */
Route::get('/widget-test', 'WidgetController@index')->name('widget');

/**
 * Auth
 */
Auth::routes(['register' => false]);

/**
 * Languages
 */
Route::get('/languages/data', 'LanguageController@datatable')->name('languages.datatable');
Route::get('/languages/{except_language?}/{only_language?}', 'LanguageController@index')->name('languages.index')
    ->where(['except_language' => '[0-9]+', 'only_language' => '[0-9]+']);
Route::match(['put', 'patch'], '/languages/{topic}/first', 'LanguageController@makeParent')
    ->name('languages.first')->where(['language' => '[0-9]+']);
Route::resource('languages', 'LanguageController');

/**
 * Topics
 */
Route::get('/topics/data', 'TopicController@datatable')->name('topics.datatable');
Route::resource('topics', 'TopicController');

/**
 * Questions
 */
Route::get('/topics/{topic}/questions/flow', 'QuestionController@tree')->name('topics.questions.flow');
Route::get('/topics/{topic}/questions/data', 'QuestionController@datatable')->name('topics.questions.datatable');
Route::get('/topics/{topic}/questions/{except_question?}/{only_question?}', 'QuestionController@index')->name('topics.questions.index')
    ->where(['topic' => '[0-9]+', 'except_question' => '[0-9]+', 'only_question' => '[0-9]+']);
Route::post('/topics/{topic}/questions/copy', 'QuestionController@copy')->name('topics.questions.copy');
Route::match(['put', 'patch'], '/topics/{topic}/questions/{question}/first', 'QuestionController@makeParent')
    ->name('topics.questions.first')->where(['topic' => '[0-9]+', 'question' => '[0-9]+']);
Route::resource('topics.questions', 'QuestionController');
