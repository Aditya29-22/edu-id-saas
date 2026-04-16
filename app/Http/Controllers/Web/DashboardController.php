<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Attendance;
use App\Models\Payment;
use App\Services\AssetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_schools' => School::count(),
            'total_students' => Student::count(),
            'total_users' => User::count(),
            'total_plans' => Plan::where('is_active', true)->count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'today_attendance' => Attendance::whereDate('date', today())->count(),
            'revenue' => Payment::where('status', 'captured')->sum('amount'),
        ];

        $recent_schools = School::latest()->take(5)->get();
        $recent_students = Student::with('school')->latest()->take(5)->get();
        $recent_payments = Payment::with('school')->where('status', 'captured')->latest()->take(5)->get();

        // Attendance trend (last 7 days)
        $attendance_trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $attendance_trend[] = [
                'date' => $date->format('M d'),
                'count' => Attendance::whereDate('date', $date)->count(),
            ];
        }

        return view('dashboard', compact('user', 'stats', 'recent_schools', 'recent_students', 'recent_payments', 'attendance_trend'));
    }

    public function schools(Request $request)
    {
        $schools = School::withCount('students', 'users')
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            })
            ->latest()
            ->paginate(10);

        return view('schools.index', compact('schools'));
    }

    public function createSchool()
    {
        return view('schools.create');
    }

    public function storeSchool(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:20|unique:schools',
            'email' => 'required|email|unique:schools',
            'phone' => 'required|string|max:15',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
        ]);

        School::create($request->all());
        return redirect()->route('schools')->with('success', 'School created successfully!');
    }

    public function students(Request $request)
    {
        $query = Student::with('school');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('roll_number', 'like', "%{$request->search}%");
            });
        }

        if ($request->school_id) {
            $query->where('school_id', $request->school_id);
        }

        $students = $query->latest()->paginate(10);
        $schools = School::all();

        return view('students.index', compact('students', 'schools'));
    }

    public function createStudent()
    {
        $schools = School::where('is_active', true)->get();
        return view('students.create', compact('schools'));
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'roll_number' => 'required|string|max:30',
            'school_id' => 'required|exists:schools,id',
            'class_name' => 'required|string|max:20',
            'section' => 'nullable|string|max:10',
            'dob' => 'nullable|date',
            'guardian_name' => 'nullable|string|max:200',
            'guardian_phone' => 'nullable|string|max:15',
        ]);

        $data = $request->except('photo');
        $data['is_active'] = true;

        if ($request->hasFile('photo')) {
            $assetService = new AssetService();
            $pathPrefix = "schools/{$request->school_id}/students";
            $imagePaths = $assetService->uploadImageOptimized($request->file('photo'), $pathPrefix);
            
            $data['photo_original_url'] = $imagePaths['original_url'];
            $data['photo_original_s3_key'] = $imagePaths['original_key'];
            $data['photo_compressed_url'] = $imagePaths['compressed_url'];
            $data['photo_compressed_s3_key'] = $imagePaths['compressed_key'];
            $data['photo_thumbnail_url'] = $imagePaths['thumbnail_url'];
            $data['photo_thumbnail_s3_key'] = $imagePaths['thumbnail_key'];
        }

        Student::create($data);
        return redirect()->route('students')->with('success', 'Student added successfully!');
    }

    public function attendance(Request $request)
    {
        $date = $request->date ?? today()->format('Y-m-d');

        $query = Attendance::with(['student', 'student.school'])
            ->whereDate('date', $date);

        if ($request->school_id) {
            $query->where('school_id', $request->school_id);
        }

        $records = $query->latest()->paginate(20);
        $schools = School::all();

        $summary = [
            'total' => Attendance::whereDate('date', $date)->count(),
            'on_time' => Attendance::whereDate('date', $date)->where('is_late', false)->count(),
            'late' => Attendance::whereDate('date', $date)->where('is_late', true)->count(),
            'exited' => Attendance::whereDate('date', $date)->where('status', 'exited')->count(),
        ];

        return view('attendance.index', compact('records', 'schools', 'date', 'summary'));
    }

    public function plans()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('plans.index', compact('plans'));
    }

    public function users(Request $request)
    {
        $users = User::with('school')
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            })
            ->when($request->role, function ($q) use ($request) {
                $q->where('role', $request->role);
            })
            ->latest()
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function createUser()
    {
        $schools = School::where('is_active', true)->get();
        return view('users.create', compact('schools'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,school_admin,teacher,student,security_guard',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;

        User::create($data);
        return redirect()->route('users')->with('success', 'User created successfully!');
    }
}
