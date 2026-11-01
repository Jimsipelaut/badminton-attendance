# 07 — Arsitektur & Alur Antarmuka Pengguna (UI Flow)

Dokumen ini menguraikan struktur aplikasi berbasis Laravel MVC, peta rute halaman, dan hierarki navigasi mobile-first.

---

## 1. Arsitektur Aplikasi (Laravel MVC)

```text
  [Peramban Web Klien (Mobile Browser)]
                  |  HTTPS Request
                  v
       [Rute HTTP (routes/web.php)]
                  |
         +--------+--------+
         | Middleware Auth |
         | Middleware RBAC |
         +--------+--------+
                  |
                  v
            [Controllers]
       - AuthController
       - SessionController
       - AttendanceScanController
       - MemberDashboardController
       - ReportController
                  |
         +--------+--------+
         |                 |
         v                 v
     [Models]       [Views / Blade]
     - User         - Layout Mobile-First
     - Session      - QR Badge Member
     - Attendance   - Camera Scanner
                    - Report Tables
```

---

## 2. Peta Rute URL (URL Routing)

### 2.1 Area Publik / Tamu (Guest)
- `GET /login` — Form login pengguna.
- `POST /login` — Proses autentikasi.
- `GET /register` — Form pendaftaran akun anggota.
- `POST /register` — Proses pendaftaran akun (status otomatis `pending`).

### 2.2 Area Anggota (`role:member`)
- `GET /dashboard` — Beranda anggota: kartu status keanggotaan, jadwal sesi terdekat, persentase kehadiran.
- `GET /my-badge` — Halaman QR Badge digital (layar penuh, tombol kecerahan layar, instruksi scan).
- `GET /my-history` — Riwayat presensi sesi latihan yang pernah dihadiri.
- `GET /profile` — Tinjau & edit data profil serta foto anggota.

### 2.3 Area Sekretaris & Admin (`role:secretary,admin`)
- `GET /admin/dashboard` — Ringkasan metrik: sesi aktif hari ini, jumlah anggota hadir, total anggota terdaftar.
- `GET /admin/sessions` — Manajemen daftar sesi latihan (CRUD).
- `POST /admin/sessions/{id}/activate` — Buka sesi latihan agar menerima presensi.
- `POST /admin/sessions/{id}/close` — Tutup sesi latihan.
- `GET /admin/sessions/{id}/scanner` — Halaman kamera scanner QR terintegrasi via HTML5 WebRTC.
- `POST /api/attendance/scan` — Endpoint AJAX/Fetch penerima data scan QR.
- `POST /admin/sessions/{id}/manual-checkin` — Endpoint pencatatan presensi manual fallback.
- `GET /admin/members/pending` — Daftar persetujuan akun anggota baru.
- `POST /admin/members/{id}/approve` — Tombol setujui pendaftaran anggota.
- `GET /admin/reports` — Filter rekapitulasi kehadiran per sesi atau rentang tanggal.
- `GET /admin/reports/export-excel` — Unduh file rekapitulasi format spreadsheet .xlsx.

---

## 3. Desain UI Flow (Alur Layar Pengguna)

### 3.1 Layar Anggota (Mobile Badge)
1. Anggota login via smartphone.
2. Menu navigasi bawah (Bottom Navigation Bar):
   - **Beranda** (Jadwal sesi & persentase kehadiran).
   - **QR Badge** (QR Code besar di tengah layar, nama, NIM, foto avatar).
   - **Riwayat** (Daftar sesi yang dihadiri beserta tanggal & jam).
   - **Profil** (Pengaturan data diri).

### 3.2 Layar Scanner Pengurus (Mobile Scanner)
1. Sekretaris membuka menu **"Scanner Sesi Aktif"**.
2. Viewfinder kamera terbuka di tengah layar.
3. Kamera diarahkan ke QR Badge smartphone anggota.
4. Pop-up instan / toast konfirmasi:
   - "✅ Berhasil Presensi: **Budi Santoso (NIM: 1221045)** - Kampus A"
   - Suara beep sukses terdengar.
5. Counter kehadiran langsung bertambah (+1) tanpa reload halaman.
6. Tombol beralih ke mode **"Input Manual"** tersedia di bawah viewfinder kamera jika QR bermasalah.
