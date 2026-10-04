import json
import re

json_file = "/home/iain/app-jdih/temp/berita_hukum.json"

with open(json_file, "r", encoding="utf-8") as f:
    news_items = json.load(f)

print(f"Cleaning trailing noise from {len(news_items)} news items...")

def truncate_trailing_noise(text):
    if not text:
        return ""
    
    # 1. Truncate starting from "di dalam Berita Humas IAIN..." or "di dalam Berita..." or "blog kami..."
    text = re.sub(r'\s*di dalam\s+Berita.*$', '', text, flags=re.DOTALL | re.IGNORECASE)
    text = re.sub(r'\s*blog kami.*$', '', text, flags=re.DOTALL | re.IGNORECASE)
    text = re.sub(r'\s*Masuk\s+untuk\s+meninggalkan\s+komentar.*$', '', text, flags=re.DOTALL | re.IGNORECASE)
    text = re.sub(r'\s*Baca Berikutnya.*$', '', text, flags=re.DOTALL | re.IGNORECASE)
    text = re.sub(r'\s*Arsip\s+Semua tanggal.*$', '', text, flags=re.DOTALL | re.IGNORECASE)
    
    # Normalisasi spasi
    return text.strip()

for item in news_items:
    item['isi'] = truncate_trailing_noise(item.get('isi', ''))

# Save cleaned JSON
with open(json_file, "w", encoding="utf-8") as f:
    json.dump(news_items, f, ensure_ascii=False, indent=2)

print("Trailing noise cleanup complete.")
