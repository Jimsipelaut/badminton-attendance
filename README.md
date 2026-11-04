# Badminton Attendance & Reporting System (UKM Bulutangkis)

Sistem operasional presensi digital dan rekapitulasi kehadiran berbasis web mobile-first untuk UKM Bulutangkis. Dirancang untuk menggantikan rekap presensi manual chat grup yang tidak efisien.

---

## 🎯 Fokus Utama
- **Presensi Cepat di Lapangan**: Pengurus/Sekretaris memindai QR Badge digital anggota via kamera smartphone.
- **Bebas Joki / Titip Absen**: Token QR unik terenkripsi dengan konfirmasi visual (foto wajah & identitas anggota tampil di layar scanner admin).
- **Fallback Manual Cepat**: Pencarian instan dan checklist kehadiran saat kamera/sinyal menghadapi kendala.
- **Rekapitulasi Otomatis**: Perhitungan persentase kehadiran per anggota, per sesi, dan per rentang tanggal dengan ekspor spreadsheet (Excel) & laporan cetak (PDF).
- **Ramping & Nol Biaya Tambahan**: Tanpa fitur tagihan finansial yang membebani alur kerja; ramah hosting resource terbatas.

---

## 📚 Struktur Dokumentasi
Dokumentasi teknis lengkap dan arsitektur tersimpan di folder `docs/`:
- [`AGENTS.md`](AGENTS.md) — Aturan kerja AI/Developer & Quality Gates.
- [`PROGRESS.md`](PROGRESS.md) — Roadmap fase & status pengerjaan saat ini.
- [`docs/DECISIONS.md`](docs/DECISIONS.md) — Log keputusan arsitektural (D-01 s/d D-06).
- [`docs/01-requirements.md`](docs/01-requirements.md) — Kebutuhan fungsional dan non-fungsional sistem.
- [`docs/02-mvp-and-priorities.md`](docs/02-mvp-and-priorities.md) — Klasifikasi prioritas fitur (P0 s/d P3).
- [`docs/03-actors-and-roles.md`](docs/03-actors-and-roles.md) — Definisi wewenang: Admin/Ketua, Sekretaris, Anggota.
- [`docs/04-flow-and-states.md`](docs/04-flow-and-states.md) — Alur sesi latihan dan siklus hidup presensi.
- [`docs/05-security-and-rules.md`](docs/05-security-and-rules.md) — Aturan keamanan token QR dan audit trail.
- [`docs/06-data-model.md`](docs/06-data-model.md) — Struktur tabel basis data relasional (MySQL).
- [`docs/07-architecture-ui-flow.md`](docs/07-architecture-ui-flow.md) — Desain antarmuka, navigasi mobile, dan arsitektur aplikasi.
- [`docs/08-deployment-guide.md`](docs/08-deployment-guide.md) — Panduan deployment produksi Alwaysdata (Rp0) / VPS & cron backup.

---

## 🚀 Panduan Cepat Menjalankan Sistem

### 1. Menjalankan di Lingkungan Lokal
```bash
# Salin konfigurasi environment
cp .env.example .env

# Pasang dependensi
composer install

# Buat application key
php artisan key:generate

# Migrasi & isi data awal (Admin, Sekretaris, Member default)
php artisan migrate:fresh --seed

# Hubungkan direktori storage foto profil
php artisan storage:link

# Jalankan server lokal
php artisan serve
```

Akun bawaan seeder:
- **Admin**: `admin@badminton.org` (Password: `password`)
- **Sekretaris**: `sekretaris@badminton.org` (Password: `password`)
- **Member**: `member@badminton.org` (Password: `password`)

### 2. Menjalankan Seluruh Automated Test
```bash
php artisan test
# Output: 25 tests passed, 201 assertions green
```

### 3. Deploy ke Produksi (Alwaysdata / VPS)
Ikuti panduan lengkap di [`docs/08-deployment-guide.md`](docs/08-deployment-guide.md) atau jalankan:
```bash
./deploy.sh
```

---

## 🛠️ Stack Teknologi
- **Backend / Web**: PHP / Laravel Framework (MVC)
- **Database**: MySQL / MariaDB
- **Frontend**: Blade, Tailwind CSS / Mobile-First Layout, HTML5 QR Scanner
- **Target Hosting**: Alwaysdata / Shared Web Hosting (HTTPS)
