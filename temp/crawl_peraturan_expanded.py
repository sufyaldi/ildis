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
HEADERS = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
}
FILENAME_CSV = "/home/iain/app-jdih/temp/crawl_peraturan_expanded.csv"

# Start URLs to crawl
START_URLS = [
    "https://peraturan.go.id/permen",
    "https://peraturan.go.id/perban",
    "https://peraturan.go.id/perpres",
    "https://peraturan.go.id/perppu"
]

KEYWORDS_FILTER = [
    "AGAMA", "PENDIDIKAN TINGGI", "IAIN", "STAIN", "UIN", "PERGURUAN TINGGI", 
    "DOSEN", "MADRASAH", "TRIDHARMA", "AKREDITASI", "GURU BESAR", "LEKTOR", 
    "IJAZAH", "MAHASISWA", "KURIKULUM", "JABATAN FUNGSIONAL", "ANGKA KREDIT",
    "REKTOR", "DEKAN", "FAKULTAS", "PRODI", "PROGRAM STUDI", "BKN", "PANRB",
    "KEMENTERIAN AGAMA", "PENDIDIKAN", "KEPEGAWAIAN", "TUKIN", "ASN", "PNS", "PPPK"
]

def fetch_html(url):
    try:
        req = urllib.request.Request(url, headers=HEADERS)
        res = urllib.request.urlopen(req, context=ctx, timeout=12)
        return res.read().decode('utf-8', errors='ignore')
    except Exception as e:
        return None

def get_detail_urls(html_content):
    if not html_content: return []
    links = set(re.findall(r'href="(/id/[^"]+)"', html_content))
    links.discard("/id/#")
    return [BASE_URL + l for l in links]

def get_next_page(html_content):
    if not html_content: return None
    m = re.search(r'<li[^>]*class="[^"]*next[^"]*"[^>]*>\s*<a[^>]*href="([^"]+)"', html_content)
    if m:
        url = m.group(1)
        if url.startswith('/'):
            return BASE_URL + url
        return url
    return None

def parse_metadata(detail_url):
    raw_html = fetch_html(detail_url)
    if not raw_html: return None
    
    data = {"source_url": detail_url}
    
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
                data['Link Download PDF'] = ""
        else:
            data[key_clean] = val_clean

    tentang_text = data.get('Tentang', '').strip()
    pemrakarsa_text = data.get('Pemrakarsa', '').strip().upper()
    jenis_text = data.get('Jenis/Bentuk Peraturan', '').strip().upper()
    
    # Filter relevansi
    combined_text = f"{tentang_text} {pemrakarsa_text} {jenis_text}".upper()
    is_relevant = any(kw in combined_text for kw in KEYWORDS_FILTER)
    if not is_relevant:
        return None

    data['status'] = data.get('Status', 'Berlaku')
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
    print(f"    -> [DISK] {len(data_list)} peraturan disimpan ke CSV.")

if __name__ == "__main__":
    print("=== Memulai Crawling Peraturan Expanded (Multi-Kategori Peraturan.go.id) ===")
    
    if os.path.exists(FILENAME_CSV):
        os.remove(FILENAME_CSV)

    crawled_urls = set()
    total_saved = 0

    for start_url in START_URLS:
        print(f"\n---> Scanning Root: {start_url}")
        current_url = start_url
        page_num = 1
        max_pages = 10 # 10 pages x 20 items = 200 items per root category

        while current_url and page_num <= max_pages:
            print(f"[*] Page {page_num}/{max_pages}: {current_url}")
            page_html = fetch_html(current_url)
            if not page_html: break
            
            detail_urls = get_detail_urls(page_html)
            print(f"    Ditemukan {len(detail_urls)} dokumen.")
            if not detail_urls: break

            batch_results = []
            for url in detail_urls:
                if url in crawled_urls: continue
                crawled_urls.add(url)

                meta = parse_metadata(url)
                if meta:
                    batch_results.append(meta)
                    total_saved += 1
                    pemrakarsa = meta.get('Pemrakarsa', meta.get('Jenis/Bentuk Peraturan', ''))
                    print(f"  [MATCH ({pemrakarsa[:20]})] {meta.get('Tentang', '')[:65]}...")
                time.sleep(0.2)
            
            save_to_csv_append(batch_results, FILENAME_CSV)
            
            next_link = get_next_page(page_html)
            if next_link and next_link != current_url:
                current_url = next_link
                page_num += 1
                time.sleep(0.5)
            else:
                break

    print(f"\n[SELESAI CRAWLING EXPANDED] Total {total_saved} peraturan disimpan di: {FILENAME_CSV}")
