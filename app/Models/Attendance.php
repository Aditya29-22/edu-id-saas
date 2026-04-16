<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use BelongsToTenant;

    protected $table = 'attendance';

    protected $fillable = [
        'student_id', 'school_id', 'date',
        'entry_time', 'entry_scanned_by', 'is_late',
        'exit_time', 'exit_scanned_by', 'status'
    ];

    protected $casts = [
        'date' => 'date',
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'is_late' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function entryScanner()
    {
        return $this->belongsTo(User::class, 'entry_scanned_by');
    }

    public function exitScanner()
    {
        return $this->belongsTo(User::class, 'exit_scanned_by');
    }

    public function scanLogs()
    {
        return $this->hasMany(AttendanceScanLog::class);
    }
}
