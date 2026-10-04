import csv
import subprocess
import re
import datetime
import os

csv_path = "/home/iain/app-jdih/temp/crawl_iainpare_internal.csv"
DB_PASS = "S6v6wzPBY+ERsYhqSiD3msLYOBHoE1l7DvAUGeCbzto"

def escape_sql(val):
    if val is None:
        return "NULL"
    val_str = str(val).replace("\\", "\\\\").replace("'", "''")
    return f"'{val_str}'"

print("=== Memulai Import Peraturan & Pedoman Internal IAIN Parepare ke Database ===")

if not os.path.exists(csv_path):
    print(f"ERROR: CSV File {csv_path} tidak ditemukan!")
    exit(1)

imported = 0
skipped = 0

with open(csv_path, 'r', encoding='utf-8-sig') as f:
    reader = csv.DictReader(f)
    for row in reader:
        judul = row.get('judul', '').strip()
        jenis = row.get('jenis', 'Keputusan Rektor').strip()
        nomor = row.get('nomor', 'SK/IAIN/2024').strip()
        tahun = row.get('tahun', '2024').strip()
        pemrakarsa = row.get('pemrakarsa', 'IAIN Parepare').strip()
        file_url = row.get('file_url', '').strip()
        
        if not judul:
            continue

        # Cek Duplikasi Judul di DB
        check_sql = f"SELECT id FROM document WHERE judul = {escape_sql(judul)} LIMIT 1;"
        cmd_check = [
            "docker", "exec", "-i", "ildis_mariadb",
            "mariadb", "-u", "root", f"-p{DB_PASS}",
            "ildis_v4", "-N", "-e", check_sql
        ]
        res_check = subprocess.run(cmd_check, capture_output=True, text=True)
        if res_check.stdout.strip():
            print(f"  [SKIP DUPLIKAT] {judul[:60]}")
            skipped += 1
            continue

        # Insert Ke Tabel document (Tipe Dokumen 1, Dokumen Type ID 15 untuk Peraturan Internal)
        sql_insert_doc = f"""
        INSERT INTO document (
            tipe_dokumen, dokumen_type_id, judul, nomor_peraturan, tahun_terbit,
            jenis_peraturan, pemrakarsa, tanggal_penetapan, tanggal_pengundangan,
            status, is_publish, integrasi, created_at, updated_at
        ) VALUES (
            1, 15, {escape_sql(judul)}, {escape_sql(nomor)}, {escape_sql(tahun)},
            {escape_sql(jenis)}, {escape_sql(pemrakarsa)}, '{tahun}-01-01', '{tahun}-01-01',
            'Berlaku', 1, 1, NOW(), NOW()
        );
        """
        
        cmd_insert = [
            "docker", "exec", "-i", "ildis_mariadb",
            "mariadb", "-u", "root", f"-p{DB_PASS}",
            "ildis_v4", "-e", sql_insert_doc
        ]
        res_insert = subprocess.run(cmd_insert, capture_output=True, text=True)
        
        if res_insert.returncode != 0:
            print(f"  [ERROR INSERT] {judul[:50]}: {res_insert.stderr[:100]}")
            continue

        # Ambil Inserted Document ID
        cmd_id = [
            "docker", "exec", "-i", "ildis_mariadb",
            "mariadb", "-u", "root", f"-p{DB_PASS}",
            "ildis_v4", "-N", "-e", f"SELECT id FROM document WHERE judul = {escape_sql(judul)} ORDER BY id DESC LIMIT 1;"
        ]
        res_id = subprocess.run(cmd_id, capture_output=True, text=True)
        doc_id = res_id.stdout.strip()

        # Insert ke Tabel data_lampiran (Link PDF / Google Drive)
        if doc_id and file_url:
            sql_lampiran = f"""
            INSERT INTO data_lampiran (id_dokumen, url_lampiran, urutan)
            VALUES ({doc_id}, {escape_sql(file_url)}, 1);
            """
            cmd_lampiran = [
                "docker", "exec", "-i", "ildis_mariadb",
                "mariadb", "-u", "root", f"-p{DB_PASS}",
                "ildis_v4", "-e", sql_lampiran
            ]
            subprocess.run(cmd_lampiran, capture_output=True, text=True)

        imported += 1
        print(f"  [SUCCESS] Imported Doc ID {doc_id}: {judul[:65]}")

print(f"\n[SELESAI IMPOR INTERNAL] Total Ter-import Baru: {imported} | Duplikat Dilewati: {skipped}")
