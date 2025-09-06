<?php

namespace App\Http\Controllers;

use App\ResponseTrait;
use App\Services\StrikeService;
use Illuminate\Http\Request;

class StrikeController extends Controller
{
    private StrikeService $service;

    public function __construct(StrikeService $service)
    {
        $this->service = $service;
    }
    use ResponseTrait;

    public function show()
    {
            $data = $this->service->show_strike();
            return $this->Success($data['data'],$data['message']);

    }
}
