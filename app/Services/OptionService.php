<?php

namespace App\Services;

use App\Models\McqOption;
use App\Models\Question;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OptionService
{
    public function create_option($option_dto): array
    {
        try {
            $user = Auth::user();
        if(!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be teacher to create options'
            ];
        }
            $question = Question::find($option_dto->question_id);
            if (!$question) {
                return [
                    'data' => null,
                    'message' => 'question not found'
                ];
            }

            if ($question->question_type === 'project') {
                return [
                    'data' => null,
                    'message' => 'Cannot add options to a project-type question'
                ];
            }
        $option = McqOption::query()->create((array)$option_dto);
        unset($option['created_at']); unset($option['updated_at']);
        Log::info('option created', [
            'option id' => $option->id,
            'teacher id' => Auth::id()
        ]);
        return [
            'data' => $option,
            'message' => 'option created successfully'
        ];
        }catch(Exception $e){
            Log::warning($e->getMessage());
            return [
                'data' => null,
                'message' => $e->getMessage()
            ];
        }
    }

    public function update_option($option_dto, $option_id)
    {
        $user = Auth::user();
        if(!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be teacher to create options'
            ];
        }
        try {

            $option = McqOption::query()->find($option_id);
            if (!$option){
                return [
                    'data' => null,
                    'message' => 'option not found'
                ];
            }
            $option->update((array)$option_dto);
            unset($option['created_at']); unset($option['updated_at']);
            Log::info('option updated', [
                'option id' => $option->id,
                'teacher id' => Auth::id()
            ]);
            return [
                'data' => $option,
                'message' => 'option updated successfully',
            ];
        }catch(Exception $e){
            Log::warning($e->getMessage());
            return [
                'data' => null,
                'message' => $e->getMessage()
            ];
        }
    }

    public function delete_option($option_id)
    {
        $user = Auth::user();
        if(!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be teacher to delete options'
            ];
        }
        $option = McqOption::query()->find($option_id);
        if (!$option){
            return [
                'data' => null,
                'message' => 'option not found'
            ];
        }
        McqOption::query()
            ->where('id', $option_id)
            ->delete();
        return [
            'data' => null,
            'message' => 'option deleted successfully'
        ];
    }

    public function show_option_by_question($question_id): array
    {
        try {
            $options = McqOption::query()
                ->where('question_id', $question_id)
                ->get(['id', 'option_text', 'is_correct', 'question_id']);

            if ($options->isEmpty()) {
                return [
                    'data' => null,
                    'message' => 'No options found for this question',
                ];
            }

            $question = Question::query()->find($question_id);
            foreach ($options as $option) {
                $option['question_text'] = $question->question_text;
                unset($option['question_id']);
            }

            return [
                'data' => $options,
                'message' => 'options retrieved successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Fetching options failed', ['error' => $e->getMessage()]);
            return [
                'data' => null,
                'message' => 'Failed to retrieve options',
            ];
        }
    }
}
