<?php

namespace App\Services;

use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;

class WalletService
{

    public function show_wallet():array
    {
        $wallet = Wallet::query()
            ->where('user_id',Auth::id())
            ->first(['id', 'balance' , 'status', 'user_id']);
        if (!$wallet){
            return [
                'data' => null,
                'message' => 'wallet not found'
            ];
        }
        return [
            'data' => $wallet,
            'message' => 'wallet retrieved successfully'
        ];
    }
}
