<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowProjectResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_id' => 'nullable|exists:questions,id'
        ];
    }
}
