<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExamRequest extends FormRequest
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
            'type' => [Rule::in(['instant', 'periodic', 'final'])],
            'exam_mode' => [Rule::in(['mcq', 'project', 'mixed'])],
            'title' => ['string', 'max:255'],
            'description' => ['string'],
            'start_date' => ['date'],
            'end_date' => ['date', 'after_or_equal:start_date'],
            'is_mandatory' => ['boolean'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'video_id' => ['nullable', 'integer', 'exists:videos,id'],

        ];
    }
}
