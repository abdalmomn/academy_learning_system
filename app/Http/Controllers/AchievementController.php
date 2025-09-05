<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DTO\AchievementDTO;
use App\Http\Requests\AchievementRequest;
use App\Services\AchievementService;
use App\ResponseTrait;
use App\Http\Controllers\Controller;

class AchievementController extends Controller
{
    use ResponseTrait;

    protected AchievementService $service;

    public function __construct(AchievementService $service)
    {
        $this->service = $service;
    }

    public function getByUser($userId)
    {
        $data = $this->service->getByUser($userId);
        return $this->Success($data['data'], $data['message']);
    }


    public function getAllMyachivement()
    {
        $data = $this->service->getAllMyachivement();
        return $this->Success($data['data'], $data['message']);
    }
    public function store(AchievementRequest $request)
    {
        $dto = AchievementDTO::fromArray($request->validated());
        $data = $this->service->store($dto);
        return $this->Success($data['data'], $data['message']);
    }

    public function update(AchievementRequest $request, $id)
    {
        $dto = AchievementDTO::fromArray($request->validated());
        $data = $this->service->update($id, $dto);
        return $this->Success($data['data'], $data['message']);
    }

    public function destroy($id)
    {
        $data = $this->service->delete($id);
        return $this->Success($data['data'], $data['message']);
    }}
