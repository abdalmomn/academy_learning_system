<?php

namespace App\Http\Controllers;

use App\ResponseTrait;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    use ResponseTrait;
    protected $walletService;
    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function show_my_wallet()
    {
        $data = $this->walletService->show_wallet();
        return $this->Success($data['data'],$data['message']);
    }
}

