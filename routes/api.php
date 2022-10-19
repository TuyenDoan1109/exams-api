<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\ExamController;
use App\Http\Controllers\Api\v1\ArticleController;
use App\Http\Controllers\Api\v1\BrandController;


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

Route::namespace('Api\v1')->prefix('v1')->group(function() {
    // PUBLIC ROUTE
    
    // users
    Route::group(['prefix' => 'users'], function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password/{token}', [AuthController::class, 'resetPassword']);
    });    
    
    // Exams
    Route::group(['prefix' => 'exams'], function () {
        Route::get('/', [ExamController::class, 'index']);
        Route::get('/{id}', [ExamController::class, 'show'])->where('id','[0-9]+');

        //testttttttttt
        Route::get('/{slug}', [ExamController::class, 'abc'])->where('slug', 'a-b-c');
        Route::get('/{slug}', [ExamController::class, 'xyz'])->where('slug', 'x-y-z');
    });

    // Articles
    Route::group(['prefix' => 'articles'], function () {
        Route::get('/', [ArticleController::class, 'index']);
        Route::get('/{id}', [ArticleController::class, 'show'])->where('id','[0-9]+');
    });









    // PROTECTED ROUTES
    Route::group(['middleware' => ['auth:api']], function() {
        // Users
        Route::group(['prefix' => 'users'], function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
        });
        Route::resource('users', 'AuthController')->except('create', 'edit', 'store');
    
        // Exams
        Route::group(['prefix' => 'exams'], function () {
            Route::post('/', [ExamController::class, 'store']);
            Route::put('/{id}', [ExamController::class, 'update']);
            Route::delete('/{id}', [ExamController::class, 'destroy']);
            Route::get('/deleted', [ExamController::class, 'deleted']);
        });
    
        // Articles
        Route::group(['prefix' => 'articles'], function () {
            Route::post('/', [ArticleController::class, 'store']);
            Route::put('/{id}', [ArticleController::class, 'update']);
            Route::delete('/{id}', [ArticleController::class, 'destroy']);
            Route::get('/deleted', [ArticleController::class, 'deleted']);
        });


        
    });


    Route::resource('brands', 'BrandController');
    
});





