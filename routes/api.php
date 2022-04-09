<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
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

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->name('auth.login');

    Route::post('/register', 'register')->name('auth.register');

    Route::post('/register-verify', 'registerVerify')->name('auth.register-verify');

    Route::post('/resend-verification-code', 'resendVerificationCode')->name('auth.resend-verification-code');
});



Route::middleware('auth:sanctum')->controller(UserController::class)->group(function () {
    Route::post('/change-email', 'changeEmail')->name('user.change-email');
    Route::post('/change-email-submit', 'changeEmailSubmit')->name('user.change-email-submit');

    Route::match(['put', 'post'], '/change-password', 'changePassword')->name('user.change-password');
});

Route::prefix('/channel')->middleware(['auth:sanctum'])->controller(ChannelController::class)->group(function () {
    Route::put('/update/{id?}', 'update')->name('channel.update');

    Route::match(['post', 'put'], '/', 'uploadBanner')->name('channel.upload-banner');

    Route::match(['post', 'put'], '/update-social-networks', 'updateSocialNetworks')->name('channel.update-social-networks');
});


Route::prefix('/video')->middleware(['auth:sanctum'])->controller(VideoController::class)->group(function () {
    Route::post('/upload', 'uploadVideo')->name('video.upload');
});


/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */
