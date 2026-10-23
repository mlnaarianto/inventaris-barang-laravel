<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Support\Facades\Auth;

trait HasTenant
{
    protected static function bootHasTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            $user = Auth::user();

            // Seeder, artisan, dan queue berjalan tanpa user login: biarkan.
            if (!$user) {
                return;
            }

            // Tanpa tenant.bypass, tenant dipaksa mengikuti user (anti-spoof id_tenant).
            if (!$user->can('tenant.bypass') || !$model->id_tenant) {
                $model->id_tenant = $user->id_tenant;
            }

            if (!$model->id_tenant) {
                abort(403, 'Akun Anda belum terhubung ke tenant, data tidak dapat dibuat.');
            }
        });

        static::updating(function ($model) {
            self::guardTenant($model, 'update');
        });

        static::deleting(function ($model) {
            self::guardTenant($model, 'delete');
        });
    }

    protected static function guardTenant($model, string $aksi): void
    {
        $user = Auth::user();

        if (!$user || $user->can('tenant.bypass')) {
            return;
        }

        if ((int) $user->id_tenant !== (int) $model->id_tenant) {
            abort(403, "Akses ditolak (tenant tidak cocok saat $aksi).");
        }
    }
}
