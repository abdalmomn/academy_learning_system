<?php

namespace App\Http\Controllers;

use App\DTO\VideoDto;
use App\Helper\videoHelper;
use App\Http\Requests\AddVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\ResponseTrait;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    use ResponseTrait;
    public $videoHelper;
    public $videoService;
    public function __construct(VideoService $videoService, videoHelper $videoHelper)
    {$this->videoService = $videoService; $this->videoHelper = $videoHelper;}

    public function add_video(AddVideoRequest $request)
    {
        $validatedData = $request->validated();
        if ($request->hasFile('url')) {
            $videoPath = $request->file('url')->store('videos', 'public');
            $validatedData['url'] = asset(Storage::url($videoPath));

            $duration = $this->videoHelper->getVideoDuration(Storage::disk('public')->path($videoPath));
            $validatedData['duration'] = $duration;

        }

        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('posters', 'public');
            $validatedData['poster'] = asset(Storage::url($posterPath));
        }

        $dto = VideoDto::fromArray($validatedData);
        $data = $this->videoService->add_video($dto);
        return $this->Success($data['data'],$data['message']);
    }

    public function update_video(UpdateVideoRequest $request, $course_id, $video_id)
    {
        $validatedData = $request->validated();
        if ($request->hasFile('url')) {
            $videoPath = $request->file('url')->store('videos', 'public');
            $validatedData['url'] = asset(Storage::url($videoPath));
            $duration = $this->videoHelper->getVideoDuration(Storage::disk('public')->path($videoPath));
            $validatedData['duration'] = $duration;
        }

        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('posters', 'public');
            $validatedData['poster'] = asset(Storage::url($posterPath));
        }
        $validatedData['course_id'] = $course_id;
        $dto = VideoDto::fromArray($validatedData);
        $data = $this->videoService->update_video($dto, $course_id,$video_id);
        return $this->Success($data['data'],$data['message']);
    }

    public function delete_video($course_id, $video_id){
        $data = $this->videoService->delete_video($course_id,$video_id);
        return $this->Success($data['data'],$data['message']);
    }

    public function show_video($video_id){
        $data = $this->videoService->show_video($video_id);
        return $this->Success($data['data'],$data['message']);
    }
}
