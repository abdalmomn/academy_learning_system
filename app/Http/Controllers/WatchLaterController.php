<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWatchLaterRequest;
use App\Services\WatchLaterService;
use App\ResponseTrait;


class WatchLaterController extends Controller
{
    use ResponseTrait;

    public function __construct(WatchLaterService $service) {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->getAll(auth()->id());
        return $this->Success($data['data'], $data['message']);
    }

    public function toggle(StoreWatchLaterRequest $request)
    {
        $dto = $request->toDTO();
        $data = $this->service->toggle($dto);
        return $this->Success($data['data'], $data['message']);
    }

    public function destroy($id)
    {
        $data = $this->service->delete($id);
        return $this->Success($data['data'], $data['message']);
    }
}
