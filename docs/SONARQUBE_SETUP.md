# SonarQube Kurulum ve Kullanım Kılavuzu

## 📋 İçindekiler
1. [SonarCloud Kurulumu](#sonarcloud-kurulumu)
2. [Local SonarQube Kurulumu](#local-sonarqube-kurulumu)
3. [Kullanım](#kullanım)
4. [Kalite Kuralları](#kalite-kuralları)
5. [Sorun Giderme](#sorun-giderme)

---

## 🌐 SonarCloud Kurulumu

### 1. SonarCloud Hesabı Oluşturma

1. [SonarCloud](https://sonarcloud.io) adresine gidin
2. GitHub hesabınızla giriş yapın
3. Organization oluşturun (organizasyon adı: `sportoonline`)

### 2. Proje Bağlama

```bash
# 1. Repository'yi SonarCloud'a import edin
# SonarCloud Dashboard > "+" > Analyze new project
# sportoonline repository'sini seçin

# 2. Token oluşturun
# My Account > Security > Generate Token
# Token adı: SportoOnline-GitHub-Actions

# 3. GitHub Secret olarak ekleyin
# GitHub > Settings > Secrets > Actions > New repository secret
# Name: SONAR_TOKEN
# Value: [oluşturduğunuz token]
```

### 3. Proje Anahtarı Yapılandırması

SonarCloud'da proje oluşturulduğunda otomatik olarak bir `projectKey` atanır. Bu anahtarı `sonar-project.properties` dosyasında güncelleyin:

```properties
sonar.projectKey=sportoonline_sportoonline
sonar.organization=sportoonline
```

---

## 💻 Local SonarQube Kurulumu (Opsiyonel)

### Docker ile Kurulum

```bash
# 1. SonarQube Docker Container'ı başlatın
docker run -d --name sonarqube \
  -p 9000:9000 \
  -e SONAR_ES_BOOTSTRAP_CHECKS_DISABLE=true \
  sonarqube:latest

# 2. SonarQube'e erişin: http://localhost:9000
# Varsayılan: admin/admin (ilk girişte değiştirin)

# 3. Token oluşturun
# Administration > Security > Users > Tokens
```

### SonarQube Scanner Kurulumu

**Windows (PowerShell):**
```powershell
# Chocolatey ile
choco install sonarqube-scanner

# Manuel kurulum
# 1. https://docs.sonarqube.org/latest/analysis/scan/sonarscanner/
# 2. ZIP'i indirin ve çıkartın
# 3. bin/ klasörünü PATH'e ekleyin
```

**macOS/Linux:**
```bash
# Homebrew ile
brew install sonar-scanner

# veya manuel
wget https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-4.8.0.2856-linux.zip
unzip sonar-scanner-cli-4.8.0.2856-linux.zip
export PATH=$PATH:/path/to/sonar-scanner/bin
```

---

## 🚀 Kullanım

### NPM Scripts ile

```bash
# SonarCloud analizi
npm run sonar

# Local SonarQube analizi
npm run sonar:local

# Lint + SonarQube
npm run lint:sonar

# Coverage ile birlikte
npm run test:coverage && npm run sonar
```

### Manuel Analiz

```bash
# Frontend analizi
sonar-scanner \
  -Dsonar.projectKey=sportoonline \
  -Dsonar.sources=src \
  -Dsonar.host.url=https://sonarcloud.io \
  -Dsonar.login=YOUR_TOKEN

# PHP analizi dahil
sonar-scanner
```

### GitHub Actions ile Otomatik Analiz

Her push ve PR'de otomatik olarak çalışır:

```yaml
# .github/workflows/code-quality.yml zaten yapılandırılmış
# Her push'da otomatik analiz yapılır
```

---

## 📊 Kalite Kuralları

### Quality Gates

SonarCloud'da aşağıdaki quality gate'ler tanımlanmıştır:

| Metrik | Threshold | Açıklama |
|--------|-----------|----------|
| **Coverage** | ≥ 80% | Kod kapsamı |
| **Duplicated Lines** | ≤ 3% | Tekrarlanan kod |
| **Maintainability Rating** | A | Bakım kolaylığı |
| **Reliability Rating** | A | Güvenilirlik |
| **Security Rating** | A | Güvenlik |
| **Security Hotspots** | 0 | Güvenlik açıkları |
| **Bugs** | 0 | Hatalar |
| **Code Smells** | ≤ 50 | Kod kokuları |

### Özel Kurallar

```properties
# sonar-project.properties dosyasında tanımlı:

# Kod karmaşıklığı
sonar.complexity.threshold=10

# Dosya boyutu
sonar.file.lines.threshold=500

# Fonksiyon karmaşıklığı
sonar.function.complexity.threshold=10
```

---

## 🔧 Konfigürasyon Detayları

### Analiz Kapsamı

**Frontend (JavaScript/TypeScript/Vue):**
- `src/` - Tüm kaynak kodlar
- Coverage: `coverage/lcov.info`
- Exclusions: test dosyaları, node_modules

**Backend (PHP/Laravel):**
- `app/` - Uygulama kodu
- `resources/` - View ve asset'ler
- Coverage: `coverage/clover.xml`
- Exclusions: vendor, migrations, seeders

### Exclusion Listesi

Analizden çıkarılan dosyalar:
```
**/node_modules/**
**/vendor/**
**/dist/**
**/coverage/**
**/storage/**
**/database/migrations/**
**/database/seeders/**
**/*.min.js
**/*.bundle.js
```

---

## 📈 Raporlar

### SonarCloud Dashboard

Analizden sonra şu raporlara erişebilirsiniz:

1. **Overview**: Genel kalite metrikleri
2. **Issues**: Tespit edilen sorunlar
3. **Measures**: Detaylı ölçümler
4. **Code**: Kaynak kod incelemesi
5. **Activity**: Analiz geçmişi

### Local Raporlar

```bash
# Coverage raporu oluşturma
npm run test:coverage

# Coverage raporu: coverage/lcov-report/index.html
# PHP coverage: coverage/php-coverage.xml

# Bundle analizi
npm run analyze
```

---

## 🐛 Sorun Giderme

### SonarQube Scanner Bulunamıyor

```bash
# Windows'ta PATH kontrolü
where sonar-scanner

# macOS/Linux'ta
which sonar-scanner

# Kurulu değilse yukarıdaki kurulum adımlarını takip edin
```

### Authentication Hatası

```bash
# Token'ın doğru olduğundan emin olun
sonar-scanner -Dsonar.login=YOUR_TOKEN -X

# .env dosyasına ekleyebilirsiniz
echo "SONAR_TOKEN=your_token" >> .env
```

### Coverage Raporu Bulunamıyor

```bash
# Coverage oluşturulduğundan emin olun
npm run test:coverage

# PHP coverage için
php artisan test --coverage-clover coverage/clover.xml

# Dosyanın var olduğunu kontrol edin
ls -la coverage/
```

### Quality Gate Başarısız

**Yüksek Code Smell sayısı:**
```bash
# ESLint ile düzeltin
npm run lint -- --fix

# Manuel inceleyin
# SonarCloud > Issues > Code Smells
```

**Düşük Coverage:**
```bash
# Daha fazla test yazın
npm run test:coverage

# Coverage raporunu inceleyin
open coverage/lcov-report/index.html
```

**Security Hotspots:**
```bash
# SonarCloud'da review edin
# Administration > Security Hotspots

# Kritik olanları düzeltin
# Düşük riskli olanları "Safe" olarak işaretleyin
```

### Branch Analysis Çalışmıyor

```bash
# Branch adını belirtin
sonar-scanner -Dsonar.branch.name=feature/my-feature

# GitHub Actions'ta otomatik algılanır
# Workflow dosyası zaten yapılandırılmış
```

---

## 📚 Ek Kaynaklar

- [SonarCloud Dokumentasyonu](https://docs.sonarcloud.io/)
- [SonarQube Kuralları](https://rules.sonarsource.com/)
- [SonarQube Scanner CLI](https://docs.sonarqube.org/latest/analysis/scan/sonarscanner/)
- [Quality Gates](https://docs.sonarcloud.io/improving/quality-gates/)

---

## ✅ Checklist

Kurulumu tamamlamak için:

- [ ] SonarCloud hesabı oluşturuldu
- [ ] Organization oluşturuldu (`sportoonline`)
- [ ] Repository import edildi
- [ ] Token oluşturuldu
- [ ] GitHub Secret eklendi (`SONAR_TOKEN`)
- [ ] `sonar-project.properties` yapılandırıldı
- [ ] İlk analiz yapıldı (`npm run sonar`)
- [ ] Quality Gate geçildi
- [ ] Dashboard badge README'ye eklendi

---

## 🎯 Sonraki Adımlar

1. **README Badge Ekle:**
   ```markdown
   [![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=sportoonline&metric=alert_status)](https://sonarcloud.io/dashboard?id=sportoonline)
   ```

2. **Pre-commit Hook Ekle:**
   ```bash
   # .git/hooks/pre-commit
   npm run lint
   npm run test
   ```

3. **Quality Gate'i Sıkılaştır:**
   - Coverage'ı 85%'e çıkar
   - Code smell threshold'u düşür
   - Complexity limit'i azalt

4. **Takım Eğitimi:**
   - SonarCloud dashboard kullanımı
   - Issue nasıl çözülür
   - Quality metrics nasıl okunur
