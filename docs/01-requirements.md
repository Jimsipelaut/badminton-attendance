# 01 — Kebutuhan Sistem (Requirements)

Dokumen ini mendefinisikan kebutuhan fungsional dan non-fungsional sistem operasional presensi UKM Bulutangkis.

---

## 1. Kebutuhan Fungsional (Functional Requirements)

### 1.1 Manajemen Akun & Autentikasi (AUTH)
- **FR-AUTH-01**: Anggota dapat mendaftarkan akun baru dengan mengisi: Nama Lengkap, Email, Password, NIM, Asal Kampus/Fakultas, Nomor WhatsApp, dan Foto Profil.
- **FR-AUTH-02**: Akun anggota baru berstatus `pending` dan wajib disetujui (`approved`) oleh Sekretaris atau Admin sebelum dapat menggunakan fitur presensi.
- **FR-AUTH-03**: Pengguna dapat masuk (login) menggunakan Email dan Password yang telah terverifikasi.
- **FR-AUTH-04**: Pengguna dapat melihat dan memperbarui informasi profil mereka sendiri.

### 1.2 Manajemen Sesi Latihan (SESI)
- **FR-SESI-01**: Sekretaris/Admin dapat membuat sesi latihan baru dengan menentukan: Tanggal, Waktu Mulai, Waktu Selesai, Lokasi Lapangan, dan Catatan/Deskripsi.
- **FR-SESI-02**: Hanya ada **satu sesi aktif** dalam satu waktu untuk proses presensi langsung.
- **FR-SESI-03**: Sekretaris/Admin dapat membuka (`open`), menutup (`closed`), atau membatalkan (`cancelled`) sesi latihan.

### 1.3 Presensi & Pemindai QR (PRES)
- **FR-PRES-01**: Setiap akun anggota memiliki QR Badge digital yang memuat token acak unik (UUID) terikat akun.
- **FR-PRES-02**: Sekretaris/Admin dapat mengakses kamera pemindai (scanner) di peramban web HP untuk memindai QR Badge anggota.
- **FR-PRES-03**: Saat QR Badge berhasil dipindai, sistem menampilkan data validasi (Nama, Foto, NIM, Kampus) dan mencatat kehadiran anggota pada sesi aktif.
- **FR-PRES-04**: Sistem menolak presensi ganda (double-check-in) untuk anggota yang sama pada sesi yang sama.
- **FR-PRES-05**: Sistem menolak presensi jika sesi latihan belum dibuka atau sudah ditutup.
- **FR-PRES-06 (Fallback)**: Sekretaris dapat mencari nama/NIM anggota pada daftar anggota sesi aktif dan melakukan presensi manual jika terjadi kendala kamera/koneksi.

### 1.4 Dashboard & Rekapitulasi (REKAP)
- **FR-REKAP-01**: Anggota dapat melihat daftar riwayat sesi latihan yang pernah dihadirinya serta akumulasi persentase kehadiran pribadi.
- **FR-REKAP-02**: Sekretaris/Admin dapat memantau daftar kehadiran anggota secara real-time pada sesi yang sedang berlangsung.
- **FR-REKAP-03**: Sekretaris/Admin dapat melihat rekapitulasi kehadiran per sesi dan per periode rentang tanggal.
- **FR-REKAP-04**: Sekretaris/Admin dapat mengunduh/ekspor laporan rekapitulasi ke format spreadsheet Excel (.xlsx) dan format cetak PDF.

---

## 2. Kebutuhan Non-Fungsional (Non-Functional Requirements)

### 2.1 Performa & Responsivitas (NFR-PERF)
- Halaman scanner QR harus memuat modul kamera dalam waktu kurang dari 2 detik pada jaringan 4G standar.
- Proses verifikasi token QR ke pencatatan basis data maksimal 500 ms per pemindaian.

### 2.2 Kompatibilitas Mobile (NFR-COMP)
- Antarmuka web harus 100% responsif mobile-first, optimal dioperasikan pada layar smartphone 360px hingga 428px.
- Pemindai QR berjalan native di browser smartphone modern (Chrome Android & Safari iOS) via HTML5 WebRTC Camera API tanpa instalasi aplikasi toko (Play Store/App Store).

### 2.3 Efisiensi Penyimpanan (NFR-STOR)
- Sistem wajib mengompresi setiap unggahan foto profil sebelum disimpan ke storage lokal dengan batasan maksimal 50 KB per foto.
- Total footprint instalasi kode dan database harus aman beroperasi di batas kuota hosting 100 MB.

### 2.4 Keamanan Data (NFR-SEC)
- Seluruh komunikasi data wajib menggunakan protokol HTTPS.
- Kata sandi wajib di-hash menggunakan algoritma Bcrypt / Argon2id.
- Hak akses menu dibatasi secara ketat berdasarkan Role (RBAC) di sisi server, bukan sekadar menyembunyikan tombol UI.
