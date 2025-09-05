<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class AnswersRequest extends FormRequest
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
            'is_correct' => ['boolean'],
            'user_id' => ['exists:users,id'],
            'question_id' => ['required', 'exists:questions,id'],
            'selected_option_id' => ['required', 'exists:mcq_options,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $questionId = $this->input('question_id');
            $optionId   = $this->input('selected_option_id');

            if ($questionId && $optionId) {
                $exists = DB::table('mcq_options')
                    ->where('id', $optionId)
                    ->where('question_id', $questionId)
                    ->exists();

                if (!$exists) {
                    $validator->errors()->add('selected_option_id', 'The selected option does not belong to the given question.');
                }
            }
        });
    }

}
