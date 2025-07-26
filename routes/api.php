<?php

use App\Http\Controllers\BannedUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MakeSupervisorOrAdminAccountController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\TeacherRequestsController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::middleware('isBanned')->group(function () {

Route::controller(AuthenticationController::class)->group(function(){
   Route::post('sign_up','sign_up');
   Route::post('sign_in','sign_in');
   Route::get('logout','logout')->middleware('auth:sanctum');
   Route::get('spatie','roles')->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function (){
    // Email verification notice
    Route::get('email/verify', function (){
      return response()->json(['message' => 'Verify your email address']);
    }) -> name('verification.notice');

    // Email verification handler
    Route::get('email/verify/{id}/{hash}', function (EmailVerificationRequest $request){
     $request->fulfill();
     return response()->json(['message' => 'Email verified successfully']);
    })->middleware('signed')->name('verification.verify');

    // Resend verification email
    Route::post('email/resend', function (Request $request){
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification email resent.']);
    })->middleware('throttle:6,1')->name('verification.resend');
});


Route::controller(ResetPasswordController::class)->group(function(){
   Route::post('/send_code', 'send_reset_password_code');
   Route::post('/check_code', 'check_reset_code');
   Route::post('/resend_code', 'resend_reset_code');
   Route::post('/reset_password', 'set_new_password');
});
////////////////////                   CATEGORY                  ////////////


Route::controller(TeacherRequestsController::class)
->middleware('auth:sanctum')
->group(function(){
   Route::get('/teacher_requests', 'show_all_teacher_requests');
   Route::post('/handle_teacher_request/{teacher_id}', 'approve_teacher_request');
});


Route::controller(MakeSupervisorOrAdminAccountController::class)
->middleware(['auth:sanctum'])
->prefix('admin')
->group(function (){
        Route::post('/create_supervisor_admin_account','create_supervisor_admin_account');
});

Route::controller(BannedUserController::class)
    ->middleware('auth:sanctum')
    ->group(function (){
       Route::post('/ban_user', 'ban_user');
       Route::post('/banned_users', 'all_banned_users');
       Route::get('/temporary_banned_users', 'temporary_banned_users');
       Route::get('/permanent_banned_users', 'permanent_banned_users');
    });



});

/////////////////////            courses               ///////////////
use App\Http\Controllers\CourseController;

Route::controller(CourseController::class)->group(function () {
    Route::get('getAllcourses',  'index');
    Route::get('getCourseDetails/{id}',  'show');
    Route::get('getMy-courses',  'myCourses');
    Route::get('getEnded-courses',  'endedCourses');
    Route::post('createCourse', 'store');
    Route::post('updateCourse/{id}', 'update');
    Route::delete('deleteCourses/{id}', 'destroy');
});

////////////////////

Route::controller(CategoryController::class)->group(function () {
    Route::get('getAllCategory', 'index');
    Route::get('getCategoryDetails/{category}', 'show');
    Route::post('CreateCategory', 'store');
    Route::post('UpdateCategory/{category}', 'update');
    Route::delete('DeleteCategory/{category}', 'destroy');
});
//
//Route::controller(SocialiteController::class)->group(function (){
//    Route::get('/google/redirect', 'redirectToGoogle');
//    Route::get('/google/callback', 'handleGoogleCallback');
//});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
