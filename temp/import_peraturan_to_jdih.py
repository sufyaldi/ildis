import csv
import subprocess
import re
import datetime

csv_path = "/home/iain/app-jdih/temp/crawl_peraturan.csv"

# Indeks pemetaan jenis peraturan ke ID document_type di DB
# PERATURAN MENTERI -> default 12 (Permen)
TYPE_PERATURAN_ID = 12

def parse_date(date_str):
    if not date_str:
        return '2026-01-01'
    if re.match(r'^\d{4}-\d{2}-\d{2}$', date_str):
        return date_str
    
    months = {
        'januari': '01', 'februari': '02', 'maret': '03', 'april': '04',
        'mei': '05', 'juni': '06', 'juli': '07', 'agustus': '08',
        'september': '09', 'oktober': '10', 'november': '11', 'desember': '12'
    }
    
    m = re.search(r'(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})', date_str)
    if m:
        day = m.group(1).zfill(2)
        month_name = m.group(2).lower()
        year = m.group(3)
        month = months.get(month_name, '01')
        return f"{year}-{month}-{day}"
    return '2026-01-01'

def escape_sql(val):
    if val is None:
        return "NULL"
    val_str = str(val).replace("\\", "\\\\").replace("'", "''")
    return f"'{val_str}'"

print("=== Memulai Fase 2 & 3: Importer Engine Database JDIH ===")

imported = 0
skipped = 0

with open(csv_path, 'r', encoding='utf-8-sig') as f:
    reader = csv.DictReader(f)
    for row in reader:
        judul = row.get('Tentang', '').strip()
        nomor = row.get('Nomor', '').strip()
        tahun = row.get('Tahun', '').strip()
        jenis = row.get('Jenis/Bentuk Peraturan', 'PERATURAN MENTERI').strip()
        pemrakarsa = row.get('Pemrakarsa', '').strip()
        pdf_url = row.get('Link Download PDF', '').strip()
        status = row.get('status', 'Berlaku').strip()
        tgl_penetapan = parse_date(row.get('Ditetapkan Tanggal', ''))
        tgl_pengundangan = parse_date(row.get('Tanggal Pengundangan', ''))
        
        if not nomor:
            continue

        # 1. Cek Duplikasi di DB
        check_sql = f"SELECT id FROM document WHERE nomor_peraturan = {escape_sql(nomor)} AND tahun_terbit = {escape_sql(tahun)} LIMIT 1;"
        cmd_check = [
            "docker", "exec", "-i", "ildis_mariadb",
            "mariadb", "-u", "root", "-pS6v6wzPBY+ERsYhqSiD3msLYOBHoE1l7DvAUGeCbzto",
            "ildis_v4", "-N", "-e", check_sql
        ]
        res_check = subprocess.run(cmd_check, capture_output=True, text=True)
        if res_check.stdout.strip():
            print(f"  [SKIP DUPLIKAT] Peraturan No. {nomor}/{tahun}")
            skipped += 1
            continue

        # 2. Insert Ke Tabel document
        sql_insert_doc = f"""
        INSERT INTO document (
            tipe_dokumen, dokumen_type_id, judul, nomor_peraturan, tahun_terbit,
            jenis_peraturan, singkatan_jenis, pemrakarsa, tanggal_penetapan, tanggal_pengundangan,
            status, is_publish, integrasi, created_at, updated_at
        ) VALUES (
            1, {TYPE_PERATURAN_ID}, {escape_sql(judul)}, {escape_sql(nomor)}, {escape_sql(tahun)},
            {escape_sql(jenis)}, 'PERMEN', {escape_sql(pemrakarsa)}, '{tgl_penetapan}', '{tgl_pengundangan}',
            {escape_sql(status)}, 1, 1, NOW(), NOW()
        );
        """
        
        cmd_insert = [
            "docker", "exec", "-i", "ildis_mariadb",
            "mariadb", "-u", "root", "-pS6v6wzPBY+ERsYhqSiD3msLYOBHoE1l7DvAUGeCbzto",
            "ildis_v4", "-e", sql_insert_doc
        ]
        res_insert = subprocess.run(cmd_insert, capture_output=True, text=True)
        
        if res_insert.returncode != 0:
            print(f"  [ERROR INSERT] {nomor}/{tahun}: {res_insert.stderr[:100]}")
            continue

        # Ambil Inserted Document ID
        cmd_id = [
            "docker", "exec", "-i", "ildis_mariadb",
            "mariadb", "-u", "root", "-pS6v6wzPBY+ERsYhqSiD3msLYOBHoE1l7DvAUGeCbzto",
            "ildis_v4", "-N", "-e", f"SELECT id FROM document WHERE nomor_peraturan = {escape_sql(nomor)} AND tahun_terbit = {escape_sql(tahun)} ORDER BY id DESC LIMIT 1;"
        ]
        res_id = subprocess.run(cmd_id, capture_output=True, text=True)
        doc_id = res_id.stdout.strip()

        # 3. Insert ke Tabel data_lampiran (Direct PDF Link)
        if doc_id and pdf_url and pdf_url.startswith('http'):
            sql_lampiran = f"""
            INSERT INTO data_lampiran (id_dokumen, url_lampiran, urutan)
            VALUES ({doc_id}, {escape_sql(pdf_url)}, 1);
            """
            cmd_lampiran = [
                "docker", "exec", "-i", "ildis_mariadb",
                "mariadb", "-u", "root", "-pS6v6wzPBY+ERsYhqSiD3msLYOBHoE1l7DvAUGeCbzto",
                "ildis_v4", "-e", sql_lampiran
            ]
            subprocess.run(cmd_lampiran, capture_output=True, text=True)

        print(f"  [SUCCESS] Imported Doc ID {doc_id}: {jenis} No. {nomor}/{tahun}")
        imported += 1

print(f"\n[SELESAI FASE 2 & 3] Total Ter-import: {imported} | Duplikat Dilewati: {skipped}")
