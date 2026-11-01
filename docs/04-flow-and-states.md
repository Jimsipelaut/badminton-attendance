# 04 — Alur Proses & Siklus Status (Flow & States)

Dokumen ini memetakan alur presensi di lapangan dan siklus status data dalam sistem.

---

## 1. Alur Operasional di Lapangan (Sequence / Activity)

```text
  [Anggota]                     [Sekretaris]                   [Sistem Web]
      |                               |                             |
      |                               |-- 1. Buka Sesi Latihan ---->| Status sesi = ACTIVE
      |                               |-- 2. Buka Scanner Kamera -->| Kamera aktif
      |                               |                             |
      |-- 3. Tunjukkan QR Badge ----->|                             |
      |   di layar smartphone         |-- 4. Scan QR via Kamera --->| Kirim token QR
      |                               |                             |
      |                               |                             |-- 5. Validasi Token
      |                               |                             |   - Sesi aktif?
      |                               |                             |   - Anggota approved?
      |                               |                             |   - Belum hadir?
      |                               |                             |
      |                               |<-- 6. Tampilkan Modal ------| Kembalikan Data:
      |                               |   Foto, Nama, NIM, Kampus   | (Nama, Foto, NIM)
      |                               |                             |
      |                               |-- 7. Verifikasi Visual & ---| Catat status:
      |                               |      Konfirmasi Hadir       | ATTENDED
      |                               |<-- 8. Audio Beep / Sukses --| Update counter sesi
      |                               |                             |
      |<-- 9. Refresh Dashboard ------|                             | Realtime riwayat update
```

---

## 2. Alur Fallback Manual (Kamera / HP Bermasalah)

Jika smartphone anggota mati atau kamera sekretaris terkendala:
1. Anggota menyebutkan Nama atau NIM kepada sekretaris.
2. Sekretaris membuka tab **"Presensi Manual"** pada sesi aktif.
3. Sekretaris mengetik nama/NIM pada input pencarian cepat.
4. Klik tombol **"Hadirkan"**.
5. Sistem menyimpan presensi dengan penanda `method: manual` dan mencatat `scanned_by: [id_sekretaris]`.

---

## 3. Siklus Status (State Machine)

### 3.1 Status Akun Anggota (`users.status`)
```text
  [Daftar Akun] ---> PENDING ---> (Disetujui Sekretaris) ---> APPROVED
                        |
                        +--------> (Ditolak) --------------> REJECTED
```
- `PENDING`: Akun baru dibuat, belum bisa scan presensi.
- `APPROVED`: Akun aktif, QR Badge aktif dan dapat dipindai.
- `REJECTED` / `SUSPENDED`: Akun dinonaktifkan sementara/permanen.

### 3.2 Status Sesi Latihan (`sessions.status`)
```text
  [Buat Sesi] ---> SCHEDULED ---> (Mulai Latihan) ---> ACTIVE ---> (Selesai Latihan) ---> CLOSED
                       |
                       +--------> (Batal Latihan) ---> CANCELLED
```
- `SCHEDULED`: Sesi dijadwalkan, belum menerima presensi.
- `ACTIVE`: Sesi sedang berlangsung di lapangan, scanner menerima presensi anggota.
- `CLOSED`: Sesi berakhir, rekapitulasi dikunci, presensi tidak dapat ditambahkan lagi.
- `CANCELLED`: Sesi dibatalkan karena hujan/gedung tutup, tidak dihitung dalam persentase rekapitulasi.

### 3.3 Status Presensi Anggota (`attendances.status`)
- `PRESENT`: Anggota hadir dan terverifikasi di lapangan.
- `LATE`: Hadir melebihi toleransi waktu (opsional).
- `EXCUSED`: Izin resmi yang diajukan sebelum sesi (opsional).
- `ABSENT`: Tidak hadir tanpa keterangan (terhitung otomatis pada sesi yang sudah `CLOSED`).
