# 05 — Keamanan & Aturan Validasi (Security & Rules)

Sistem menerapkan validasi ketat di sisi server untuk mencegah manipulasi kehadiran dan kecurangan presensi.

---

## 1. Keamanan QR Code (Token Security)

### 1.1 Struktur Payload QR Code
- Dilarang memuat informasi identitas mentah seperti `{"nama":"Budi","nim":"12345"}` di dalam QR Code.
- QR Code hanya memuat string token unik (UUID v4 / secure hash acak 64 karakter) yang digenerate sistem:
  ```text
  Format: urn:ukm-badminton:token:<uuid-v4>
  Contoh: urn:ukm-badminton:token:8f7e2a1b-3c4d-5e6f-7a8b-9c0d1e2f3a4b
  ```

### 1.2 Regenerasi & Revokasi Token
- Jika smartphone anggota hilang atau QR dicurigai bocor ke pihak luar, anggota atau sekretaris dapat menekan tombol **"Regenerate QR"** di profil.
- Token lama langsung tidak berlaku (invalid), token baru diterbitkan seketika.

---

## 2. Aturan Validasi Presensi (Server-Side Rules)

Setiap request `POST /api/attendance/scan` wajib melewati aturan berikut secara sekuensial:

1. **Otorisasi Petugas**: Pengguna pengirim request wajib memiliki peran `admin` atau `secretary`. Jika tidak, return `403 Forbidden`.
2. **Status Sesi**: Harus ada sesi latihan yang berstatus `ACTIVE`. Jika sesi sudah `CLOSED` atau tidak ada sesi aktif, return `422 Unprocessable Entity` ("Tidak ada sesi latihan yang sedang aktif").
3. **Validitas Token QR**: Token yang di-scan harus cocok dengan salah satu akun pengguna aktif di database. Jika tidak ditemukan, return `404 Not Found` ("QR Code tidak dikenali").
4. **Status Akun Anggota**: Akun anggota harus berstatus `APPROVED`. Jika masih `PENDING` atau `SUSPENDED`, return `403 Forbidden` ("Akun anggota belum disetujui pengurus").
5. **Anti-Duplikasi (Pencegahan Double-Check-In)**: Periksa tabel `attendances` dengan kombinasi `session_id` dan `user_id`. Jika rekaman sudah ada, return `409 Conflict` ("Anggota sudah tercatat hadir pada sesi ini pada pukul [Waktu]").
6. **Pencatatan Transaksi**: Jika seluruh syarat terpenuhi, simpan rekaman presensi dengan cap waktu presisi (`timestamp`), metode (`qr` atau `manual`), dan identitas sekretaris yang bertugas (`recorded_by`).

---

## 3. Perlindungan Terhadap Serangan Umum

- **CSRF Protection**: Seluruh form submit dan pemanggilan AJAX/Fetch wajib menyertakan token CSRF (`X-CSRF-TOKEN`).
- **Rate Limiting**: Endpoint scan presensi dibatasi maksimal 60 request per menit per IP pengurus untuk mencegah brute-force token scanner.
- **SQL Injection Prevention**: Seluruh query basis data wajib menggunakan Eloquent ORM atau PDO Prepared Statements parameter binding.
- **XSS Protection**: Seluruh output data teks pengguna di-escape secara otomatis via template engine Blade `{{ $variable }}`.
