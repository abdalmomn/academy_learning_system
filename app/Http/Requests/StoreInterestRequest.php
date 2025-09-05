<?php

namespace App\Http\Requests;

use App\DTO\InterestDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreInterestRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',

        ];
    }
    public function toDTO()
    {
        return new InterestDTO(
            user_id: auth()->id(),
            category_id: $this->category_id,
        );
    }
}
