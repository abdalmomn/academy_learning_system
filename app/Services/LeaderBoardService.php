<?php

namespace App\Services;
use App\DTO\LeaderBoardDTO;
use App\Models\LeaderBoard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class LeaderBoardService
{
    public function getAll()  // show leaderboard
    {
        try {
            $leaders = LeaderBoard::with('leader')
                ->orderByDesc('points')
                ->get();

            if ($leaders->isEmpty()) {
                return [
                    'data' => null,
                    'message' => 'No leaders found'
                ];
            }

            return [
                'data' => $leaders,
                'message' => 'Leaderboard data'
            ];
        } catch (\Exception $e) {
            Log::error('Fetching leaderboard failed', ['error' => $e->getMessage()]);
            return [
                'data' => null,
                'message' => $e->getMessage()
//                'message' => 'Failed to fetch leaderboard'
            ];
        }
    }

    public function store(LeaderBoardDTO $dto)  // add to leaderboard
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole( ['admin', 'supervisor'])) {
            return [
                'data' => null,
                'message' => 'Unauthorized - admin or supervisor only'
            ];
        }

        DB::beginTransaction();
        try {
            $exists = LeaderBoard::where('leader_id', $dto->leader_id)
                ->where('leader_type', $dto->leader_type)
                ->exists();

            if ($exists) {
                return [
                    'data' => null,
                    'message' => 'This leader already exists in leaderboard'
                ];
            }
            $leaderBoard = LeaderBoard::query()->create([
                'leader_id'   => $dto->leader_id,
                'leader_type' => $dto->leader_type,
                'points'      => $dto->points,
            ]);

            DB::commit();
            Log::info('Leader added to leaderboard', ['leader_id' => $dto->leader_id]);

            return [
                'data' => $leaderBoard,
                'message' => 'Leader added successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Leader creation failed', [
                'error' => $e->getMessage(),
                'leader_id' => $dto->leader_id
            ]);
            return [
                'data' => null,
                'message' => 'Failed to add leader'
            ];
        }
    }

    public function delete( $id)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole( ['admin', 'supervisor'])) {
            return [
                'data' => null,
                'message' => 'Unauthorized - admin or supervisor only'
            ];
        }

        DB::beginTransaction();
        try {
            $leaderBoard = LeaderBoard::find($id);
            if (!$leaderBoard) {
                return [
                    'data' => null,
                    'message' => 'Leader not found'
                ];
            }

            $leaderBoard->delete();
            DB::commit();

            Log::info('Leader deleted from leaderboard', ['id' => $id]);
            return [
                'data' => null,
                'message' => 'Leader deleted successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Leader deletion failed', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            return [
                'data' => null,
                'message' => 'Failed to delete leader'
            ];
        }
    }
}
