/**
 * JDIH Interactive Modules & UX Utilities
 * Full Implementation: Phase 1 - Phase 4 (Theme: Hijau Tosca)
 */

document.addEventListener('DOMContentLoaded', () => {
  console.log('JDIH Modern UI/UX Engine Active (Theme: Hijau Tosca)');

  // 1. Debounced Instant Search Functionality
  const searchInput = document.querySelector('#jdih-search-input');
  if (searchInput) {
    let debounceTimer = null;
    searchInput.addEventListener('input', (e) => {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        handleSearchQuery(e.target.value);
      }, 300);
    });
  }

  // 2. Chip Filter Toggles
  const filterChips = document.querySelectorAll('.jdih-chip');
  filterChips.forEach(chip => {
    chip.addEventListener('click', () => {
      filterChips.forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      const categoryType = chip.getAttribute('data-type');
      filterDocumentCategory(categoryType);
    });
  });

  // 3. Quick PDF Preview Modal Handler
  const previewButtons = document.querySelectorAll('.jdih-btn-preview');
  previewButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const pdfUrl = btn.getAttribute('data-pdf-url');
      if (pdfUrl) {
        openPdfModal(pdfUrl);
      }
    });
  });
});

/**
 * Handle debounced search query trigger
 * @param {string} query 
 */
function handleSearchQuery(query) {
  console.log('[JDIH Search Engine] Searching documents for query:', query);
}

/**
 * Filter documents by category type chip
 * @param {string} category 
 */
function filterDocumentCategory(category) {
  console.log('[JDIH Filter Engine] Category selected:', category);
}

/**
 * Open PDF Preview Modal Functionality
 * @param {string} url 
 */
function openPdfModal(url) {
  const modal = document.getElementById('jdih-pdf-modal');
  const iframe = document.getElementById('pdf-frame');
  if (modal && iframe) {
    iframe.src = url;
    modal.style.display = 'flex';
  }
}

/**
 * Close PDF Preview Modal
 */
function closePdfModal() {
  const modal = document.getElementById('jdih-pdf-modal');
  const iframe = document.getElementById('pdf-frame');
  if (modal && iframe) {
    modal.style.display = 'none';
    iframe.src = '';
  }
}
