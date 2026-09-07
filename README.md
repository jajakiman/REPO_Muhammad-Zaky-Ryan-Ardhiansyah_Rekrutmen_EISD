# AksesLoka

AksesLoka adalah aplikasi sistem informasi berbasis web (Laravel 12) untuk pemetaan fasilitas aksesibilitas dan penanganan laporan masalah fasilitas di lingkungan kampus (SDGs 11).

## Menjalankan secara Lokal

Persyaratan: PHP 8.2 atau lebih baru dan Composer (murni PHP, tanpa alat build eksternal).

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Buka `http://127.0.0.1:8000` di peramban.

Jalankan pengujian otomatis dengan:

```bash
php artisan test
```

## Arsitektur dan Desain

- **Framework**: Laravel 12 (Blade, MVC, Form Requests, Policies, Database Transactions).
- **Frontend**: Native CSS dengan palet Academic Navy & Safety Orange (WCAG AA) dan Vanilla JS untuk integrasi peta Leaflet & OpenStreetMap (murni aset web standar).
- **Basis Data**: PostgreSQL Supabase (driver `pgsql`) di produksi, SQLite in-memory saat pengujian otomatis.
- **Penyimpanan Foto**: Server-side storage adapter (Supabase Storage / disk `report-photos`).
- **Aksesibilitas**: Peta publik disertai alternatif daftar tekstual, navigasi keyboard penuh, kontras tinggi, dan indikator status multimodal (teks + warna).

## Deployment ke Render (Docker Web Service)

Aplikasi telah siap dideploy ke **Render** sebagai Docker Web Service:

1. Buat **Web Service** baru di dashboard Render dan hubungkan dengan repositori GitHub ini.
2. Pilih runtime **Docker**.
3. Atur Environment Variables di Render Dashboard:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_KEY` (hasil dari `php artisan key:generate --show`)
   - `LOG_CHANNEL=stderr`
   - `DB_CONNECTION=pgsql`
   - `DB_HOST=<supabase-pooler-host>`
   - `DB_PORT=6543` (atau port pooler Supabase)
   - `DB_DATABASE=postgres`
   - `DB_USERNAME=<supabase-user>`
   - `DB_PASSWORD=<supabase-password>`
   - `DB_SSLMODE=require`
   - `RUN_MIGRATIONS=true` (untuk auto-migrate skema dan seeder pada startup container)
4. Health-check endpoint otomatis tersedia di `/up` (mengembalikan HTTP 200 OK).
