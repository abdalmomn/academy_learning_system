<?php

namespace App\Http\Controllers;

use App\DTO\ProfileDto;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\ResetPassword\ResetPasswordRequest;
use App\ResponseTrait;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use ResponseTrait;
    public $profileService;
    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show_my_profile_details()
    {
        $data = $this->profileService->show_my_profile();
        return $this->Success($data['data'],$data['message']);
    }
    public function show_user_profile_details($user_id)
    {
        $data = $this->profileService->show_user_profile($user_id);
        return $this->Success($data['data'],$data['message']);
    }
    public function edit_profile(ProfileRequest $request)
    {
        $validated_data = $request->validated();
        if ($request->hasFile('profile_photo')){
            $image_path = $request->file('profile_photo')->store('profile_images','public');
            $image_url = Storage::disk('public')->path($image_path);
            $validated_data['profile_photo'] = $image_url;
        }

        $profileDto = ProfileDto::fromArray($validated_data);
        $data = $this->profileService->edit_profile($profileDto);
        return $this->Success($data['data'],$data['message']);
    }

    public function delete_account(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);
        $data = $this->profileService->delete_account($request->input('password'));
        return $this->Success($data['data'],$data['message']);
    }
}
