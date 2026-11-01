# 03 — Aktor & Matriks Hak Akses (Roles)

Sistem menggunakan kontrol akses berbasis peran (Role-Based Access Control / RBAC) yang dievaluasi di level middleware HTTP server.

---

## 1. Definisi Peran

### 1.1 Admin / Ketua UKM (`admin`)
Penanggung jawab tertinggi operasional UKM. Memiliki hak penuh atas konfigurasi sistem, manajemen akun pengurus, dan pengawasan rekapitulasi.

### 1.2 Sekretaris (`secretary`)
Petugas lapangan yang mengelola sesi latihan, presensi kehadiran, dan rekapitulasi data.
Tugas utama:
- Menyetujui pendaftaran akun anggota baru.
- Membuat, membuka, dan menutup sesi latihan.
- Mengoperasikan scanner kamera di lapangan.
- Melakukan presensi manual jika anggota terkendala perangkat.
- Mengunduh rekapitulasi presensi mingguan/bulanan.

### 1.3 Anggota (`member`)
Mahasiswa/peserta UKM Bulutangkis.
Tugas utama:
- Mendaftar dan melengkapi identitas diri (NIM, kampus, foto).
- Menunjukkan QR Badge digital saat tiba di lapangan.
- Melihat riwayat dan akumulasi persentase kehadiran pribadi.

---

## 2. Matriks Otorisasi Fitur

| Fitur / Tindakan | Admin | Sekretaris | Anggota | Tamu (Guest) |
|---|:---:|:---:|:---:|:---:|
| Registrasi Akun | - | - | - | ✅ |
| Login / Logout | ✅ | ✅ | ✅ | - |
| Lihat Profil Pribadi | ✅ | ✅ | ✅ | - |
| Edit Profil Pribadi | ✅ | ✅ | ✅ | - |
| Tampilkan QR Badge Pribadi | ✅ | ✅ | ✅ | - |
| Lihat Riwayat Presensi Sendiri | ✅ | ✅ | ✅ | - |
| Setujui Akun Anggota Baru | ✅ | ✅ | ❌ | - |
| Buat / Edit / Hapus Sesi Latihan | ✅ | ✅ | ❌ | - |
| Buka / Tutup Sesi Presensi | ✅ | ✅ | ❌ | - |
| Buka Kamera Scanner QR | ✅ | ✅ | ❌ | - |
| Submit Presensi QR | ✅ | ✅ | ❌ | - |
| Presensi Manual (Fallback) | ✅ | ✅ | ❌ | - |
| Lihat Rekapitulasi Seluruh Anggota | ✅ | ✅ | ❌ | - |
| Ekspor Laporan Excel / PDF | ✅ | ✅ | ❌ | - |
| Kelola Akun Pengurus (Promote Role) | ✅ | ❌ | ❌ | - |
| Akses Log Aktivitas Sistem | ✅ | ❌ | ❌ | - |

---

## 3. Aturan Middleware Server
- Setiap rute pengurus dilindungi middleware `role:admin,secretary`.
- Akses API submit presensi (`POST /attendance/scan`) wajib memvalidasi sesi aktif dan status pengguna aktif secara atomik di sisi server.
