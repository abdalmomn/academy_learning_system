<?php

use App\Http\Controllers\BannedUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\MakeSupervisorOrAdminAccountController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\TeacherRequestsController;
use App\Http\Controllers\VideoController;
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




Route::controller(ResetPasswordController::class)->group(function(){
   Route::post('/send_code', 'send_reset_password_code');
   Route::post('/check_code', 'check_reset_code');
   Route::post('/resend_code', 'resend_reset_code');
   Route::post('/reset_password', 'set_new_password');
});


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

Route::controller(ProfileController::class)
->middleware('auth:sanctum')
->group(function(){
   Route::get('/my_profile' , 'show_my_profile_details');
   Route::get('/profile/{user_id}' , 'show_user_profile_details');
   Route::post('/profile/edit' , 'edit_profile');

   Route::post('/profile/delete' , 'delete_account');
});

});


Route::controller(CourseController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
    Route::get('getAllcourses',  'index');
    Route::get('getCourseDetails/{id}',  'show');
    Route::get('getMy-courses',  'myCourses');
    Route::get('getEnded-courses',  'endedCourses');
    Route::post('createCourse', 'store');
    Route::post('updateCourse/{id}', 'update');
    Route::delete('deleteCourses/{id}', 'destroy');
    Route::post('update_requirement/{requirement_id}', 'update_course_requirements');
    Route::delete('delete_requirement/{requirement_id}', 'delete_course_requirements');
    Route::get('pending-courses',  'pending_courses');
    Route::post('approve-course/{course_id}',  'approve_course');
    });



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

Route::controller(VideoController::class)
    ->middleware('auth:sanctum')->group(function(){
   Route::post('add_video' , 'add_video');
   Route::post('update_video/{course_id}/{video_id}' , 'update_video');
   Route::delete('delete_video/{course_id}/{video_id}' , 'delete_video');
   Route::get('show_video/{video_id}' , 'show_video');
    });

Route::controller(PromoCodeController::class)
    ->middleware('auth:sanctum')->group(function(){
    Route::post('create_promo_code','create_promo_code');
    Route::get('my_promo_codes','show_my_promo_codes');
    Route::get('all_promo_codes','show_all_promo_codes');
    Route::delete('delete_promo_code/{code_id}','delete_promo_code');
    Route::delete('delete_promo_codes','delete_all_promo_codes');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
