# 🏪 Ürün Yükleme Kapasitesi ve Limitleri

## 📊 Veritabanı Kapasitesi

### Teorik Limitler
- **MySQL/MariaDB**: ~18 milyon TB (InnoDB)
- **PostgreSQL**: Sınırsız
- **SQLite**: 281 TB

### Pratik Limitler (Önerilen)
| Satıcı Planı | Ürün Limiti | Aylık Ücret |
|--------------|-------------|-------------|
| Basic        | 100 ürün    | Ücretsiz    |
| Premium      | 1,000 ürün  | 99 ₺        |
| Enterprise   | 10,000 ürün | 499 ₺       |
| Unlimited    | Sınırsız    | 999 ₺       |

**Platform Toplamı**: 500,000 ürün (performans optimizasyonu için)

## 📸 Dosya Yükleme Limitleri

### Ürün Görselleri
- **Görsel sayısı**: 10 fotoğraf/ürün
- **Dosya boyutu**: 5 MB/görsel (5120 KB)
- **Toplam**: 50 MB/ürün
- **Formatlar**: JPG, JPEG, PNG, WebP
- **Minimum boyut**: 800x800 px (önerilen)
- **Maksimum boyut**: 4000x4000 px

### Review Fotoğrafları
- **Fotoğraf sayısı**: 5 fotoğraf/review
- **Dosya boyutu**: 5 MB/fotoğraf
- **Toplam**: 25 MB/review
- **Formatlar**: JPG, JPEG, PNG, WebP

### CSV Toplu Yükleme
- **Dosya boyutu**: 10 MB
- **Satır limiti**: Sınırsız (önerilen: 10,000/dosya)
- **Format**: CSV, UTF-8 encoding
- **Kolonlar**: name, description, price, stock, category_id, sku

### Excel Toplu Yükleme
- **Dosya boyutu**: 20 MB
- **Satır limiti**: 50,000/dosya
- **Format**: XLSX, XLS

## ⚙️ PHP Yapılandırması

### Gerekli Ayarlar
```ini
upload_max_filesize = 10M
post_max_size = 60M
max_file_uploads = 20
memory_limit = 256M
max_execution_time = 60
```

### Windows XAMPP/WAMP
Dosya: `C:\xampp\php\php.ini`

### Linux/Ubuntu
Dosya: `/etc/php/8.1/fpm/php.ini`

### Restart gerektirir:
```bash
# Apache
sudo service apache2 restart

# Nginx + PHP-FPM
sudo service php8.1-fpm restart
sudo service nginx restart
```

## 🚀 Toplu Yükleme Yöntemleri

### 1. CSV İle Yükleme
```csv
name,description,price,stock,category_id,sku
"Futbol Topu","Adidas profesyonel futbol topu",299.99,50,2,FB-001
"Koşu Ayakkabısı","Nike Air Max 2024",899.99,30,5,KA-002
```

**API Endpoint**:
```bash
POST /api/products/bulk-upload-csv
Content-Type: multipart/form-data

csv_file: [file]
```

### 2. Excel İle Yükleme
**API Endpoint**:
```bash
POST /api/products/bulk-upload-excel
Content-Type: multipart/form-data

excel_file: [file]
```

### 3. API İle Toplu Güncelleme
**Tek seferde**: 1,000 ürün max

```bash
PUT /api/products/bulk-update
Authorization: Bearer {token}

{
  "products": [
    {"id": 1, "price": 299.99, "stock": 100},
    {"id": 2, "price": 399.99, "stock": 50}
  ]
}
```

### 4. API İle Toplu Silme
**Tek seferde**: 1,000 ürün max

```bash
DELETE /api/products/bulk-delete
Authorization: Bearer {token}

{
  "product_ids": [1, 2, 3, 4, 5]
}
```

## 📈 Performans Optimizasyonu

### İndeksleme
```sql
CREATE INDEX idx_products_seller ON products(user_id);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_price ON products(price);
CREATE INDEX idx_products_active ON products(is_active);
CREATE FULLTEXT INDEX idx_products_search ON products(name, description);
```

