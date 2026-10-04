import json
import subprocess
import re
import datetime

json_file = "/home/iain/app-jdih/temp/berita_hukum.json"

with open(json_file, "r", encoding="utf-8") as f:
    news_items = json.load(f)

print(f"Loaded {len(news_items)} full substance news items...")

# Indonesian month mapping
MONTHS = {
    'jan': '01', 'januari': '01',
    'feb': '02', 'februari': '02',
    'mar': '03', 'maret': '03',
    'apr': '04', 'april': '04',
    'mei': '05',
    'jun': '06', 'juni': '06',
    'jul': '07', 'juli': '07',
    'agu': '08', 'agustus': '08',
    'sep': '09', 'september': '09',
    'okt': '10', 'oktober': '10',
    'nov': '11', 'november': '11',
    'des': '12', 'desember': '12'
}

def parse_date(date_raw):
    if not date_raw:
        return '2026-01-01'
    date_str = str(date_raw).strip()
    
    # Check YYYY-MM-DD
    if re.match(r'^\d{4}-\d{2}-\d{2}$', date_str):
        return date_str
        
    # Check "30 Juli, 2026" or "15 Agustus 2026"
    m = re.search(r'(\d{1,2})\s+([A-Za-z]+)\s*,?\s*(\d{4})', date_str)
    if m:
        day = m.group(1).zfill(2)
        month_name = m.group(2).lower()
        year = m.group(3)
        month = MONTHS.get(month_name[:3], '01')
        return f"{year}-{month}-{day}"
        
    return '2026-01-01'

def escape_str(val):
    if val is None:
        return "NULL"
    val_str = str(val).replace("\\", "\\\\").replace("'", "''")
    return f"'{val_str}'"

inserted = 0
for item in news_items:
    judul = escape_str(item.get('judul', ''))
    tanggal = parse_date(item.get('tanggal'))
    isi = escape_str(item.get('isi', ''))
    image = escape_str(item.get('image', ''))
    
    sql = f"""
    INSERT INTO berita (tanggal, judul, isi, image, status, created_at)
    VALUES ('{tanggal}', {judul}, {isi}, {image}, 1, NOW());
    """
    
    cmd = [
        "docker", "exec", "-i", "ildis_mariadb",
        "mariadb", "-u", "root", "-pS6v6wzPBY+ERsYhqSiD3msLYOBHoE1l7DvAUGeCbzto",
        "ildis_v4", "-e", sql
    ]
    
    res = subprocess.run(cmd, capture_output=True, text=True)
    if res.returncode == 0:
        inserted += 1
    else:
        print(f"Failed '{item.get('judul')}': {res.stderr[:100]}")

print(f"Successfully imported {inserted} / {len(news_items)} full substance berita into database!")
