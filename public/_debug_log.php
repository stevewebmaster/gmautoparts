<?php
/*
 |--------------------------------------------------------------------------
 | TEMPORARY diagnostic log viewer  —  DELETE THIS FILE WHEN DONE.
 |--------------------------------------------------------------------------
 | Standalone (does not boot Laravel) so it still works when the app 500s.
 | Open on the phone:  https://YOUR-DOMAIN/_debug_log.php?key=gm-temp-7Qz4
 | Then screenshot the page. Remove this file afterwards:  rm public/_debug_log.php
 */

$SECRET = 'gm-temp-7Qz4'; // change if you like; must match the ?key= in the URL

if (!isset($_GET['key']) || !hash_equals($SECRET, (string) $_GET['key'])) {
    http_response_code(404);
    exit('Not found.');
}

header('Content-Type: text/plain; charset=utf-8');

$log = __DIR__ . '/../storage/logs/laravel.log';

if (!is_file($log)) {
    exit("No log file at: {$log}\n");
}

// Show the last ~40KB so the most recent errors are at the bottom.
$bytes = 40000;
$size  = filesize($log);
$fh    = fopen($log, 'r');
if ($size > $bytes) {
    fseek($fh, -$bytes, SEEK_END);
}
echo "==== last " . min($bytes, $size) . " bytes of laravel.log ====\n\n";
echo fread($fh, $bytes);
fclose($fh);
