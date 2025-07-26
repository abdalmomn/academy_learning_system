<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Dto\CourseDto;
use App\Services\CourseService;
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
}
