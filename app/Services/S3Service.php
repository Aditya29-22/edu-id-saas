<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class S3Service
{
    private string $disk = 's3';

    public function upload(
        UploadedFile|string $file,
        string $schoolCode,
        string $folder,
        ?string $filename = null
    ): array {
        $s3Key = "{$schoolCode}/{$folder}/";

        if ($file instanceof UploadedFile) {
            $filename = $filename ?? time() . '_' . $this->sanitizeFilename($file->getClientOriginalName());
            $s3Key .= $filename;
            Storage::disk($this->disk)->put($s3Key, file_get_contents($file), 'public-read');
        } else {
            $filename = $filename ?? time() . '.png';
            $s3Key .= $filename;
            Storage::disk($this->disk)->put($s3Key, $file, 'public-read');
        }

        $s3Url = Storage::disk($this->disk)->url($s3Key);

        return [
            's3_key' => $s3Key,
            's3_url' => $s3Url,
            'cdn_url' => $this->getCDNUrl($s3Key),
        ];
    }

    public function uploadRaw(string $content, string $s3Key): array
    {
        Storage::disk($this->disk)->put($s3Key, $content, 'public-read');

        return [
            's3_key' => $s3Key,
            's3_url' => Storage::disk($this->disk)->url($s3Key),
            'cdn_url' => $this->getCDNUrl($s3Key),
        ];
    }

    public function delete(string $s3Key): bool
    {
        return Storage::disk($this->disk)->delete($s3Key);
    }

    public function exists(string $s3Key): bool
    {
        return Storage::disk($this->disk)->exists($s3Key);
    }

    public function getCDNUrl(string $s3Key): string
    {
        $cdnDomain = config('services.cloudfront.domain');
        if ($cdnDomain) {
            return "https://{$cdnDomain}/{$s3Key}";
        }
        return Storage::disk($this->disk)->url($s3Key);
    }

    private function sanitizeFilename(string $filename): string
    {
        return preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    }
}
