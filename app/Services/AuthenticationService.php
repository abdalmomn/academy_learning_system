<?php

namespace App\Services;

use App\DTO\SignInDto;
use App\DTO\SignUpDto;
use App\Events\UserRegistered;
use App\Helper\RolesAndPermissionsHelper;
use App\Models\AcademicCertificate;
use App\Models\BannedUser;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\isFalse;
use function PHPUnit\Framework\isTrue;

class AuthenticationService
{
    protected $helper;
    public function __construct()
    {
        $this-> helper = new RolesAndPermissionsHelper();
    }

    public function sign_up(SignUpDto $signUpDto): array
    {
        DB::beginTransaction();
        try {
        $userData = (array) $signUpDto;
        $userData['is_approved'] = !($signUpDto->user_type === 'teacher');

        //create a user with Request file information
        $user = User::query()->create($userData);
            $certificates = [];
            if ($signUpDto->user_type === 'teacher' && !empty($signUpDto->file_path)) {
                foreach ($signUpDto->file_path as $path) {
                    $certificates[] = AcademicCertificate::query()->create([
                        'file_path' => $path,
                        'description' => $signUpDto->description ?? null,
                        'teacher_id' => $user->id
                    ]);
                }
            }
        //create a user with Request file information
        $user = User::query()->create((array)$signUpDto);

        $user->assignRole($signUpDto->user_type);
        $user = $this->helper->give_and_load_permissions_and_roles($signUpDto->user_type,$user);

            if ($user->is_approved) {
                Auth::login($user);
                $user->token = $user->createToken("token")->plainTextToken;
            } else {
                unset($user->token);
            }

        //login user immediately
        // Auth::login($user);

//        event(new UserRegistered($user));
        Event::dispatch(new UserRegistered($user));

        //Commit the transaction if there is no problems
        DB::commit();

        //create token to the user when he signed up
        $user['token'] = $user->createToken("token")->plainTextToken;

            //save success signup in log file
        Log::info( 'New user signed up', [
            'user_id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'social_id' => $signInDto->social_id ?? null

        ]);

        return [
            'data' => $signUpDto->user_type == 'teacher'
                ? ['user' => $user , 'academic_certificates' => $certificates ]
                : $user,
            'message' => $user->is_approved
                ? 'Signed up successfully.'
                : 'Your account is pending admin approval.'
//            'data' => $user,
//            'message' => 'signed up successfully'
        ];
        }catch(Exception $e){
            DB::rollBack();
            //save failed signup in log file
            Log::error('User signup failed', [
                'error' => $e->getMessage(),
                'data' => [
                    'username' => $signUpDto->username,
                    'email' => $signUpDto->email,
                    'social_id' => $signInDto->social_id ?? null
                ]
            ]);
            return [
                'data' => null,
                'message' => 'There was a problem signing up. Please try again later.'
            ];
        }
    }

    public function sign_in(SignInDto $signInDto): array
    {
        try {
        $user = null;
        // show if the user log in with username or email (optionally)
        $field = filter_var($signInDto->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            if (Auth::attempt([$field => $signInDto->login, 'password' => $signInDto->password])) {
                $user = Auth::user();
                $is_banned = BannedUser::query()
                ->where('user_id',Auth::id())
                ->first();
                if ($is_banned){
                    return [
                        'data' => null,
                        'message' => 'Your account is banned'
                    ];
                }
                // ❗ Check approval status
                if (!$user->is_approved) {
                    Auth::logout(); // Immediately log out unapproved user

                    Log::warning('Unapproved user attempted to login', [
                        'user_id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                    ]);

                    return [
                        'data' => null,
                        'message' => 'Your account is awaiting admin approval.'
                    ];
                }

                //give the user his permissions
                $user = $this->helper->appendRolesAndPermissions($user);
                //create token to the user when he logged in
                $user['token'] = $user->createToken("authToken")->plainTextToken;
            }

        //save success login in log file
        Log::info('user logged in', [
            'username' => $user['username'],
            'email' => $user['email']
        ]);

        return [
            'data' => $user,
            'message' => 'user logged in successfully',
        ];
        } catch(Exception $e){
            //save failed login in log file
            Log::error('user login failed' , [
                'error' => $e->getMessage(),
                'data' => [
                    'username' => $signInDto->login,
                    'social_id' => $signInDto->social_id ?? null
                ]
            ]);
            return [
                'data' => null,
                'message' => 'information not matching or account is not exist'
            ];
        }
    }


    public function logout(Request $request): array
    {
        $user = Auth::user();
        //delete all user tokens
        $request->user()->tokens()->delete();

        //save success logged out in log file
        Log::info('user logged out', [
            'username' =>  $user['username'],
            'email' => $user['email']
        ]);
        return [
            'data' => null,
            'message' => 'logged out successfully'
        ];
    }
}
