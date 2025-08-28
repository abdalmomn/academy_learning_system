<?php

namespace App\Http\Controllers;

use App\DTO\PaymentDto;
use App\Http\Requests\CheckoutRequest;
use App\ResponseTrait;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ResponseTrait;
    public $checkoutService;
    public function __construct(CheckoutService $checkoutService)
    {$this->checkoutService = $checkoutService;}

    public function checkout_page(Request $request)
    {
        $data =$this->checkoutService->checkout_page($request);
        return $this->Success($data['data'],$data['message']);
    }
    public function checkout(CheckoutRequest $request)
    {
        $dto = PaymentDto::fromArray($request->validated());
        $data =$this->checkoutService->checkout($dto);
        return $this->Success($data['data'],$data['message']);
    }

    public function stripe_success(Request $request)
    {
        $data =$this->checkoutService->stripe_success($request);
        return $this->Success($data['data'],$data['message']);
    }
    public function stripe_cancel()
    {
        $data =$this->checkoutService->stripe_cancel();
        return $this->Success($data['data'],$data['message']);
    }
}
