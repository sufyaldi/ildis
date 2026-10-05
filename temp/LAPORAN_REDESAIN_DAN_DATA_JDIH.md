# LAPORAN RESMI REDESAIN UI/UX DAN PENGAYAAN DATA 
## JARINGAN DOKUMENTASI DAN INFORMASI HUKUM (JDIH) IAIN PAREPARE

---

| Parameter Sistem | Rincian |
| :--- | :--- |
| **Nama Aplikasi** | JDIH IAIN Parepare (Yii2 Framework PHP) |
| **Lingkungan Deployment** | Docker Containers (`ildis_app`, `ildis_mariadb`) |
| **Database** | MariaDB Database `ildis_v4` |
| **Tema Warna Utama** | **Hijau Tosca** (`#0d9488` / `#0f766e`) |
| **Total Dokumen Ter-import** | **157 Dokumen Peraturan & Pedoman** |
| **Total Berita Ter-import** | **47 Berita Hukum Terverifikasi** |
| **Lokasi File Laporan** | `/home/iain/app-jdih/temp/LAPORAN_REDESAIN_DAN_DATA_JDIH.md` |

---

### 1. PENDAHULUAN & TUJUAN UTAMA

Laporan ini disusun sebagai bentuk pertanggungjawaban teknis atas perombakan antarmuka pengguna (*UI/UX Redesign*), penyelarasan identitas visual instansi, serta pembersihan dan pengayaan data peraturan pada sistem **Jaringan Dokumentasi dan Informasi Hukum (JDIH) IAIN Parepare**. 

Perombakan ini bertujuan untuk:
1. Modernisasi tampilan sesuai standar visual lembaga Perguruan Tinggi Keagamaan Negeri (PTKN).
2. Memastikan integrasi data yang transparan dengan portal **JDIHN Nasional (bphn.go.id / peraturan.go.id)**.
3. Menyediakan repositori produk hukum internal (Pedoman, Ortaker, SPMI, Kode Etik, dan SK Rektor) secara lengkap dan mudah diakses oleh civitas akademika dan masyarakat luas.

---

### 2. REDESAIN UI/UX & PENYESUAIAN IDENTITAS VISUAL

#### A. Tema Warna Hijau Tosca
Sesuai arahan dan identitas institusi, seluruh elemen visual aplikasi telah diperbarui ke tema **Hijau Tosca**:
- **Design Tokens CSS**: Didefinisikan di `/home/iain/app-jdih/assets/css/jdih-modern.css` dan `/home/iain/app-jdih/assets/css/jdih-theme-override.css`.
- **Primary Color Palette**: `#0d9488` (Default Tosca), `#0f766e` (Hover / Active Darker Tosca), `#ccfbf1` (Light Background Accent).
- **Tombol "Lihat Semua Berita"**: Menggunakan gaya *Transparent Outline* di kondisi normal yang bertransisi menjadi *Full Solid Hijau Tosca* saat kursor diarahkan (*hover*).

#### B. Header & Footer Identity & Mobile Responsiveness
- **Dual-Logo Header & Brand Text**:
  - Berdampingan di sisi paling kiri: **Logo Resmi IAIN Parepare** (`logo_iain_rapat.png`, disesuaikan tinggi visualnya `54px`) dan **Logo JDIHN** (`48px`).
  - Teks Merek Utama: **JDIH**, pembatas vertikal `|`, teks **IAIN PAREPARE**, dan sub-judul **Jaringan Dokumentasi & Informasi Hukum**.
  - **Optimasi Responsif Mobile (Android/iOS)**:
    - Pada layar smartphone/mobile, teks panjang *"IAIN PAREPARE - Jaringan Dokumentasi & Informasi Hukum"* serta garis pembatas `|` di-hidden secara otomatis via Bootstrap responsive utility classes (`d-none d-md-flex`).
    - Hal ini memberikan ruang yang bersih dan luas bagi ikon **Menu Garis 3 (Hamburger Toggle)** di pojok kanan atas agar tidak tertutupi atau terdorong.
- **Footer**:
  - Alamat Resmi: *Jl. Amal Bakti No. 08, Soreang, Kota Parepare, Sulawesi Selatan*.
  - Kontak Email Resmi: `jdih@iainpare.ac.id`.
  - Hak Cipta & Kredit Sistem: **`© 2019 Institut Agama Islam Negeri (IAIN Parepare) · Powered by BPHN RI`**.

