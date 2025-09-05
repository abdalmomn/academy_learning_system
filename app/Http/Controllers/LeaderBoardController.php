<?php

namespace App\Http\Controllers;

use App\DTO\LeaderBoardDTO;
use App\Http\Requests\LeaderBoardRequest;
use App\ResponseTrait;
use App\Services\LeaderBoardService;
use Illuminate\Http\Request;

class LeaderBoardController extends Controller
{
    use ResponseTrait;

    protected LeaderBoardService $service;

    public function __construct(LeaderBoardService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->getAll();
        return $this->Success($data['data'], $data['message']);
    }

    public function store(LeaderBoardRequest $request)
    {
        $dto = LeaderBoardDTO::fromArray($request->validated());
        $data = $this->service->store($dto);
        return $this->Success($data['data'], $data['message']);
    }

    public function destroy($id)
    {
        $data = $this->service->delete($id);
        return $this->Success($data['data'], $data['message']);
    }
}
