<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->role === 'super_admin') {
                if (request()->has('tenant_id') && request('tenant_id')) {
                    $builder->where($model->getTable() . '.school_id', request('tenant_id'));
                }
                return;
            }

            if ($user->school_id) {
                $builder->where($model->getTable() . '.school_id', $user->school_id);
            }
        }
    }
}
