<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * Encrypts plain-text billing/fax account passwords at rest (Crypt::encryptString, APP_KEY).
 * Idempotent: values that already decrypt are skipped. Never prints values.
 */
class EncryptLegacyPasswords extends Command
{
    protected $signature = 'security:encrypt-legacy-passwords {--dry-run} {--backup= : write id + encrypted value JSON here}';
    protected $description = 'Encrypt plain-text user_phone_numbers.i_password and vfaxsignup.accpass in place';

    const TARGETS = [['user_phone_numbers', 'i_password'], ['vfaxsignup', 'accpass']];

    public function handle(): int
    {
        $backup = [];
        foreach (self::TARGETS as [$table, $col]) {
            $done = 0; $skipped = 0;
            DB::table($table)->whereNotNull($col)->where($col, '<>', '')->orderBy('id')
                ->chunkById(200, function ($rows) use ($table, $col, &$done, &$skipped, &$backup) {
                    foreach ($rows as $r) {
                        $v = (string) $r->$col;
                        if (self::isEncrypted($v)) { $skipped++; $backup[$table][] = ['id' => $r->id, 'enc' => $v]; continue; }
                        $enc = Crypt::encryptString($v);
                        if (!$this->option('dry-run')) DB::table($table)->where('id', $r->id)->where($col, $v)->update([$col => $enc]);
                        $backup[$table][] = ['id' => $r->id, 'enc' => $enc];
                        $done++;
                    }
                });
            $this->info("$table.$col: encrypted $done, already encrypted $skipped" . ($this->option('dry-run') ? ' (dry run)' : ''));
        }
        if ($path = $this->option('backup')) {
            file_put_contents($path, json_encode(['created_at' => now()->toIso8601String(), 'note' => 'ids + Crypt ciphertext only (APP_KEY)', 'rows' => $backup]));
            @chmod($path, 0600);
        }
        return self::SUCCESS;
    }

    public static function isEncrypted(string $v): bool
    {
        if (strlen($v) < 100) return false;
        try { Crypt::decryptString($v); return true; } catch (DecryptException $e) { return false; }
    }
}
