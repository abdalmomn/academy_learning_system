<?php

namespace App\Http\Controllers;

use App\ResponseTrait;
use App\Services\dashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ResponseTrait;
    protected $dashboardService;
    public function __construct(dashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function show_all_users()
    {
        $data = $this->dashboardService->show_all_users();
        return $this->Success($data['data'],$data['message']);
    }
    public function show_all_teachers()
    {
        $data = $this->dashboardService->show_all_teachers();
        return $this->Success($data['data'],$data['message']);
    }
    public function show_all_students()
    {
        $data = $this->dashboardService->show_all_students();
        return $this->Success($data['data'],$data['message']);
    }
    public function show_all_supervisors()
    {
        $data = $this->dashboardService->show_all_supervisors();
        return $this->Success($data['data'],$data['message']);
    }
}
