<?php

namespace App\Support;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class TenantContext
{
    public static function bisaBypass(): bool
    {
        return Auth::user()?->can('tenant.bypass') ?? false;
    }

    /**
     * Tenant yang sedang dikelola.
     * - Admin (tenant.bypass): tenant yang dipilih di session, atau null (mode lihat semua)
     * - Lainnya: tenant milik user
     */
    public static function current(): ?Tenant
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        if (self::bisaBypass()) {
            $id = session('tenant_context_id');
            return $id ? Tenant::find($id) : null;
        }

        return $user->tenant;
    }
}