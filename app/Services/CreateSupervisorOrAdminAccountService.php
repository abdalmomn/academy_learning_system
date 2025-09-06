<?php

namespace App\Services;

use App\DTO\SignUpDto;
use App\Events\UserRegistered;
use App\Helper\RolesAndPermissionsHelper;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class CreateSupervisorOrAdminAccountService
{
    protected $helper;
    public function __construct()
    {
        $this-> helper = new RolesAndPermissionsHelper();
    }

    public function create_supervisor_admin_account(SignUpDto $signUpDto): array
    {
        if (!Auth::guard('sanctum')->user()->hasRole('admin', 'web')) {
            return [
                'data' => null,
                'message' => 'Only admins can create supervisor/admin accounts.'
            ];
        }

        DB::beginTransaction();
        try {
            $userData = (array) $signUpDto;
            $userData['is_approved'] = true;

            $user = User::query()->create($userData);
            $user->assignRole(Role::findByName($signUpDto->user_type, 'web'));
            $user = $this->helper->give_and_load_permissions_and_roles($signUpDto->user_type, $user);
            //Event::dispatch(new UserRegistered($user));

            DB::commit();

            Log::info('Admin created an account', [
                'admin_id' => Auth::id(),
                'account_id' => $user->id,
                'account_type' => $signUpDto->user_type,
                'email' => $user->email,
            ]);

            return [
                'data' => $user,
                'message' => $signUpDto->user_type == 'admin'? 'admin account created successfully.' : 'Supervisor account created successfully.'
            ];
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('failed to create an account', [
                'admin_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return [
                'data' => null,
                'message' => 'error in creating supervisor account.'
            ];
        }
    }

}
