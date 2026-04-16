<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $attendanceService)
    {}

    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $result = $this->attendanceService->processScan(
            $request->qr_data,
            $request->user()->id
        );

        $statusCode = $result['success'] ? 200 : 400;

        return response()->json($result, $statusCode);
    }

    public function index(Request $request): JsonResponse
    {
        $query = Attendance::with(['student.user', 'entryScanner', 'exitScanner']);

        if ($request->has('date')) {
            $query->where('date', $request->date);
        } else {
            $query->where('date', now()->toDateString());
        }

        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('date', [$request->from, $request->to]);
        }

        if ($request->has('class')) {
            $query->whereHas('student', fn($q) => $q->where('class', $request->class));
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('late_only')) {
            $query->where('is_late', true);
        }

        $attendance = $query->orderBy('entry_time', 'desc')->paginate(50);

        return response()->json(['success' => true, 'data' => $attendance]);
    }

    public function myAttendance(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student record not found.'
            ], 404);
        }

        $attendance = Attendance::where('student_id', $student->id)
            ->when($request->month, function ($q) use ($request) {
                $q->whereMonth('date', $request->month)
                  ->whereYear('date', $request->year ?? now()->year);
            })
            ->orderBy('date', 'desc')
            ->paginate(31);

        return response()->json(['success' => true, 'data' => $attendance]);
    }

    public function dailySummary(Request $request): JsonResponse
    {
        $date = $request->date ?? now()->toDateString();
        $schoolId = $request->tenant_id;

        $totalStudents = \App\Models\Student::where('school_id', $schoolId)
                                            ->where('is_active', true)
                                            ->count();

        $present = Attendance::where('school_id', $schoolId)
                            ->where('date', $date)
                            ->count();

        $late = Attendance::where('school_id', $schoolId)
                         ->where('date', $date)
                         ->where('is_late', true)
                         ->count();

        $exited = Attendance::where('school_id', $schoolId)
                           ->where('date', $date)
                           ->where('status', 'exited')
                           ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date,
                'total_students' => $totalStudents,
                'present' => $present,
                'absent' => $totalStudents - $present,
                'late' => $late,
                'exited' => $exited,
                'still_inside' => $present - $exited,
            ]
        ]);
    }
}
