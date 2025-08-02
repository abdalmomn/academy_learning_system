<?php

namespace App\Services;

use App\DTO\BannedUserDto;
use App\Models\BannedUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isEmpty;

class BannedUserService
{
    public function ban_user(BannedUserDto $bannedUserDto): array
    {
        //only supervisors can ban users
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->hasRole('supervisor')) {
            return [
                'data' => null,
                'message' => 'Only supervisors can ban a user.',
                'status' => 403
            ];
        }

        //check if user exists
        $targetUser = User::find($bannedUserDto->user_id);
        if (!$targetUser) {
            return [
                'data' => null,
                'message' => 'User not found.',
                'status' => 404
            ];
        }

        //no banning admins or supervisors
        if ($targetUser->hasRole('admin') || $targetUser->hasRole('supervisor')) {
            return [
                'data' => null,
                'message' => 'Cannot ban administrators or other supervisors.',
                'status' => 403
            ];
        }

        //check if the ban is duplicate
        if (BannedUser::where('user_id', $bannedUserDto->user_id)->exists()) {
            return [
                'data' => null,
                'message' => 'User is already banned.',
                'status' => 409
            ];
        }

        //create banned user record
        $ban = BannedUser::create((array)$bannedUserDto);

        return [
            'data' => $ban,
            'message' => $bannedUserDto->expires_at ? 'User banned temporarily.' : 'User banned permanently.',
            'status' => 201
        ];
    }

    public function show_all_banned_users(): array
    {
        if (!Auth::user()->hasRole('supervisor')) {
            return [
                'data' => null,
                'message' => 'Only supervisors can show banned users.',
                'status' => 403
            ];
        }
        //get all banned users records
        $banned_users = BannedUser::query()
            ->select('user_id', 'expires_at', 'reason')
            ->get();
        //check if the banned user table is empty
        if ($banned_users->isEmpty()){
            return [
                'data' => null,
                'message' => 'there is no banned users'
            ];
        }else{
            //return banned users with message
            return [
                'data' => $banned_users,
                'message' => 'return all banned users successfully'
            ];
        }
    }

    public function show_all_temporary_banned_users(): array
    {
        if (!Auth::user()->hasRole('supervisor')) {
            return [
                'data' => null,
                'message' => 'Only supervisors can show banned users.',
                'status' => 403
            ];
        }
        //get all banned users records
        $banned_users = BannedUser::query()
            ->where('expires_at','!=', null)
            ->select('user_id', 'expires_at', 'reason')
            ->get();
        //check if the banned user table is empty
        if ($banned_users->isEmpty()){
            return [
                'data' => null,
                'message' => 'there is no temporary banned users'
            ];
        }else{
            //return banned users with message
            return [
                'data' => $banned_users,
                'message' => 'return all temporary banned users successfully'
            ];
        }
    }
    public function show_all_permanent_banned_users(): array
    {
        if (!Auth::user()->hasRole('supervisor')) {
            return [
                'data' => null,
                'message' => 'Only supervisors can show banned users.',
                'status' => 403
            ];
        }
        //get all banned users records
        $banned_users = BannedUser::query()
            ->where('expires_at','=', null)
            ->select('user_id', 'expires_at', 'reason')
            ->get();
        //check if the banned user table is empty
        if ($banned_users->isEmpty()){
            return [
                'data' => null,
                'message' => 'there is no temporary banned users'
            ];
        }else{
            //return banned users with message
            return [
                'data' => $banned_users,
                'message' => 'return all temporary banned users successfully'
            ];
        }
    }
}
