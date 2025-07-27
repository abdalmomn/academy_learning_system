<?php

namespace App\Http\Controllers;

use App\DTO\SignInDto;
use App\DTO\SignUpDto;
use App\Http\Requests\signInRequest;
use App\Http\Requests\signUpRequest;
use App\Models\User;
use App\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use App\Services\AuthenticationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AuthenticationController extends Controller
{
    use ResponseTrait;
    protected $authenticationService;
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function sign_up(SignUpRequest $request)
    {
        $data = $request->validated();

        $file_urls = [];
        if ($request->hasFile('file_path')) {
            foreach ($request->file('file_path') as $file) {
                $file_path = $file->store('certificates', 'public');
                $file_url = Storage::disk('public')->path($file_path);
                $file_urls[] = $file_url;
            }
        }
        $data['file_path'] = $file_urls;


        $signUpDto = SignUpDto::fromArray($data);
            $signUpDto = SignUpDto::fromArray($request->validated());
            $data = $this->authenticationService->sign_up($signUpDto);
            return $this->Success($data['data'],$data['message']);
    }

    public function sign_in(SignInRequest $request)
    {
            $signInDto = SignInDto::fromArray($request->validated());
            $data = $this->authenticationService->sign_in($signInDto);
            return $this->Success($data['data'],$data['message']);
    }

    public function logout(Request $request)
    {
            $data = $this->authenticationService->logout($request);
            return $this->Success($data['data'],$data['message']);
    }

}
