<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddVideoRequest extends FormRequest
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
            'description' => 'nullable|string|max:1000',
            'url'         => 'required|file',
            'title'       => 'required|string|max:255',
            'poster'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'course_id' => 'required|exists:courses,id',
        ];
    }
}
