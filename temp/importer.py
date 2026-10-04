import json
import subprocess
import os

json_file = "/home/iain/app-jdih/temp/berita_hukum.json"

with open(json_file, "r", encoding="utf-8") as f:
    news_items = json.load(f)

print(f"Loaded {len(news_items)} news items for database import...")

# Escape string for MySQL query safely
def escape_str(val):
    if val is None:
        return "NULL"
    val_str = str(val).replace("\\", "\\\\").replace("'", "''")
    return f"'{val_str}'"

inserted = 0
for item in news_items:
    judul = escape_str(item.get('judul', ''))
    tanggal = escape_str(item.get('tanggal', '2026-01-01'))
    isi = escape_str(item.get('isi', ''))
    image = escape_str(item.get('image', ''))
    
    # Query SQL
    sql = f"""
    INSERT INTO berita (tanggal, judul, isi, image, status, created_at)
    VALUES ({tanggal}, {judul}, {isi}, {image}, 1, NOW());
    """
    
    # Run query via docker exec mariadb
    cmd = [
        "docker", "exec", "-i", "ildis_mariadb",
        "mariadb", "-u", "root", "-pS6v6wzPBY+ERsYhqSiD3msLYOBHoE1l7DvAUGeCbzto",
        "ildis_v4", "-e", sql
    ]
    
    res = subprocess.run(cmd, capture_output=True, text=True)
    if res.returncode == 0:
        inserted += 1
    else:
        print(f"Failed inserting '{item.get('judul')}': {res.stderr}")

print(f"Successfully imported {inserted} / {len(news_items)} berita into database!")
