<?php

use Illuminate\Support\Facades\Route;

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



// Authentication Routes...
Route::get('/login', 'Auth\Admin\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\Admin\LoginController@login');
Route::post('/logout','Auth\Admin\LoginController@logout')->name('logout');
Route::group(['middleware' => ['admin']], function() {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/jobsList', 'HomeController@jobsList')->name('jobs.list');
    Route::get('/jobs/checked/{id}', 'JobsController@checked')->name('jobs.checked');
    Route::get('/jobs/files/remove/{id}', 'JobsController@removeFile')->name('store.job.files.remove');
    Route::post('/jobs/files', 'JobsController@storeFiles')->name('store.job.files');
    Route::get('/jobs/files', 'JobsController@getFiles')->name('get.job.files');
    Route::resource('/jobs', 'JobsController');
    Route::get('/organizations/hr', 'OrganizationsController@getHr')->name('organizations.hr');
    Route::resource('/organizations', 'OrganizationsController');
    Route::resource('/users', 'UsersController');
    Route::post('/categories/removeFile/{id}', 'CategoriesController@removeFile');
    Route::resource('/categories', 'CategoriesController');
    Route::resource('/subcategories', 'SubcategoriesController');
    Route::resource('/areas', 'AreasController');
    Route::resource('/cities', 'CitiesController');
    Route::post('/quizzes/update/{id}', 'QuizzesController@update')->name('quizzes.new.update');
    Route::resource('/quizzes', 'QuizzesController');
    Route::resource('/years', 'YearsController');
    Route::resource('/schools', 'SchoolsController');
    Route::resource('/blogs', 'BlogsController');
    Route::resource('/posts', 'PostsController');
//    Route::resource('/locations', 'LocationsController');



    Route::resource('/addresses', 'AddressesController');
    Route::resource('/stageOfEducations', 'StageOfEducationsController');

    Route::get('/notification', 'NotificationController@index')->name('notification');
    Route::post('/notification', 'NotificationController@index')->name('notification.send');

    Route::get('/quiz-materials', 'QuizMaterialsController@index')->name('quiz.materials');
    Route::get('/quiz-materials/create', 'QuizMaterialsController@create')->name('quiz.materials.create');
    Route::post('/quiz-materials/create', 'QuizMaterialsController@create')->name('quiz.materials.store');
    Route::get('/quiz-materials/{material}', 'QuizMaterialsController@edit')->name('quiz.materials.edit');
    Route::post('/quiz-materials/{material}', 'QuizMaterialsController@edit')->name('quiz.materials.update');
});

Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
    Route::get('/notification', 'NotificationController@getList');
});
