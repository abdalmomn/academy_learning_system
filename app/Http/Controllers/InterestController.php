<?php

namespace App\Http\Controllers;

use App\ResponseTrait;
use Illuminate\Http\Request;
use App\Http\Requests\StoreInterestRequest;
use App\Services\InterestService;
use App\Traits\ApiResponse;

class InterestController extends Controller
{
    use ResponseTrait;

    public function __construct(
        protected InterestService $interestService
    ) {}

    public function index()
    {
        $data = $this->interestService->getAll(auth()->id());
        return $this->Success($data['data'], $data['message']);
    }



    public function update($id, StoreInterestRequest $request)
    {
        $dto = $request->toDTO();
        $data = $this->interestService->update($id, $dto);
        return $this->Success($data['data'], $data['message']);
    }



    public function toggle(StoreInterestRequest $request)
    {
        $dto = $request->toDTO();
        $data = $this->interestService->toggle($dto);
        return $this->Success($data['data'], $data['message']);
    }
}
