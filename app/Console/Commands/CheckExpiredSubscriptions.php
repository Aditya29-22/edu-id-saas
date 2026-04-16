<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired';
    protected $description = 'Check and mark expired subscriptions';

    public function handle(SubscriptionService $service): int
    {
        $count = $service->checkExpiredSubscriptions();
        $this->info("Expired {$count} subscriptions.");
        return Command::SUCCESS;
    }
}
