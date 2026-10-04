<!-- 
  JDIH Modern Custom Component: Document List & Detail Modal (Theme: Hijau Tosca)
  Location: app-jdih/custom_views/document-list.php
-->
<div class="jdih-section-container">
  <div class="jdih-section-header">
    <h2 class="jdih-section-title">Dokumen Hukum Terbaru</h2>
    <div class="jdih-view-controls">
      <span class="jdih-result-count">Menampilkan <strong>12</strong> dokumen</span>
    </div>
  </div>

  <!-- Document Card Grid -->
  <div class="jdih-card-grid">
    
    <!-- Sample Card 1 (Berlaku) -->
    <article class="jdih-doc-card">
      <div class="jdih-card-header">
        <span class="jdih-badge jdih-badge-active">BERLAKU</span>
        <span class="jdih-doc-category">Peraturan Daerah</span>
      </div>
      <h3 class="jdih-doc-title">
        <a href="/produk-hukum/detail/102">Peraturan Daerah Nomor 5 Tahun 2024 tentang Penyelenggaraan Sistem Informasi Daerah</a>
      </h3>
      <p class="jdih-doc-snippet">Mengatur tata kelola integrasi data pemerintah daerah, keamanan informasi, serta standar layanan publik berbasis elektronik.</p>
      
      <div class="jdih-card-meta">
        <div class="jdih-meta-item">
          <svg class="jdih-meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          <span>Ditetapkan: 12 Juli 2024</span>
        </div>
        <div class="jdih-card-actions">
          <button type="button" class="jdih-btn-outline jdih-btn-preview" data-pdf-url="/documents/perda-5-2024.pdf">
            <svg class="jdih-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            Preview PDF
          </button>
          <a href="/documents/perda-5-2024.pdf" class="jdih-btn-primary" download>Unduh</a>
        </div>
      </div>
    </article>

    <!-- Sample Card 2 (Diubah) -->
    <article class="jdih-doc-card">
      <div class="jdih-card-header">
        <span class="jdih-badge jdih-badge-changed">DIUBAH</span>
        <span class="jdih-doc-category">Peraturan Walikota</span>
      </div>
      <h3 class="jdih-doc-title">
        <a href="/produk-hukum/detail/98">Peraturan Walikota Nomor 14 Tahun 2023 tentang Tata Cara Pengelolaan Retribusi Daerah</a>
      </h3>
      <p class="jdih-doc-snippet">Perubahan atas Peraturan Walikota Nomor 2 Tahun 2020 mengenai penyesuaian tarif retribusi pelayanan pasar dan kebersihan.</p>
      
      <div class="jdih-card-meta">
        <div class="jdih-meta-item">
          <svg class="jdih-meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          <span>Ditetapkan: 05 Maret 2023</span>
        </div>
        <div class="jdih-card-actions">
          <button type="button" class="jdih-btn-outline jdih-btn-preview" data-pdf-url="/documents/perwal-14-2023.pdf">
            <svg class="jdih-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            Preview PDF
          </button>
          <a href="/documents/perwal-14-2023.pdf" class="jdih-btn-primary" download>Unduh</a>
        </div>
      </div>
    </article>

  </div>
</div>

<!-- Floating PDF Reader Modal Container -->
<div id="jdih-pdf-modal" class="jdih-modal-backdrop" style="display:none;">
  <div class="jdih-modal-content">
    <div class="jdih-modal-header">
      <h3 id="pdf-modal-title">Pratinjau Dokumen Hukum</h3>
      <button type="button" class="jdih-modal-close" onclick="closePdfModal()">&times;</button>
    </div>
    <div class="jdih-modal-body">
      <iframe id="pdf-frame" src="" width="100%" height="500px" style="border:none;"></iframe>
    </div>
  </div>
</div>
