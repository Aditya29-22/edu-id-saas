<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Http\UploadedFile;

class ImageService
{
    public function processStudentPhoto(UploadedFile $file): array
    {
        $image = Image::make($file);

        $original = (clone $image)
            ->resize(1200, 1200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('jpg', 95);

        $compressed = (clone $image)
            ->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('jpg', 80);

        $thumbnail = (clone $image)
            ->fit(150, 150)
            ->encode('jpg', 70);

        return [
            'original' => (string) $original,
            'compressed' => (string) $compressed,
            'thumbnail' => (string) $thumbnail,
        ];
    }

    public function compress(UploadedFile $file, int $quality = 80, ?int $maxWidth = null): string
    {
        $image = Image::make($file);

        if ($maxWidth && $image->width() > $maxWidth) {
            $image->resize($maxWidth, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        return (string) $image->encode('jpg', $quality);
    }

    public function getChecksum(UploadedFile $file): string
    {
        return md5_file($file->getRealPath());
    }
}
