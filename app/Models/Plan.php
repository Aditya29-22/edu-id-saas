<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'code', 'description',
        'price_monthly', 'price_yearly',
        'max_students', 'max_users', 'storage_gb',
        'custom_templates', 'analytics_access', 'api_access',
        'is_active'
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'custom_templates' => 'boolean',
        'analytics_access' => 'boolean',
        'api_access' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
