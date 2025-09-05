<?php

namespace App\Http\Requests;

use App\DTO\WatchLaterDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreWatchLaterRequest extends FormRequest
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
            'video_id' => 'required|exists:videos,id',
        ];
    }
    public function toDTO()
    {
        return new WatchLaterDTO(
            user_id: auth()->id(),
            video_id: $this->video_id,
        );
    }
}
