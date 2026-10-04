import urllib.request
import urllib.parse
import csv
import time
import os
import re
import html
import ssl
import subprocess
import datetime

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

HEADERS = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
}
FILENAME_CSV = "/home/iain/app-jdih/temp/crawl_iainpare_internal.csv"

# List of target URLs across domain & subdomains
TARGET_URLS = [
    "https://iainpare.ac.id/pedoman",
    "https://iainpare.ac.id/standar-pelayanan",
    "https://iainpare.ac.id/rencana-strategis",
    "https://pasca.iainpare.ac.id/pedoman",
    "https://pasca.iainpare.ac.id/dokumen",
    "https://pasca.iainpare.ac.id/renstra",
    "https://fakshi.iainpare.ac.id/pedoman",
    "https://fakshi.iainpare.ac.id/renstra",
    "https://fuad.iainpare.ac.id/pedoman-tata-kelola",
    "https://fuad.iainpare.ac.id/informasi-produk-hukum",
    "https://febi.iainpare.ac.id/pedoman",
    "https://lp2m.iainpare.ac.id/pedoman",
    "https://lpm.iainpare.ac.id/pedoman"
]

# Add individual LPM policy pages
LPM_PAGES = [
    "https://lpm.iainpare.ac.id/pedoman-penerimaan-mahasiswa-asing-tahun-2024",
    "https://lpm.iainpare.ac.id/pedoman-penerapan-sistem-penugasan-dosen-berdasarkan-kebutuhan-kualifikasi-keahlian-dan-pengalaman-1",
    "https://lpm.iainpare.ac.id/pedoman-pembuatan-rps",
    "https://lpm.iainpare.ac.id/pedoman-rpl",
    "https://lpm.iainpare.ac.id/pedoman-tugas-akhir-berbasis-publikasi",
    "https://lpm.iainpare.ac.id/pedoman-penerimaan-beasiswa",
    "https://lpm.iainpare.ac.id/pedoman-penasehat-akademik"
]
TARGET_URLS.extend(LPM_PAGES)

def fetch_html(url):
    try:
        req = urllib.request.Request(url, headers=HEADERS)
        res = urllib.request.urlopen(req, context=ctx, timeout=10)
        return res.read().decode('utf-8', errors='ignore')
    except Exception as e:
        return None

def main():
    print("=== Memulai Crawling Produk Hukum & Pedoman Internal IAIN Parepare ===")
    
    os.makedirs(os.path.dirname(FILENAME_CSV), exist_ok=True)
    results = []
    seen_links = set()

    for page_url in TARGET_URLS:
        print(f"[*] Fetching page: {page_url}")
        html_content = fetch_html(page_url)
        if not html_content:
            continue

        domain_match = re.search(r'https?://([^/]+)', page_url)
        domain_name = domain_match.group(1) if domain_match else "iainpare.ac.id"

        matches = re.findall(r'<a[^>]+href=[\"\']([^\"]+)[\"\'][^>]*>(.*?)</a>', html_content, re.DOTALL)
        for href, text in matches:
            clean_text = html.unescape(re.sub(r'<[^>]+>', '', text)).strip()
            clean_text = re.sub(r'\s+', ' ', clean_text)
            
            # Format absolute URL
            full_url = href
            if href.startswith('/'):
                full_url = f"https://{domain_name}{href}"

            is_doc_link = any(k in href.lower() for k in ['drive.google', '.pdf', 'download', 'dropbox', 'docs.google', 'web/content'])
            if is_doc_link and full_url not in seen_links and len(clean_text) > 3:
                seen_links.add(full_url)
                
                # Determine type & year
                year_match = re.search(r'20\d{2}', clean_text)
                tahun = year_match.group(0) if year_match else "2024"

                jenis = "Keputusan Rektor"
                if "pedoman" in clean_text.lower():
                    jenis = "Pedoman IAIN Parepare"
                elif "kode etik" in clean_text.lower():
                    jenis = "Kode Etik IAIN Parepare"
                elif "renstra" in clean_text.lower() or "rencana strategis" in clean_text.lower():
                    jenis = "Rencana Strategis (Renstra)"
                elif "standar" in clean_text.lower():
                    jenis = "Standar Pelayanan Minimum"

                results.append({
                    "judul": clean_text,
                    "jenis": jenis,
                    "nomor": f"SK/IAIN/{tahun}",
                    "tahun": tahun,
                    "pemrakarsa": f"IAIN Parepare ({domain_name})",
                    "file_url": full_url,
                    "source_page": page_url
                })
                print(f"  [FOUND] {clean_text[:65]} -> {full_url[:50]}...")

    with open(FILENAME_CSV, mode="w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=["judul", "jenis", "nomor", "tahun", "pemrakarsa", "file_url", "source_page"])
        writer.writeheader()
        writer.writerows(results)

    print(f"\n[CRAWLING SELESAI] Found {len(results)} produk hukum/pedoman IAIN Parepare. Saved to {FILENAME_CSV}")

if __name__ == "__main__":
    main()
