<?php

namespace App\Helper;

use App\Models\User;

class RolesAndPermissionsHelper
{
    //to add roles and permissions to user array
    public function appendRolesAndPermissions($user)
    {
        $roles=[];
        foreach ($user->roles as $role){
            $roles []= $role->name;
        }
        unset($user['roles']);
        $user['roles']=$roles;

        $permissions=[];
        foreach ($user->permissions as $permission) {
            $permissions [] =$permission->name;
        }

        unset($user['permissions']);
        $user['permissions']=$permissions;

        return $user;
    }


    public function give_and_load_permissions_and_roles($Role,$user)
    {
//        if (!$user->hasRole($Role)) {
//            $user->assignRole($Role);
//        }
//        // Assign permissions associated with the client role
//        $permissions = $user->getRoleNames()->map(function($roleName) {
//            return \Spatie\Permission\Models\Role::findByName($roleName,'web')->permissions->pluck('name')->toArray();


        // Assign permissions associated with the client role
        $permissions = $user->getRoleNames()->map(function($roleName) {
            return \Spatie\Permission\Models\Role::findByName($roleName)->permissions->pluck('name')->toArray();
        })->flatten()->unique();
        $user->givePermissionTo($permissions);

        // Load roles and permissions for response
        $user->load('roles', 'permissions');

        // Reload user instance to get updated roles and permissions
        $user = User::query()->find($user['id']);
        $user = $this->appendRolesAndPermissions($user);
        $user['token'] = $user->createToken("token")->plainTextToken;
        return $user;
    }
}
