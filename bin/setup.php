<?php

declare(strict_types=1);

$root = dirname(__DIR__);
if (!is_file($root.'/.env')) {
    file_put_contents($root.'/.env', 'API_GOJAGS_TOKEN='.bin2hex(random_bytes(32))."\nDB_DATABASE=storage/api-gojags.sqlite\n");
}
$config = require $root.'/bootstrap.php';
if (!is_dir(dirname($config['database']))) mkdir(dirname($config['database']), 0775, true);
$db = new PDO('sqlite:'.$config['database'], null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$db->exec('PRAGMA foreign_keys = ON');
$db->exec('CREATE TABLE IF NOT EXISTS batches (id TEXT PRIMARY KEY, name TEXT NOT NULL)');
$db->exec('CREATE TABLE IF NOT EXISTS participants (id TEXT PRIMARY KEY, batch_id TEXT NOT NULL REFERENCES batches(id), name TEXT NOT NULL, email TEXT NOT NULL UNIQUE, angkatan TEXT NOT NULL, gender TEXT NOT NULL, agama TEXT NOT NULL)');
$db->beginTransaction();
$batch = $db->prepare('INSERT OR IGNORE INTO batches (id, name) VALUES (?, ?)');
$participant = $db->prepare('INSERT OR IGNORE INTO participants (id, batch_id, name, email, angkatan, gender, agama) VALUES (?, ?, ?, ?, ?, ?, ?)');
for ($n = 1; $n <= 3; $n++) {
    $batch->execute(['batch-'.$n, 'Peserta Pelatihan Angkatan '.$n]);
    for ($i = 1; $i <= 10; $i++) {
        $participant->execute(['peserta-'.$n.'-'.$i, 'batch-'.$n, 'Peserta Angkatan '.$n.' Nomor '.$i, 'peserta.'.$n.'.'.$i.'@example.test', 'Angkatan '.$n, $i <= 6 ? 'Laki-laki' : 'Perempuan', $i % 5 === 0 ? 'Kristen' : 'Islam']);
    }
}
$db->commit();
echo "Setup selesai: 3 batch, masing-masing 10 peserta. Token berada di .env.\n";
