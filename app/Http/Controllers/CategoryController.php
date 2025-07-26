<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\DTO\CategoryDto;
use App\ResponseTrait;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategoryRequest;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    use ResponseTrait;

    protected CategoryService $service;

    public function __construct(CategoryService $service)
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


    public function store(StoreCategoryRequest $request)
    {
        $dto = CategoryDto::fromArray($request->validated());
        $data = $this->service->store($dto);
        return $this->Success($data['data'], $data['message']);
    }

    public function update(StoreCategoryRequest $request,  $id)
    {
        $dto = CategoryDto::fromArray($request->validated());
        $data = $this->service->update($id, $dto);
        return $this->Success($data['data'], $data['message']);
    }

    public function destroy( $id)
    {
        $data = $this->service->delete($id);
        return $this->Success($data['data'], $data['message']);
    }
}
