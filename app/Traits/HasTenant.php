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
            if ($user && !$model->id_tenant) {
                $model->id_tenant = $user->id_tenant;
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

        if (!$user || $user->hasRole('admin')) {
            return;
        }

        if ($user->id_tenant !== $model->id_tenant) {
            abort(403, "Akses ditolak (tenant tidak cocok saat $aksi).");
        }
    }
}