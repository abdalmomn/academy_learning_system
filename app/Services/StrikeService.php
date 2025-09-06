<?php

namespace App\Services;

use App\DTO\StrikeDto;
use App\Models\Strike;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StrikeService
{
    public function show_strike(): array
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return [
                    'data' => null,
                    'message' => 'Unauthorized'
                ];
            }

            $strike = Strike::query()
                ->where('user_id', $user->id)
                ->first();

            if (!$strike) {
                return [
                    'data' => null,
                    'message' => 'No strike found for this user'
                ];
            }

            Log::info('Strike retrieved successfully', [
                'user_id' => $user->id,
                'strike_id' => $strike->id
            ]);

            $dto = StrikeDto::fromArray($strike->toArray());

            return [
                'data' => $dto,
                'message' => 'Strike retrieved successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Fetching strike failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'data' => null,
                'message' => $e->getMessage(),
//                'message' => 'Failed to get strike'
            ];
        }
    }
}
