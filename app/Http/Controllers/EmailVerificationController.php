<?php

namespace App\Http\Controllers;

use App\ResponseTrait;
use App\Services\EmailVerificationService;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    use ResponseTrait;
    protected $service;
    public function __construct(EmailVerificationService $service)
    {
        $this->service = $service;
    }

    public function verify(Request $request, $id, $hash)
    {
        $result = $this->service->verify($request, $id, $hash);
        return view('verify_email', [
            'status' => $result['status'],
            'message' => $result['message']
        ]);
    }
}
