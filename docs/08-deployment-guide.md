# 08 — Panduan Deployment Produksi (Hosting Alwaysdata / VPS)

Dokumen ini menjelaskan langkah demi langkah untuk mendeploy sistem operasional presensi UKM Bulutangkis ke lingkungan produksi (Alwaysdata Free Tier 100 MB atau VPS Linux).

---

## 1. Prasyarat Lingkungan Produksi
- **PHP**: Versi 8.2 atau 8.3 dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`.
- **Database**: MySQL 8.0+ atau MariaDB 10.5+.
- **Web Server**: Apache (`mod_rewrite` aktif) atau Nginx.
- **Protokol**: HTTPS Wajib (Let's Encrypt SSL Gratis).
- **Composer**: Versi 2.x.

---

## 2. Langkah Setup di Hosting Alwaysdata (Rp0 Free Tier)

### 2.1 Pendaftaran & Buat Akun
1. Buka [alwaysdata.com](https://www.alwaysdata.com/) dan buat akun gratis (pilih subdomain gratis, misal `badminton-ukm.alwaysdata.net`).
2. Masuk ke Dashboard Administrasi Alwaysdata.

### 2.2 Konfigurasi Basis Data MySQL
1. Buka menu **Databases > MySQL**.
2. Buat basis data baru, misal: `badminton-ukm_db`.
3. Buat user database baru dan berikan hak akses penuh (`ALL PRIVILEGES`) ke basis data tersebut.
4. Catat Host (biasanya `mysql-badminton-ukm.alwaysdata.net`), Nama Database, Username, dan Password.

### 2.3 Upload Kode Proyek via SSH / Git
1. Akses terminal SSH Alwaysdata:
   ```bash
   ssh [username]@ssh-[subdomain].alwaysdata.net
   ```
2. Clone repositori ke direktori home:
   ```bash
   cd ~
   git clone https://github.com/Jimsipelaut/badminton-attendance.git
   cd badminton-attendance
   ```
3. Salin file environment produksi:
   ```bash
   cp .env.production.example .env
   ```
4. Buka `.env` via `nano .env` dan sesuaikan:
   - `APP_URL=https://badminton-ukm.alwaysdata.net`
   - `DB_HOST=mysql-badminton-ukm.alwaysdata.net`
   - `DB_DATABASE=badminton-ukm_db`
   - `DB_USERNAME=badminton-ukm`
   - `DB_PASSWORD=[password_anda]`
5. Generate `APP_KEY`:
   ```bash
   php artisan key:generate
   ```

### 2.4 Instalasi Dependensi & Migrasi
Jalankan script deploy otomatis:
```bash
chmod +x deploy.sh
./deploy.sh
```
Atau langkah manual:
```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2.5 Konfigurasi Web Server (Virtual Host)
1. Buka menu **Web > Sites** di dashboard Alwaysdata.
2. Edit entri situs yang ada:
   - **Type**: `PHP`
   - **PHP version**: `8.3`
   - **Application path**: `/home/[username]/badminton-attendance/public` *(PENTING: harus mengarah ke subfolder `/public`)*
3. Simpan perubahan.

### 2.6 Aktifkan SSL / HTTPS
1. Buka menu **Web > SSL Certificates**.
2. Klik tombol **Add an SSL Certificate**.
3. Pilih domain `badminton-ukm.alwaysdata.net` dan pilih penyedia **Let's Encrypt**.
4. Klik Submit. SSL otomatis aktif dan diperpanjang gratis.

---

## 3. Konfigurasi Cron Job (Jadwal Otomatis)
Alwaysdata menyediakan fasilitas Cron Jobs gratis untuk menjalankan tugas harian seperti backup database dan pembersihan sampah cache.

1. Buka menu **Environment > Scheduled Tasks**.
2. Tambahkan Scheduled Task baru:
   - **Command**: `/usr/bin/php /home/[username]/badminton-attendance/artisan schedule:run >> /dev/null 2>&1`
   - **Frequency**: `Every minute` (`* * * * *`)
3. Task ini akan otomatis mengeksekusi pencadangan database (`db:backup --prune=7`) setiap pukul 02:00 WIB.

---

## 4. Alur Pembaruan Kode (Continuous Deployment)
Jika ada perubahan fitur baru di GitHub:
```bash
cd ~/badminton-attendance
git pull origin main
./deploy.sh
```
Aplikasi akan masuk mode maintenance sementara, memperbarui migrasi/cache, lalu menyala kembali tanpa downtime panjang.

---

## 5. Prosedur Pemulihan Bencana (Disaster Recovery / Restore)
File backup SQL tersimpan di folder `storage/app/backups/`.
Untuk memulihkan database dari file backup tertentu:
```bash
mysql -h mysql-badminton-ukm.alwaysdata.net -u [username] -p [database_name] < storage/app/backups/backup_YYYY-MM-DD_HHMMSS.sql
```
Semua tabel dan presensi riil akan pulih seketika.
