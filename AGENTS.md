# AGENTS.md — Aturan untuk AI / Pengembang

Berlaku untuk seluruh AI agent (Hermes, Claude, ChatGPT, Copilot) atau kontributor manusia yang mengerjakan repositori ini.

---

## 1. Sumber Kebenaran (Urutan Otoritas)
1. `docs/DECISIONS.md` (keputusan terbaru mengikat)
2. `docs/02-mvp-and-priorities.md` & `docs/01-requirements.md`
3. Dokumen desain: `docs/03`–`docs/07`
4. `PROGRESS.md`
5. Percakapan chat / instruksi ad-hoc (sementara; wajib dicatat ke `docs/DECISIONS.md` agar sah)

Jika ada konflik antar dokumen: **tunjukkan konfliknya secara eksplisit, jangan memilih diam-diam.**

---

## 2. Aturan Kerja Sekuensial
- Kerjakan **satu fase pada satu waktu** (merujuk pada tabel `PROGRESS.md`).
- Alur fase: Jelaskan apa yang dikerjakan → Tulis/ubah kode/dokumen → Verifikasi perilaku → Tandai `[x] DONE` di `PROGRESS.md` → Lanjut fase berikutnya.
- **Dilarang menulis kode implementasi** sebelum dokumen desain (`docs/`) disepakati dan Fase Desain ditandai DONE.
- Jangan mengubah scope atau requirement tanpa persetujuan pemilik proyek. Setiap perubahan wajib masuk `docs/DECISIONS.md`.
- Fitur baru wajib diklasifikasikan: P0 (Kritis/MVP), P1 (Penting), P2 (Nanti), P3 (Di luar cakupan).

---

## 3. Batasan Keras & Anti-Slop
- **Dilarang modul pembayaran / tagihan / kas**: Fokus sistem murni presensi dan rekapitulasi kehadiran anggota UKM (D-01).
- **Dilarang over-engineering**: Tidak ada microservices, enterprise message bus, atau multi-tenancy yang tidak dibutuhkan satu organisasi UKM.
- **Arah presensi**: Sekretaris/Admin yang melakukan scan terhadap QR Badge anggota di lokasi lapangan (D-02).
- **Keamanan QR**: QR Code anggota tidak boleh berisi string teks mentah identitas. Wajib berisi token unik yang divalidasi ke database via HTTPS (D-03).
- **Pencegahan titip absen**: Verifikasi visual (foto profil & nama muncul di layar scanner sekretaris) (D-04).
- **Fallback**: Wajib menyediakan pencarian manual anggota untuk sesi aktif saat kendala teknis/kamera (D-05).
- **Storage & Biaya**: Tetap patuh pada batas shared hosting / free tier (kompresi foto profil, avatar inisial fallback) (D-06).

---

## 4. Aturan Penulisan Kode & Commit
- Tulis kode lengkap saat membuat file baru (cantumkan path absolut/relatif yang tepat).
- Pisahkan logika bisnis dari tampilan (MVC: Model, View, Controller).
- Jangan commit file kredensial (`.env`, `credentials.json`, secret keys).
- Setiap commit wajib memiliki pesan deskriptif.

---

## 5. Quality Gate Penyelesaian Fase
Sebelum menandai fase selesai, verifikasi:
1. Sesuai requirement documented?
2. Tidak ada penambahan fitur tersembunyi (scope creep)?
3. Arsitektur data konsisten dengan `docs/06-data-model.md`?
4. Hak akses dan otorisasi aman (Role-Based Access Control)?
5. Flow mudah digunakan di lapangan (mobile-first)?
