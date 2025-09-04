<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['instant', 'periodic', 'final'])],
            'exam_mode' => ['required', Rule::in(['mcq', 'project', 'mixed'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_mandatory' => ['required', 'boolean'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'video_id' => ['nullable', 'integer', 'exists:videos,id'],
        ];
    }
}
