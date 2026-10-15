<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$respond = static function (int $status, array $data): never {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
};
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    $respond(405, ['message' => 'Method tidak diizinkan.']);
}
if ($path === '/up') $respond(200, ['status' => 'ok']);
if ($path !== '/api/batches') $respond(404, ['message' => 'Endpoint tidak ditemukan.']);
$config = require dirname(__DIR__).'/bootstrap.php';
if ($config['token'] === '') $respond(503, ['message' => 'Token API belum dikonfigurasi.']);
if (!hash_equals('Bearer '.$config['token'], $_SERVER['HTTP_AUTHORIZATION'] ?? '')) $respond(401, ['message' => 'Tidak terautentikasi.']);
try {
    if (!is_file($config['database'])) $respond(503, ['message' => 'Jalankan setup database terlebih dahulu.']);
    $db = new PDO('sqlite:'.$config['database'], null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    $batches = $db->query('SELECT id, name FROM batches ORDER BY id')->fetchAll();
    $members = $db->prepare('SELECT id, name, email, angkatan, gender, agama FROM participants WHERE batch_id = ? ORDER BY id');
    foreach ($batches as &$batch) {
        $members->execute([$batch['id']]);
        $batch['participants'] = $members->fetchAll();
    }
    unset($batch);
    $respond(200, ['data' => $batches]);
} catch (Throwable $e) {
    error_log($e->getMessage());
    $respond(500, ['message' => 'Data peserta tidak dapat dimuat.']);
}
