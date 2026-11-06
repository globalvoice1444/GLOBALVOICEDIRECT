<?php

namespace App\Http\Controllers;

/** Builds phone-script downloads (txt, PDF, DOCX) with no external libraries. */
class ScriptDoc
{
    public static function txt(string $title, array $items): string
    {
        $o = $title . "\r\n" . str_repeat('=', min(70, mb_strlen($title))) . "\r\n\r\n";
        foreach ($items as [$h, $t]) $o .= $h . "\r\n" . wordwrap($t, 90, "\r\n") . "\r\n\r\n";
        return "\xEF\xBB\xBF" . $o . "Prepared with Global Voice Direct\r\n";
    }

    private static function enc(string $s): string
    {
        $s = strtr($s, ['–' => '-', '—' => '-', '‘' => "'", '’' => "'", '“' => '"', '”' => '"', '…' => '...']);
        $s = @iconv('UTF-8', 'Windows-1252//TRANSLIT', $s) ?: $s;
        return strtr($s, ['\\' => '\\\\', '(' => '\\(', ')' => '\\)']);
    }

    public static function pdf(string $title, array $items): string
    {
        $lines = [['B', 18, $title], ['', 11, '']];
        foreach ($items as [$h, $t]) {
            $lines[] = ['B', 13, $h];
            foreach (explode("\n", wordwrap($t, 88, "\n", true)) as $l) $lines[] = ['', 11, $l];
            $lines[] = ['', 11, ''];
        }
        $lines[] = ['', 9, 'Prepared with Global Voice Direct'];
        $pages = []; $cur = ''; $y = 760;
        foreach ($lines as [$f, $sz, $txt]) {
            $lh = $sz + 5;
            if ($y - $lh < 50) { $pages[] = $cur; $cur = ''; $y = 760; }
            $y -= $lh;
            if ($txt !== '') $cur .= 'BT /' . ($f === 'B' ? 'F2' : 'F1') . " $sz Tf 56 $y Td (" . self::enc($txt) . ") Tj ET\n";
        }
        $pages[] = $cur;
        $objs = []; $n = count($pages);
        $objs[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $kids = []; for ($i = 0; $i < $n; $i++) $kids[] = (5 + 2 * $i) . ' 0 R';
        $objs[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . "] /Count $n >>";
        $objs[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objs[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
        foreach ($pages as $i => $c) {
            $objs[5 + 2 * $i] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents ' . (6 + 2 * $i) . ' 0 R >>';
            $objs[6 + 2 * $i] = '<< /Length ' . strlen($c) . " >>\nstream\n$c\nendstream";
        }
        $o = "%PDF-1.4\n"; $off = [];
        ksort($objs);
        foreach ($objs as $k => $v) { $off[$k] = strlen($o); $o .= "$k 0 obj\n$v\nendobj\n"; }
        $x = strlen($o); $cnt = count($objs) + 1;
        $o .= "xref\n0 $cnt\n0000000000 65535 f \n";
        foreach ($off as $v) $o .= sprintf("%010d 00000 n \n", $v);
        return $o . "trailer\n<< /Size $cnt /Root 1 0 R >>\nstartxref\n$x\n%%EOF\n";
    }

    public static function docx(string $title, array $items): string
    {
        $e = fn ($s) => htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $p = fn ($t, $b = false, $sz = 22) => '<w:p><w:r><w:rPr>' . ($b ? '<w:b/>' : '') . "<w:sz w:val=\"$sz\"/></w:rPr><w:t xml:space=\"preserve\">" . $e($t) . '</w:t></w:r></w:p>';
        $body = $p($title, true, 36);
        foreach ($items as [$h, $t]) $body .= $p($h, true, 26) . $p($t) . $p('');
        $body .= $p('Prepared with Global Voice Direct', false, 16);
        $doc = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body>' . $body . '<w:sectPr><w:pgSz w:w="12240" w:h="15840"/><w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440"/></w:sectPr></w:body></w:document>';
        $tmp = tempnam(sys_get_temp_dir(), 'gvdx');
        $z = new \ZipArchive(); $z->open($tmp, \ZipArchive::OVERWRITE);
        $z->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/></Types>');
        $z->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>');
        $z->addFromString('word/document.xml', $doc);
        $z->close(); $b = file_get_contents($tmp); @unlink($tmp);
        return $b;
    }
}
