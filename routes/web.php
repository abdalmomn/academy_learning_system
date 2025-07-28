<?php

use App\Http\Controllers\SocialiteController;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\EmailVerificationController;

Route::get('/', function () {
    return view('welcome');
});
Route::controller(SocialiteController::class)->group(function (){
    Route::get('/google/redirect', 'redirectToGoogle');
    Route::get('/google/callback', 'handleGoogleCallback');
});


    // Email verification notice
    Route::get('email/verify', function (){
        return response()->json(['message' => 'Verify your email address']);
    }) -> name('verification.notice');

    // Email verification handler


Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

    // Resend verification email
Route::post('email/resend', function (Request $request){
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification email resent.']);
})->middleware('throttle:6,1')->name('verification.resend');
