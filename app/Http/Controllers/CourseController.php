<?php

namespace App\Http\Controllers;

use App\DTO\ApproveCourseDto;
use App\Http\Requests\ApproveCourseRequest;
use App\Http\Requests\StoreCourseRequest;
use App\Dto\CourseDto;
use App\Services\CourseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\ResponseTrait;

class CourseController extends Controller
{
    use ResponseTrait;

    private CourseService $service;

    public function __construct(CourseService $service)
    {
        $this->service = $service;
    }

    public function index()//get all coures with status published
    {
        $data = $this->service->getAllActive();
        return $this->Success($data['data'], $data['message']);
    }

    public function show($id)
    {
        $data = $this->service->getById($id);
        return $this->Success($data['data'], $data['message']);
    }

    public function myCourses()
    {
        $data = $this->service->getMyCourses();
        return $this->Success($data['data'], $data['message']);
    }

    public function endedCourses()
    {
        $data = $this->service->getEndedCourses();
        return $this->Success($data['data'], $data['message']);
    }

    public function store(StoreCourseRequest $request)
    {
        $dto = CourseDto::fromArray($request->validated());
        $data = $this->service->store($dto);
        return $this->Success($data['data'], $data['message']);
    }

    public function update(StoreCourseRequest $request, $id)
    {
        $dto = CourseDto::fromArray($request->validated());
        $data = $this->service->update($id, $dto);
        return $this->Success($data['data'], $data['message']);
    }

    public function destroy($id)
    {
        $data = $this->service->delete($id);
        return $this->Success($data['data'], $data['message']);
    }

    public function update_course_requirements(Request $request,$requirement_id)
    {
        $data = $this->service->update_course_requirements($request,$requirement_id);
        return $this->Success($data['data'], $data['message']);
    }

    public function delete_course_requirements($requirement_id)
    {
        $data = $this->service->delete_course_requirements($requirement_id);
        return $this->Success($data['data'], $data['message']);
    }

    public function pending_courses()
    {
        $data = $this->service->get_pending_courses();
        return $this->Success($data['data'], $data['message']);
    }

    public function approve_course(ApproveCourseRequest $request,$course_id)
    {
        $dto = ApproveCourseDto::fromArray($request->validated());
        $data = $this->service->approve_update_course_status($dto,$course_id);
        return $this->Success($data['data'], $data['message']);
    }
}
