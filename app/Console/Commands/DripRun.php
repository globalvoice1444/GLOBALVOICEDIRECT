<?php

namespace App\Console\Commands;

use App\Support\Drip;
use App\Support\Workflow;
use Illuminate\Console\Command;

class DripRun extends Command
{
    protected $signature = 'drip:run {--dry : Report what would send without sending or logging}';
    protected $description = 'Enroll verified signups, send due drip emails and advance automation workflows (respects DRIP_SENDING and PAUSED)';

    public function handle()
    {
        $dry = (bool) $this->option('dry');
        $r = Drip::run($dry);
        if (!$dry) { try { $r += Workflow::run(); } catch (\Throwable $e) { $r['wf_error'] = mb_substr($e->getMessage(), 0, 200); }
            try { $r += \App\Support\Broadcast::run(); } catch (\Throwable $e) { $r['bc_error'] = mb_substr($e->getMessage(), 0, 200); } }
        $this->line(json_encode($r));
        return 0;
    }
}
