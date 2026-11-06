<?php

namespace App\Console\Commands;

use App\Support\CustomerEmail;
use Illuminate\Console\Command;

/** 2026-11-02 Customer email campaigns worker. Sends nothing unless EC_SENDING=true. */
class EcRun extends Command
{
    protected $signature = 'ec:run';
    protected $description = 'Send due customer email campaign batches (throttled per customer).';

    public function handle(): int
    {
        $o = CustomerEmail::run();
        $this->line(json_encode($o));
        if ($o['sent'] || $o['failed'] || $o['paused']) \Log::info('ec:run', $o);
        return 0;
    }
}
