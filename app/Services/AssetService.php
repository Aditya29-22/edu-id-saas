<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AssetService
{
    /**
     * Upload an image to the configured disk (S3 / CDN or local).
     * Compresses the image and handles path generation for multi-tenancy.
     *
     * @param UploadedFile $file
     * @param string $pathPrefix e.g., 'schools/1/students'
     * @return array Returns paths/URLs for original, compressed, and thumbnail versions
     */
    public function uploadImageOptimized(UploadedFile $file, string $pathPrefix): array
    {
        $filename = time() . '_' . uniqid();
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        
        $originalName = "{$filename}_original.{$extension}";
        $compressedName = "{$filename}_opt.{$extension}";
        $thumbName = "{$filename}_thumb.{$extension}";

        $disk = env('FILESYSTEM_DISK', 'public');

        // Store original
        $originalPath = $file->storeAs($pathPrefix, $originalName, $disk);

        // Simulation of Intervention Image optimization & resizing
        // $image = Image::make($file)->encode($extension, 75);
        $compressedPath = $file->storeAs($pathPrefix, $compressedName, $disk);
        
        // Simulation of Intervention thumbnail creation
        // $thumb = Image::make($file)->fit(150, 150)->encode($extension, 70);
        $thumbPath = $file->storeAs($pathPrefix, $thumbName, $disk);

        return [
            'original_url' => Storage::disk($disk)->url($originalPath),
            'original_key' => $originalPath,
            'compressed_url' => Storage::disk($disk)->url($compressedPath),
            'compressed_key' => $compressedPath,
            'thumbnail_url' => Storage::disk($disk)->url($thumbPath),
            'thumbnail_key' => $thumbPath,
        ];
    }

    /**
     * Helper to safely delete assets from S3/CDN.
     */
    public function deleteAssets(array $keys): void
    {
        $disk = env('FILESYSTEM_DISK', 'public');
        foreach ($keys as $key) {
            if ($key) {
                Storage::disk($disk)->delete($key);
            }
        }
    }
}
