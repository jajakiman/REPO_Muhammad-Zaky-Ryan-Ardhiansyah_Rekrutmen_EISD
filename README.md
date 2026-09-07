# AksesLoka

AksesLoka adalah aplikasi Laravel 12 untuk informasi fasilitas aksesibilitas dan penanganan laporan masalah fasilitas di lingkungan kampus.

## Menjalankan secara lokal

Persyaratan: PHP 8.2 atau lebih baru dan Composer.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

Buka `http://127.0.0.1:8000`. Nilai rahasia harus diatur melalui `.env` dan tidak boleh dikomit.

Jalankan pengujian dengan:

```bash
php artisan test
```

Dokumen kebutuhan ada di `PRD-AksesLoka.md`, sedangkan token dan aturan antarmuka ada di `DESIGN.md`.
