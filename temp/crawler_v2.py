import urllib.request
import urllib.parse
import re
import ssl
import json
import csv
import datetime
import html
import sys

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

HEADERS = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'}
BASE_URL = "https://www.iainpare.ac.id"
SEARCH_KEYWORD = "hukum"

crawled_news = []
visited_urls = set()

def fetch_url(url):
    try:
        req = urllib.request.Request(url, headers=HEADERS)
        res = urllib.request.urlopen(req, context=ctx, timeout=12)
        return res.read().decode('utf-8', errors='ignore')
    except Exception as e:
        return None

def extract_clean_article(raw_html, url):
    if not raw_html:
        return None
    
    # Extract title from <h1> or <title>
    title_match = re.search(r'<h1[^>]*>(.*?)</h1>', raw_html, re.DOTALL)
    title = re.sub(r'<[^>]+>', '', title_match.group(1)).strip() if title_match else ""
    title = html.unescape(re.sub(r'\s+', ' ', title))
    
    # Isolate main body
    main_match = re.search(r'<main[^>]*>(.*?)</main>', raw_html, re.DOTALL)
    if not main_match:
        main_match = re.search(r'<article[^>]*>(.*?)</article>', raw_html, re.DOTALL)
    
    content_html = main_match.group(1) if main_match else raw_html

    # Strip script, style, header, footer, nav
    content_html = re.sub(r'<script[^>]*>.*?</script>', '', content_html, flags=re.DOTALL)
    content_html = re.sub(r'<style[^>]*>.*?</style>', '', content_html, flags=re.DOTALL)
    content_html = re.sub(r'<header[^>]*>.*?</header>', '', content_html, flags=re.DOTALL)
    content_html = re.sub(r'<footer[^>]*>.*?</footer>', '', content_html, flags=re.DOTALL)
    content_html = re.sub(r'<nav[^>]*>.*?</nav>', '', content_html, flags=re.DOTALL)

    # Extract image
    img_match = re.search(r'<img[^>]+src="([^"]+/web/image/[^"]+)"', raw_html)
    img_url = img_match.group(1) if img_match else ""
    if img_url and not img_url.startswith('http'):
        img_url = BASE_URL + img_url

    # Extract date
    date_match = re.search(r'(\d{1,2}\s+(?:Jan|Feb|Mar|Apr|Mei|Jun|Jul|Agu|Sep|Okt|Nov|Des)[a-z]*\s*,?\s*\d{4})', raw_html, re.I)
    date_str = date_match.group(1) if date_match else datetime.date.today().strftime('%Y-%m-%d')

    # Convert to plain text preserving paragraphs
    paragraphs = []
    text = re.sub(r'<[^>]+>', '\n', content_html)
    text = html.unescape(text)

    for line in text.splitlines():
        line_str = line.strip()
        # Skip site noise
        if len(line_str) > 3 and not re.search(r'^(Log in|Beranda|IAIN PAREPARE|Pencarian|Bagikan|Tweet|Share|Copyright|\[email)', line_str, re.I):
            paragraphs.append(line_str)

    full_text = '\n\n'.join(paragraphs)

    # Check keyword "hukum"
    if SEARCH_KEYWORD.lower() in title.lower() or SEARCH_KEYWORD.lower() in full_text.lower():
        return {
            'url': url,
            'judul': title if title else "Berita IAIN Parepare",
            'tanggal': date_str,
            'isi': full_text,
            'image': img_url
        }
    return None

print("Starting accurate article crawler for keyword 'hukum'...")

page = 1
max_pages = 25

while page <= max_pages:
    page_url = f"{BASE_URL}/blog/page/{page}" if page > 1 else f"{BASE_URL}/blog"
    print(f"Crawling page {page}/{max_pages}...")
    page_html = fetch_url(page_url)
    if not page_html:
        break
    
    post_links = set(re.findall(r'href="(/blog/[^"]+-\d+)"', page_html))
    for link in post_links:
        full_url = BASE_URL + link if not link.startswith('http') else link
        if full_url in visited_urls:
            continue
        visited_urls.add(full_url)
        
        raw_post = fetch_url(full_url)
        item = extract_clean_article(raw_post, full_url)
        if item and len(item['isi']) > 100:
            crawled_news.append(item)
            print(f"  [MATCH] {item['judul']} (Substance Length: {len(item['isi'])} chars)")
            
    page += 1

print(f"\nCrawling complete. Total full substance news: {len(crawled_news)}")

# Save JSON
json_path = "/home/iain/app-jdih/temp/berita_hukum.json"
with open(json_path, "w", encoding="utf-8") as f:
    json.dump(crawled_news, f, ensure_ascii=False, indent=2)

# Save CSV
csv_path = "/home/iain/app-jdih/temp/berita_hukum.csv"
with open(csv_path, "w", encoding="utf-8", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=['url', 'judul', 'tanggal', 'isi', 'image'])
    writer.writeheader()
    writer.writerows(crawled_news)

print("Saved clean JSON & CSV.")
