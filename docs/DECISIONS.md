# DECISIONS — Log Keputusan Arsitektural

Catatan keputusan resmi yang mengikat jalannya proyek.

---

### D-01: Penghapusan Seluruh Modul Pembayaran, Tagihan, dan Kas
- **Tanggal**: 2026-11-01
- **Status**: Disetujui
- **Konteks**: Konsep awal memiliki modul tagihan otomatis per sesi, penandaan status Lunas oleh bendahara, dan rekap pembayaran. Ini memicu konflik wewenang penagihan tunai di lapangan antara sekretaris vs bendahara, kerumitan verifikasi transfer tanpa mutasi bank, dan pembengkakan kode.
- **Keputusan**: Seluruh fitur tagihan, status bayar, tunggakan, dan peran Bendahara dihapus total dari sistem.
- **Konsekuensi**: Sistem fokus 100% pada pencatatan presensi dan rekapitulasi kehadiran anggota. Kompleksitas berkurang drastis, risiko salah pencatatan uang hilang.

---

### D-02: Arah Presensi Berbasis Badge Scan (Sekretaris Scan HP Anggota)
- **Tanggal**: 2026-11-01
- **Status**: Disetujui
- **Konteks**: Konsep anggota scan QR di layar laptop sekretaris memiliki celah kritis: QR statis per sesi dapat difoto dan dikirim ke grup WhatsApp untuk di-scan joki dari luar lapangan.
- **Keputusan**: Alur dibalik. Setiap anggota memiliki QR Badge digital di dashboard akun masing-masing. Sekretaris membuka menu pemindai di HP admin/sekretaris dan memindai QR anggota yang hadir secara fisik di lapangan.
- **Konsekuensi**: Anggota wajib bertatap muka dengan sekretaris di meja presensi. Titip absen via forward foto QR gugur karena sekretaris melihat fisik orangnya.

---

### D-03: Isi QR Code Berupa Secure Unique Token (Bukan Plain Text Identitas)
- **Tanggal**: 2026-11-01
- **Status**: Disetujui
- **Konteks**: Jika QR berisi teks `Nama, NIM, Kampus`, siapa pun dapat memalsukan QR dengan generator bebas di internet, serta tidak dapat di-revoke jika terjadi kebocoran.
- **Keputusan**: QR Code anggota hanya menyimpan kode acak unik (UUID v4 / hash token) yang terikat pada `user_id`. Saat di-scan, scanner meminta verifikasi ke backend via endpoint terproteksi untuk menarik data identitas terkini.
- **Konsekuensi**: QR tidak dapat dipalsukan, payload data QR sangat kecil dan cepat dibaca oleh kamera smartphone low-end sekalipun.

---

### D-04: Verifikasi Visual Layar Scanner (Anti-Joki Screenshot)
- **Tanggal**: 2026-11-01
- **Status**: Disetujui
- **Konteks**: Mencegah skenario anggota titip screenshot QR ke temannya yang hadir di lapangan.
- **Keputusan**: Sesaat setelah QR terbaca oleh kamera sekretaris, sistem memunculkan kartu konfirmasi modal berisi: **Foto Profil Anggota**, **Nama Lengkap**, **NIM**, dan **Asal Kampus**. Sekretaris melihat wajah orang yang berdiri di depan meja presensi sebelum menekan konfirmasi hadir (atau auto-confirm jika mode cepat diaktifkan).
- **Konsekuensi**: Memerlukan anggota mengunggah foto profil yang jelas saat registrasi/melengkapi profil.

---

### D-05: Mekanisme Fallback Input Manual dengan Audit Trail
- **Tanggal**: 2026-11-01
- **Status**: Disetujui
- **Konteks**: Kendala di lapangan seperti baterai HP anggota habis, kamera HP sekretaris bermasalah, atau sinyal drop.
- **Keputusan**: Sekretaris disediakan fitur "Pencarian Cepat & Check-in Manual" pada sesi yang sedang aktif. Setiap presensi manual wajib dicatat dengan penanda `method = 'manual'` dan mencatat ID sekretaris yang melakukan input.
- **Konsekuensi**: Operasional presensi di lapangan tidak pernah terhenti karena kendala teknis perangkat.

---

### D-06: Kebijakan Penyimpanan Foto Profil & Batasan Storage
- **Tanggal**: 2026-11-01
- **Status**: Disetujui
- **Konteks**: Hosting Alwaysdata free tier memiliki kuota disk terbatas (100 MB). File foto mentah kamera smartphone (3–10 MB) akan menghabiskan ruang dalam hitungan hari.
- **Keputusan**: Foto profil wajib dikompresi di sisi klien/server sebelum disimpan (maksimal lebar/tinggi 400px, kualitas JPEG 75%, target ukuran < 50 KB). Sediakan avatar inisial otomatis jika anggota belum memiliki foto.
- **Konsekuensi**: Sistem dapat menampung ratusan data anggota tanpa melebihi kuota hosting gratis.

---

### D-07: Kerangka Dokumentasi Murni Operasional UKM
- **Tanggal**: 2026-11-01
- **Status**: Disetujui
- **Konteks**: Pengguna menegaskan dokumen bertujuan murni untuk operasional sistem UKM, bukan laporan skripsi berformat kaku.
- **Keputusan**: Menghapus duplikasi diagram struktural lama (DFD level 0-2). Mengadopsi diagram alur praktis (Activity & Sequence Diagram) dan Skema Relasional (ERD) standar industri web engineering.
- **Konsekuensi**: Dokumentasi to-the-point, mudah dipahami pengurus baru dan pengembang penerus tanpa jargon teoretis berlebih.
