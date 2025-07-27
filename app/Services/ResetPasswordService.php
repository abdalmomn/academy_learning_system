<?php

namespace App\Services;

use App\DTO\ResetPasswordCheckCodeDto;
use App\DTO\ResetPasswordCheckEmailDto;
use App\Events\UserResetCode;
use App\Helper\JwtHelper;
use App\Models\ResetPassword;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Helper\generateResetTokenHelper;

class ResetPasswordService
{

    public function send_reset_password_code(ResetPasswordCheckEmailDto $checkEmailDto):array
    {
        //get user information from user table
        $user = User::query()
            ->where('email', $checkEmailDto->email)
            ->first();
        //if the user does not exist
        if (!$user) {
            return [
                'data' => null,
                'message' => 'User not found with this email address',
            ];
        }

        DB::beginTransaction();

        try {
        //to generate integer reset code between 100000 and 999999
        $code = generateResetTokenHelper::generate_reset_code();
        //to create token that send to each next function to recognize who is the user? (replace the session)
        $jwtToken = generateResetTokenHelper::generate_reset_token($checkEmailDto);
        // Remove any existing code for this user
        ResetPassword::query()->where('email', $user->email)->delete();
        //create new record in resetPassword table with correct information
        $data = ResetPassword::query()->create([
            'email' => $checkEmailDto->email,
            'token' => $jwtToken,
            'reset_code' => $code,
            'code_expires_at' => now()->addMinutes(10),
            'token_expires_at' => now()->addMinutes(30)
        ]);
        //send reset code with job in the background
        Event::dispatch(new UserResetCode($code,$user));

        //commit the transaction if everything is ok
        DB::commit();
        //logging user information to track the data
        Log::info('reset password code has been sent to user: ' , [
            'data' => [
                'name' => $user->username,
                'email' => $user->email
            ]
        ]);
        //return user information and success message
        return [
            'data' => $data,
            'message' => 'reset password code has been sent successfully'
        ];
        }catch(Exception $e){
            DB::rollBack();
            //track if there is error in any last transactions
            Log::error('reset password code can not be sent' , [
                'error' => $e->getMessage(),
                'data' => [
                    'name' => $user->username ?? 'N/A',
                    'email' => $user->email ?? $checkEmailDto->email,
                ]
            ]);
            //return no data and fail message
            return [
                'data' => null,
                'message' => 'there is problem sending you a reset password code'
            ];
        }
    }

    public function check_reset_code(ResetPasswordCheckCodeDto $checkCodeDto): array
    {
        try {
            //check the received token if it is for the same user
            $decoded = JwtHelper::validateToken($checkCodeDto->reset_token);
            if (!$decoded || $decoded->scope !== 'password_reset') {
//                return [
//                    'data' => null,
//                    'message' => 'reset token is invalid.',
//                ];
                return response()->json(['error' => 'Invalid token'], 401);
            }
            //get the user record that have email and code
            $token_record = ResetPassword::query()
                ->where('token', $checkCodeDto->reset_token)
                ->where('reset_code', $checkCodeDto->code)
                ->where('code_expires_at', '>', now())
                ->first();

            if (!$token_record) {
                return [
                    'data' => null,
//                    'message' => 'Reset code is invalid or code is expired.',
                    'message' => 'Reset code or email is invalid.',
                ];
            }
            //logging user information to track the data
            Log::info('User provided correct reset code', [
                'reset_token' => $checkCodeDto->reset_token,
                'code' => $checkCodeDto->code,
            ]);
            //return user information and success message
            return [
                'data' => $token_record,
                'message' => 'reset password code is correct'
            ];
        }catch(Exception $e){
            //track if there is error in any last transactions
            Log::error('Error checking reset code', [
                'error' => $e->getMessage(),
                'reset_token' => $checkCodeDto->reset_token,
            ]);
            //return no data and fail message
            return [
                'data' => null,
                'message' => 'there is problem checking reset password code'
            ];
        }
    }

    public function resend_reset_code(string $reset_token): array
    {
        //get the user record that have user email
        $reset_record = ResetPassword::query()
            ->where('token', $reset_token)
            ->first();

        if (!$reset_record) {
            return [
                'data' => null,
                'message' => 'Invalid reset token.',
            ];
        }
        //get the email itself
        $email = $reset_record->email;

        DB::beginTransaction();
        try {
            //to generate integer reset code between 100000 and 999999
            $code = generateResetTokenHelper::generate_reset_code();
            $tokenData = (object)['email' => $email];
            //to create token that send to each next function to recognize who is the user? (replace the session)
            $jwtToken = generateResetTokenHelper::generate_reset_token($tokenData);
            // Remove any existing code for this user
            ResetPassword::query()->where('email', $email)->delete();
            //create new record in resetPassword table with correct information
            $data = ResetPassword::query()->create([
                'email' => $email,
                'token' => $jwtToken,
                'reset_code' => $code,
                'code_expires_at' => now()->addMinutes(10),
                'token_expires_at' => now()->addMinutes(30)
            ]);
            //send reset code with job in the background
            Event::dispatch(new UserResetCode($code, $email));
            //commit the transaction if everything is ok
            DB::commit();
            //logging user information to track the data
            Log::info('Reset password code resent', ['email' => $email]);
            //return user information and success message
            return [
                'data' => $data,
                'message' => 'Resend code successful'
            ];
        } catch(Exception $e) {
            DB::rollBack();
            //track if there is error in any last transactions
            Log::error('Resend failed', [
                'error' => $e->getMessage(),
                'email' => $email
            ]);
            //return no data and fail message
            return [
                'data' => null,
                'message' => 'Resend failed'
            ];
        }
    }


    public function set_new_password(string $reset_token, $request): array
    {
        //get the reset password record that has email
        $reset_record = ResetPassword::query()
            ->where('token', $reset_token)
            ->first();
//        if (!$reset_record){
//            return [
//                'data' => null,
//                'message' => 'invalid reset token'
//            ];
//        }
        //get the user record based on email
        $user = User::query()
            ->where('email' , $reset_record->email)
            ->first();

        //if there is no user
        if (!$user) {
            return [
                'data' => null,
                'message' => 'User not found',
            ];
        }
        try {
            DB::beginTransaction();
//            //check if the new password same as old one
//            if (Hash::check($request, $user->password)) {
//                return [
//                    'data' => null,
//                    'message' => 'The new password must be different from your current password.'
//                ];
//            }
            //update the password of the user based on form request
        User::query()
            ->where('email', $user->email)
            ->update([
                'password' => Hash::make($request)
            ]);
        //delete the user record in reset password table
        $reset_record->delete();
            //commit the transaction if everything is ok
        DB::commit();
            //logging user information to track the data
        Log::info('user reset his password successfully', [
            'username' => $user->username,
            'email' => $user->email
        ]);
            //return user information and success message
        return [
            'data' => $user,
            'message' => 'password reset successfully'
        ];
        }catch(Exception $e){
            DB::rollBack();
            //track if there is error in any last transactions
            Log::error('there is a problem in reset password' , [
                'error' => $e->getMessage(),
                'email' => $user->email ?? null
            ]);
            //return no data and fail message
            return [
                'data' => null,
                'message' => 'there is a problem, can not reset your password at this moment'
            ];
        }
    }
}
