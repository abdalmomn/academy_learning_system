<?php

use App\Http\Controllers\BannedUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FaqCategoryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\MakeSupervisorOrAdminAccountController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\TeacherRequestsController;
use App\Http\Controllers\VerifyPinController;
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
   Route::get('/username' , 'username');
});

});


Route::controller(CourseController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
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

    Route::get('get_all_courses',  'all_courses');
    Route::post('attendance_register',  'attendance_register');
    });
Route::controller(CourseController::class)
    ->group(function () {
        Route::get('getCourseDetails/{id}',  'show');
        Route::get('getAllActivecourses',  'index');
    });



Route::controller(CategoryController::class)->group(function () {
    Route::get('getAllCategory', 'index');
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
   Route::get('show_videos_by_course/{course_id}' , 'show_videos_by_course');
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
    ->middleware('auth:sanctum')->group(function(){
        Route::get('GetAllComments'        ,   'index');
        Route::get('GetCommetById/{id}'   ,    'show');
        Route::post('CreateComment'       ,   'store');
        Route::post('UpdateComment/{id}'  ,  'update');
        Route::delete('DeleteComment/{id}', 'destroy');
        Route::post('videos/{id}/lock-comments',  'lockComments');
        Route::post('videos/{id}/unlock-comments', 'unlockComments');
    });

// FAQ Categories

Route::controller(FaqCategoryController::class)
    ->middleware('auth:sanctum')->group(function(){
        Route::get('GetAll_faq-categories', 'index');
        Route::get('GetOne_faq-categories/{id}', 'show');
        Route::post('Create_faq-categories',  'store'); // admin - supervisor
        Route::post('Update_faq-categories/{id}', 'update'); // admin - supervisor
        Route::delete('Delete_faq-categories/{id}','destroy'); // admin - supervisor

    });

// FAQs

Route::controller(FaqCategoryController::class)
    ->middleware('auth:sanctum')->group(function(){
        Route::get('/faqs', 'index');
        Route::get('/faqs/{id}', 'show');
        Route::post('/faqs',  'store'); // admin - supervisor
        Route::post('/faqs/{id}', 'update'); // admin - supervisor
        Route::delete('/faqs/{id}',  'destroy'); // admin - supervisor

    });


    Route::get('/user', function (Request $request) {
            return $request->user();
        })->middleware('auth:sanctum');



Route::controller(ExamController::class)
    ->middleware('auth:sanctum')->group(function(){
        Route::post('create_exam' , 'create_exam');
        Route::get('show_exam/{exam_id}' , 'show_exam');
        Route::get('show_exam_by_course/{course_id}' , 'show_exams_by_course');
        Route::get('show_all_exams' , 'show_all_exams');
        Route::post('update_exam/{exam_id}' , 'update_exam');
        Route::delete('delete_exam/{exam_id}' , 'delete_exam');
        Route::post('store_exam_answer' , 'store_exam_answer');
        Route::get('get_exam_result/{exam_id}' , 'get_exam_result');
        Route::get('certificate' , 'certificate');
        Route::post('submit_project_answer' , 'submit_project_by_students');
    });

Route::controller(QuestionController::class)
    ->middleware('auth:sanctum')->group(function(){
        Route::post('create_question' , 'create_question');
        Route::post('update_question/{question_id}' , 'update_question');
        Route::get('show_question/{question_id}' , 'show_question');
        Route::delete('delete_question/{question_id}' , 'delete_question');
    });
Route::controller(OptionController::class)
    ->middleware('auth:sanctum')->group(function(){
        Route::post('create_option' , 'create_option');
        Route::post('update_option/{option_id}' , 'update_option');
        Route::delete('delete_option/{option_id}' , 'delete_option');
        Route::get('show_option_by_question/{question_id}' , 'show_option_by_question');
    });

Route::controller(DashboardController::class)
    ->middleware('auth:sanctum')->group(function(){
        Route::get('show_all_users' , 'show_all_users');
        Route::get('show_all_teachers' , 'show_all_teachers');
        Route::get('show_all_students' , 'show_all_students');
        Route::get('show_all_supervisors' , 'show_all_supervisors');
    });

