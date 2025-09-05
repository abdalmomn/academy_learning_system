<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuestionRequest extends FormRequest
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
            'question_text' => ['required', 'string', 'max:65535'],
            'question_type' => ['required', 'in:mcq,project'],
            'mark'          => ['required', 'integer', 'min:1'],
            'exam_id'       => ['required', 'exists:exams,id'],
            'project_file'  => ['required_if:question_type,project', 'file', 'mimes:jpg,png,pdf,docx,zip,mp4'],

        ];
    }
}
