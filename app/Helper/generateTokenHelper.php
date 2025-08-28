<?php

namespace App\Helper;

class generateTokenHelper
{
    public static function generate_code(): int
    {
        return str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function generate_reset_token($request): string
    {
        return // Create JWT token
            JwtHelper::generateToken([
                'email' => $request->email,
                'scope' => 'password_reset'
            ], 30); // 30-min expiry
    }

}
