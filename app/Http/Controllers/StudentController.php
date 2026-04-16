<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Services\ImageService;
use App\Services\QRService;
use App\Services\S3Service;
use App\Services\IDCardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function __construct(
        private S3Service $s3Service,
        private ImageService $imageService,
        private QRService $qrService,
        private IDCardService $idCardService
    ) {}

    public function index(): JsonResponse
    {
        $students = Student::with(['user', 'school'])
            ->when(request('class'), fn($q) => $q->where('class', request('class')))
            ->when(request('section'), fn($q) => $q->where('section', request('section')))
            ->when(request('search'), function ($q) {
                $q->where('student_id', 'LIKE', '%' . request('search') . '%')
                  ->orWhereHas('user', fn($uq) =>
                      $uq->where('name', 'LIKE', '%' . request('search') . '%')
                  );
            })
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $students]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'school_id' => 'required|exists:schools,id',
            'roll_number' => 'required|string|max:20',
            'class' => 'required|string|max:20',
            'section' => 'required|string|max:10',
            'date_of_birth' => 'required|date|before:today',
            'blood_group' => 'nullable|string|max:5',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:15',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $school = School::findOrFail($validated['school_id']);

        return DB::transaction(function () use ($validated, $school, $request) {
            // 1. Create user account
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'student',
                'school_id' => $school->id,
            ]);

            // 2. Generate student ID
            $studentId = Student::generateStudentId($school->code);

            // 3. Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'school_id' => $school->id,
                'student_id' => $studentId,
                'roll_number' => $validated['roll_number'],
                'class' => $validated['class'],
                'section' => $validated['section'],
                'date_of_birth' => $validated['date_of_birth'],
                'blood_group' => $validated['blood_group'] ?? null,
                'parent_name' => $validated['parent_name'],
                'parent_phone' => $validated['parent_phone'],
                'address' => $validated['address'] ?? null,
                'academic_year' => $this->getAcademicYear(),
            ]);

            // 4. Upload photo if provided
            if ($request->hasFile('photo')) {
                $this->uploadStudentPhoto($student, $request->file('photo'), $school->code);
            }

            // 5. Generate QR Code
            $this->generateStudentQR($student, $school);

            return response()->json([
                'success' => true,
                'message' => 'Student registered successfully.',
                'data' => $student->fresh(['user'])
            ], 201);
        });
    }

    public function generateIDCard(int $id): JsonResponse
    {
        $student = Student::with(['user', 'school.activeTemplate'])->findOrFail($id);

        $idCardImage = $this->idCardService->generate($student);

        $s3Key = "{$student->school->code}/id-cards/{$student->student_id}.png";
        $result = $this->s3Service->uploadRaw($idCardImage, $s3Key);

        $student->update([
            'id_card_generated' => true,
            'id_card_url' => $result['cdn_url'] ?: $result['s3_url'],
            'id_card_s3_key' => $result['s3_key'],
            'id_card_generated_at' => now(),
        ]);

        Asset::create([
            'school_id' => $student->school_id,
            'uploaded_by' => auth()->id(),
            'type' => 'id_card',
            'original_name' => "{$student->student_id}_idcard.png",
            'mime_type' => 'image/png',
            'size' => strlen($idCardImage),
            's3_key' => $result['s3_key'],
            's3_url' => $result['s3_url'],
            'cdn_url' => $result['cdn_url'],
            'related_model' => 'Student',
            'related_id' => $student->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ID Card generated successfully.',
            'data' => [
                'id_card_url' => $result['cdn_url'] ?: $result['s3_url'],
            ]
        ]);
    }

    private function uploadStudentPhoto(Student $student, $file, string $schoolCode): void
    {
        $checksum = $this->imageService->getChecksum($file);
        $duplicate = Asset::where('checksum', $checksum)
                         ->where('school_id', $student->school_id)
                         ->where('type', 'student_photo')
                         ->first();

        if ($duplicate) {
            throw new \Exception('This photo has already been uploaded for another student.');
        }

        $images = $this->imageService->processStudentPhoto($file);

        $variants = ['original', 'compressed', 'thumbnail'];
        $photoData = [];

        foreach ($variants as $variant) {
            $s3Key = "{$schoolCode}/student-photos/{$student->student_id}_{$variant}.jpg";
            $result = $this->s3Service->uploadRaw($images[$variant], $s3Key);

            $photoData["photo_{$variant}_url"] = $result['cdn_url'] ?: $result['s3_url'];
            $photoData["photo_{$variant}_s3_key"] = $result['s3_key'];

            Asset::create([
                'school_id' => $student->school_id,
                'uploaded_by' => auth()->id(),
                'type' => 'student_photo',
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => 'image/jpeg',
                'size' => strlen($images[$variant]),
                's3_key' => $result['s3_key'],
                's3_url' => $result['s3_url'],
                'cdn_url' => $result['cdn_url'],
                'related_model' => 'Student',
                'related_id' => $student->id,
                'checksum' => $variant === 'original' ? $checksum : null,
            ]);
        }

        $student->update($photoData);
    }

    private function generateStudentQR(Student $student, School $school): void
    {
        $qrPayload = $this->qrService->generateQRPayload(
            $student->id,
            $school->id,
            $student->student_id
        );

        $qrImage = $this->qrService->generateQRImage($qrPayload);

        $s3Key = "{$school->code}/qr-codes/{$student->student_id}.png";
        $result = $this->s3Service->uploadRaw($qrImage, $s3Key);

        $student->update([
            'qr_data' => $qrPayload,
            'qr_image_url' => $result['cdn_url'] ?: $result['s3_url'],
            'qr_image_s3_key' => $result['s3_key'],
        ]);
    }

    private function getAcademicYear(): string
    {
        $now = now();
        $year = $now->year;
        return $now->month >= 4 ? "{$year}-" . ($year + 1) : ($year - 1) . "-{$year}";
    }
}
