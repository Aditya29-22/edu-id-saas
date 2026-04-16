<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceScanLog;
use App\Models\School;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    private QRService $qrService;

    public function __construct(QRService $qrService)
    {
        $this->qrService = $qrService;
    }

    public function processScan(string $qrData, int $scannedByUserId): array
    {
        // 1. Decrypt QR
        $payload = $this->qrService->decryptQRPayload($qrData);

        if (!$payload) {
            return [
                'success' => false,
                'message' => 'Invalid QR code.',
                'type' => 'error'
            ];
        }

        // 2. Find student
        $student = Student::with('school')
                         ->where('id', $payload['student_id'])
                         ->where('school_id', $payload['school_id'])
                         ->where('is_active', true)
                         ->first();

        if (!$student) {
            return [
                'success' => false,
                'message' => 'Student not found or inactive.',
                'type' => 'error'
            ];
        }

        // 3. Verify scanner belongs to same school
        $scanner = \App\Models\User::find($scannedByUserId);
        if (!$scanner || ($scanner->role !== 'super_admin' && $scanner->school_id != $student->school_id)) {
            return [
                'success' => false,
                'message' => 'Unauthorized: Scanner does not belong to this school.',
                'type' => 'error'
            ];
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();
        $school = $student->school;

        return DB::transaction(function () use ($student, $today, $now, $school, $scannedByUserId) {
            // 4. Check existing attendance for today
            $attendance = Attendance::where('student_id', $student->id)
                                   ->where('date', $today)
                                   ->lockForUpdate()
                                   ->first();

            if (!$attendance) {
                // FIRST SCAN: MARK ENTRY
                $isLate = $now->format('H:i:s') > $school->late_threshold;

                $attendance = Attendance::create([
                    'student_id' => $student->id,
                    'school_id' => $school->id,
                    'date' => $today,
                    'entry_time' => $now,
                    'entry_scanned_by' => $scannedByUserId,
                    'is_late' => $isLate,
                    'status' => 'entered',
                ]);

                AttendanceScanLog::create([
                    'attendance_id' => $attendance->id,
                    'action' => 'entry',
                    'scanned_at' => $now,
                    'scanned_by' => $scannedByUserId,
                ]);

                return [
                    'success' => true,
                    'message' => 'Entry marked successfully.' . ($isLate ? ' (LATE)' : ''),
                    'type' => 'entry',
                    'is_late' => $isLate,
                    'student_name' => $student->user->name ?? 'Unknown',
                    'student_id' => $student->student_id,
                    'time' => $now->format('h:i A'),
                ];

            } elseif ($attendance->status === 'entered') {
                // SECOND SCAN: MARK EXIT
                $attendance->update([
                    'exit_time' => $now,
                    'exit_scanned_by' => $scannedByUserId,
                    'status' => 'exited',
                ]);

                AttendanceScanLog::create([
                    'attendance_id' => $attendance->id,
                    'action' => 'exit',
                    'scanned_at' => $now,
                    'scanned_by' => $scannedByUserId,
                ]);

                return [
                    'success' => true,
                    'message' => 'Exit marked successfully.',
                    'type' => 'exit',
                    'student_name' => $student->user->name ?? 'Unknown',
                    'student_id' => $student->student_id,
                    'time' => $now->format('h:i A'),
                ];

            } elseif ($attendance->status === 'exited') {
                // ALREADY EXITED: PREVENT DUPLICATE
                return [
                    'success' => false,
                    'message' => 'Student has already marked entry and exit for today.',
                    'type' => 'duplicate',
                    'student_name' => $student->user->name ?? 'Unknown',
                ];
            }

            return [
                'success' => false,
                'message' => 'Unexpected attendance state.',
                'type' => 'error'
            ];
        });
    }
}
