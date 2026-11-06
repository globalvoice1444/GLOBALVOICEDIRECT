<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DripSeed extends Command
{
    protected $signature = 'drip:seed';
    protected $description = 'Seed drip sequences from database/drip/seed.json (idempotent; all sequences created PAUSED)';

    public function handle()
    {
        $data = json_decode(file_get_contents(database_path('drip/seed.json')), true);
        foreach ($data['sequences'] as $s) {
            if (DB::table('drip_sequences')->where('slug', $s['slug'])->exists()) { $this->line('exists: ' . $s['slug']); continue; }
            $id = DB::table('drip_sequences')->insertGetId(['name' => $s['name'], 'slug' => $s['slug'], 'type' => $s['type'], 'trigger' => $s['trigger'],
                'status' => 'PAUSED', 'description' => $s['description'], 'enroll_after' => null, 'created_at' => now(), 'updated_at' => now()]);
            foreach ($s['steps'] as $i => $st) DB::table('drip_steps')->insert(['sequence_id' => $id, 'position' => $i + 1, 'day_offset' => $st['day'],
                'name' => $st['name'], 'subject' => $st['subject'], 'preview' => $st['preview'], 'purpose' => mb_substr($st['purpose'], 0, 255),
                'body_html' => $st['body'], 'conditions' => json_encode($st['cond']), 'status' => $st['status'], 'created_at' => now(), 'updated_at' => now()]);
            $this->line('seeded: ' . $s['slug'] . ' (' . count($s['steps']) . ' steps, PAUSED)');
        }
        return 0;
    }
}
