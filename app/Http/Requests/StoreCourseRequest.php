<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
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
                  'course_name' => 'required|string|max:255',
                  'description' => 'required|string',
                  'rating' => 'required|numeric|min:0|max:5',
                  'status' => 'required|in:draft,published,archived,pending_approval',
                  'is_paid' => 'required|boolean',
                  'start_date' => 'required|date',
                  'end_date' => 'required|date|after_or_equal:start_date',
                  'category_id' => 'required|exists:categories,id',
              ];
    }
}
