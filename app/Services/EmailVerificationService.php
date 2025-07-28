<?php

namespace App\Services;

use Illuminate\Http\Request;
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

        return [
            'status' => true,
            'message' => 'Email verified successfully',
        ];
    }
}