### Cache Stratejisi
- **Ürün listesi**: 15 dakika cache
- **Ürün detayı**: 30 dakika cache
- **Kategori ağacı**: 1 saat cache
- **Satıcı stats**: 5 dakika cache

### Pagination
- **Frontend**: 24 ürün/sayfa
- **API**: 50 ürün/sayfa (default)
- **Admin**: 100 ürün/sayfa
- **Max**: 500 ürün/sayfa

## 🗄️ Depolama Gereksinimleri

### Disk Alanı Hesaplama

**Ürün başına ortalama**:
- Veritabanı kaydı: ~2 KB
- Fotoğraflar (10x): ~20 MB (orijinal)
- Thumbnail'ler: ~2 MB
- **Toplam**: ~22 MB/ürün

**100,000 ürün için**:
- Veritabanı: ~200 MB
- Fotoğraflar: ~2.2 TB
- **Toplam**: ~2.2 TB

**Önerilen**:
- SSD: 500 GB (veritabanı + cache)
- HDD/S3: 5 TB (fotoğraflar)

## 📦 Sunucu Gereksinimleri

### Küçük Ölçek (< 10,000 ürün)
- **CPU**: 2 core
- **RAM**: 4 GB
- **Disk**: 100 GB SSD
- **Bant Genişliği**: 100 Mbps

### Orta Ölçek (10,000 - 100,000 ürün)
- **CPU**: 4 core
- **RAM**: 8 GB
- **Disk**: 500 GB SSD + 2 TB HDD
- **Bant Genişliği**: 1 Gbps

### Büyük Ölçek (> 100,000 ürün)
- **CPU**: 8+ core
- **RAM**: 16+ GB
- **Disk**: 1 TB NVMe SSD + S3/Cloudflare R2
- **Bant Genişliği**: 10 Gbps
- **CDN**: Cloudflare/AWS CloudFront
- **Load Balancer**: Nginx/HAProxy

## 🔐 Güvenlik ve Kısıtlamalar

### Rate Limiting
```php
// Ürün oluşturma
60 requests/minute/user (tek ürün)
10 requests/hour/user (CSV yükleme)

// Ürün listeleme
1000 requests/minute (genel)
```

### Validasyon
- SKU unique kontrolü
- Kategori ID validasyonu
- Fiyat > 0
- Stok >= 0
- Görsel format kontrolü (JPG, PNG, WebP)
- Görsel virus scan (ClamAV)

### Spam Koruması
- reCAPTCHA v3 (toplu yüklemede)
- Duplicate detection (aynı ürün 2x yüklenemez)
- Image hash comparison
- Seller verification (onaylı satıcılar)

## 📊 İstatistikler ve Raporlama

### Ürün Metrikleri
```bash
GET /api/seller/product-stats
```

Response:
```json
{
  "total_products": 1250,
  "active_products": 980,
  "out_of_stock": 45,
  "low_stock": 120,
  "total_views": 45230,
  "total_sales": 890,
  "total_revenue": 125890.50
}
```

### Limit Kontrolü
```bash
GET /api/products/check-limit
```

Response:
```json
{
  "current_count": 450,
  "limit": 1000,
  "remaining": 550,
  "plan": "premium",
  "percentage_used": 45.0
}
```

## 🎯 En İyi Uygulamalar

1. **Görsel Optimizasyonu**
   - WebP formatı kullan (70% daha küçük)
   - Lazy loading uygula
   - CDN kullan (Cloudflare R2)
   - Responsive images (srcset)

2. **Veri Tabanı**
   - Soft delete kullan (deleted_at)
   - Archive eski ürünler (>1 yıl satılmayan)
   - Partitioning (100K+ ürün için)

3. **Cache**
   - Redis kullan (database yerine)
   - Full-page cache (Varnish)
   - Browser cache (7 gün)

4. **Monitoring**
   - Disk kullanımı izle
   - Query performance log
   - Error tracking (Sentry)
   - Uptime monitoring

## 📞 Destek

Ürün yükleme ile ilgili sorunlar için:
- Email: support@sportoonline.com
- Dokümantasyon: /docs/bulk-upload
- API Reference: /api/documentation
