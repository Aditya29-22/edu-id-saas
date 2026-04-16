<?php

namespace App\Services;

use Aws\CloudFront\CloudFrontClient;

class CDNService
{
    private ?CloudFrontClient $client;
    private string $domain;
    private string $distributionId;

    public function __construct()
    {
        $this->domain = config('services.cloudfront.domain', '');
        $this->distributionId = config('services.cloudfront.distribution_id', '');

        if ($this->domain) {
            $this->client = new CloudFrontClient([
                'version' => 'latest',
                'region' => config('services.aws.region', 'ap-south-1'),
                'credentials' => [
                    'key' => config('services.aws.key'),
                    'secret' => config('services.aws.secret'),
                ],
            ]);
        }
    }

    public function getUrl(string $s3Key): string
    {
        if ($this->domain) {
            return "https://{$this->domain}/{$s3Key}";
        }
        return '';
    }

    public function invalidate(array $paths): bool
    {
        if (!$this->client || !$this->distributionId) {
            return false;
        }

        try {
            $this->client->createInvalidation([
                'DistributionId' => $this->distributionId,
                'InvalidationBatch' => [
                    'Paths' => [
                        'Quantity' => count($paths),
                        'Items' => array_map(fn($p) => '/' . ltrim($p, '/'), $paths),
                    ],
                    'CallerReference' => 'inv-' . time(),
                ],
            ]);
            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
}
