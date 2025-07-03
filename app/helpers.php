<?php

use App\Models\User;

if (!function_exists('getAdminId')) {
    function getAdminId(): ?int {
        return User::where('role', 'admin')->value('user_id');
    }
}
