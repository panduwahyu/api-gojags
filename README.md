# api-gojags

API PHP 8.3 + SQLite, terpisah dari gojags-asrama, tanpa dependency framework.

```powershell
cd D:\laragon\www\api-gojags
php bin/setup.php
php -S 127.0.0.1:8001 -t public public/index.php
```

Setup dapat diulang tanpa menghapus/mengganti data. Tiga batch contoh berisi
masing-masing sepuluh peserta dengan id, nama, email, angkatan, gender, dan agama.
Data contoh memakai alamat email `example.test`, bukan alamat peserta sungguhan.

`GET /up` adalah healthcheck publik. `GET /api/batches` membutuhkan header
`Authorization: Bearer <API_GOJAGS_TOKEN>` dan mengembalikan `{data: [{id, name,
participants: [{id, name, email, angkatan, gender, agama}]}]}`.
Jangan mengirim password peserta melalui API.

Salin token dari `.env` ke `API_GOJAGS_TOKEN` backend gojags-asrama, dan atur
`API_GOJAGS_URL=http://127.0.0.1:8001`. Token tidak diberikan ke frontend.
Klik Sinkronisasi data peserta, pilih batch, lalu Alokasikan kamar otomatis.
Akun lokal baru memiliki password acak; atur password melalui proses pengelolaan
akun sebelum digunakan login. Sinkronisasi tidak mengganti password akun.

Untuk hosting, arahkan web root ke public, atur API_GOJAGS_TOKEN, dan gunakan
penyimpanan SQLite permanen melalui DB_DATABASE (volume jika di Railway).
Gunakan HTTPS untuk koneksi antar-host. Tidak ada akses CORS browser karena
konsumen API adalah backend Laravel. Jangan commit .env atau database peserta.
