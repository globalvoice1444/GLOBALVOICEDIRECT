<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/** 2026-11-02 Customer sequences worker (own mailbox only). */
class EcSeqRun extends Command
{
    protected $signature = 'ec:seq-run';
    protected $description = 'Send due customer sequence steps.';
    public function handle(): int { $o = \App\Support\CustomerSequences::run(); $this->line(json_encode($o)); if ($o['sent'] || $o['failed']) \Log::info('ec:seq-run', $o); return 0; }
}
