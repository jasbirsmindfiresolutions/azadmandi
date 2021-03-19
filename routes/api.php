<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/signup', [UsersController::class, 'signup']);

Route::post('/login-request', [UsersController::class, 'loginRequest']);

Route::post('signin', [UsersController::class, 'signin']);

Route::post('upload-profile-picture', [UsersController::class, 'uploadProfilePicture']);

Route::get('get-profile-picture', [UsersController::class, 'getProfilePicture']);
