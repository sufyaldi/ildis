import urllib.request
import urllib.parse
import re
import ssl
import json
import csv
import datetime
import html

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
        res = urllib.request.urlopen(req, context=ctx, timeout=10)
        return res.read().decode('utf-8', errors='ignore')
    except Exception as e:
        print(f"Error fetching {url}: {e}")
        return None

def parse_blog_post(url):
    html_content = fetch_url(url)
    if not html_content:
        return None
    
    # Check title
    title_match = re.search(r'<h1[^>]*class="[^"]*target_blog_post_title[^"]*"[^>]*>(.*?)</h1>', html_content, re.DOTALL)
    if not title_match:
        title_match = re.search(r'<h1[^>]*>(.*?)</h1>', html_content, re.DOTALL)
    
    title = re.sub(r'<[^>]+>', '', title_match.group(1)).strip() if title_match else ""
    title = html.unescape(title)
    
    # Check date
    date_match = re.search(r'(\d{2}/\d{2}/\d{4}|\d{4}-\d{2}-\d{2})', html_content)
    date_str = date_match.group(1) if date_match else datetime.date.today().strftime('%Y-%m-%d')
    if '/' in date_str:
        parts = date_str.split('/')
        date_str = f"{parts[2]}-{parts[1]}-{parts[0]}"

    # Check content
    content_match = re.search(r'id="blog_post_content"[^>]*>(.*?)</div>\s*</section>', html_content, re.DOTALL)
    if not content_match:
        content_match = re.search(r'<article[^>]*>(.*?)</article>', html_content, re.DOTALL)
    
    raw_content = content_match.group(1) if content_match else html_content
    clean_content = re.sub(r'<[^>]+>', ' ', raw_content).strip()
    clean_content = html.unescape(re.sub(r'\s+', ' ', clean_content))

    # Check image
    img_match = re.search(r'<img[^>]+src="([^"]+/web/image/[^"]+)"', html_content)
    img_url = img_match.group(1) if img_match else ""
    if img_url and not img_url.startswith('http'):
        img_url = BASE_URL + img_url

    # Check keyword "hukum" in title or content
    if SEARCH_KEYWORD.lower() in title.lower() or SEARCH_KEYWORD.lower() in clean_content.lower():
        return {
            'url': url,
            'judul': title,
            'tanggal': date_str,
            'isi': clean_content,
            'image': img_url
        }
    return None

print("Starting crawling for keyword 'hukum'...")

# Crawl multiple pages of /blog
page = 1
max_pages = 25

while page <= max_pages:
    page_url = f"{BASE_URL}/blog/page/{page}" if page > 1 else f"{BASE_URL}/blog"
    print(f"Crawling index page {page}: {page_url}")
    page_html = fetch_url(page_url)
    if not page_html:
        break
    
    post_links = set(re.findall(r'href="(/blog/[^"]+-\d+)"', page_html))
    print(f"Found {len(post_links)} post links on page {page}")
    
    for link in post_links:
        full_url = BASE_URL + link if not link.startswith('http') else link
        if full_url in visited_urls:
            continue
        visited_urls.add(full_url)
        
        item = parse_blog_post(full_url)
        if item:
            crawled_news.append(item)
            print(f"  [MATCH] Found: {item['judul']} ({item['tanggal']})")
            
    page += 1

print(f"\nTotal matched news items found: {len(crawled_news)}")

# Save to JSON
json_path = "/home/iain/app-jdih/temp/berita_hukum.json"
with open(json_path, "w", encoding="utf-8") as f:
    json.dump(crawled_news, f, ensure_ascii=False, indent=2)
print(f"Saved JSON to {json_path}")

# Save to CSV
csv_path = "/home/iain/app-jdih/temp/berita_hukum.csv"
with open(csv_path, "w", encoding="utf-8", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=['url', 'judul', 'tanggal', 'isi', 'image'])
    writer.writeheader()
    writer.writerows(crawled_news)
print(f"Saved CSV to {csv_path}")

