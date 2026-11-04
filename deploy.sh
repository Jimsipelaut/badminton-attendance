#!/usr/bin/env bash
# Script Deployment Produksi Otomatis — Sistem Presensi UKM Bulutangkis
set -e

echo "=== [1/6] Memulai Mode Pemeliharaan ==="
php artisan down || true

echo "=== [2/6] Memperbarui Dependensi Composer ==="
composer install --no-dev --optimize-autoloader --no-interaction

echo "=== [3/6] Menjalankan Migrasi Basis Data ==="
php artisan migrate --force

echo "=== [4/6] Menghubungkan Storage Symlink ==="
php artisan storage:link || true

echo "=== [5/6] Mengoptimalkan Cache Route, Config & View ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== [6/6] Mematikan Mode Pemeliharaan ==="
php artisan up

echo "=== Deployment Selesai & Aplikasi Siap Diakses ==="
