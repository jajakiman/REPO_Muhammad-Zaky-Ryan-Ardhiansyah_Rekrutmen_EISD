# AksesLoka

AksesLoka adalah aplikasi Laravel 12 untuk informasi fasilitas aksesibilitas dan penanganan laporan masalah fasilitas di lingkungan kampus.

## Menjalankan secara lokal

Persyaratan: PHP 8.2 atau lebih baru, Composer, serta Node.js dan npm.

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`. Nilai rahasia harus diatur melalui `.env` dan tidak boleh dikomit.

## Pengembangan

Jalankan server aplikasi dan Vite bersama-sama dengan:

```bash
composer run dev
```

Jalankan pengujian dengan:

```bash
php artisan test
```

Dokumen kebutuhan ada di `PRD-AksesLoka.md`, sedangkan token dan aturan antarmuka ada di `DESIGN.md`.
