<?php

namespace App\Services;
use App\DTO\ApproveCourseDto;
use App\Helper\videoHelper;
use App\Jobs\ApproveScheduledCourseJob;
use App\Models\Category;
use App\Models\Course;
use App\Dto\CourseDto;
use App\Models\CourseRequirement;
use App\Models\User;
use App\Models\UserAttendance;
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
        $courses = Course::query()
            ->with('user:id,username')
            ->select(
                'id','course_name','description','poster','rating',
                'price','status','is_paid','user_id','start_date',
                'end_date'
            )
            ->get();
        foreach ($courses as $course) {
            $teacher_name = User::query()
            ->where('id', $course->user_id)
            ->select(['username as teacher_name'])
            ->first();
            $course['teacher_name'] = $teacher_name;
            unset($course['user']);unset($course['user_id']);
        }
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
            $user = Auth::user();
            $query = Course::where('status', 'published');
            $type = null;
            if ($user->hasRole('woman')){
                $type = 'woman';
            }
            //courses for child
        elseif ($user->hasRole('child')) {
//            $query->where('type', 'children');
            $type = 'child';

                 //courses for teacher and guest and supervisor
            } elseif ($user->hasAnyRole(['guest', 'teacher', 'supervisor'])) {
                $type = 'general';
            } else {
                return ['data' => null, 'message' => 'You do not have access to view courses'];
            }
            $courses = Course::where('status', 'published')
                ->select('id', 'course_name','description' , 'poster', 'price', 'rating','is_paid', 'status','user_id','category_id')
                ->with('category:id,category_name')
                ->get();
            foreach ($courses as $course){
                unset($course->category['id']);
            }
            foreach ($courses as $course){
                $course['teacher_name'] = User::query()->select(['username as teacher_name'])->where('id', $course->user_id)->first();
                unset($course['user_id']);
            }
            if($courses->isempty()) {
                return ['data' => null, 'message' => 'there is not Active courses '];
            }
//            $courses = $query->get();

            if ($courses->isEmpty()) {
                return ['data' => null, 'message' => "There are no active courses for $type"];
            }
            return ['data' => $courses, 'message' => "Active courses retrieved successfully for $type"];

        } catch (\Exception $e) {
            Log::error('Fetching active courses failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to fetch active courses'];
        }
    }

    public function getById($id)
    {
        try {
            $course = Course::find($id);
            if (!$course) {
                return [
                    'data' => null,
                    'message' => 'course not found'
                ];
            }

            $requirements = CourseRequirement::where('course_id', $course->id)->pluck('requirements');
            $course['requirements'] = $requirements;

            $teacher_name = User::query()
                ->where('id', $course->user_id)
                ->select(['username as teacher_name'])
                ->first();

            $videos = Video::query()->where('course_id',$course->id)->get();
            $courseTotalDurationSeconds = 0;
            foreach ($videos as $video){
                $courseTotalDurationSeconds += $this->video_helper->hmsToSeconds($video->duration);
            }

            $course['total_duration'] = $this->video_helper->secondsToHms($courseTotalDurationSeconds);
            $course['videos_count'] = $videos->count();
            $user_count = $course->user()->count();
            $course['users_count'] = $user_count - 1;
            $category = Category::query()->find($course->category_id);
            $course['category_name'] = $category->category_name;
            unset($course['user_id'], $course['category_id'], $course['created_at'], $course['updated_at']);

            return [
                'data' => [
                    'course_details' => [$course, $teacher_name],
                ],
                'message' => 'Course details retrieved successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Fetching course failed', ['error' => $e->getMessage(), 'id' => $id]);
            return ['data' => null, 'message' => 'Failed to fetch course'];
        }
    }


    public function getMyCourses()
    {
        try {
            $user = auth()->user();
            $courses = $user->courses()
                ->select('courses.id','courses.course_name','courses.description',
                    'courses.poster','courses.price','courses.rating','courses.status','courses.user_id')
                ->withPivot(['is_completed'])
                ->get()
                ->map(function ($course) {
                    $teacher_name = User::query() ->where('id', $course->user_id) ->select(['username as teacher_name']) ->first();

                    return [
                        'id' => $course->id,
                        'course_name' => $course->course_name,
                        'description' => $course->description,
                        'poster' => $course->poster,
                        'price' => $course->price,
                        'rating' => $course->rating,
                        'status' => $course->status,
                        'teacher_name' => $teacher_name,
                        'is_completed' => $course->pivot->is_completed,
                    ];
                });

            if ($courses->isEmpty()) {
                return [
                    'data' => null,
                    'message' => 'there is no courses right now'
                ];
            }

            return [
                'data' => $courses,
                'message' => 'Your courses retrieved successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Fetching user courses failed', ['error' => $e->getMessage()]);
            return [
                'data' => null,
                'message' => 'Failed to fetch your courses'
            ];
        }
    }


    public function getEndedCourses()//done
    {
        $user = Auth::user();
        if (!$user->hasRole('supervisor')){
            return [
                'data' => null,
                'message' => 'must be a supervisor to get ended course'
            ];
        }
        try {
            $courses = Course::where('end_date', '<', now()->toDateString())
                ->select('id', 'course_name','description' , 'poster', 'price', 'rating','is_paid', 'status','user_id')
                ->get();
            foreach ($courses as $course){
                $course['teacher_name'] = User::query()->select(['username as teacher_name'])->where('id', $course->user_id)->first();
                unset($course['user_id']);
            }
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
                'message' => 'must be a teacher to show pending courses'
            ];
        }
        try {
            $courses = Course::where('status', '=', 'pending_approval')
                ->select('id', 'course_name','description' , 'poster', 'price', 'rating','is_paid', 'status','user_id')
                ->get();
            foreach ($courses as $course){
                $course['teacher_name'] = User::query()->select(['username as teacher_name'])->where('id', $course->user_id)->first();
                unset($course['user_id']);
            }
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
    public function getCoursesByCategory($categoryId){
        try {
        $category = Category::find($categoryId);
        if (!$category) {
            return ['data' => null, 'message' => 'this category is not found'];
        }
        $user = Auth::user();
        if (!$user) {
            return ['data' => null, 'message' => 'unauthorized'];
        }
        $query = Course::where('category_id', $categoryId)->where('status', 'published');

//        if ($user->hasRole('woman')) {
//            $query->where('type', 'female');
//        } elseif ($user->hasRole('child')) {
//            $query->where('type', 'children');
//        }
        $courses = $query->get();
        if ($courses->isEmpty()) {
            return ['data' => null, 'message' => 'There are not active courses for this category'];
        }
        return ['data' => $courses, 'message' => "Active courses retrieved successfully for category: $categoryId"];
    }catch (\Exception $e) {
         //add to log  file
            Log::error('Fetching active courses failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to fetch active courses'];

        }
    }

    public function attendance_register($request): array
    {
        $user_id = auth()->id();
        $request['is_attendance'] = true;

        $video = Video::with('course')->findOrFail($request['video_id']);
        $course = $video->course;

        if ($course->price > 0) {
            return [
                'data' => null,
                'message' => 'Attendance is only required for free courses.'
            ];
        }

        $attendance = UserAttendance::updateOrCreate(
            [
                'user_id' => $user_id,
                'video_id' => $request['video_id'],
            ],
            [
                'is_attendance' => true,
            ]
        );
        unset($attendance['updated_at']); unset($attendance['created_at']);
        return [
            'data' => $attendance,
            'message' => 'Attendance registered successfully.'
        ];
    }


}
