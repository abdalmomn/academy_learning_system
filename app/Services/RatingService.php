<?php

namespace App\Services;

use App\DTO\CourseRatingDto;
use App\DTO\TeacherRatingDto;
use App\Models\CourseRating;
use App\Models\TeacherRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RatingService
{

    public function rate_course($course_id, $rate)
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole(['woman', 'child'])) {
                return [
                    'data' => null,
                    'message' => 'only women and child can rate courses'
                ];
            }

            $rating = DB::transaction(function () use ($course_id, $rate, $user) {
                return CourseRating::updateOrCreate(
                    ['course_id' => $course_id, 'user_id' => $user->id],
                    ['rate' => $rate]
                );
            });

            Log::info('Course rated successfully', [
                'user_id' => $user->id,
                'course_id' => $course_id,
                'rate' => $rate,
            ]);

            $dto = CourseRatingDto::fromArray($rating->toArray());

            return [
                'data' => $dto,
                'message' => 'Course rated successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to rate course', [
                'course_id' => $course_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return [
                'data' => null,
                'message' => 'Failed to rate course'
            ];
        }
    }

    public function get_course_ratings($course_id)
    {
        try {
            $ratings = CourseRating::query()
                ->where('course_id', $course_id)
                ->get();

            if ($ratings->isEmpty()) {
                return [
                    'data' => null,
                    'average_rate' => null,
                    'message' => 'No ratings found for this course'
                ];
            }

            $averageRate = (int)round($ratings->avg('rate'), 2);


            Log::info('Course ratings retrieved', [
                'course_id' => $course_id,
                'count' => $ratings->count(),
                'average_rate' => $averageRate,
            ]);

            return [
                'data' =>$averageRate,
//                'average_rate' => $averageRate,
                'message' => 'Course ratings retrieved successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to fetch course ratings', [
                'course_id' => $course_id,
                'error' => $e->getMessage(),
            ]);
            return [
                'data' => null,
                'average_rate' => null,
                'message' => 'Failed to get course ratings'
            ];
        }
    }

    public function rate_teacher($teacher_id, $rate)
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole(['woman', 'child'])) {
                return [
                    'data' => null,
                    'message' => 'only women and child can rate teachers'
                ];
            }

            $rating = DB::transaction(function () use ($teacher_id, $rate, $user) {
                return TeacherRating::updateOrCreate(
                    ['teacher_id' => $teacher_id, 'user_id' => $user->id],
                    ['rate' => $rate]
                );
            });

            Log::info('Teacher rated successfully', [
                'user_id' => $user->id,
                'teacher_id' => $teacher_id,
                'rate' => $rate,
            ]);

            $dto = TeacherRatingDto::fromArray($rating->toArray());

            return [
                'data' => $dto,
                'message' => 'Teacher rated successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to rate teacher', [
                'teacher_id' => $teacher_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return [
                'data' => null,
                'message' => 'Failed to rate teacher'
            ];
        }
    }

    public function get_teacher_ratings($teacher_id)
    {
        try {
            $ratings = TeacherRating::query()
                ->where('teacher_id', $teacher_id)
                ->get();

            if ($ratings->isEmpty()) {
                return [
                    'data' => null,
                    'message' => 'No ratings found for this teacher'
                ];
            }

            $averageRate = (int)round($ratings->avg('rate'), 2);

            Log::info('Teacher ratings retrieved', [
                'teacher_id' => $teacher_id,
                'count' => $ratings->count(),
                'average_rate' => $averageRate,
            ]);

            return [
                'data' => $averageRate,
                'message' => 'Teacher ratings retrieved successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to fetch teacher ratings', [
                'teacher_id' => $teacher_id,
                'error' => $e->getMessage(),
            ]);
            return [
                'data' => null,
                'message' => 'Failed to get teacher ratings'
            ];
        }
    }
}
