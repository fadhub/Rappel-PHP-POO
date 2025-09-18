<?php
declare(strict_types=1);

$opts = getopt('', ['input:', 'published-only', 'limit::', 'help']);
if (isset($opts['help'])) { /* print usage */ exit(0); }
$input = $opts['input'] ?? null;
if (!$input) { fwrite(STDERR, "Erreur: --input requis\n"); exit(2); }

$fh = ($input === '-') ? STDIN : @fopen($input, 'r');
if (!$fh) { fwrite(STDERR, "Erreur: ouverture input\n"); exit(3); }

$rows = [];
$header = fgetcsv($fh);
while (($line = fgetcsv($fh)) !== false) {
    $rows[] = array_combine($header, $line);
}
if ($fh !== STDIN) fclose($fh);

// normalisation
$items = array_map(function(array $r): array {
    return [
        'title'     => trim((string)($r['title'] ?? 'Sans titre')),
        'excerpt'   => ($r['excerpt'] ?? null) !== '' ? (string)$r['excerpt'] : null,
        'views'     => (int)($r['views'] ?? 0),
        'published' => in_array(strtolower((string)($r['published'] ?? 'true')), ['1','true','yes','y','on'], true),
        'author'    => (string)($r['author'] ?? 'N/A'),
    ];
}, $rows);

if (isset($opts['published-only'])) {
    $items = array_values(array_filter($items, fn($a) => $a['published']));
}
if (isset($opts['limit'])) {
    $items = array_slice($items, 0, max(1, (int)$opts['limit']));
}

echo json_encode($items, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . PHP_EOL;
exit(0);
