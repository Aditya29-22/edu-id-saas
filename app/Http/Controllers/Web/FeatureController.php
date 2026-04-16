<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\School;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FeatureController extends Controller
{
    // QR Code Management
    public function qrCodes(Request $request)
    {
        $query = Student::with('school');

        if ($request->school_id) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('roll_number', 'like', "%{$request->search}%");
            });
        }

        $students = $query->latest()->paginate(12);
        $schools = School::all();

        return view('qrcodes.index', compact('students', 'schools'));
    }

    public function generateQr($studentId)
    {
        $student = Student::with('school')->findOrFail($studentId);

        // Generate unique token if not exists
        if (!$student->qr_token) {
            $student->qr_token = Str::uuid()->toString();
            $student->save();
        }

        $qrData = json_encode([
            'student_id' => $student->id,
            'token' => $student->qr_token,
            'school_id' => $student->school_id,
        ]);

        $qrSvg = QrCode::format('svg')
            ->size(250)
            ->color(26, 26, 46)
            ->backgroundColor(255, 255, 255)
            ->margin(1)
            ->generate($qrData);

        return response()->json([
            'success' => true,
            'qr_svg' => base64_encode($qrSvg),
            'student' => $student,
        ]);
    }

    public function generateBulkQr(Request $request)
    {
        $request->validate(['school_id' => 'required|exists:schools,id']);

        $students = Student::where('school_id', $request->school_id)
            ->whereNull('qr_token')
            ->get();

        foreach ($students as $student) {
            $student->qr_token = Str::uuid()->toString();
            $student->save();
        }

        return redirect()->route('qrcodes', ['school_id' => $request->school_id])
            ->with('success', "QR codes generated for {$students->count()} students!");
    }

    // Attendance Scanner
    public function scannerPage()
    {
        $schools = School::all();
        $todayStats = [
            'total_scans' => Attendance::whereDate('date', today())->count(),
            'entries' => Attendance::whereDate('date', today())->whereNotNull('entry_time')->count(),
            'exits' => Attendance::whereDate('date', today())->where('status', 'exited')->count(),
            'late' => Attendance::whereDate('date', today())->where('is_late', true)->count(),
        ];

        $recentScans = Attendance::with(['student', 'student.school'])
            ->whereDate('date', today())
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('scanner.index', compact('schools', 'todayStats', 'recentScans'));
    }

    public function processScan(Request $request)
    {
        $request->validate(['qr_data' => 'required|string']);

        try {
            $data = json_decode($request->qr_data, true);

            if (!$data || !isset($data['student_id']) || !isset($data['token'])) {
                return response()->json(['success' => false, 'message' => 'Invalid QR code format'], 400);
            }

            $student = Student::with('school')->find($data['student_id']);

            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found'], 404);
            }

            if ($student->qr_token !== $data['token']) {
                return response()->json(['success' => false, 'message' => 'Invalid QR token. This may be an old or forged QR code.'], 403);
            }

            if (!$student->is_active) {
                return response()->json(['success' => false, 'message' => 'Student account is deactivated'], 403);
            }

            // Check/create attendance record for today
            $attendance = Attendance::where('student_id', $student->id)
                ->whereDate('date', today())
                ->first();

            $now = Carbon::now();
            $school = $student->school;

            if (!$attendance) {
                // First scan → Entry
                $isLate = false;
                if ($school && $school->late_threshold) {
                    $threshold = Carbon::parse($school->late_threshold);
                    $isLate = $now->format('H:i:s') > $threshold->format('H:i:s');
                }

                $attendance = Attendance::create([
                    'student_id' => $student->id,
                    'school_id' => $student->school_id,
                    'date' => today(),
                    'entry_time' => $now,
                    'entry_scanned_by' => auth()->id(),
                    'is_late' => $isLate,
                    'status' => 'entered',
                ]);

                return response()->json([
                    'success' => true,
                    'action' => 'entry',
                    'is_late' => $isLate,
                    'message' => $isLate
                        ? "⚠️ LATE ENTRY: {$student->first_name} {$student->last_name} at {$now->format('h:i A')}"
                        : "✅ ENTRY: {$student->first_name} {$student->last_name} at {$now->format('h:i A')}",
                    'student' => [
                        'name' => "{$student->first_name} {$student->last_name}",
                        'class' => "{$student->class_name}-{$student->section}",
                        'roll' => $student->roll_number,
                        'school' => $school->name ?? '',
                    ],
                    'time' => $now->format('h:i A'),
                ]);
            } elseif ($attendance->status === 'entered') {
                // Second scan → Exit
                $attendance->update([
                    'exit_time' => $now,
                    'exit_scanned_by' => auth()->id(),
                    'status' => 'exited',
                ]);

                return response()->json([
                    'success' => true,
                    'action' => 'exit',
                    'message' => "🚪 EXIT: {$student->first_name} {$student->last_name} at {$now->format('h:i A')}",
                    'student' => [
                        'name' => "{$student->first_name} {$student->last_name}",
                        'class' => "{$student->class_name}-{$student->section}",
                        'roll' => $student->roll_number,
                        'school' => $school->name ?? '',
                    ],
                    'time' => $now->format('h:i A'),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "⛔ {$student->first_name} has already exited today. No re-entry allowed.",
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error processing scan: ' . $e->getMessage()], 500);
        }
    }

    // ID Card Preview
    public function idCards(Request $request)
    {
        $query = Student::with('school');

        if ($request->school_id) {
            $query->where('school_id', $request->school_id);
        }

        $students = $query->latest()->paginate(8);
        $schools = School::all();

        return view('idcards.index', compact('students', 'schools'));
    }
}
