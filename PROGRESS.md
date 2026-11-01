# PROGRESS

Status indikator: `[ ]` TODO · `[~]` IN PROGRESS · `[x]` DONE · `[!]` BLOCKED

Target: Sistem Operasional Presensi & Rekapitulasi UKM Bulutangkis (Mobile-First Web).

---

## Tabel Fase Pengembangan

| Fase | Nama Fase | Isi Kerja | Status |
|---|---|---|---|
| 1 | Konsep & Requirement | Finalisasi scope, batasan sistem, dokumen requirements & prioritas | [x] DONE |
| 2 | Perancangan Sistem | Aktor, alur/state, aturan keamanan, data model (ERD/skema), arsitektur UI | [x] DONE |
| 3 | Project Setup & Scaffold | Inisialisasi framework Laravel, database migration, seeding role awal | [ ] TODO |
| 4 | Authentication & Profile | Login, Register anggota, persetujuan akun sekretaris, profil (NIM, kampus, foto) | [ ] TODO |
| 5 | Manajemen Sesi Latihan | Buat/tutup sesi latihan oleh sekretaris, status sesi aktif | [ ] TODO |
| 6 | QR Presensi & Scanner | Generate QR Badge unik anggota, modul scanner kamera sekretaris, fallback manual | [ ] TODO |
| 7 | Dashboard & Rekapitulasi | Dashboard admin/sekretaris (kehadiran real-time), dashboard anggota, ekspor Excel/PDF | [ ] TODO |
| 8 | Pengujian Lapangan | Uji coba scan multi-perangkat di sesi latihan riil, penyesuaian UX | [ ] TODO |
| 9 | Deployment | Setup hosting produksi (Alwaysdata/VPS), konfigurasi domain, SSL & cron backup | [ ] TODO |

---

## Log Aktivitas
- **2026-11-01**: Inisialisasi repositori Git dan branch `main`.
- **2026-11-01**: Pemangkasan modul tagihan/kas (D-01). Perubahan alur presensi menjadi Admin-Scan-Anggota (D-02) dengan secure UUID token (D-03).
- **2026-11-01**: Fase 1 (Konsep & Requirement) & Fase 2 (Perancangan Desain Lengkap) rampung (`docs/01`–`docs/07` dan `DECISIONS.md`).
