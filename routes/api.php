<?php

use App\Http\Controllers\product\CommentController;
use App\Http\Controllers\Product\ViewController;
use App\Http\Controllers\User\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\LikeController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group([
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::group(['middleware' => 'authUser:api',], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('me', [AuthController::class, 'me']);
    });


});
Route::group(['middleware' => 'authUser:api'], function () {
    Route::get('views/{id}', [ViewController::class, 'view']);
    Route::post('likes/{id}', [LikeController::class, 'likeAble']);
    Route::group(['prefix' => 'comments'], function () {
        Route::post('add/{id}', [CommentController::class, 'add']);
        Route::get('showComment/{id}', [CommentController::class, 'showComment']);
        Route::delete('delete/{id}', [CommentController::class, 'deleteComment']);
    }
    );
    Route::post('add', [ProductController::class, 'store']);
    Route::post('show/{id}', [ProductController::class, 'show']);
    Route::get('showAll', [ProductController::class, 'index']);
    Route::post('searchName', [ProductController::class, 'searchName']);
    Route::post('searchDate', [ProductController::class, 'searchDate']);
    Route::post('searchCat', [ProductController::class, 'searchCat']);
    Route::delete('delete/{id}', [ProductController::class, 'destroy']);
    Route::post('update/{id}', [ProductController::class, 'update']);
});
