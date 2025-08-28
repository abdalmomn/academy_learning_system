<?php

namespace App\Services;
use App\DTO\ApproveCourseDto;
use App\Helper\videoHelper;
use App\Jobs\ApproveScheduledCourseJob;
use App\Models\Course;
use App\Dto\CourseDto;
use App\Models\CourseRequirement;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isEmpty;

class CourseService
{
    public $video_helper;
    public function __construct(videoHelper $video_helper){$this->video_helper = $video_helper;}

    public function get_all_courses(): array
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')){
            return [
                'data' => null,
                'message' => 'must be a admin to see all teacher'
            ];
        }
        $courses = Course::query()->select(
                    'id','course_name','description','poster','rating', 'price'
                    ,'status', 'is_paid', 'start_date', 'end_date','user_id','category_id')
        ->get();
        if ($courses->isEmpty()){
            return [
                'data' => null,
                'message' => 'there is no courses right now'
            ];
        }
        return [
            'data' => $courses,
            'message' => 'retrieve all courses successfully'
        ];
    }

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
            if (!$course){
                return [
                    'data' => null,
                    'message' => 'course not found'
                ];
            }
            $requirements = CourseRequirement::where('course_id', $course->id)->pluck('requirements');
            $course['requirements'] = $requirements;

            $videos = Video::query()
                ->where('course_id',$course->id)
                ->get();
            $courseTotalDurationSeconds = 0;

            foreach ($videos as $video){
                $courseTotalDurationSeconds += $this->video_helper->hmsToSeconds($video->duration);
            }

            $course['total_duration'] = $this->video_helper->secondsToHms($courseTotalDurationSeconds);
            $course['videos_count'] = $videos->count();
            $user_count = $course->user()->count();
            $course['users_count'] = $user_count - 1;

            if (!$course) {
                return ['data' => null, 'message' => 'Course not found'];
            }
            return ['data' => [
                'course_details' => $course,
                'videos' => $videos
                ], 'message' => 'Course details retrieved successfully'];
        } catch (\Exception $e) {
            Log::error('Fetching course failed', ['error' => $e->getMessage(), 'id' => $id]);
            return ['data' => null, 'message' => 'Failed to fetch course'];
        }
    }

    public function getMyCourses()
    {
        try {
            $user = auth()->user();

            $courses = $user->courses()->withPivot(['is_completed', 'certificate_id'])
                ->get();
            if ($courses->isEmpty()){
                return ['data' => null, 'message' => 'there is no courses right now'];
            }
            return ['data' => $courses, 'message' => 'Your courses retrieved successfully'];
        } catch (\Exception $e) {
            Log::error('Fetching user courses failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to fetch your courses'];
        }
    }

    public function getEndedCourses()//done
    {
        $user = Auth::user();
        if (!$user->hasRole('supervisor')){
            return [
                'data' => null,
                'message' => 'must be a teacher to update a course requirement'
            ];
        }
        try {
            $courses = Course::where('end_date', '<', now()->toDateString())->get();
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
        $user = Auth::user();
        if (!$user->hasRole(['teacher','supervisor'])){
            return [
                'data' => null,
                'message' => 'must be a supervisor or teacher to delete a course'
            ];
        }
        DB::beginTransaction();
        try {
            $course = Course::find($id);
            if (!$course) {
                return ['data' => null, 'message' => 'Course not found'];
            }
            $course->user()->detach();
            $course->rate()->delete();
            $course->requirements()->delete();
            $course->exams()->delete();
            $course->purchases()->delete();
            $course->videos()->delete();

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
        $user = Auth::user();
        if (!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be a teacher to create a course'
            ];
        }
        DB::beginTransaction();
        try {
            //update this function to only get (array)$dto instead of this long code
            $course = Course::query()
                ->create([
                'course_name' => $dto->course_name,
                'description' => $dto->description,
                'rating' => 0.0,
                'price' => $dto->price,
                'status' => 'pending_approval',
                'is_paid' => $dto->is_paid,
                'start_date' => $dto->start_date,
                'end_date' => $dto->end_date,
                'user_id' => Auth::id(),
                'category_id' => $dto->category_id,
                'poster' => $dto->poster,
            ]);
            $course->refresh();
            //to set up the requirements of this course
            $requirements = request()->input('requirements', []); //array of strings
            foreach ($requirements as $req) {
                CourseRequirement::create([
                    'course_id' => $course->id,
                    'requirements' => $req,
                ]);
            }
            //create a user_courses record
            $course->user()->attach(Auth::id(),[
                'is_completed' => false,
                'certificate_id' => null,
            ]);
            $course['requirements'] = $requirements;
            DB::commit();
            Log::info('Course created', ['id' => $course->id]);
            return [
                'data' => $course,
                'message' => 'Course created successfully, wait for approval'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Course creation failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to create course'];
        }
    }

    public function update($id, CourseDto $dto): array
    {
        $user = Auth::user();
        if (!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be a teacher to create a course'
            ];
        }
        DB::beginTransaction();
        try {
            $course = Course::find($id);
            if (!$course) {
                return ['data' => null, 'message' => 'Course not found'];
            }

            $course->update((array)$dto);
            DB::commit();
            Log::info('Course updated', ['id' => $id]);
            return ['data' => $course, 'message' => 'Course updated successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Course update failed', ['error' => $e->getMessage(), 'id' => $id]);
            return ['data' => null, 'message' => 'Failed to update course'];
        }
    }

    public function get_pending_courses(): array
    {
        $user = Auth::user();
        if (!$user->hasRole('supervisor')){
            return [
                'data' => null,
                'message' => 'must be a teacher to update a course requirement'
            ];
        }
        try {
            $courses = Course::where('status', '=', 'pending_approval')->get();
            if($courses->isempty()) {
                return ['data' => null, 'message' => 'there is not  courses right now'];
            }else{
                return ['data' => $courses, 'message' => 'pending courses retrieved successfully'];
            }
        } catch (\Exception $e) {
            Log::error('Fetching pending courses failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to fetch pending courses'];
        }
    }

    public function update_course_requirements($request,$requirement_id): array
    {
        $user = Auth::user();
        if (!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be a teacher to update a course requirement'
            ];
        }
        DB::beginTransaction();
        try {
            $requirement = CourseRequirement::find($requirement_id);
            if (!$requirement) {
                return ['data' => null, 'message' => 'requirement not found'];
            }

            $requirement->update(['requirements' => $request->requirements]);
            DB::commit();
            Log::info('requirement updated', ['id' => $requirement_id]);
            return ['data' => $requirement, 'message' => 'requirement updated successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('requirement update failed', ['error' => $e->getMessage(), 'id' => $requirement_id]);
            return ['data' => null, 'message' => 'Failed to update requirement'];
        }
    }

    public function delete_course_requirements($requirement_id): array
    {
        $user = Auth::user();
        if (!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be a teacher to delete a course requirement'
            ];
        }
        DB::beginTransaction();
        try {
            $requirement = CourseRequirement::find($requirement_id);
            if (!$requirement) {
                return ['data' => null, 'message' => 'requirement not found'];
            }

            $requirement->delete();
            DB::commit();
            Log::info('requirement deleted', ['id' => $requirement_id]);
            return ['data' => null, 'message' => 'requirement deleted successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('requirement delete failed', ['error' => $e->getMessage(), 'id' => $requirement_id]);
            return ['data' => null, 'message' => 'Failed to delete requirement'];
        }
    }

    public function approve_update_course_status(ApproveCourseDto $dto,$course_id): array
    {
        $user = Auth::user();
        if (!$user->hasRole('supervisor')){
            return [
                'data' => null,
                'message' => 'must be a supervisor to update a course requirement'
            ];
        }
        try {
            $course = Course::query()->find($course_id);
            if (!$course) {
                return ['data' => null, 'message' => 'course not found'];
            }

            if ($dto->status === 'approved') {

                //if the start date is in  the future
                if ($course->start_date && now()->lt($course->start_date)) {
                    //automatically publish the course
                    ApproveScheduledCourseJob::dispatch($course->id)->delay($course->start_date);
                    $course->update(['status' => 'approved']);

                    Log::info('course scheduled for automatic approval', ['id' => $course_id]);
                    return [
                        'data' => null,
                        'message' => 'course has been scheduled for automatic approval'
                    ];
                }
                //immeditlly approve the course
                $course->update(['status' => 'published']);

                Log::info('course approved immediately', ['id' => $course_id]);
                return [
                    'data' => null,
                    'message' => 'Course approved successfully'
                ];
            }
            //if there is another status (rejected ...)
            $course->update(['status' => $dto->status]);
            Log::info('course approved', ['id' => $course_id]);
            return ['data' => null, 'message' => 'course approved successfully'];
        } catch (\Exception $e) {
            Log::error('course approve failed', ['error' => $e->getMessage(), 'id' => $course_id]);
            return ['data' => null, 'message' => 'Failed to approve course'];
        }
    }
}
