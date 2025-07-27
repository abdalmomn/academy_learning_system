<?php

namespace App\Services;
use App\Models\Course;
use App\Dto\CourseDto;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class CourseService
{
    public function getAllActive()//done
    {
        try {
            $courses = Course::where('status', 'published')->get();
            if($courses->isempty()) {
                return ['data' => null, 'message' => 'there is not Active courses '];
            }else{
                return ['data' => $courses, 'message' => 'Active courses retrieved successfully'];
            }
        } catch (\Exception $e) {
            Log::error('Fetching active courses failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to fetch active courses'];
        }
    }

    public function getById($id)//done
    {
        try {
            $course = Course::find($id);
            if (!$course) {
                return ['data' => null, 'message' => 'Course not found'];
            }
            return ['data' => $course, 'message' => 'Course details retrieved successfully'];
        } catch (\Exception $e) {
            Log::error('Fetching course failed', ['error' => $e->getMessage(), 'id' => $id]);
            return ['data' => null, 'message' => 'Failed to fetch course'];
        }
    }

    public function getMyCourses()
    {
        try {
            $user = auth()->user();
            if (!$user || auth()->user()->role !== 'teacher'){
                return ['data' => null, 'message' => 'Unauthorized - teacher only'];

            }
//            $userId = auth()->id();
            $courses = Course::where('user_id', $user->auth()->id )->get();
            return ['data' => $courses, 'message' => 'Your courses retrieved successfully'];
        } catch (\Exception $e) {
            Log::error('Fetching user courses failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to fetch your courses'];
        }
    }

    public function getEndedCourses()//done
    {
        try {
            $today = now()->toDateString();
            $courses = Course::where('end_date', '<', $today)->where('status', 'published')->get();
            if($courses->isempty()) {
                return ['data' => null, 'message' => 'there is not  courses today '];
            }else{
                return ['data' => $courses, 'message' => 'Ended courses retrieved successfully'];
            }
        } catch (\Exception $e) {
            Log::error('Fetching ended courses failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to fetch ended courses'];
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $course = Course::find($id);
            if (!$course) {
                DB::rollBack();
                return ['data' => null, 'message' => 'Course not found'];
            }
            $course->delete();
            DB::commit();
            Log::info('Course deleted', ['id' => $id]);
            return ['data' => null, 'message' => 'Course deleted successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Course deletion failed', ['error' => $e->getMessage(), 'id' => $id]);
            return ['data' => null, 'message' => 'Failed to delete course'];
        }
    }

    public function store(CourseDto $dto)
    {
        DB::beginTransaction();
        try {
            $course = Course::create([
                'course_name' => $dto->course_name,
                'description' => $dto->description,
                'rating' => $dto->rating,
                'status' => $dto->status,
                'is_paid' => $dto->is_paid,
                'start_date' => $dto->start_date,
                'end_date' => $dto->end_date,
                'user_id' => auth()->id(),
                'category_id' => $dto->category_id,
            ]);
            DB::commit();
            Log::info('Course created', ['id' => $course->id]);
            return ['data' => $course, 'message' => 'Course created successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Course creation failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to create course'];
        }
    }

    public function update($id, CourseDto $dto)
    {
        DB::beginTransaction();
        try {
            $course = Course::find($id);
            if (!$course) {
                DB::rollBack();
                return ['data' => null, 'message' => 'Course not found'];
            }

            $course->update([
                'course_name' => $dto->course_name,
                'description' => $dto->description,
                'rating' => $dto->rating,
                'status' => $dto->status,
                'is_paid' => $dto->is_paid,
                'start_date' => $dto->start_date,
                'end_date' => $dto->end_date,
                'category_id' => $dto->category_id,
            ]);

            DB::commit();
            Log::info('Course updated', ['id' => $id]);
            return ['data' => $course, 'message' => 'Course updated successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Course update failed', ['error' => $e->getMessage(), 'id' => $id]);
            return ['data' => null, 'message' => 'Failed to update course'];
        }
    }
}
