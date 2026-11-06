<?php

namespace App\Console\Commands;

use App\Fax\FaxInbox;
use Illuminate\Console\Command;

class FaxPurge extends Command
{
    protected $signature = 'fax:purge {--days=}';
    protected $description = 'Delete received faxes older than the retention period (default 90 days)';

    public function handle(): int
    {
        $r = FaxInbox::purge($this->option('days') !== null ? (int) $this->option('days') : null);
        $this->info("Deleted {$r['deleted']} fax(es).");
        return self::SUCCESS;
    }
}
