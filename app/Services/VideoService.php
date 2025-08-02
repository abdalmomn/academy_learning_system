<?php

namespace App\Services;

use App\DTO\VideoDto;
use App\Helper\videoHelper;
use App\Models\Course;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Support\Facades\Storage;

class VideoService
{


    public function add_video(VideoDto $videoDto)
    {
        $user = Auth::user();
        if (!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be a teacher to update a course requirement'
            ];
        }
        try {
            $course = Course::find($videoDto->course_id);
            if (!$course) {
                return [
                    'data' => null,
                    'message' => 'course not found.'
                ];
            }

            $video = Video::query()->create((array)$videoDto);

            Log::info('video added successfully.', [
                'video_id' => $video->id,
                'course_id' => $video->course_id
            ]);

            return [
                'data' => $video,
                'message' => 'video added successfully.'
            ];
        } catch (\Exception $e) {
            Log::error('fail to add video.', [
                'error' => $e->getMessage(),
                'input' => (array) $videoDto
            ]);

            return [
                'data' => null,
                'message' => 'fail to add video.'
            ];
        }
    }

    public function update_video(VideoDto $videoDto, $course_id, $video_id): array
    {
        $user = Auth::user();
        if (!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be a teacher to update a course requirement'
            ];
        }
        try {
            $course = Course::query()->find($course_id);
            if (!$course) {
                return [
                    'data' => null,
                    'message' => 'course not found.',
                ];
            }

            $video = Video::query()->find($video_id);

            if (!$video) {
                return [
                    'data' => null,
                    'message' => 'video not found.',
                ];
            }

            Video::query()
                ->where('id',$video_id)
                ->update((array)$videoDto);
            $video = Video::query()->find($video_id);

            return [
                'data' => $video,
                'message' => 'video updated successfully.',
            ];
        } catch (\Exception $e) {
            Log::error('video update failed', ['error' => $e->getMessage()]);

            return [
                'data' => null,
                'message' => 'fail to update video.',
            ];
        }
    }

    public function delete_video($course_id,$video_id)
    {
        $user = Auth::user();
        if (!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be a teacher to update a course requirement'
            ];
        }
        try {
            $course = Course::query()->find($course_id);
            if (!$course) {
                return [
                    'data' => null,
                    'message' => 'course not found.',
                ];
            }

            $video = Video::query()->find($video_id);

            if (!$video) {
                return [
                    'data' => null,
                    'message' => 'video not found.',
                ];
            }

            Video::query()
                ->where('id',$video_id)
                ->delete();

            return [
                'data' => null,
                'message' => 'video deleted successfully.',
            ];
        } catch (\Exception $e) {
            Log::error('video delete failed', ['error' => $e->getMessage()]);

            return [
                'data' => null,
                'message' => 'fail to delete video.',
            ];
        }
    }

    public function show_video($video_id):array
    {
        try {
            $video = Video::query()->find($video_id);
            if (!$video) {
                return [
                    'data' => null,
                    'message' => 'video not found.',
                ];
            }

            return [
                'data' => $video,
                'message' => 'video retrieved successfully',
            ];
        } catch (\Exception $e) {
            Log::error('retrieve video failed', ['error' => $e->getMessage()]);

            return [
                'data' => null,
                'message' => 'fail to retrieve video.',
            ];
        }
    }
}
