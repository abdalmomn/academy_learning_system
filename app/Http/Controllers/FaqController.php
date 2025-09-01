<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DTO\FaqDTO;
use App\Http\Requests\StoreFaqRequest;
use App\Services\FaqService;
use App\ResponseTrait;
class FaqController extends Controller
{
    use ResponseTrait;

    protected FaqService $service;

    public function __construct(FaqService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->getAll();
        return $this->Success($data['data'], $data['message']);
    }

    public function show($id)
    {
        $data = $this->service->getById($id);
        return $this->Success($data['data'], $data['message']);
    }

    public function store(StoreFaqRequest $request)
    {
        $dto = FaqDTO::fromArray($request->validated());
        $data = $this->service->store($dto);
        return $this->Success($data['data'], $data['message']);
    }

    public function update(StoreFaqRequest $request, $id)
    {
        $dto = FaqDTO::fromArray($request->validated());
        $data = $this->service->update($id, $dto);
        return $this->Success($data['data'], $data['message']);
    }

    public function destroy($id)
    {
        $data = $this->service->delete($id);
        return $this->Success($data['data'], $data['message']);
    }
}
