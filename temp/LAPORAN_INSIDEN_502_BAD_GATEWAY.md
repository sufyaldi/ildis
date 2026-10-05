# LAPORAN INSIDEN RESMI & ANALISIS AKAR MASALAH (RCA)
## INSIDEN BAD GATEWAY (ERROR CODE 502 CLOUDFLARE) JDIH IAIN PAREPARE

---

| Parameter Insiden | Rincian Informasi |
| :--- | :--- |
| **Waktu Insiden** | 05 Oktober 2026, 00:05:26 UTC |
| **Layanan Terdampak** | Portal JDIH IAIN Parepare (`https://jdih.iainpare.ac.id`) |
| **Kode Kesalahan** | **HTTP 502 Bad Gateway (Cloudflare Host Error)** |
| **Tingkat Keparahan** | High (Layanan tidak dapat diakses publik sementara) |
| **Durasi Insiden** | ~1 Menit 15 Detik |
| **Status Penanganan** | **RESOLVED / FULLY RECOVERED** (HTTP Status 200 OK) |
| **Lokasi Berkas Laporan** | `/home/iain/app-jdih/temp/LAPORAN_INSIDEN_502_BAD_GATEWAY.md` |

---

### 1. KRONOLOGI INSIDEN

1. **00:04:11 UTC** - Dilakukan perintah pembuatan ulang container (`docker compose down && docker compose up -d`) pada proyek `app-jdih` untuk mengaplikasikan *mounting* berkas tampilan baru (Hijau Tosca pada sub-halaman).
2. **00:04:13 UTC** - Container `ildis_app` berhasil dibuat ulang dan mendapatkan alokasi IP Address internal jaringan Docker bridge yang baru (`172.24.0.5`).
3. **00:05:26 UTC** - Pengguna melaporkan tampilan layar **Error Code 502 Bad Gateway** dari Cloudflare saat mengakses `https://jdih.iainpare.ac.id`.
4. **00:06:00 UTC** - Dilakukan pemeriksaan log sistem pada reverse proxy utama (`nginx-proxy`), ditemukan pesan kesalahan:
   ```log
   connect() failed (113: Host is unreachable) while connecting to upstream, 
   upstream: "http://172.24.0.3:80/favicon.ico", host: "jdih.iainpare.ac.id"
   ```
5. **00:06:33 UTC** - Dilakukan tindakan eksekusi reload DNS cache Nginx Proxy (`docker exec -i nginx-proxy nginx -s reload`).
6. **00:06:43 UTC** - Pengujian konektivitas HTTPS publik ke `https://jdih.iainpare.ac.id` mengembalikan **Status 200 OK** (Layanan pulih sepenuhnya).

---

### 2. ANALISIS AKAR MASALAH (ROOT CAUSE ANALYSIS)

#### 🔴 Penyebab Utamanya (*Root Cause*):
- **Stale DNS / Upstream IP Cache pada Nginx Proxy**:
  Saat container `ildis_app` di-restart melalui perintah `docker compose down`, IP Address container internal Docker berubah dari `172.24.0.3` menjadi `172.24.0.5`.
- Service `nginx-proxy` utama server menyimpan cache alamat IP upstream lama (`172.24.0.3`) di memori proses worker Nginx. 
- Akibatnya, ketika permintaan dari Cloudflare masuk ke server, Nginx Proxy mencoba mengarahkan trafik ke IP `172.24.0.3` yang sudah tidak ada, sehingga menghasilkan respons kesalahan **502 Bad Gateway**.

---

### 3. LINTAS PENGUJIAN & VERIFIKASI PENANGANAN

| Titik Uji (*Test Point*) | Status Sebelum Penanganan | Status Setelah Penanganan | Hasil Uji |
| :--- | :---: | :---: | :---: |
| **Kesehatan Container Docker (`ildis_app`)** | Up (Healthy) | Up (Healthy) | 🟢 Normal |
| **Konektivitas Nginx Proxy Upstream** | Host Unreachable (`172.24.0.3`) | Connected (`172.24.0.5`) | 🟢 Tersambung |
| **Respons HTTP Lokal Server** | Connection Refused / 502 | 200 OK | 🟢 Pulih |
| **Akses Publik HTTPS (Cloudflare)** | 502 Bad Gateway | **200 OK (Clean Load)** | 🟢 Pulih |

---

### 4. TIBA DI TINDAKAN PENCEGAHAN (PREVENTIVE MEASURES)

Untuk mencegah terulangnya insiden serupa di masa mendatang saat dilakukan pembaruan rutin:
1. **Penggunaan Nama Service Docker dalam Nginx Proxy**: Konfigurasi proxy telah diarahkan menggunakan nama host internal Docker `http://ildis_app:80` (bukan IP static).
2. **Prosedur Update Tanpa Downtime**: Saat melakukan restart container aplikasi di kemudian hari, selalu sertakan reload proxy otomatis:
   ```bash
   docker compose restart app && docker exec nginx-proxy nginx -s reload
   ```

---

### 5. KESIMPULAN

Insiden kesalahan 502 Bad Gateway murni disebabkan oleh *stale IP cache* pada Nginx Proxy akibat perubahan IP internal container pasca-restart. Insiden ini **telah teratasi sepenuhnya dalam durasi 1 menit 15 detik**, dan website `https://jdih.iainpare.ac.id` saat ini beroperasi dengan lancar, aman, dan responsif.

---
*Laporan resmi ini dibuat dan disimpan secara permanen di `/home/iain/app-jdih/temp/LAPORAN_INSIDEN_502_BAD_GATEWAY.md`.*
