<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\BannedUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FaqCategoryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\InterestController;
use App\Http\Controllers\LeaderBoardController;
use App\Http\Controllers\MakeSupervisorOrAdminAccountController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\TeacherRequestsController;
use App\Http\Controllers\VerifyPinController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\WatchLaterController;
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
   Route::get('/username' , 'username');
});

});


Route::controller(CourseController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
    Route::get('getAllActivecourses',  'index');
    Route::get('get_all_courses',  'all_courses');
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
    //filter for category
    Route::get('/categories/{category-id}/courses',  'getCoursesByCategory');

    });
Route::controller(CourseController::class)
    ->group(function () {
        Route::get('getAllcourses',  'index');
        Route::get('getCourseDetails/{id}',  'show');
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

Route::controller(PaymentController::class)
    ->middleware('auth:sanctum')->group(function (){
   Route::get('checkout', 'checkout_page');
   Route::post('payment', 'checkout');
   Route::get('payment/success', 'stripe_success')->name('payment.success');
   Route::get('payment/cancel', 'stripe_cancel')->name('payment.cancel');
});


Route::post('/verify_pin_code',[VerifyPinController::class,'verify_pin_code'])->middleware('auth:sanctum');

//
//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');
Route::controller(CommentController::class)
    ->middleware('auth:sanctum')
    ->group(function(){
        Route::get('GetAllComments/{videoId}'        ,[CommentController::class,'index']);
        Route::get('GetCommetById/{id}'   ,   [CommentController::class,'show']);
        Route::post('CreateComment'       ,   'store');
        Route::post('UpdateComment/{id}'  ,  'update');
        Route::delete('DeleteComment/{videoid}/{id}', 'destroy');
        Route::post('videos/{id}/lock-comments',  'lockComments');
        Route::post('videos/{id}/unlock-comments', 'unlockComments');
    });

// FAQ Categories

Route::controller(FaqCategoryController::class)
    ->middleware('auth:sanctum')->group(function(){
        Route::get('GetAll_faq-categories',[FaqCategoryController::class,'index']);
        Route::get('GetOne_faq-categories/{id}', [FaqCategoryController::class,'show']);
        Route::post('Create_faq-categories',  'store'); // admin - supervisor
        Route::post('Update_faq-categories/{id}', 'update'); // admin - supervisor
        Route::delete('Delete_faq-categories/{id}','destroy'); // admin - supervisor

    });


// FAQs

Route::controller(FaqController::class)
    ->middleware('auth:sanctum')->group(function(){
        Route::get('GetAll_faq', 'index');
        Route::get('GetOne_faq/{id}', 'show');
        Route::post('Create_faq',  'store'); // admin - supervisor
        Route::post('Update_faq/{id}', 'update'); // admin - supervisor
        Route::delete('Delete_faq/{id}',  'destroy'); // admin - supervisor

    });


Route::middleware('auth:sanctum')->group(function () {
    Route::get('watch-later', [WatchLaterController::class, 'index']);
    Route::post('watch-later/toggle', [WatchLaterController::class, 'toggle']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('gettAll', [InterestController::class, 'index']);
    Route::post('updateInterstes/{id}', [InterestController::class, 'update']);
    Route::post('interests/toggle', [InterestController::class, 'toggle']);
});

Route::controller(LeaderBoardController::class)
    ->middleware('auth:sanctum')->group(function(){
        Route::get('GetAll_leaderboard',  'index');
        Route::post('AddTo_leaderboard',  'store');
        Route::delete('DeleteFrom_leaderboard/{id}', 'destroy');
    });

Route::controller(AchievementController::class)
    ->middleware('auth:sanctum')->group(function() {
        Route::get('GetAllAchiev/{userId}', [AchievementController::class, 'getByUser']);
        Route::get('getAllMyachivement', [AchievementController::class, 'getAllMyachivement']);
        Route::post('AddAchiev', [AchievementController::class, 'store']);
        Route::post('UpdateAchiev/{id}', [AchievementController::class, 'update']);
        Route::delete('DeleteAchiev/{id}', [AchievementController::class, 'destroy']);
    });

    Route::get('/user', function (Request $request) {
            return $request->user();
        })->middleware('auth:sanctum');



