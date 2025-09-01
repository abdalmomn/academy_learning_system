<?php

namespace App\Http\Controllers;

use App\DTO\CommentDto;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\ResponseTrait;
use App\Services\CommentService;
use App\Models\Comment;

class CommentController extends Controller
{
    use ResponseTrait;

    protected CommentService $service;

    public function __construct(CommentService $service)
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

    public function store(CreateCommentRequest $request)
    {
        $dto = CommentDTO::fromArray($request);
        $data = $this->service->create($dto);
        return $this->Success($data['data'], $data['message']);
    }

    public function update(UpdateCommentRequest $request, $id)
    {
        $dto = new CommentDTO(
            $request->comment,
            $request->user()->id,
            $request->video_id ?? null
        );
        $data = $this->service->update($id, $dto);
        return $this->Success($data['data'], $data['message']);
    }

    public function destroy($id)
    {
        $data = $this->service->delete($id);
        return $this->Success($data['data'], $data['message']);
    }
    public function lockComments($id)
    {
        $data = $this->service->lockComments($id);
        return $this->Success($data['data'], $data['message']);
    }

    public function unlockComments($id)
    {
        $data = $this->service->unlockComments($id);
        return $this->Success($data['data'], $data['message']);
    }

}
