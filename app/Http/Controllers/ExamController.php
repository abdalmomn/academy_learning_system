<?php

namespace App\Http\Controllers;

use App\DTO\ExamDto;
use App\Http\Requests\AnswersRequest;
use App\Http\Requests\CreateExamRequest;
use App\Http\Requests\SubmitProjectRequest;
use App\Http\Requests\UpdateExamRequest;
use App\ResponseTrait;
use App\Services\ExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{
    use ResponseTrait;
    protected $examService;
    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    public function create_exam(CreateExamRequest $request)
    {
        $validated_data = $request->validated();
        if ($validated_data['exam_mode'] === 'project' && $request->hasFile('project_file')) {
            $path = $request->file('project_file')->store('public/project_exams');
            $validated_data['project_file'] = $path;
        }
        $examDto = ExamDto::fromArray($validated_data);
        $data = $this->examService->create_exam($examDto);
        return $this->Success($data['data'],$data['message']);
    }
    public function update_exam(UpdateExamRequest $request,$exam_id)
    {
        $validated_data = ExamDto::fromArray($request->validated());
        $data = $this->examService->update_exam($validated_data, $exam_id);
        return $this->Success($data['data'],$data['message'], $data['code']);
    }

    public function show_exam($exam_id)
    {
        $data = $this->examService->show_single_exam($exam_id);
        return $this->Success($data['data'],$data['message'],$data['code'] ?? 200);
    }
    public function show_exams_by_course($course_id)
    {
        $data = $this->examService->show_exams_by_course($course_id);
        return $this->Success($data['data'],$data['message'],$data['code'] ?? 200);
    }
    public function show_all_exams()
    {
        $data = $this->examService->show_all_exams();
        return $this->Success($data['data'],$data['message']);
    }
    public function delete_exam($exam_id)
    {
        $data = $this->examService->delete_exam($exam_id);
        return $this->Success($data['data'],$data['message']);
    }

    public function store_exam_answer(AnswersRequest $request)
    {
        $data = $this->examService->store_exam_answer($request->validated());
        return $this->Success($data['data'],$data['message']);
    }
    public function get_exam_result($exam_id)
    {
        $data = $this->examService->get_exam_result($exam_id);
        return $this->Success($data['data'],$data['message']);
    }
    public function certificate()
    {
        $data = $this->examService->certificate();
        return $this->Success($data['data'],$data['message']);
    }
    public function submit_project_by_students(SubmitProjectRequest $request)
    {
        $validated_data = $request->validated();
        if ($request->hasFile('file_path')){
            $file_path = $request->file('file_path')->store('public/project_exams');
            $validated_data['file_path'] = Storage::url($file_path);
        }
        $data = $this->examService->submit_project_by_students($validated_data);
        return $this->Success($data['data'],$data['message']);
    }

}
