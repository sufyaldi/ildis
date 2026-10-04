# Laporan Instalasi & Konfigurasi Kontainer JDIH (ILDIS)

**Tanggal Pelaksanaan:** 4 Oktober 2026  
**Lokasi Direktori:** `/home/iain/app-jdih`  
**Domain Subdomain:** `jdih.iainpare.ac.id`  

---

## 1. Ringkasan Eksekutif

Telah selesai dilakukan instalasi aplikasi **ILDIS (Indonesian Legal Documentation and Information System)** berbasis Docker Container resmi BPHN Kemenkumham pada server host dengan memanfaatkan reverse proxy existing (`nginx-proxy`) dan sertifikat SSL Let's Encrypt aktif.

---

## 2. Rincian Konfigurasi & Kredensial

| Parameter | Keterangan / Nilai |
| :--- | :--- |
| **URL Frontend** | [https://jdih.iainpare.ac.id](https://jdih.iainpare.ac.id) |
| **URL Backend (CMS)** | [https://jdih.iainpare.ac.id/backend](https://jdih.iainpare.ac.id/backend) |
| **Username Superadmin** | `admin` |
| **Password Superadmin** | `Adm1n@jdih` |
| **Tipe Database** | MariaDB 10.11 (Container) |
| **Nama Database** | `ildis_v4` |
| **Port Internal Host** | `8085` (HTTP) |
| **Port Publik Host** | `80` (HTTP Redirect) & `443` (HTTPS) |
| **SSL Certificate** | Let's Encrypt (`/etc/letsencrypt/live/ppid2.iainpare.ac.id/`) |

---

## 3. Komponen Kontainer Docker

Layanan kontainer berjalan di dalam network Docker `app-jdih_default`:

1. **`ildis_app`** (Image: `ghcr.io/bphndigitalservice/ildis:latest`):
   - Menjalankan core aplikasi PHP 8.3 & Web Server internal (Nginx).
   - Di-binding ke port host `8085:80` dan terhubung ke `nginx-proxy`.
2. **`ildis_mariadb`** (Image: `mariadb:10.11`):
   - Database internal terisolasi untuk data produk hukum JDIH.
   - Status: Healthy.
3. **`ildis_cron`** (Image: `ghcr.io/bphndigitalservice/ildis-cron:latest`):
   - Scheduler cron background job aplikasi ILDIS.

---

## 4. Konfigurasi Jaringan & Reverse Proxy

1. **Integrasi Docker Network**:
   - Kontainer `nginx-proxy` dihubungkan ke network Docker `app-jdih_default`:
     ```bash
     docker network connect app-jdih_default nginx-proxy
     ```
2. **Pengalihan Subdomain Nginx**:
   - File konfigurasi: `/home/iain/nginx/conf.d/ppid2.conf`
   - Blok `server_name jdih.iainpare.ac.id` diarahkan ke upstream:
     ```nginx
     proxy_pass http://ildis_app:80;
     ```
   - Backup konfigurasi sebelumnya disimpan di `/home/iain/nginx/conf.d/ppid2.conf.backup-before-ildis`.
3. **Status Layanan Lain**:
   - Layanan `ppid.iainpare.ac.id` telah diverifikasi tetap aktif dan tidak terganggu sama sekali (HTTP 200).

---

## 5. File Penting Proyek

- **`/home/iain/app-jdih/.env`**: Konfigurasi variabel lingkungan, secret key, dan kredensial database.
- **`/home/iain/app-jdih/docker-compose.yml`**: Definisi service, volume persistent, dan network kontainer.
- **`/home/iain/app-jdih/install.sh`**: Skrip installer resmi ILDIS.
- **`/home/iain/app-jdih/nginx/default.conf`**: Konfigurasi internal Nginx kontainer ILDIS.
- **`/home/iain/app-jdih/logs/`**: Log Nginx kontainer.

---

## 6. Panduan Pengelolaan & Operasional

### Menjalankan / Mematikan Layanan
```bash
# Masuk ke direktori
cd /home/iain/app-jdih

# Melihat status kontainer
docker compose ps

# Melihat log aplikasi secara realtime
docker compose logs -f app

# Menghentikan kontainer
docker compose down

# Menjalankan kontainer di background
docker compose up -d
```

### Mengaktifkan Google reCAPTCHA v3 (Opsional untuk Produksi)
Edit file `/home/iain/app-jdih/.env`:
```dotenv
RECAPTCHA_ENABLED=true
RECAPTCHA_SITE_KEY=<site_key>
RECAPTCHA_SECRET_KEY=<secret_key>
```
Lalu muat ulang:
```bash
docker compose -f /home/iain/app-jdih/docker-compose.yml --env-file /home/iain/app-jdih/.env up -d app
```


---

## 7. Riwayat Pembaruan (Update)

- **Waktu Eksekusi:** 4 Oktober 2026, 12:35 UTC
- **Perintah:** `./install.sh --update`
- **Cadangan Otomatis:** `/home/iain/app-jdih/backups/ildis_20261004_123452.sql.gz` (Ukuran: 699 KB)
- **Versi Terpasang Saat Ini:** `v4.18.2` (Versi rilis terbaru dari repo BPHN)
- **Status Migrasi Database:** Up-to-date (tidak ada migrasi tertunda)
- **Verifikasi Status:**
  - JDIH ([https://jdih.iainpare.ac.id](https://jdih.iainpare.ac.id)): `HTTP 200 OK`
  - PPID ([https://ppid.iainpare.ac.id](https://ppid.iainpare.ac.id)): `HTTP 200 OK`
