<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceScanLog extends Model
{
    protected $fillable = ['attendance_id', 'action', 'scanned_at', 'scanned_by'];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
