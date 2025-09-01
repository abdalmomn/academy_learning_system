<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DTO\FaqCategoryDTO;
use App\Http\Requests\StoreFaqCategoryRequest;
use App\Services\FaqCategoryService;
use App\ResponseTrait;
class FaqCategoryController extends Controller
{
    use ResponseTrait;

    protected FaqCategoryService $service;

    public function __construct(FaqCategoryService $service)
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

    public function store(StoreFaqCategoryRequest $request)
    {
        $dto = FaqCategoryDTO::fromArray($request->validated());
        $data = $this->service->store($dto);
        return $this->Success($data['data'], $data['message']);
    }

    public function update(StoreFaqCategoryRequest $request, $id)
    {
        $dto = FaqCategoryDTO::fromArray($request->validated());
        $data = $this->service->update($id, $dto);
        return $this->Success($data['data'], $data['message']);
    }

    public function destroy($id)
    {
        $data = $this->service->delete($id);
        return $this->Success($data['data'], $data['message']);
    }
}
