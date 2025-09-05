<?php

namespace App\Services;
use App\Models\Achievement;
use App\DTO\AchievementDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class AchievementService
{
    public function getByUser( $userId)
    {
        try {
            $achievements = Achievement::with('user')
                ->where('user_id', $userId)
                ->get();

            if ($achievements->isEmpty()) {
                return [
                    'data' => null,
                    'message' => 'No achievements found for this user'
                ];
            }

            return [
                'data' => $achievements,
                'message' => 'Achievements for user id ' . $userId
            ];
        } catch (\Exception $e) {
            Log::error('Fetching achievements failed', ['error' => $e->getMessage(), 'user_id' => $userId]);
            return [
                'data' => null,
                'message' => $e->getMessage()
//                'message' => 'Failed to fetch achievements'
            ];
        }
    }
    public function getAllMyachivement()
    {
        $user = auth()->user(); // المستخدم الحالي
        try {
            $achievements = Achievement::with('user')
                ->where('user_id', $user->id)
                ->get();

            if ($achievements->isEmpty()) {
                return [
                    'data' => null,
                    'message' => 'No achievements found for this user'
                ];
            }

            return [
                'data' => $achievements,
                'message' => 'Achievements for current user'
            ];
        } catch (\Exception $e) {
            Log::error('Fetching achievements failed', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return [
                'data' => null,
                'message' => 'Failed to fetch achievements'
            ];
        }
    }

    public function store(AchievementDTO $dto)
    {
        $user = auth()->user();
        DB::beginTransaction();
        try {
            $achievement = Achievement::create([
                'title'       => $dto->title,
                'description' => $dto->description,
                'icon_path'   => $dto->icon_path,
                'user_id'     => $user->id
            ]);

            DB::commit();
            Log::info('Achievement created', ['user_id' => $user->id, 'title' => $dto->title]);

            return [
                'data' => $achievement,
                'message' => 'Achievement added successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Achievement creation failed', ['error' => $e->getMessage(), 'data' => $dto]);
            return [
                'data' => null,
                'message' => 'Failed to add achievement'
            ];
        }
    }

    public function update($id, AchievementDTO $dto)
    {
        $user = auth()->user();
        DB::beginTransaction();
        try {
            $achievement = Achievement::find($id);
            if (!$achievement) {
                return [
                    'data' => null,
                    'message' => 'Achievement not found'
                ];
            }

            if ($achievement->user_id !== $user->id) {
                return [
                    'data' => null,
                    'message' => 'Unauthorized - only the owner can update this achievement'
                ];
            }

            $achievement->update([
                'title'       => $dto->title,
                'description' => $dto->description,
                'icon_path'   => $dto->icon_path,
                // لا نسمح بتغيير user_id
            ]);

            DB::commit();
            Log::info('Achievement updated', ['id' => $id]);

            return [
                'data' => $achievement,
                'message' => 'Achievement updated successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Achievement update failed', ['error' => $e->getMessage(), 'id' => $id]);
            return [
                'data' => null,
                'message' => 'Failed to update achievement'
            ];
        }
    }

    public function delete($id)
    {
        $user = auth()->user();
        DB::beginTransaction();
        try {
            $achievement = Achievement::find($id);
            if (!$achievement) {
                return [
                    'data' => null,
                    'message' => 'Achievement not found'
                ];
            }

            if ($achievement->user_id !== $user->id) {
                return [
                    'data' => null,
                    'message' => 'Unauthorized - only the owner can delete this achievement'
                ];
            }

            $achievement->delete();
            DB::commit();
            Log::info('Achievement deleted', ['id' => $id]);

            return [
                'data' => null,
                'message' => 'Achievement deleted successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Achievement deletion failed', ['error' => $e->getMessage(), 'id' => $id]);
            return [
                'data' => null,
                'message' => 'Failed to delete achievement'
            ];
        }
    }
}
