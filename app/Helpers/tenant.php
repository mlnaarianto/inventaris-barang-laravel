<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('getCurrentTenant')) {
    function getCurrentTenant()
    {
        return Auth::user()?->tenant;
    }
}