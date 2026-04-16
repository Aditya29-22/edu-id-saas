<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'street', 'city', 'state', 'pincode', 'country',
        'email', 'phone', 'logo_url', 'logo_s3_key',
        'entry_time', 'late_threshold', 'exit_time',
        'active_template_id', 'subscription_status', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'entry_time' => 'string',
        'late_threshold' => 'string',
        'exit_time' => 'string',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
                    ->where('status', 'active')
                    ->where('end_date', '>=', now())
                    ->latest();
    }

    public function templates()
    {
        return $this->hasMany(Template::class);
    }

    public function activeTemplate()
    {
        return $this->belongsTo(Template::class, 'active_template_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscription_status === 'active';
    }
}
