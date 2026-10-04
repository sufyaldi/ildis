import urllib.request
import urllib.parse
import csv
import time
import os
import re
import html
import ssl

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

BASE_URL = "https://peraturan.go.id"
START_URL = "https://peraturan.go.id/permen" 
HEADERS = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
}
FILENAME_CSV = "/home/iain/app-jdih/temp/crawl_peraturan.csv"

KEYWORDS_FILTER = ["AGAMA", "PENDIDIKAN TINGGI", "IAIN", "STAIN", "UIN", "PERGURUAN TINGGI", "DOSEN", "MADRASAH"]

def fetch_html(url):
    try:
        req = urllib.request.Request(url, headers=HEADERS)
        res = urllib.request.urlopen(req, context=ctx, timeout=12)
        return res.read().decode('utf-8', errors='ignore')
    except Exception as e:
        return None

def get_detail_urls(html_content):
    if not html_content: return []
    links = set(re.findall(r'href="(/id/[^"]+)"[^>]*title="lihat detail"', html_content))
    return [BASE_URL + l for l in links]

def get_next_page(html_content):
    if not html_content: return None
    m = re.search(r'<li[^>]*class="[^"]*next[^"]*"[^>]*>\s*<a[^>]*href="([^"]+)"', html_content)
    if m:
        return BASE_URL + m.group(1)
    return None

def parse_metadata(detail_url):
    raw_html = fetch_html(detail_url)
    if not raw_html: return None
    
    data = {"source_url": detail_url}
    
    # Extract rows from tables #w2 and #w3
    rows = re.findall(r'<tr[^>]*>\s*<th[^>]*>(.*?)</th>\s*<td[^>]*>(.*?)</td>\s*</tr>', raw_html, re.DOTALL)
    if not rows:
        rows = re.findall(r'<tr[^>]*>\s*<td[^>]*>(.*?)</td>\s*<td[^>]*>(.*?)</td>\s*</tr>', raw_html, re.DOTALL)
        
    for k, v in rows:
        key_clean = re.sub(r'<[^>]+>', '', k).replace(':', '').strip()
        val_clean = html.unescape(re.sub(r'<[^>]+>', ' ', v)).strip()
        val_clean = re.sub(r'\s+', ' ', val_clean)
        
        if "Dokumen Peraturan" in key_clean:
            pdf_m = re.search(r'href="([^"]+\.pdf[^"]*)"', v, re.I)
            if pdf_m:
                pdf_link = pdf_m.group(1)
                data['Link Download PDF'] = pdf_link if pdf_link.startswith('http') else BASE_URL + pdf_link
            else:
                data['Link Download PDF'] = "Tidak tersedia"
        else:
            data[key_clean] = val_clean

    tentang_text = data.get('Tentang', '').strip()
    pemrakarsa_text = data.get('Pemrakarsa', '').strip().upper()
    
    # Filter relevansi: Hanya simpan jika berhubungan dengan Kemenag / PTKN / Agama
    is_relevant = any(kw in tentang_text.upper() or kw in pemrakarsa_text for kw in KEYWORDS_FILTER)
    if not is_relevant:
        return None

    if tentang_text.upper().startswith('PERUBAHAN'):
        data['label'] = 'perubahan'
        data['status'] = 'Berlaku'
    elif tentang_text.upper().startswith('PENCABUTAN'):
        data['label'] = 'pencabutan'
        data['status'] = 'Tidak Berlaku'
    else:
        data['label'] = 'induk'
        data['status'] = 'Berlaku'
                        
    return data

def save_to_csv_append(data_list, filename):
    if not data_list: return
    headers_ordered = []
    for item in data_list:
        for key in item.keys():
            if key not in headers_ordered:
                headers_ordered.append(key)
    
    file_exists = os.path.isfile(filename)
    with open(filename, mode='a', newline='', encoding='utf-8-sig') as f:
        writer = csv.DictWriter(f, fieldnames=headers_ordered)
        if not file_exists:
            writer.writeheader()
        writer.writerows(data_list)
    print(f"    -> [DISK] {len(data_list)} peraturan Kemenag/PTKN disimpan ke CSV.")

if __name__ == "__main__":
    print("=== Memulai Fase 1: Crawling & Filter Peraturan Kemenag & PTKN ===")
    current_url = START_URL
    page_num = 1
    max_pages = 20

    if os.path.exists(FILENAME_CSV):
        os.remove(FILENAME_CSV)

    while current_url and page_num <= max_pages:
        print(f"[*] Scanning Halaman Listing {page_num}/{max_pages}: {current_url}")
        page_html = fetch_html(current_url)
        if not page_html: break
        
        detail_urls = get_detail_urls(page_html)
        print(f"    Ditemukan {len(detail_urls)} dokumen peraturan.")
        if not detail_urls: break

        batch_results = []
        for url in detail_urls:
            meta = parse_metadata(url)
            if meta:
                batch_results.append(meta)
                print(f"  [MATCH KEMENAG/PTKN] {meta.get('Tentang', '')[:70]}...")
        
        save_to_csv_append(batch_results, FILENAME_CSV)
        
        next_link = get_next_page(page_html)
        if next_link and next_link != current_url:
            current_url = next_link
            page_num += 1
            time.sleep(1)
        else:
            break

    print(f"\n[SELESAI FASE 1] File dataset tersimpan di: {FILENAME_CSV}")
