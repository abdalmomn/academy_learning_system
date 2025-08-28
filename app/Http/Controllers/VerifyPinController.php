<?php

namespace App\Http\Controllers;

use App\Http\Requests\PinCodeRequest;
use App\ResponseTrait;
use App\Services\VerifyPinService;
use Illuminate\Http\Request;

class VerifyPinController extends Controller
{
    use ResponseTrait;
    public $pinCodeService;
    public function __construct(VerifyPinService $pinCodeService)
    {
        $this->pinCodeService = $pinCodeService;
    }

    public function verify_pin_code(PinCodeRequest $request)
    {
        $data = $this->pinCodeService->verify_pin($request->validated());
        return $this->Success($data['data'],$data['message']);
    }
}
