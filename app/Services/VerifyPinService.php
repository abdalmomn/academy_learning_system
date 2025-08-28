<?php

namespace App\Services;

use App\Models\PinCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerifyPinService
{

    public function verify_pin($request):array
    {
        $user = User::query()
                ->where('id',Auth::id())
                ->first();
        if (!$user){
            return [
                'data' => null,
                'message' => 'user not found'
            ];
        }
        if (!$user->hasRole('child')){
            return [
                'data' => null,
                'message' => 'just for children users'
            ];
        }
        $pin_code = PinCode::query()
            ->where('pin_code',$request['pin_code'])
            ->where('user_id',$user->id)
            ->select('pin_code','user_id')
            ->first();
        if (!$pin_code){
            return [
                'data' => null,
                'message' => 'pin code not found'
            ];
        }
        return [
            'data' => $pin_code,
            'message' => 'pin code is correct'
        ];
    }
}
