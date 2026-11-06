<?php

namespace App\Console\Commands;

use App\Fax\FaxInbox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FaxReprocess extends Command
{
    protected $signature = 'fax:reprocess {email_id : Resend received-email id}';
    protected $description = 'Re-fetch a Resend inbound email and run it through fax ingest (replaces a quarantined copy)';

    public function handle(): int
    {
        $id = (string) $this->argument('email_id');
        $old = DB::table('gvd_faxes')->where('email_id', $id)->first();
        if ($old && $old->status === 'stored') { $this->info("Already stored as {$old->uuid}"); return self::SUCCESS; }
        $email = FaxInbox::fetchResendEmail($id); // fetch first, so a fetch failure leaves the quarantined copy alone
        if ($old) { @unlink(FaxInbox::path($old)); DB::table('gvd_faxes')->where('id', $old->id)->delete(); }
        $r = FaxInbox::ingest($email);
        $this->info(json_encode($r));
        return self::SUCCESS;
    }
}
