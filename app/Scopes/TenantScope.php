<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();

        if (!$user || $user->can('tenant.bypass')) {
            return;
        }

        if ($user->id_tenant) {
            $builder->where($model->getTable() . '.id_tenant', $user->id_tenant);
        } else {
            // user tanpa tenant tidak boleh melihat data apa pun
            $builder->whereRaw('1 = 0');
        }
    }
}
