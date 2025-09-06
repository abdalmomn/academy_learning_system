<?php

namespace App\Services;

use App\DTO\SocialiteDto;
use App\Helper\RolesAndPermissionsHelper;
use App\Models\ProfileDetail;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteService
{
    protected $helper;
    public function __construct()
    {
        $this-> helper = new RolesAndPermissionsHelper();
    }
    public function handleGoogleCallback()
    {
        //get the user information
        $googleUser = Socialite::driver('google')->stateless()->user();

        DB::beginTransaction();
        try {
            //get the user info from the database
            $user = User::query()->where('email', $googleUser->email)->first();

            //get the user type from the session or set the default one
            $userType = session('user_type', 'guest') ?? $user->roles->pluck('name');
            //use dto for data transaction
            $dto = new SocialiteDto($googleUser, $userType);

            //if the user is exists in the database then update the social_id
            if ($user){
                $user->update([
                    'social_id' => $dto->social_id,
                    'social_type' => $dto->social_type,
                    'email_verified_at' => Carbon::now(),
                ]);
                if (!$user->hasRole($userType)) {
                    $user->assignRole($userType);
                }
                $user = $this->helper->appendRolesAndPermissions($user);
            }else {
                //or create a new user with random password
                $user = User::query()->create(array_merge(
                    $dto->toArray(),
                    ['password' => Hash::make(Str::random(8))]
                ));
                ProfileDetail::query()->create(['user_id' => $user->id]);

                if (!$user->hasRole($userType)) {
                    $user->assignRole($userType);
                }
                $user = $this->helper->give_and_load_permissions_and_roles($dto->user_type,$user);
            }


            //make the user login after create it
            //Auth::login($user);
            //create user token
            $user['token'] = $user->createToken("token")->plainTextToken;

            DB::commit();
            //save the success log
            Log::info('logged in successfully', [
                'data' => [
                    'username' => $dto->username,
                    'email' => $dto->email,
                ]
            ]);
            return [
                'data' => $user,
                'message' => 'user logged in successfully'
            ];
        }catch (Exception $e){
            DB::rollBack();
            //save the fail log
            Log::error('there is problem in google login', [
                'error' => $e->getMessage(),
                'data' => [
                    'username' => $googleUser->name ?? 'unknown',
                    'email' => $googleUser->email ?? 'unknown',
                ]
            ]);
            return [
                'data' => null,
                'message' => 'There was a problem google login. Please try again later.'
            ];
        }
    }
}
