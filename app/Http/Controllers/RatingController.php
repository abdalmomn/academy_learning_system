<?php

namespace App\Http\Controllers;

use App\ResponseTrait;
use App\Services\RatingService;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    private RatingService $service;
    use ResponseTrait;

    public function __construct(RatingService $service)
    {
        $this->service = $service;
    }

    public function rateCourse(Request $request,  $course_id)
    {
        $data = $this->service->rate_course($course_id, $request->rate);
        return $this->Success($data['data'], $data['message']);
    }

    public function getCourseRatings( $course_id)
    {
        $data = $this->service->get_course_ratings($course_id);
        return $this->Success($data['data'], $data['message']);
    }

    public function rateTeacher(Request $request,  $teacher_id)
    {
        $data = $this->service->rate_teacher($teacher_id, $request->rate);
        return $this->Success($data['data'], $data['message']);
    }

    public function getTeacherRatings( $teacher_id)
    {
        $data = $this->service->get_teacher_ratings($teacher_id);
        return $this->Success($data['data'], $data['message']);
    }
}
