<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'school_id', 'uploaded_by', 'type',
        'original_name', 'mime_type', 'size',
        's3_key', 's3_url', 'cdn_url',
        'related_model', 'related_id', 'checksum', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
