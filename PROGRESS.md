# PROGRESS

Status indikator: `[ ]` TODO · `[~]` IN PROGRESS · `[x]` DONE · `[!]` BLOCKED

Target: Sistem Operasional Presensi & Rekapitulasi UKM Bulutangkis (Mobile-First Web).

---

## Tabel Fase Pengembangan

| Fase | Nama Fase | Isi Kerja | Status |
|---|---|---|---|
| 1 | Konsep & Requirement | Finalisasi scope, batasan sistem, dokumen requirements & prioritas | [x] DONE |
| 2 | Perancangan Sistem | Aktor, alur/state, aturan keamanan, data model (ERD/skema), arsitektur UI | [x] DONE |
| 3 | Project Setup & Scaffold | Inisialisasi framework Laravel, database migration, seeding role awal | [x] DONE |
| 4 | Authentication & Profile | Login, Register anggota, persetujuan akun sekretaris, profil (NIM, kampus, foto) | [x] DONE |
| 5 | Manajemen Sesi Latihan | Buat/tutup sesi latihan oleh sekretaris, status sesi aktif | [x] DONE |
| 6 | QR Presensi & Scanner | Generate QR Badge unik anggota, modul scanner kamera sekretaris, fallback manual | [x] DONE |
| 7 | Dashboard & Rekapitulasi | Dashboard admin/sekretaris (kehadiran real-time), dashboard anggota, ekspor Excel/PDF | [x] DONE |
| 8 | Pengujian Lapangan | Uji coba scan multi-perangkat di sesi latihan riil, penyesuaian UX | [x] DONE |
| 9 | Deployment | Setup hosting produksi (Alwaysdata/VPS), konfigurasi domain, SSL & cron backup | [ ] TODO |

---

## Log Aktivitas
- **2026-11-01**: Inisialisasi repositori Git dan branch `main`.
- **2026-11-01**: Pemangkasan modul tagihan/kas (D-01). Perubahan alur presensi menjadi Admin-Scan-Anggota (D-02) dengan secure UUID token (D-03).
- **2026-11-01**: Fase 1 (Konsep & Requirement) & Fase 2 (Perancangan Desain Lengkap) rampung (`docs/01`–`docs/07` dan `DECISIONS.md`).
- **2026-11-01**: Fase 3 DONE. Framework Laravel scaffolded, skema basis data MySQL selesai dimigrasi (users, training_sessions, attendances, activity_logs), seeder role akun awal (Admin, Sekretaris, Member) sukses di-seed dan diverifikasi.
- **2026-11-01**: Fase 4 DONE. Autentikasi multi-role (Admin, Sekretaris, Member), registrasi anggota auto UUID token QR, middleware approval & role, verifikasi akun pending oleh sekretaris, dashboard anggota, halaman QR Badge personal, dan pengujian feature test (4 passed, 16 assertions).
- **2026-11-01**: Fase 5 DONE. Manajemen Sesi Latihan (penjadwalan sesi baru, aktivasi sesi dengan auto-close sesi lama agar tunggal aktif FR-SESI-02, penutupan sesi, pembatalan sesi, banner sesi aktif di dashboard pengurus, dan proteksi akses RBAC). Pengujian feature test sesi lolos (4 passed, 14 assertions). Total 10 tests green.
- **2026-11-01**: Fase 6 DONE. Modul QR Presensi & Scanner kamera HTML5 WebRTC (`html5-qrcode`), kartu verifikasi visual anti-joki (tampilkan foto, NIM, nama, kampus FR-ABSEN-02), feedback sintetis Web Audio API beep, pencegahan presensi ganda di sesi yang sama (FR-ABSEN-03), fallback presensi manual sekretaris (FR-ABSEN-04), dan daftar hadir sesi real-time. Pengujian 8 test case lolos (total suite: 18 tests green, 53 assertions).
- **2026-11-01**: Fase 7 DONE. Modul Dashboard & Rekapitulasi Presensi (rekap per sesi dengan filter rentang tanggal, rekap per anggota dengan kalkulasi persentase kehadiran, halaman detail kehadiran per sesi latihan, dan stream ekspor file CSV standar UTF-8 BOM untuk Microsoft Excel & Google Sheets tanpa beban library berat). Pengujian 5 test case lolos (total suite: 23 tests green, 72 assertions).
- **2026-11-01**: Fase 8 DONE. Pengujian Lapangan & Penyesuaian UX Riil: simulasi pemindaian beruntun 20 anggota di lapangan (< 500ms per scan NFR-PERF), validasi anti-duplikasi, penolakan akun pending, fallback manual dengan catatan alasan, sinkronisasi realtime status hadir di riwayat anggota, dan ekspor CSV konsisten. Penyesuaian UX: Mode Fast-Scan (antrean padat dengan cooldown 1.0s), haptic vibration feedback via `navigator.vibrate` untuk suasana bising GOR, WakeLock API & tips kecerahan layar pada QR Badge anggota, serta filter pencarian instan pada tab presensi manual. Total suite: 25 tests green, 201 assertions.
