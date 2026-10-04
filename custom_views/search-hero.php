<!-- 
  JDIH Modern Custom Component: Header & Search Hero (Theme: Hijau Tosca)
  Location: app-jdih/custom_views/search-hero.php
-->
<header class="jdih-navbar">
  <div class="jdih-container">
    <a href="/" class="jdih-brand">
      <div class="jdih-logo-icon">JDIH</div>
      <div class="jdih-brand-text">
        <span class="jdih-brand-title">JDIH PORTAL</span>
        <span class="jdih-brand-subtitle">Jaringan Dokumentasi & Informasi Hukum</span>
      </div>
    </a>
    <nav class="jdih-nav-menu">
      <a href="/" class="jdih-nav-link active">Beranda</a>
      <a href="/produk-hukum" class="jdih-nav-link">Produk Hukum</a>
      <a href="/monografi" class="jdih-nav-link">Monografi</a>
      <a href="/matriks" class="jdih-nav-link">Matriks</a>
      <a href="/tentang" class="jdih-nav-link">Tentang Kami</a>
    </nav>
  </div>
</header>

<section class="jdih-hero-section">
  <div class="jdih-hero-content">
    <h1 class="jdih-hero-title">Cari Dokumen & Produk Hukum</h1>
    <p class="jdih-hero-subtitle">Akses cepat, transparan, dan terintegrasi untuk Peraturan Daerah, Peraturan Wali Kota/Bupati, dan Keputusan Hukum.</p>

    <!-- Search Box Container -->
    <div class="jdih-search-box">
      <div class="jdih-search-input-wrapper">
        <svg class="jdih-search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input type="text" id="jdih-search-input" class="jdih-search-input" placeholder="Ketik kata kunci, nomor peraturan, atau judul dokumen..." autocomplete="off">
        <button type="button" id="btn-do-search" class="jdih-btn-primary">Cari Dokumentasi</button>
      </div>

      <!-- Quick Filter Chips -->
      <div class="jdih-filter-chips">
        <span class="jdih-chip-label">Filter Cepat:</span>
        <button type="button" class="jdih-chip active" data-type="all">Semua</button>
        <button type="button" class="jdih-chip" data-type="perda">Peraturan Daerah</button>
        <button type="button" class="jdih-chip" data-type="perwal">Peraturan Walikota/Bupati</button>
        <button type="button" class="jdih-chip" data-type="keputusan">SK / Keputusan</button>
      </div>
    </div>
  </div>
</section>
