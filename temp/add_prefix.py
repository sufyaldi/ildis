import json

json_file = "/home/iain/app-jdih/temp/berita_hukum.json"

with open(json_file, "r", encoding="utf-8") as f:
    news_items = json.load(f)

prefix = "JDIH IAIN Parepare - "

updated_count = 0
for item in news_items:
    content = item.get('isi', '').strip()
    if content:
        # Jika belum ada prefix, tambahkan <b>JDIH IAIN Parepare - </b> di awal paragraf pertama
        if not content.startswith("JDIH IAIN Parepare - ") and not content.startswith("<b>JDIH IAIN Parepare - </b>"):
            item['isi'] = f"<b>{prefix}</b>{content}"
            updated_count += 1
        elif content.startswith("JDIH IAIN Parepare - "):
            item['isi'] = content.replace("JDIH IAIN Parepare - ", f"<b>{prefix}</b>", 1)
            updated_count += 1

with open(json_file, "w", encoding="utf-8") as f:
    json.dump(news_items, f, ensure_ascii=False, indent=2)

print(f"Updated {updated_count} news items with prefix 'JDIH IAIN Parepare - '")
