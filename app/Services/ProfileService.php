<?php

namespace App\Services;

use App\DTO\ProfileDto;
use App\Helper\profileHelper;
use App\Models\AcademicCertificate;
use App\Models\Achievement;
use App\Models\BannedUser;
use App\Models\Comment;
use App\Models\Course;
use App\Models\ExamResult;
use App\Models\LeaderBoard;
use App\Models\McqAnswer;
use App\Models\Notification;
use App\Models\ProfileDetail;
use App\Models\ProjectSubmission;
use App\Models\PromoCode;
use App\Models\Purchase;
use App\Models\Strike;
use App\Models\TeacherRating;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileService
{
    protected $helper;
    public function __construct()
    {
        $this-> helper = new profileHelper();
    }

    public function show_my_profile(): array
    {
        try {
        $profile = $this->helper->fetch_profile(Auth::id());
        if(!$profile){
            return [
                'data' => null,
                'message' => 'there is an error, user not found'
            ];
        }

        return [
            'data' => $profile,
            'message' => 'get data successfully'
        ];
        }catch (Exception $e){
            return [
                'data' => null,
                'message' => $e->getMessage()
            ];
        }
    }

    public function show_user_profile($user_id): array
    {
        try {
            $profile = $this->helper->fetch_profile($user_id);
            if(!$profile){
                return [
                    'data' => null,
                    'message' => 'there is an error, user not found'
                ];
            }
            return [
                'data' => $profile,
                'message' => 'get data successfully'
            ];
        }catch (Exception $e){
            return [
                'data' => null,
                'message' => $e->getMessage()
            ];
        }
    }

    public function edit_profile(ProfileDto $profileDto): array
    {
        $profile = ProfileDetail::query()
            ->where('user_id',Auth::id())
            ->first();

        if (!$profile){
            return [
                'data' => null,
                'message' => 'there is an error, user not found'
            ];
        }
        $profile->update((array)$profileDto);
        $profile->save();

        $user = User::query()
            ->where('id',Auth::id())
            ->with('profile_details')
            ->first();

        return [
            'data' => $user,
            'message' => 'data'
        ];
    }

    public function delete_account($password): array
    {
        $user_id = Auth::id();

        $user = User::query()
            ->where('id', $user_id)
            ->first();
        DB::beginTransaction();
        try {

            if (Hash::check($password, $user->password)) {

                PromoCode::query()
                    ->where('teacher_id', $user_id)->delete();

                TeacherRating::query()
                    ->where('user_id', $user_id)
                    ->orWhere('teacher_id', $user_id)
                    ->delete();

                BannedUser::query()
                    ->where('user_id', $user_id)->delete();

                Comment::query()
                    ->where('user_id', $user_id)->delete();

                Course::query()
                    ->where('user_id', $user_id)->delete();

                Purchase::query()
                    ->where('user_id', $user_id)->delete();

                Notification::query()
                    ->where('user_id', $user_id)->delete();

                LeaderBoard::query()
                    ->where('leader_id', $user_id)->delete();

                Strike::query()
                    ->where('user_id', $user_id)->delete();

                Wallet::query()
                    ->where('user_id', $user_id)->delete();

                McqAnswer::query()
                    ->where('user_id', $user_id)->delete();

                ExamResult::query()
                    ->where('user_id', $user_id)->delete();

                ProjectSubmission::query()
                    ->where('user_id', $user_id)->delete();

                $user->attendedVideos()->detach();
                $user->courses()->detach();
                $user->watch_later()->detach();
                $user->achievements()->detach();


                AcademicCertificate::query()
                    ->where('teacher_id', $user_id)->delete();

                ProfileDetail::query()
                    ->where('user_id', $user_id)->delete();

                $user->delete();

                Log::info("User deleted successfully", [
                    'user_id' => $user_id,
                    'username' => $user->username,
                    'email' => $user->email,
                ]);

                DB::commit();
                return [
                    'data' => null,
                    'message' => 'account deleted successfully'
                ];
            } else {
                Log::warning("User deletion failed due to invalid password", [
                    'user_id' => $user_id,
                    'username' => $user->username,
                    'email' => $user->email,
                ]);

                return [
                    'data' => null,
                    'message' => 'Invalid password'
                ];
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("User deletion error", [
                'user_id' => $user_id,
                'error' => $e->getMessage()
            ]);
            return [
                'data' => null,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function get_user_name()
    {
        $user = User::query()
            ->where('id',Auth::id())
            ->select('username')
            ->first();
        if (!$user){
            return [
                'data' => null,
                'message' => 'user not found'
            ];
        }
        return [
            'data' => $user,
            'message' => 'username retrieved successfully'
        ];
    }
}