#### C. Layout Mounting via Docker
Perubahan tema visual diterapkan secara aman tanpa merusak struktur internal kerangka Yii2 dengan melakukan *volume mount* langsung di `docker-compose.yml`:
- Custom CSS Override (`assets/css/jdih-theme-override.css`)
- Custom Layout Main (`custom_views/main.php`)
- Custom Sub-Page Views (`custom_views/sekilas-sejarah.php`, `pengelola.php`, `visi.php`, `misi.php`, dll.)
- Custom Header Logo (`custom_views/logo_iainpare.png`) & Footer Partial Views.

---

### 3. PENGAYAAN & PEMBERSIHAN DATA BERITA HUKUM

Sistem telah melakukan penelusuran (*crawling*) pada portal publik `iainpare.ac.id` untuk kata kunci **"hukum"** dan berhasil memproses 304 artikel berita:

1. **Pembersihan Konten (Data Cleaning)**:
   - Menghapus seluruh skrip otomatis Odoo, widget HTML berlebih, dan kalimat tak berguna dari kalimat pembuka hingga karakter terakhir.
2. **Pencantuman Prefiks Resmi**:
   - Setiap konten berita diawali dengan standar pembuka: **`<b>JDIH IAIN PAREPARE - </b>`**.
3. **Hasil Impor Berita**:
   - **47 Artikel Berita Substantif** berhasil dimasukkan ke tabel `berita` pada database `ildis_v4` dengan status terpublikasi.

---

### 4. PENGAYAAN DATA PERATURAN & PEDOMAN (TOTAL 157 DOKUMEN)

Data peraturan diisi secara bertahap dan terverifikasi dari sumber-sumber kredibel:

| Kategori Dokumen | Jumlah Dokumen | Cakupan Substansi Utama |
| :--- | :---: | :--- |
| **Pedoman & Dokumen Mutu LPM** | 27 | SPMI, RTM, SOP Pembelajaran, MBKM, RPS, RPL, BKD, Tracer Study |
| **Folder Dokumen Utama Kampus & SPI** | 50 | Ortaker, Tata Kelola, RIP, Kode Etik Dosen/SPI/Tendik, SAKIP/SPIP |
| **Kemendiktisaintek & Kemendikbudristek** | 15 | Tukin Diktisaintek (Perpres 19/2025), Permen PMB PTN, Standar Pendidikan |
| **KemenPAN-RB & BKN** | 16 | E-Kinerja BKN (Perba 2/2026), Jabatan Fungsional Dosen, Disiplin ASN |
| **Kemenag & Peraturan Perundang-undangan** | 49 | PMA Ortaker PTKN, Perpres Transformasi IAIN, Tunjangan Jabatan |
| **TOTAL KESELURUHAN** | **157** | **Dokumen Peraturan & Pedoman Terpublikasi** |

---

### 5. PENANGANAN URL & SISTEM KARTU METADATA

Berdasarkan evaluasi antarmuka pada lembar detail dokumen:
- **Widget `Lampiran & Berkas`**: Khusus untuk file PDF lokal fisik yang dapat diunduh langsung dari server.
- **Informasi Tambahan > `SUMBER`**: 100% dari 157 dokumen telah dilengkapi tautan URL aktif (*Google Drive Resmi Kampus, Server LPM, atau Portal JDIHN peraturan.go.id*).

---

### 6. INTEGRASI JDIHN NATIONAL FEED

Seluruh dokumen yang dimasukkan telah memenuhi syarat harvesting Portal JDIHN Nasional:
- `is_publish = 1`
- `integrasi = 1`
- Struktur skema tabel sesuai standar BPHN Kementerian Hukum dan HAM RI.

---

### 7. PENUTUP & KESIMPULAN

Sistem **JDIH IAIN Parepare** kini telah bertransformasi menjadi portal repositori hukum modern, responsif, dan kaya akan data peraturan internal maupun nasional. Dengan skema warna Hijau Tosca dan 157 dokumen terstruktur, aplikasi ini siap menyajikan informasi hukum yang transparan dan akuntabel.

*Laporan ini dibuat dan disimpan secara otomatis di `/home/iain/app-jdih/temp/LAPORAN_REDESAIN_DAN_DATA_JDIH.md`.*
