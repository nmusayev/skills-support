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


Route::post('login', 'UserController@login');
Route::post('register', 'UserController@register');

Route::group(['middleware' => 'auth:api'], function()
{
    // GET and UPDATE USER
    Route::get('user', 'UserController@details');
//    Route::post('user/all/', 'UserController@search');
    Route::put('user', 'UserController@update');
    Route::post('/updateProfileImage', 'UserController@updateProfileImage');

    // CREATE, GET all, GET specific, GET general, and DELETE SKILL
    Route::post('skill', 'SkillController@store');
    Route::get('skill', 'SkillController@index');
    Route::get('skill/all', 'SkillController@all');
    Route::delete('skill/{skill}', 'SkillController@destroy');
    Route::get('skill/general', 'SkillController@general');
    Route::get('skill/{skill}', 'SkillController@show');

    // CREATE, RECENT, SPECIFIC, UPDATE and DELETE QUESTION
    Route::post('question', 'QuestionController@store');
    Route::put('question/{question}', 'QuestionController@update');
    Route::delete('question/{question}', 'QuestionController@destroy');

    // GET, CREATE, EDIT and DELETE ANSWER
    Route::post('/answer', 'AnswerController@store');
    Route::patch('/answer/{answer}', 'AnswerController@update');
    Route::delete('/answer/{answer}', 'AnswerController@destroy');

    // VOTE UP, DOWN and BEST ANSWER
    Route::post('/answer/{answer}/up', 'AnswerController@up');
    Route::post('/answer/{answer}/down', 'AnswerController@down');
    Route::post('/answer/{answer}/best', 'AnswerController@best');

    // Languages
    Route::get('/language', 'LanguageController@index');
    Route::get('/language/all', 'LanguageController@all');
    Route::post('/user/language', 'UserController@addLanguage');
    Route::delete('/user/language', 'UserController@deleteLanguage');
});


// GET USER, SKILL, LANGUAGE with USER ID
Route::get('user/all/{name?}', 'UserController@all');
Route::get('user/{user}', 'UserController@detailsWithId');
Route::get('skill/user/{user}', 'SkillController@skillsWithId');
Route::get('language/user/{user}', 'LanguageController@languagesWithId');
Route::get('skill/general/user/{user}', 'SkillController@generalWithId');
Route::get('skill/{skill}/user/{user}', 'SkillController@showWithId');


// Getting Questions
Route::get('/question/recent', 'QuestionController@recent');

// Question-Detail
Route::get('question/{question}', 'QuestionController@show');
//Route::get('question/{question}/public-view', 'QuestionController@showToPublic');
Route::get('/question/{question}/answer', 'AnswerController@questionAnswers');
Route::get('/question/search/{key}', 'QuestionController@search');
