<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('web/getToken', 'WebController@getToken');
// FOR WEB VERSION
Route::get('web/getData', 'WebController@getData');
Route::get('web/validatePhone', 'WebController@validatePhone');



Route::post('register', 'AuthController@register');
Route::post('register/{provider}', 'AuthController@registerWithProvider');
Route::post('login', 'AuthController@authenticate');
Route::post('user/verification', 'AuthController@verification');
Route::get('user/simpleTypes', 'UsersController@getSimpleTypes');

// Test routes
Route::get('test/sms', 'AuthController@testSMS');
Route::get('organizations/{id}', 'OrganizationsController@show');
Route::group(['prefix' => 'blogs'], function() {
    Route::get('/', 'BlogPostController@getBlogs');
});
Route::group(['prefix' => 'posts'], function() {
    Route::get('/', 'BlogPostController@getPosts');
    Route::get('/{id}', 'BlogPostController@showPost');
    Route::get('/{id}/favorite', 'BlogPostController@postFavorite');
});
Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('user', 'AuthController@getAuthenticatedUser');
    Route::get('get_user/{id}', 'AuthController@getUser');
    Route::post('contactus', 'MainController@contactus');

    Route::group(['prefix' => 'profile'], function() {
        Route::get('requests/list', 'ProfileController@requests');
        Route::get('favorite/list', 'ProfileController@favorites');
        Route::delete('favorite/{id}', 'ProfileController@favoriteDelete');
        Route::get('getInfo', 'ProfileController@getInfo');
        Route::post('school/store', 'ProfileController@storeSchool');
        Route::post('additionalInfo/store', 'ProfileController@storeAdditionalInfo');
        Route::post('birthday/store', 'ProfileController@storeBirthday');
        Route::post('city/store', 'ProfileController@storeCity');
        Route::post('details/store', 'ProfileController@storeDetails');
    });


    Route::post('/jobs_r/{job_id}/faq/{id}/apply', 'AuthJobsController@apply');
    Route::get('/jobs_r/{job_id}/faq/filter/getData', 'AuthJobsController@filterGetData');
    Route::post('/jobs_r/{job_id}/faq/{skip}/{sort}', 'AuthJobsController@getJobs'); 
    Route::get('/jobs_r/{job_id}/faq/{id}', 'AuthJobsController@view');
    Route::get('/jobs_r/{job_id}/faq/index', 'FaqController@index');
    Route::post('/jobs_r/{job_id}/faq/store', 'FaqController@store');
    Route::post('/jobs_r/{job_id}/faq/{id}/answer/', 'FaqController@answer');
    Route::post('/jobs_r/{job_id}/faq/{id}/answer/back/', 'FaqController@answerBack');
    Route::get('/jobs_r/{job_id}/faq/job-questions', 'FaqController@jobQuestion');
    Route::get('/jobs_r/faq/{job_id}/reviews/', 'JobReviewsController@index');
    Route::get('/jobs_r/faq/{job_id}/reviews/getData', 'JobReviewsController@getData');
    Route::post('/jobs_r/faq/{job_id}/reviews/store', 'JobReviewsController@store');

    Route::group(['prefix' => 'jobs'], function() {
        Route::post('/{id}/apply', 'AuthJobsController@apply');
        Route::get('filter/getData', 'AuthJobsController@filterGetData');
        Route::post('{skip}/{sort}', 'AuthJobsController@getJobs');
        Route::get('{skip}/{sort}', 'AuthJobsController@getJobs');
        Route::get('/{id}', 'AuthJobsController@view');
        Route::get('view_edit/{id}', 'AuthJobsController@view');
        Route::group(['prefix' => '{job_id}/faq'], function() {
            Route::get('/index', 'FaqController@index');
            Route::post('/store', 'FaqController@store');
            Route::post('/{id}/answer', 'FaqController@answer');
            Route::post('{id}/answer/back', 'FaqController@answerBack');
            Route::get('/job-questions', 'FaqController@jobQuestion');
        });
        Route::group(['prefix' => '{job_id}/reviews'], function() {
            Route::get('/index', 'JobReviewsController@index');
            Route::get('/getData', 'JobReviewsController@getData');
            Route::post('/store', 'JobReviewsController@store');
        });
    });
    Route::group(['prefix' => 'hr'], function() {
        Route::get('faq/questions', 'FaqController@hrQuestion');
        Route::get('jobs/questions/{hr_id}', 'FaqController@getHrJobsQuestions');
    });
    Route::get('job/{id}/favorite', 'AuthJobsController@addFavorite');
    Route::group(['prefix' => 'chat'], function() {
        Route::get('conversations', 'ChatController@conversations');
        Route::get('conversation/{id}', 'ChatController@messages');
        Route::post('conversation/{id}/store', 'ChatController@store');
    });

    Route::group(['prefix' => 'libraries'], function() {
        Route::get('categories', 'LibrariesController@getCategories');
        Route::get('areas', 'LibrariesController@getAreas');
        Route::get('categories/{id}', 'LibrariesController@getCategory');
        Route::get('subcategories/{id}', 'LibrariesController@getSubCategoryByID');
        Route::get('category/{id}', 'LibrariesController@getSubcategory');
        Route::get('cities/{id}', 'LibrariesController@getCities');
        Route::get('duration/{year}', 'LibrariesController@getDurationByYear');
    });
    Route::group(['middleware' => ['ishr']], function() {
        Route::get('/account', 'HrController@account');
        Route::post('/account/store', 'HrController@accountStore');
        Route::post('/account/store/{digit_code}', 'HrController@activateNewNumber');
        Route::post('job/{id}/status/{status}', 'HrJobController@updateStatus');
        Route::get('job/{id}/contenders', 'HrJobController@getContenders');
        Route::get('job/contenders/all', 'HrJobController@getContendersAll');
        Route::get('job/contenders/chosen/all', 'HrJobController@getChosenContendersAll');
        Route::get('job/{id}/opportunity', 'HrJobController@opportunity');
        Route::get('chat/conversation/open/{user_id}', 'ChatController@open');
        Route::get('opportunity/{id}', 'HrJobController@opportunityView');
        Route::get('opportunity/type/data', 'HrJobController@opportunityGetTypes');
        Route::get('opportunity/{type}/open/getData', 'HrJobController@opportunityGetData');
        Route::post('opportunity/{type}/store', 'HrJobController@opportunityStore');
        Route::get('opportunity/{id}/get', 'HrJobController@opportunityGet');
        Route::get('opportunity/{id}/edit', 'HrJobController@opportunityEdit');
        Route::post('opportunity/{id}/update', 'HrJobController@opportunityUpdate');
        Route::get('opportunity/{id}/chosen/contenders', 'HrJobController@getOpportunityChosenContenders');
        Route::get('opportunity/{id}/contenders/{contender_id}', 'HrJobController@getOpportunityContender');
        Route::post('opportunity/{id}/contenders/{contender_id}/update/{status}', 'HrJobController@opportunityContenderUpdateStatus');
    });



//    Route::get('quizzes/{role_id}', 'QuizzesController@getByRoleID');
//    Route::post('quizzes/{answer_id}', 'QuizzesController@saveUserAnswer');
});
Route::post('/notifications/{id}/view-yes', 'NotificationController@viewYes');

//Route::group(['middleware' => ['web']], function () {
//    Route::get('auth/{provider}', 'AuthController@redirectToProvider');
//    Route::get('auth/{provider}/callback', 'Auth\AuthController@handleProviderCallback');
//});

// Quiz
Route::group(['prefix' => 'quiz'], function() {
    Route::get('/get', 'Api\QuizController@getQuiz');
    Route::get('/get/result', 'Api\QuizController@getQuizResult');

    Route::post('/set', 'Api\QuizController@setQuiz');
});


