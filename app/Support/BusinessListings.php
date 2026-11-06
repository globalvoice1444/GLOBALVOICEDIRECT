<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Business Listings logic (ported from the WP gvd-citations plugin). Free path only: Bing CSV + owner guide. */
class BusinessListings
{
    const DAYS = ['mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday'];
    const STATUSES = ['new', 'bing_queued', 'bing_uploaded', 'guide_sent'];
    const TABLE = 'gvd_business_listings';

    public static function exportDir(): string { return storage_path('app/gvd-listings-exports'); }

    public static function e164($raw, string $country = 'US')
    {
        $d = preg_replace('/\D+/', '', (string) $raw);
        if (strpos(trim((string) $raw), '+') === 0) return (strlen($d) >= 8 && strlen($d) <= 15) ? "+$d" : false;
        if (in_array($country, ['US', 'CA'], true)) {
            if (strlen($d) === 11 && $d[0] === '1') $d = substr($d, 1);
            return (strlen($d) === 10 && $d[0] >= '2') ? "+1$d" : false;
        }
        return false;
    }
    public static function prettyPhone($e): string { return preg_match('/^\+1(\d{3})(\d{3})(\d{4})$/', (string) $e, $m) ? "($m[1]) $m[2]-$m[3]" : (string) $e; }
    public static function titleCase($s): string { return preg_replace_callback('/\b([a-z])/', fn ($m) => strtoupper($m[1]), strtolower(trim(preg_replace('/\s+/', ' ', (string) $s)))); }
    public static function normStreet($s): string
    {
        $s = self::titleCase($s);
        $map = ['Street' => 'St', 'Avenue' => 'Ave', 'Road' => 'Rd', 'Boulevard' => 'Blvd', 'Drive' => 'Dr', 'Lane' => 'Ln', 'Court' => 'Ct', 'Highway' => 'Hwy', 'Parkway' => 'Pkwy', 'Place' => 'Pl', 'Suite' => 'Ste', 'North' => 'N', 'South' => 'S', 'East' => 'E', 'West' => 'W'];
        foreach ($map as $k => $v) $s = preg_replace('/\b' . $k . '\b\.?/', $v, $s);
        return str_replace('.', '', $s);
    }
    public static function fmtTime($t): string { return $t ? date('g:i A', strtotime($t)) : ''; }
    public static function hoursText($r): array
    {
        $h = json_decode($r->hours ?? '[]', true) ?: []; $out = [];
        foreach (self::DAYS as $k => $day) { $x = $h[$k] ?? ['closed' => true]; $out[$day] = !empty($x['closed']) ? 'Closed' : self::fmtTime($x['open']) . ' – ' . self::fmtTime($x['close']); }
        return $out;
    }
    public static function cats($r): array { return array_merge([$r->category], json_decode($r->extra_categories ?? '[]', true) ?: []); }

    /** Column names follow Bing Places' agency bulk template (BingPlacesAgencySample.xlsx, Listings sheet): required = Store ID, Business Name, Is this a Chain Business, Address Line 1, City, State, Zip Code, Country Code, Business Owner's Email (non-chain), Primary Category. */
    public static function bingColumns(): array
    {
        $c = ['Store ID', 'Business Name', 'Is this a Chain Business', 'Address Line 1', 'Address Line 2', 'City', 'State', 'Zip Code', 'Country Code', 'Phone Number', 'Business Owner\'s Email', 'Primary Category', 'Secondary Categories', 'Website', 'Description'];
        foreach (self::DAYS as $day) $c[] = $day;
        return $c;
    }
    public static function bingRow($r): array
    {
        $h = json_decode($r->hours ?? '[]', true) ?: [];
        $cats = self::cats($r);
        $row = [preg_replace('/\D/', '', $r->phone), $r->business_name, 'N', $r->street, $r->suite, $r->city, $r->state, (string) $r->zip, $r->country, $r->phone, $r->email,
            $cats[0] ?? '', implode(';', array_slice($cats, 1)), $r->website, $r->description];
        foreach (self::DAYS as $k => $day) { $x = $h[$k] ?? ['closed' => true]; $row[] = !empty($x['closed']) ? 'Closed' : $x['open'] . '-' . $x['close']; }
        return $row;
    }
    /** Writes CSV for rows in new/bing_queued (storage/app, not public); returns [filename, ids] or null. */
    public static function buildExport(bool $markQueued): ?array
    {
        $rows = DB::table(self::TABLE)->whereIn('status', ['new', 'bing_queued'])->orderBy('id')->get();
        if ($rows->isEmpty()) return null;
        $dir = self::exportDir(); if (!is_dir($dir)) mkdir($dir, 0750, true);
        $name = 'bing-bulk-' . now('America/New_York')->format('Ymd-His') . '-' . Str::random(6) . '.csv';
        $fh = fopen("$dir/$name", 'w'); fwrite($fh, "\xEF\xBB\xBF"); fputcsv($fh, self::bingColumns());
        $ids = [];
        foreach ($rows as $r) {
            fputcsv($fh, array_map(function ($v) { $v = (string) $v; return preg_match('/^[=+\-@]/', $v) && !preg_match('/^\+\d+$/', $v) ? "'" . $v : $v; }, self::bingRow($r)));
            $ids[] = (int) $r->id;
        }
        fclose($fh);
        if ($markQueued && $ids) DB::table(self::TABLE)->where('status', 'new')->whereIn('id', $ids)->update(['status' => 'bing_queued', 'updated_at' => now()]);
        return [$name, $ids];
    }

    public static function exportFiles(): array
    {
        $f = glob(self::exportDir() . '/bing-bulk-*.csv') ?: []; rsort($f);
        return array_map('basename', array_slice($f, 0, 30));
    }
}
