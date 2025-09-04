<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class dashboardService
{

    public function show_all_users():array
    {
        $user = Auth::user();
        if(!$user->hasRole('admin')){
            return [
                'data' => null,
                'message' => 'must be admin to show all users'
            ];
        }
        $users = User::query()->select('id','username', 'email', 'email_verified_at')->get();
        if ($users->isEmpty()){
            return [
                'data' => null,
                'message' => 'there are no users right now'
            ];
        }
        return [
            'data' => $users,
            'message' => 'retrieved all users successfully'
        ];
    }

    public function show_all_teachers():array
    {
        $user = Auth::user();
        if(!$user->hasRole(['admin','supervisor'])){
            return [
                'data' => null,
                'message' => 'must be admin to show all teachers'
            ];
        }
        $teachers = User::query()
            ->role('teacher')
            ->with('roles:id,name')
            ->get(['id','username','email','is_approved','email_verified_at']);
        foreach ($teachers as $teacher){
            unset($teacher['roles']);
        }
        if ($teachers->isEmpty()){
            return [
                'data' => null,
                'message' => 'there are no teachers right now'
            ];
        }
        return [
            'data' => $teachers,
            'message' => 'retrieved all teachers successfully'
        ];
    }

    public function show_all_students():array
    {
        $user = Auth::user();
        if(!$user->hasRole(['admin','supervisor'])){
            return [
                'data' => null,
                'message' => 'must be admin to show all students'
            ];
        }
        $students = User::query()
            ->role(['child', 'woman'])
            ->with('roles:id,name')
            ->get(['id','username','email','is_approved','email_verified_at']);
        foreach ($students as $student){
            $student['role_name'] = $student->roles->pluck('name')->first();
            unset($student['roles']);
        }

        if ($students->isEmpty()){
            return [
                'data' => null,
                'message' => 'there are no students right now'
            ];
        }
        return [
            'data' => $students,
            'message' => 'retrieved all students successfully'
        ];
    }
    public function show_all_supervisors():array
    {
        $user = Auth::user();
        if(!$user->hasRole('admin')){
            return [
                'data' => null,
                'message' => 'must be admin to show all supervisors'
            ];
        }
        $supervisors = User::query()
            ->role('supervisor')
            ->with('roles:id,name')
            ->get(['id','username','email','is_approved','email_verified_at']);
        foreach ($supervisors as $supervisor){
            unset($supervisor['roles']);
        }
        if ($supervisors->isEmpty()){
            return [
                'data' => null,
                'message' => 'there are no supervisor right now'
            ];
        }
        return [
            'data' => $supervisors,
            'message' => 'retrieved all supervisor successfully'
        ];
    }
}
