<?php

declare(strict_types=1);

$local = is_file(__DIR__.'/.env') ? parse_ini_file(__DIR__.'/.env', false, INI_SCANNER_RAW) : [];
$config = static fn (string $key, string $default = ''): string => getenv($key) !== false ? (string) getenv($key) : (string) ($local[$key] ?? $default);
$path = $config('DB_DATABASE', 'storage/api-gojags.sqlite');
if (!preg_match('~^(?:[A-Za-z]:[\\/]|/)~', $path)) $path = __DIR__.'/'.$path;
return ['token' => $config('API_GOJAGS_TOKEN'), 'database' => $path];
