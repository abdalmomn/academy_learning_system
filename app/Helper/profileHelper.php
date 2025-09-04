<?php

namespace App\Helper;

use App\Models\User;

class profileHelper
{
    public function fetch_profile($user_id)
    {
        $user = User::query()
            ->where('id',$user_id)
            ->select('id','username', 'email', 'email_verified_at')
            ->with('profile_details:id,first_name,last_name,date_of_birth,phone_number,profile_photo,user_id')
            ->with('courses')
            ->with('ratings')
            ->with('academic_certificates')
            ->first();
        if ($user->hasRole(['child','woman'])){
            unset($user['academic_certificates']);
            unset($user['ratings']);
        }
//        $user['course_count'] = $user->courses->count();

        if ($user->hasRole('teacher')){
            $user->load(['courses' => function($q) {
                $q->withCount('students');
            }]);
            $totalStudents = $user->courses->sum('students_count');
            $user['total_students'] = $totalStudents-1;
            $user['student_who_rates'] = $user->ratings->count('id') ?? 0;
            $user['total_rate'] = $user->ratings->sum('rate')/$user['student_who_rates'];
        }else{
            unset($user->teacher_ratings_received);
        }
        unset($user['roles']);
        unset($user['ratings']);
        $user->courses->each(function ($course) {
            unset($course->pivot);
        });
        return $user;
    }
}
