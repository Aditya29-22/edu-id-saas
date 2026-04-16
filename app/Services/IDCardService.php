<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Template;
use Intervention\Image\Facades\Image;

class IDCardService
{
    private S3Service $s3Service;

    public function __construct(S3Service $s3Service)
    {
        $this->s3Service = $s3Service;
    }

    public function generate(Student $student): string
    {
        $student->load(['user', 'school.activeTemplate']);

        $template = $student->school->activeTemplate;

        if (!$template) {
            return $this->generateDefaultIDCard($student);
        }

        return $this->generateFromTemplate($student, $template);
    }

    private function generateDefaultIDCard(Student $student): string
    {
        $card = Image::canvas(400, 600, '#ffffff');

        $card->text($student->school->name, 200, 30, function ($font) {
            $font->size(18);
            $font->color('#003366');
            $font->align('center');
        });

        if ($student->photo_compressed_url) {
            try {
                $photo = Image::make($student->photo_compressed_url)->fit(120, 150);
                $card->insert($photo, 'top', 0, 60);
            } catch (\Exception $e) {
                // Skip photo if unavailable
            }
        }

        $y = 230;
        $details = [
            'Name' => $student->user->name,
            'ID' => $student->student_id,
            'Class' => $student->class . ' - ' . $student->section,
            'Roll No' => $student->roll_number,
            'DOB' => $student->date_of_birth->format('d/m/Y'),
            'Blood' => $student->blood_group ?? 'N/A',
        ];

        foreach ($details as $label => $value) {
            $card->text("{$label}: {$value}", 200, $y, function ($font) {
                $font->size(14);
                $font->color('#333333');
                $font->align('center');
            });
            $y += 25;
        }

        if ($student->qr_image_url) {
            try {
                $qr = Image::make($student->qr_image_url)->resize(100, 100);
                $card->insert($qr, 'bottom', 0, 30);
            } catch (\Exception $e) {
                // Skip QR if unavailable
            }
        }

        return (string) $card->encode('png');
    }

    private function generateFromTemplate(Student $student, Template $template): string
    {
        $card = Image::make($template->front_image_url);
        $layout = $template->layout;

        if ($student->photo_compressed_url && isset($layout['photo'])) {
            $photo = Image::make($student->photo_compressed_url)
                         ->fit($layout['photo']['width'], $layout['photo']['height']);
            $card->insert($photo, 'top-left', $layout['photo']['x'], $layout['photo']['y']);
        }

        $textFields = ['name', 'studentId', 'class', 'schoolName'];
        $dataMap = [
            'name' => $student->user->name,
            'studentId' => $student->student_id,
            'class' => $student->class . ' - ' . $student->section,
            'schoolName' => $student->school->name,
        ];

        foreach ($textFields as $field) {
            if (isset($layout[$field]) && isset($dataMap[$field])) {
                $card->text($dataMap[$field], $layout[$field]['x'], $layout[$field]['y'], function ($font) use ($layout, $field) {
                    $font->size($layout[$field]['fontSize'] ?? 14);
                    $font->color($layout[$field]['color'] ?? '#000000');
                });
            }
        }

        if ($student->qr_image_url && isset($layout['qrCode'])) {
            $qr = Image::make($student->qr_image_url)
                       ->resize($layout['qrCode']['width'], $layout['qrCode']['height']);
            $card->insert($qr, 'top-left', $layout['qrCode']['x'], $layout['qrCode']['y']);
        }

        return (string) $card->encode('png');
    }
}
