<?php

<<<<<<< HEAD
use App\Http\Controllers\SocialiteController;
=======
>>>>>>> ca7ced0 (first version: database, models and spatie role)
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
<<<<<<< HEAD
Route::controller(SocialiteController::class)->group(function (){
    Route::get('/google/redirect', 'redirectToGoogle');
    Route::get('/google/callback', 'handleGoogleCallback');
});
=======
>>>>>>> ca7ced0 (first version: database, models and spatie role)
