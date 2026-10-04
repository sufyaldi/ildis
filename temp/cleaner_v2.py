import json
import re
import html
import csv

input_json = "/home/iain/app-jdih/temp/berita_hukum.json"

with open(input_json, "r", encoding="utf-8") as f:
    news_items = json.load(f)

print(f"Deep cleaning {len(news_items)} news items...")

def deep_clean(text):
    if not text:
        return ""
    
    text = html.unescape(text)
    
    # 1. Hapus cookie/timeZone JS scripts
    text = re.sub(r'if\s*\(!/\(\^\|;\\s\)tz=/.*?\(\);\s*\}\s*', '', text, flags=re.DOTALL)
    text = re.sub(r'window\.dataLayer\s*=\s*window\.dataLayer.*?;', '', text, flags=re.DOTALL)
    text = re.sub(r'function\s+gtag\(.*?\)\{.*?\};?', '', text, flags=re.DOTALL)
    text = re.sub(r'gtag\(.*?\);?', '', text, flags=re.DOTALL)
    
    # 2. Hapus Odoo inline code
    text = re.sub(r'var odoo = \{.*?\};', '', text, flags=re.DOTALL)
    text = re.sub(r'odoo\.__session_info__ = \{.*?\};', '', text, flags=re.DOTALL)
    text = re.sub(r'odoo\.define\(.*?\);', '', text, flags=re.DOTALL)
    
    # 3. Hapus noise header/navigation
    text = re.sub(r'^.*?IAIN PAREPARE\s*', '', text)
    text = re.sub(r'Log in\s*Beranda.*?(?=[A-Z0-9])', '', text, flags=re.DOTALL)
    text = re.sub(r'Pencarian\s*Blog.*?(?=[A-Z0-9])', '', text, flags=re.DOTALL)
    text = re.sub(r'Lanjut membaca.*', '', text, flags=re.DOTALL)
    text = re.sub(r'Postingan Terkait.*', '', text, flags=re.DOTALL)
    text = re.sub(r'Bagikan di Facebook.*', '', text, flags=re.DOTALL)

    # 4. Hapus emoji & karakter 4-byte unicode yang bisa merusak MySQL utf8
    text = re.sub(r'[\U00010000-\U0010ffff]', '', text)
    
    # 5. Normalisasi spasi
    text = re.sub(r'\s+', ' ', text).strip()
    return text

for item in news_items:
    item['judul'] = deep_clean(item.get('judul', ''))
    item['isi'] = deep_clean(item.get('isi', ''))

# Save cleaned JSON & CSV
cleaned_json = "/home/iain/app-jdih/temp/berita_hukum.json"
cleaned_csv = "/home/iain/app-jdih/temp/berita_hukum.csv"

with open(cleaned_json, "w", encoding="utf-8") as f:
    json.dump(news_items, f, ensure_ascii=False, indent=2)

with open(cleaned_csv, "w", encoding="utf-8", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=['url', 'judul', 'tanggal', 'isi', 'image'])
    writer.writeheader()
    writer.writerows(news_items)

print("Deep cleaning finished.")
