<?php

namespace App\Services;
use App\Models\WatchLater;
use App\DTO\WatchLaterDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
class WatchLaterService
{
    public function getAll($userId)
        {
            try {
                $watchLater = WatchLater::with('video')
                    ->where('user_id', $userId)
                    ->latest()
                    ->get();

                if ($watchLater->isEmpty()) {
                    return [
                        'data' => null,
                        'message' => 'No videos in watch later list'
                    ];
                }

                return [
                    'data' => $watchLater,
                    'message' => 'All watch later videos'
                ];
            } catch (Exception $e) {
                Log::error('Fetching watch later failed', ['error' => $e->getMessage()]);
                return [
                    'data' => null,
                    'message' => 'Failed to fetch watch later list'
                ];
            }
        }

            public function toggle(WatchLaterDTO $dto)
            {
                DB::beginTransaction();
                try {
                    $watchLater = WatchLater::where('user_id', $dto->user_id)
                        ->where('video_id', $dto->video_id)
                        ->first();

                    if ($watchLater) {
                        $watchLater->delete();
                        DB::commit();
                        Log::info('Video removed from watch later', [
                            'user_id' => $dto->user_id,
                            'video_id' => $dto->video_id
                        ]);
                        return [
                            'data' => null,
                            'message' => 'Video removed from watch later'
                        ];
                    }

                    $new = WatchLater::create([
                        'user_id' => $dto->user_id,
                        'video_id' => $dto->video_id,
                    ]);

                    DB::commit();
                    Log::info('Video added to watch later', [
                        'user_id' => $dto->user_id,
                        'video_id' => $dto->video_id
                    ]);
                    return [
                        'data' => $new,
                        'message' => 'Video added to watch later'
                    ];
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('Toggle watch later failed', [
                        'error' => $e->getMessage(),
                        'user_id' => $dto->user_id,
                        'video_id' => $dto->video_id
                    ]);
                    return [
                        'data' => null,
                        'message' => 'Failed to toggle watch later'
                    ];
                }
            }

            public function delete($id)
            {
                DB::beginTransaction();
                try {
                    $watchLater = WatchLater::find($id);
                    if (!$watchLater) {
                        return [
                            'data' => null,
                            'message' => 'Watch later entry not found'
                        ];
                    }

                    $watchLater->delete();
                    DB::commit();
                    Log::info('Watch later entry deleted', ['id' => $id]);
                    return [
                        'data' => null,
                        'message' => 'Watch later entry deleted successfully'
                    ];
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('Deleting watch later failed', [
                        'error' => $e->getMessage(),
                        'id' => $id
                    ]);
                    return [
                        'data' => null,
                        'message' => 'Failed to delete watch later entry'
                    ];
                }
            }
}
