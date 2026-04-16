<?php

namespace App\Traits;

use App\Scopes\TenantScope;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (auth()->check() && !$model->school_id) {
                $user = auth()->user();
                if ($user->role !== 'super_admin' && $user->school_id) {
                    $model->school_id = $user->school_id;
                }
            }
        });
    }
}
