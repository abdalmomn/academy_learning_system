<?php

namespace App\Helper;

use App\Models\User;

class profileHelper
{
    public function fetch_profile($user_id)
    {
        return User::query()
            ->where('id',$user_id)
            ->with('profile_details')
            ->first();
    }
}
