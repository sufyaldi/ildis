import json
import re
import html
import csv

input_json = "/home/iain/app-jdih/temp/berita_hukum.json"

with open(input_json, "r", encoding="utf-8") as f:
    news_items = json.load(f)

print(f"Cleaning {len(news_items)} news items...")

def clean_text(text):
    if not text:
        return ""
    
    # 1. Unescape HTML entities (&nbsp;, &quot;, &amp;, etc)
    text = html.unescape(text)
    
    # 2. Hapus JS inline Odoo variables & session info
    text = re.sub(r'var odoo = \{.*?\};', '', text, flags=re.DOTALL)
    text = re.sub(r'odoo\.__session_info__ = \{.*?\};', '', text, flags=re.DOTALL)
    text = re.sub(r'odoo\.define\(.*?\);', '', text, flags=re.DOTALL)
    text = re.sub(r'<script.*?>.*?</script>', '', text, flags=re.DOTALL)
    text = re.sub(r'<style.*?>.*?</style>', '', text, flags=re.DOTALL)

    # 3. Hapus noise header/navigation Odoo yang tersisa
    text = re.sub(r'^.*?IAIN PAREPARE\s*', '', text)
    text = re.sub(r'Log in\s*Beranda.*?(?=[A-Z0-9])', '', text, flags=re.DOTALL)
    text = re.sub(r'Pencarian\s*Blog.*?(?=[A-Z0-9])', '', text, flags=re.DOTALL)
    text = re.sub(r'Lanjut membaca.*', '', text, flags=re.DOTALL)
    text = re.sub(r'Postingan Terkait.*', '', text, flags=re.DOTALL)
    text = re.sub(r'Bagikan di Facebook.*', '', text, flags=re.DOTALL)
    
    # 4. Hapus karakter kontrol non-printable & unicode acak
    text = re.sub(r'[\x00-\x08\x0b\x0c\x0e-\x1f\x7f-\x9f]', '', text)

    # 5. Normalisasi spasi dan baris baru
    text = re.sub(r'\s+', ' ', text).strip()
    
    return text

cleaned_count = 0
for item in news_items:
    orig_title = item.get('judul', '')
    orig_content = item.get('isi', '')
    
    item['judul'] = clean_text(orig_title)
    item['isi'] = clean_text(orig_content)
    cleaned_count += 1

# Save cleaned JSON & CSV
cleaned_json = "/home/iain/app-jdih/temp/berita_hukum.json"
cleaned_csv = "/home/iain/app-jdih/temp/berita_hukum.csv"

with open(cleaned_json, "w", encoding="utf-8") as f:
    json.dump(news_items, f, ensure_ascii=False, indent=2)

with open(cleaned_csv, "w", encoding="utf-8", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=['url', 'judul', 'tanggal', 'isi', 'image'])
    writer.writeheader()
    writer.writerows(news_items)

print(f"Cleaned {cleaned_count} items. Saved to JSON & CSV.")
