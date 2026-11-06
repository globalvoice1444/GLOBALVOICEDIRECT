<?php

namespace App\Portal;

/** Minimal evaluator/builder for the billing system's time-period strings (Time::Period style). */
class Period
{
    const WD = ['su' => 1, 'mo' => 2, 'tu' => 3, 'we' => 4, 'th' => 5, 'fr' => 6, 'sa' => 7];
    const MO = ['jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6, 'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12];

    public static function active(?string $p, \DateTimeInterface $t): bool
    {
        $p = trim((string) $p);
        if ($p === '' || strcasecmp($p, 'always') === 0) return true;
        if (strcasecmp($p, 'never') === 0 || strcasecmp($p, 'none') === 0) return false;
        foreach (explode(',', $p) as $alt) if (self::altActive($alt, $t)) return true;
        return false;
    }

    private static function altActive(string $alt, \DateTimeInterface $t): bool
    {
        if (!preg_match_all('/(yr|year|mo|month|md|mday|wd|wday|hr|hour|min|minute)\s*\{([^}]*)\}/i', $alt, $m, PREG_SET_ORDER)) return false;
        $now = ['yr' => (int) $t->format('Y'), 'mo' => (int) $t->format('n'), 'md' => (int) $t->format('j'), 'wd' => (int) $t->format('w') + 1,
            'hr' => (int) $t->format('G'), 'min' => (int) $t->format('i')];
        $alias = ['year' => 'yr', 'month' => 'mo', 'mday' => 'md', 'wday' => 'wd', 'hour' => 'hr', 'minute' => 'min'];
        foreach ($m as [$all, $scale, $vals]) {
            $s = strtolower($scale); $s = $alias[$s] ?? $s;
            $hit = false;
            foreach (preg_split('/\s+/', trim($vals)) as $tok) {
                if ($tok === '') continue;
                [$a, $b] = array_pad(explode('-', $tok, 2), 2, null);
                $a = self::num($s, $a); $b = $b === null ? $a : self::num($s, $b);
                if ($a === null || $b === null) continue;
                $v = $now[$s];
                if ($a <= $b ? ($v >= $a && $v <= $b) : ($v >= $a || $v <= $b)) { $hit = true; break; }
            }
            if (!$hit) return false;
        }
        return true;
    }

    private static function num(string $s, ?string $v): ?int
    {
        $v = strtolower(trim((string) $v));
        if ($s === 'wd' && isset(self::WD[substr($v, 0, 2)]) && !ctype_digit($v)) return self::WD[substr($v, 0, 2)];
        if ($s === 'mo' && isset(self::MO[substr($v, 0, 3)]) && !ctype_digit($v)) return self::MO[substr($v, 0, 3)];
        return ctype_digit($v) ? (int) $v : null;
    }

    /** Weekly: days (su..sa), from/to "HH:MM" (to exclusive). */
    public static function weekly(array $days, string $from, string $to): string
    {
        $d = implode(' ', array_values(array_intersect(array_keys(self::WD), array_map('strtolower', $days))));
        [$fh, $fm] = array_map('intval', explode(':', $from . ':0')); [$th, $tm] = array_map('intval', explode(':', $to . ':0'));
        $end = $th * 60 + $tm - 1; if ($end < $fh * 60 + $fm) $end = 23 * 60 + 59;
        $eh = intdiv($end, 60); $em = $end % 60;
        $wd = $d !== '' ? "wd{{$d}} " : '';
        if ($fh === $eh) return "{$wd}hr{{$fh}} min{{$fm}-{$em}}";
        $parts = ["{$wd}hr{{$fh}} min{{$fm}-59}"];
        if ($eh - $fh > 1) $parts[] = "{$wd}hr{" . ($fh + 1) . '-' . ($eh - 1) . '}';
        $parts[] = "{$wd}hr{{$eh}} min{0-{$em}}";
        return implode(', ', $parts);
    }

    /** Specific dates (Y-m-d), whole day. */
    public static function dates(array $days): string
    {
        $out = [];
        foreach ($days as $d) if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $d, $m)) $out[] = 'yr{' . (int) $m[1] . '} mo{' . (int) $m[2] . '} md{' . (int) $m[3] . '}';
        return implode(', ', array_unique($out));
    }

    public static function describe(?string $p, ?string $desc = null): string
    {
        if ($desc && !in_array(strtolower($desc), ['', 'default'], true)) return $desc;
        $p = trim((string) $p);
        return ($p === '' || strcasecmp($p, 'always') === 0) ? 'Always' : $p;
    }
}
