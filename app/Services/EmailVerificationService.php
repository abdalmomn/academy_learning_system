<?php

namespace App\Services;

use App\Events\FamilyPinCode;
use App\Helper\generateTokenHelper;
use App\Models\PinCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class EmailVerificationService
{
    public function verify(Request $request, $id, $hash): array
    {
        if (! URL::hasValidSignature($request)) {
            return [
                'status' => false,
                'message' => 'Invalid or expired verification link',
            ];
        }

        $user = User::findOrFail($id);

        if (! hash_equals(sha1($user->email), $hash)) {
            return [
                'status' => false,
                'message' => 'Invalid hash',
            ];
        }

        if ($user->hasVerifiedEmail()) {
            return [
                'status' => true,
                'message' => 'Email already verified',
            ];
        }

        $user->markEmailAsVerified();


        if ($user->hasRole('child')){
            $code = generateTokenHelper::generate_code();
            PinCode::query()->create([
               'user_id' => $user->id,
               'pin_code' => $code
            ]);
            Event::dispatch(new FamilyPinCode($user,$code));
        }
        return [
            'status' => true,
            'message' => 'Email verified successfully',
        ];
    }
}
