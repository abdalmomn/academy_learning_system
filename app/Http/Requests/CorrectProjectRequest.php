<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorrectProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ممكن تضيف شرط التحقق من دور الأستاذ مثلاً
        return true;
    }

    public function rules(): array
    {
        return [
            'grade' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string|max:2000',
        ];
    }
}
