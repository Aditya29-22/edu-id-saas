<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'name', 'school_id', 'type',
        'front_image_url', 'front_image_s3_key',
        'back_image_url', 'back_image_s3_key',
        'layout', 'is_active'
    ];

    protected $casts = [
        'layout' => 'array',
        'is_active' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function scopeSystem($query)
    {
        return $query->whereNull('school_id')->where('type', 'system');
    }
}
