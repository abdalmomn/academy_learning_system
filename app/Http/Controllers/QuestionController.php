<?php

namespace App\Http\Controllers;

use App\DTO\QuestionDto;
use App\Http\Requests\CreateQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\ResponseTrait;
use App\Services\QuestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    use ResponseTrait;
    protected $questionService;
    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }

    public function create_question(CreateQuestionRequest $request)
    {
        $validatedData = $request->validated();
        $file_path = null;

        if ($request->hasFile('project_file')) {
            $file_path = $request->file('project_file')->store('projects', 'public');
            $validatedData['project_file'] = Storage::url($file_path);
        }

        $data = $this->questionService->create_question($validatedData, $file_path);

        if ($data['data'] && $data['data']->question_type === 'project' && isset($validatedData['project_file'])) {
            $data['data']->project_file = $validatedData['project_file'];
        }

        return $this->Success($data['data'], $data['message']);
    }



    public function update_question(UpdateQuestionRequest $request,$question_id)
    {
        $validated_data = QuestionDto::fromArray($request->validated());
        $data = $this->questionService->update_question($validated_data,$question_id);
        return  $this->Success($data['data'], $data['message']);
    }
    public function show_question($question_id)
    {
        $data = $this->questionService->show_single_question($question_id);
        return  $this->Success($data['data'], $data['message']);
    }
    public function delete_question($question_id)
    {
        $data = $this->questionService->delete_question($question_id);
        return  $this->Success($data['data'], $data['message']);
    }
}
