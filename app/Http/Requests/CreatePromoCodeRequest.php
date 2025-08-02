<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePromoCodeRequest extends FormRequest
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
            'discount_percentage' => 'nullable|numeric|between:0,100',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_in' => 'nullable|date|after:today',
            'teacher_id' => 'required|exists:users,id',
        ];
    }
}
