<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ProjectSubmission;
use App\Models\Question;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuestionService
{
    public function create_question($request, $file = null): array
    {
        try {
            $user = Auth::user();
            if (!$user->hasRole('teacher')) {
                return [
                    'data' => null,
                    'message' => 'must be teacher to create questions'
                ];
            }
            $exam = Exam::find($request['exam_id']);
            if (!$exam) {
                return [
                    'data' => null,
                    'message' => 'exam not found'
                ];
            }
            if ($exam->exam_mode !== $request['question_type']) {
                return [
                    'data' => null,
                    'message' => "Question type must match the exam mode ({$exam->exam_mode})"
                ];
            }
            $question = Question::query()->create($request);


            if ($question->question_type === 'project' && $file) {

                ProjectSubmission::create([
                    'file_path'     => $request['project_file'],
                    'user_id'       => $user->id,
                    'question_id'   => $question->id,
                ]);
            }

            unset($question['created_at']);
            unset($question['updated_at']);

            Log::info('question created', [
                'question id' => $question->id,
                'teacher id'  => Auth::id()
            ]);

            return [
                'data' => $question,
                'message' => 'question created successfully'
            ];

        } catch (\Exception $e) {
            Log::warning($e->getMessage());
            return [
                'data' => null,
                'message' => $e->getMessage()
            ];
        }
    }


    public function update_question($question_dto, $question_id): array
    {
        $user = Auth::user();
        if(!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be teacher to create questions'
            ];
        }
        try {
            $question = Question::query()->find($question_id);
            if (!$question){
                return [
                    'data' => null,
                    'message' => 'question not found'
                ];
            }
            $question->update($question_dto->toArray());
            unset($question['created_at']); unset($question['updated_at']);
            Log::info('question updated', [
                'question id' => $question->id,
                'teacher id' => Auth::id()
            ]);
            return [
                'data' => $question,
                'message' => 'question updated successfully',
            ];
        }catch(Exception $e){
            Log::warning($e->getMessage());
            return [
                'data' => null,
                'message' => $e->getMessage()
            ];
        }
    }

    public function show_single_question($question_id): array
    {
        $question = Question::query()
            ->with('options:id,option_text,is_correct,question_id')
            ->find($question_id);
        if (!$question){
            return [
                'data' => null,
                'message' => 'question not found'
            ];
        }
        unset($question['created_at']); unset($question['updated_at']);
        return [
            'data' => $question,
            'message' => 'question retrieved successfully'
        ];
    }

    public function delete_question($question_id): array
    {
        $user = Auth::user();
        if(!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be teacher to delete questions'
            ];
        }
        $question = Question::query()->find($question_id);
        if (!$question){
            return [
                'data' => null,
                'message' => 'question not found'
            ];
        }
        Question::query()
            ->where('id', $question_id)
            ->with('options')
            ->delete();
        return [
            'data' => null,
            'message' => 'question deleted successfully'
        ];
    }
}
