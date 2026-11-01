# 02 — Cakupan MVP & Prioritas Fitur

Sistem ini menganut prinsip pengiriman bertahap (incremental delivery) agar fungsi inti dapat segera digunakan di lapangan tanpa terhambat oleh fitur sekunder.

---

## 1. Klasifikasi Prioritas

### P0 — Wajib / Kritis (Cakupan Inti MVP)
Fitur yang wajib selesai agar sistem dapat berfungsi pada latihan mingguan UKM:
- Autentikasi dasar: Login, Register anggota, Approval akun oleh sekretaris.
- Manajemen Profil: Input Nama Lengkap, NIM, Asal Kampus, dan Unggah Foto Profil (terkompresi).
- Manajemen Sesi: Buat sesi baru, buka sesi, tutup sesi latihan.
- QR Badge Anggota: Penampil QR Code unik per anggota di dashboard mobile.
- Scanner QR Sekretaris: Kamera pemindai di web mobile pengurus dengan verifikasi instan.
- Anti-Duplikasi Presensi: Penolakan otomatis pemindaian berulang pada sesi yang sama.
- Fallback Manual: Cari nama anggota dan centang hadir manual saat kendala perangkat.
- Dashboard Kehadiran Real-time: Pengurus melihat nama-nama yang sudah hadir di lapangan.
- Ekspor Laporan Sederhana: Download rekapitulasi kehadiran format Excel (.xlsx).

### P1 — Penting (Rilis v1.1)
Fitur pendukung kenyamanan operasional setelah MVP stabil:
- Ekspor Laporan Cetak format PDF siap tanda tangan ketua/pembina UKM.
- Filter dan pencarian riwayat presensi berdasarkan rentang tanggal tertentu.
- Mode Fast-Scan (pemindaian beruntun tanpa modal konfirmasi tambahan saat antrean panjang).
- Log Aktivitas (audit trail untuk pencatatan siapa yang mengoreksi atau mengubah data hadir).

### P2 — Fitur Lanjutan (Direncanakan)
- Konfirmasi rencana kehadiran sebelum sesi dimulai (polling hadir/absen untuk estimasi kok/lapangan).
- Statistik tren kehadiran grafik mingguan/bulanan anggota.
- Notifikasi pengingat sesi latihan via integrasi WhatsApp Gateway / Web Push.

### P3 — Di Luar Cakupan (Dilarang Masuk Sistem)
Fitur yang secara eksplisit tidak akan dibuat:
- Modul pencatatan uang kas / tagihan / bukti transfer (dikelola terpisah via buku kas bendahara).
- Fitur chat / forum diskusi antar anggota (sudah terfasilitasi di grup WhatsApp).
- Feed media sosial / galeri dokumentasi foto latihan.
- Fitur booking lapangan eksternal atau pembayaran sewa gedung.

---

## 2. Kriteria Sukses MVP (Definition of Done)
1. Anggota baru dapat mendaftar dan disetujui sekretaris dalam < 1 menit.
2. Sekretaris dapat membuka sesi baru dan melakukan scan QR 20 anggota berturut-turut tanpa freeze/crash.
3. Anggota dapat melihat status hadir dan riwayat pribadinya seketika (real-time).
4. Sekretaris dapat mengunduh file rekapitulasi presensi akhir sesi dalam format Excel yang rapi.
