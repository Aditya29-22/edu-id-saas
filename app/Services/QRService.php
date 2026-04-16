<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Crypt;

class QRService
{
    public function generateQRPayload(int $studentId, int $schoolId, string $studentCode): string
    {
        $data = [
            'student_id' => $studentId,
            'school_id' => $schoolId,
            'student_code' => $studentCode,
            'timestamp' => now()->timestamp,
            'hash' => hash('sha256', $studentId . $schoolId . $studentCode . config('app.key'))
        ];

        return Crypt::encryptString(json_encode($data));
    }

    public function decryptQRPayload(string $encryptedData): ?array
    {
        try {
            $decrypted = Crypt::decryptString($encryptedData);
            $data = json_decode($decrypted, true);

            if (!$data || !isset($data['student_id'], $data['school_id'], $data['hash'])) {
                return null;
            }

            $expectedHash = hash('sha256',
                $data['student_id'] . $data['school_id'] . $data['student_code'] . config('app.key')
            );

            if (!hash_equals($expectedHash, $data['hash'])) {
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function generateQRImage(string $payload, int $size = 300): string
    {
        return QrCode::format('png')
                     ->size($size)
                     ->errorCorrection('H')
                     ->generate($payload);
    }
}
