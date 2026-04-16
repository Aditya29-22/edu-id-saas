<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'user_id', 'school_id', 'student_id', 'roll_number',
        'first_name', 'last_name',
        'class_name', 'section', 'gender', 'date_of_birth', 'blood_group',
        'guardian_name', 'guardian_phone', 'address',
        'photo_original_url', 'photo_original_s3_key',
        'photo_compressed_url', 'photo_compressed_s3_key',
        'photo_thumbnail_url', 'photo_thumbnail_s3_key',
        'qr_data', 'qr_token', 'qr_image_url', 'qr_image_s3_key',
        'id_card_generated', 'id_card_url', 'id_card_s3_key',
        'id_card_generated_at', 'academic_year', 'is_active'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'id_card_generated' => 'boolean',
        'id_card_generated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public static function generateStudentId(string $schoolCode): string
    {
        $year = date('Y');
        $lastStudent = self::where('student_id', 'LIKE', "{$schoolCode}-{$year}-%")
                          ->orderBy('student_id', 'desc')
                          ->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->student_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s-%04d', $schoolCode, $year, $newNumber);
    }
}
