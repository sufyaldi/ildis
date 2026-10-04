# LAPORAN PEMELIHARAAN DAN PEMBARUAN SISTEM
## APLIKASI JARINGAN DOKUMENTASI DAN INFORMASI HUKUM (ILDIS / JDIH)
**INSTITUT AGAMA ISLAM NEGERI (IAIN) PAREPARE**

---

### IDENTITAS DOKUMEN

| Parameter | Keterangan |
| :--- | :--- |
| **Nomor Dokumen** | 002/TI/JDIH/X/2026 |
| **Jenis Kegiatan** | Pemeliharaan & Pembaruan Sistem (System Update & Maintenance) |
| **Nama Aplikasi** | ILDIS (Indonesian Legal Documentation and Information System) v4 |
| **Entitas Target** | Subdomain `jdih.iainpare.ac.id` |
| **Waktu Pelaksanaan** | Minggu, 04 Oktober 2026 - Pukul 12:35 UTC (20:35 WITA) |
| **Lokasi Direktori** | `/home/iain/app-jdih` |
| **Pelaksana** | Tim IT / Administrator Sistem |

---

### I. LATAR BELAKANG DAN TUJUAN

1. **Latar Belakang**:
   Dalam rangka menjaga keandalan, stabilitas, dan keamanan sistem layanan dokumentasi hukum di lingkungan IAIN Parepare, dilakukan pembaruan berkala terhadap aplikasi ILDIS sesuai dengan rilis patch resmi dari repositori nasional Badan Pembinaan Hukum Nasional (BPHN) Kemenkumham.
2. **Tujuan**:
   - Menjalankan rutinitas pembaruan aplikasi menggunakan skrip resmi `./install.sh --update`.
   - Mengambil versi image kontainer produksi terbaru dari GitHub Container Registry (`ghcr.io`).
   - Menerapkan patch bug fixes versi `v4.18.2` (perbaikan sinkronisasi totalCount pagination pada dokumen).
   - Memastikan tidak ada downtime atau gangguan interoperabilitas terhadap sistem lain di server yang sama (`ppid.iainpare.ac.id`).

---

### II. TAHAPAN PELAKSANAAN UPDATE

Prosedur pembaruan dilaksanakan secara sistematis mengikuti SOP pemeliharaan kontainer:

```
[1. Pre-Check] ──────> [2. Backup Database] ──────> [3. Image Pull]
                                                           │
[6. Verifikasi Akhir] <── [5. Restart & Migrasi] <── [4. Safe Container Stop]
```

#### 1. Pra-Pemeriksaan (Pre-Check)
- Memverifikasi integritas direktori kerja `/home/iain/app-jdih` dan file konfigurasi `.env`.
- Memeriksa ketersediaan versi rilis upstream pada repositori resmi `bphndigitalservice/ildis`.
- Mendeteksi versi berjalan dan target pembaruan: `v4.18.2`.

#### 2. Pencadangan Data Otomatis (Automated Backup)
Sebelum kontainer dimatikan, skrip melakukan dump database secara konsisten untuk mitigasi risiko:
- **Metode**: `mysqldump --single-transaction --routines --triggers`
- **File Hasil**: `/home/iain/app-jdih/backups/ildis_20261004_123452.sql.gz`
- **Ukuran File**: 699 KB (Terkompresi Gzip)
- **Status Cadangan**: **BERHASIL (Verified)**

#### 3. Penarikan Image Kontainer Baru (Image Pulling)
Melakukan sinkronisasi layer image kontainer dari GitHub Packages:
- `ghcr.io/bphndigitalservice/ildis:latest` (Core Application) -> **Pulled**
- `ghcr.io/bphndigitalservice/ildis-cron:latest` (Job Scheduler) -> **Pulled**
- `mariadb:10.11` (Database Engine) -> **Pulled & Up to Date**

#### 4. Penghentian Terjadwal & Isolasi Proses
- Kontainer `ildis_cron` dihentikan terlebih dahulu guna mencegah penulisan data di tengah proses migrasi.
- Kontainer aplikasi `ildis_app` dihentikan secara aman (graceful shutdown).

#### 5. Rekreasi Kontainer & Migrasi Skema (Recreation & Migration)
- Rekreasi kontainer `ildis_app` dengan layer image baru.
- Pengecekan kesiapan database MariaDB (`healthcheck: healthy`).
- Eksekusi Yii Database Migration Tool (Yii v2.0.55):
  - *Hasil*: Skema database telah selaras dan berada pada status terbaru (*No new migrations found. System is up-to-date*).
- Menghidupkan kembali service background scheduler `ildis_cron`.

---

### III. HASIL PENGUJIAN DAN VERIFIKASI POST-UPDATE

Pengujian fungsional dan konektivitas pasca-pembaruan dilakukan pada tingkat host dan gateway reverse proxy:

| Titik Uji / Parameter | Target URL / Host | Metode Uji | Respon HTTP | Status |
| :--- | :--- | :--- | :--- | :--- |
| **Port Internal Kontainer** | `http://127.0.0.1:8085` | cURL GET/HEAD | `HTTP/1.1 200 OK` | **Normal** |
| **Redirect Port 80 (HTTP)** | `http://jdih.iainpare.ac.id:80` | cURL -I | `HTTP/1.1 301 Moved Permanently` (To HTTPS) | **Normal** |
| **Layanan Publik JDIH (HTTPS)** | `https://jdih.iainpare.ac.id/` | cURL -k -I | `HTTP/1.1 200 OK` (X-Powered-By: PHP/8.3.35) | **Normal** |
| **CMS Backend Admin** | `https://jdih.iainpare.ac.id/backend` | cURL -k -I | `HTTP/1.1 302 Found` (Redirect to Login) | **Normal** |
| **Uji Dampak Sistem PPID** | `https://ppid.iainpare.ac.id/` | cURL -k -I | `HTTP/1.1 200 OK` (Odoo Web Service) | **Tidak Terdampak (Normal)** |

---

### IV. INVENTARIS ARTIFAK DAN FILE TERKAIT

1. **Dokumen Laporan Update**:
   - `/home/iain/app-jdih/temp/LAPORAN_UPDATE.md`
2. **Arsip Cadangan Database**:
   - `/home/iain/app-jdih/backups/ildis_20261004_123452.sql.gz`
3. **Log Eksekusi Update**:
   - Log sistem runtime: `/home/iain/app-jdih/logs/nginx/`
   - Log aplikasi Yii: `docker compose exec app cat /var/www/runtime/logs/app.log`

---

### V. KESIMPULAN DAN REKOMENDASI

1. **Kesimpulan**:
   Pembaruan aplikasi ILDIS (JDIH) IAIN Parepare ke versi `v4.18.2` telah berhasil dilaksanakan secara penuh tanpa kendala teknis. Layanan publik hukum dapat diakses kembali dengan performa optimal dan perlindungan enkripsi SSL Let's Encrypt yang valid.
2. **Rekomendasi Pemeliharaan**:
   - Menjadwalkan pengunduhan salinan arsip backup (`.sql.gz`) ke penyimpanan offsite secara berkala.
   - Melakukan peninjauan log kontainer mingguan menggunakan perintah `docker compose logs --tail=100 app`.

---
*Laporan ini disusun secara otomatis dan sistematis sebagai bukti pertanggungjawaban teknis pemeliharaan server.*
