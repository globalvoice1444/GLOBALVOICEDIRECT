<?php

namespace App\Console\Commands;

use App\Support\BusinessListings;
use Illuminate\Console\Command;

class ListingsBingExport extends Command
{
    protected $signature = 'listings:bing-export';
    protected $description = 'Build the Bing Places bulk CSV for new/queued business listings (storage/app, not public).';

    public function handle(): int
    {
        $res = BusinessListings::buildExport(true);
        $this->info($res ? 'Export ' . $res[0] . ' (' . count($res[1]) . ' rows)' : 'Nothing to export.');
        return 0;
    }
}
