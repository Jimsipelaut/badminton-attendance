# 06 — Model Data & Skema Database (MySQL)

Skema database dirancang relasional, efisien, dan dinormalisasi untuk mendukung kebutuhan presensi dan rekapitulasi cepat.

---

## 1. Diagram Relasi Entitas (ERD Konseptual)

```text
  +------------------+          +--------------------+
  |      users       | 1      * |    attendances     |
  +------------------+----------+--------------------+
  | id (PK)          |          | id (PK)            |
  | name             |          | session_id (FK)    |---+
  | email            |          | user_id (FK)       |   |
  | password         |          | check_in_time      |   |
  | nim              |          | method (qr/manual) |   |
  | university       |          | status             |   |
  | phone_number     |          | notes              |   |
  | avatar_url       |          | recorded_by (FK)   |   |
  | qr_token (UQ)    |          +--------------------+   |
  | role             |                                   |
  | status           |                                   |
  +------------------+                                   |
           | 1                                           |
           |                                             |
           | *                                           | *
  +------------------+ 1                                 |
  |  training_sess.  |-----------------------------------+
  +------------------+
  | id (PK)          |
  | title            |
  | session_date     |
  | start_time       |
  | end_time         |
  | venue            |
  | status           |
  | created_by (FK)  |
  +------------------+
```

---

## 2. Rincian Kamus Data (Tabel)

### 2.1 Tabel `users`
Menyimpan seluruh entitas pengguna (Admin, Sekretaris, Anggota).

| Kolom | Tipe Data | Nullable | Keterangan |
|---|---|:---:|---|
| `id` | BIGINT UNSIGNED AUTO_INCREMENT | No | Primary Key |
| `name` | VARCHAR(100) | No | Nama lengkap pengguna |
| `email` | VARCHAR(100) | No | Email unik (login) |
| `password` | VARCHAR(255) | No | Hash Bcrypt |
| `nim` | VARCHAR(30) | No | Nomor Induk Mahasiswa |
| `university` | VARCHAR(100) | No | Asal Kampus / Fakultas |
| `phone_number` | VARCHAR(25) | Yes | Nomor WhatsApp aktif |
| `avatar_path` | VARCHAR(255) | Yes | Path foto profil lokal terkompresi |
| `qr_token` | VARCHAR(64) | No | Unique UUID/token acak untuk QR Badge |
| `role` | ENUM('admin', 'secretary', 'member') | No | Default: 'member' |
| `status` | ENUM('pending', 'approved', 'rejected') | No | Default: 'pending' |
| `approved_at` | TIMESTAMP | Yes | Waktu disetujui |
| `approved_by` | BIGINT UNSIGNED | Yes | FK ke users.id (pengurus yang menyetujui) |
| `created_at` | TIMESTAMP | Yes | Standar Laravel |
| `updated_at` | TIMESTAMP | Yes | Standar Laravel |

*Indeks:*
- `UNIQUE INDEX users_email_unique (email)`
- `UNIQUE INDEX users_qr_token_unique (qr_token)`
- `INDEX users_nim_index (nim)`

---

### 2.2 Tabel `training_sessions`
Menyimpan jadwal dan status sesi latihan UKM.

| Kolom | Tipe Data | Nullable | Keterangan |
|---|---|:---:|---|
| `id` | BIGINT UNSIGNED AUTO_INCREMENT | No | Primary Key |
| `title` | VARCHAR(100) | No | Judul sesi (cth: "Latihan Rutin Minggu #14") |
| `session_date` | DATE | No | Tanggal pelaksanaan |
| `start_time` | TIME | No | Jam mulai sesi |
| `end_time` | TIME | No | Jam selesai sesi |
| `venue` | VARCHAR(100) | No | Nama lapangan / GOR |
| `notes` | TEXT | Yes | Catatan agenda latihan |
| `status` | ENUM('scheduled', 'active', 'closed', 'cancelled') | No | Default: 'scheduled' |
| `created_by` | BIGINT UNSIGNED | No | FK ke users.id (pembuat sesi) |
| `created_at` | TIMESTAMP | Yes | Standar Laravel |
| `updated_at` | TIMESTAMP | Yes | Standar Laravel |

*Indeks:*
- `INDEX sessions_date_status_index (session_date, status)`

---

### 2.3 Tabel `attendances`
Menyimpan rekaman presensi kehadiran anggota di sesi latihan.

| Kolom | Tipe Data | Nullable | Keterangan |
|---|---|:---:|---|
| `id` | BIGINT UNSIGNED AUTO_INCREMENT | No | Primary Key |
| `training_session_id` | BIGINT UNSIGNED | No | FK ke training_sessions.id |
| `user_id` | BIGINT UNSIGNED | No | FK ke users.id (anggota yang hadir) |
| `check_in_time` | TIMESTAMP | No | Waktu presensi dicatat |
| `method` | ENUM('qr_scan', 'manual') | No | Default: 'qr_scan' |
| `status` | ENUM('present', 'late', 'excused') | No | Default: 'present' |
| `notes` | VARCHAR(255) | Yes | Alasan (khusus presensi manual/izin) |
| `recorded_by` | BIGINT UNSIGNED | No | FK ke users.id (sekretaris bertugas) |
| `created_at` | TIMESTAMP | Yes | Standar Laravel |
| `updated_at` | TIMESTAMP | Yes | Standar Laravel |

*Indeks & Constraints:*
- `UNIQUE INDEX attendance_session_user_unique (training_session_id, user_id)` (Mencegah presensi ganda)
- `FOREIGN KEY (training_session_id) REFERENCES training_sessions(id) ON DELETE CASCADE`
- `FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`
- `FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE RESTRICT`

---

### 2.4 Tabel `activity_logs` (P1)
Mencatat audit trail perubahan manual atau pembatalan status presensi.

| Kolom | Tipe Data | Nullable | Keterangan |
|---|---|:---:|---|
| `id` | BIGINT UNSIGNED AUTO_INCREMENT | No | Primary Key |
| `user_id` | BIGINT UNSIGNED | No | FK ke users.id (pelaku aksi) |
| `action` | VARCHAR(50) | No | Jenis aksi (cth: "approve_user", "manual_attendance") |
| `description` | TEXT | No | Rincian aktivitas |
| `created_at` | TIMESTAMP | Yes | Waktu kejadian |
