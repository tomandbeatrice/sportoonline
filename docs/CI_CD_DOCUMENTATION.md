# CI/CD Pipeline ve Kod Analiz Dokümantasyonu

## 🚀 Genel Bakış

SportoOnline projesi için kapsamlı CI/CD pipeline'ları ve otomatik kod analiz sistemleri kurulmuştur.

## 📋 Kurulu Pipeline'lar

### 1. Code Quality Analysis (`.github/workflows/code-quality.yml`)

**Çalışma Zamanı:** Her push ve PR'de

**İçerik:**
- ✅ **ESLint & Prettier**: Kod stil ve kalite kontrolü
- ✅ **TypeScript Check**: Tip güvenliği doğrulama
- ✅ **SonarCloud**: Kapsamlı kod kalitesi analizi
- ✅ **CodeClimate**: Teknik borç ve maintainability analizi
- ✅ **Security Scan**: Güvenlik açığı taraması
- ✅ **Bundle Size**: Paket boyutu analizi
- ✅ **Lighthouse CI**: Performans metrikleri

**Oluşturulan Raporlar:**
- ESLint JSON raporu
- TypeScript type coverage
- SonarCloud kalite gate
- CodeClimate maintainability score
- Bundle size trend
- Lighthouse performance scores

### 2. Performance Monitoring (`.github/workflows/performance-monitoring.yml`)

**Çalışma Zamanı:** Her gece saat 02:00

**İçerik:**
- 📊 **Bundle Size Trend**: Paket boyutu izleme
- ⚡ **Lighthouse Metrics**: Periyodik performans ölçümü
- 🔍 **Code Complexity**: Kod karmaşıklığı analizi
- 📦 **Dependency Updates**: Güncel olmayan paketleri kontrol

### 3. Security Scan (`.github/workflows/security.yml`)

**Çalışma Zamanı:** Her hafta pazartesi saat 03:00

**İçerik:**
- 🔐 **NPM Audit**: Node.js güvenlik taraması
- 🛡️ **Snyk**: Zafiyet tespiti
- 🔒 **OWASP Dependency Check**: Bağımlılık güvenliği
- 🕵️ **CodeQL**: Gelişmiş kod analizi
- 🔑 **Gitleaks**: Secret tarama
- 🐘 **PHP Security**: Laravel güvenlik kontrolü

### 4. Automated Testing (`.github/workflows/testing.yml`)

**Çalışma Zamanı:** Her push ve PR'de

**İçerik:**
- 🧪 **Frontend Unit Tests**: Vitest testleri
- 🐘 **Backend Unit Tests**: PHPUnit testleri
- 🎭 **E2E Tests**: Playwright testleri
- 📸 **Visual Regression**: Percy snapshot testleri
- 📊 **Coverage Reports**: Codecov entegrasyonu

## 🔧 Konfigürasyon Dosyaları

### SonarCloud (`sonar-project.properties`)

```properties
sonar.projectKey=sportoonline
sonar.organization=sportoonline
sonar.sources=src
sonar.tests=tests
sonar.javascript.lcov.reportPaths=coverage/lcov.info
```

**Kurulum:**
1. [SonarCloud](https://sonarcloud.io) hesabı oluşturun
2. GitHub repository'yi import edin
3. `SONAR_TOKEN` secret'ını GitHub'a ekleyin

### CodeClimate (`.codeclimate.yml`)

```yaml
checks:
  method-complexity:
    threshold: 10
  file-lines:
    threshold: 500
```

**Kurulum:**
1. [CodeClimate](https://codeclimate.com) hesabı oluşturun
2. Repository'yi bağlayın
3. `CC_TEST_REPORTER_ID` secret'ını ekleyin

### Lighthouse CI (`lighthouserc.js`)

```javascript
assertions: {
  'categories:performance': ['warn', { minScore: 0.9 }],
  'categories:accessibility': ['error', { minScore: 0.9 }]
}
```

## 📊 Kalite Metrikleri

### SonarCloud Quality Gates
- **Bugs**: 0 (A rating)
- **Vulnerabilities**: 0 (A rating)
- **Code Smells**: < 50
- **Coverage**: > 80%
- **Duplications**: < 3%
- **Maintainability Rating**: A

### CodeClimate Thresholds
- **Maintainability**: A-B rating
- **Test Coverage**: > 75%
- **Method Complexity**: < 10
- **File Lines**: < 500

### Lighthouse Targets
- **Performance**: > 90
- **Accessibility**: > 90
- **Best Practices**: > 90
- **SEO**: > 90
- **FCP**: < 2s
- **LCP**: < 2.5s

## 🔐 Gerekli GitHub Secrets

Aşağıdaki secret'ları GitHub Settings > Secrets > Actions'a ekleyin:

```bash
# Code Quality
SONAR_TOKEN=your_sonarcloud_token
CC_TEST_REPORTER_ID=your_codeclimate_reporter_id

# Security
SNYK_TOKEN=your_snyk_token

# Testing
CODECOV_TOKEN=your_codecov_token
PERCY_TOKEN=your_percy_token

# Performance
LHCI_GITHUB_APP_TOKEN=your_lighthouse_token
```

## 📝 Kullanım

### Manuel Çalıştırma

```bash
# Kod kalitesi kontrolü
npm run lint
npm run prettier:check

# Testler
npm run test
npm run test:coverage

# Build
npm run build

# Bundle analizi
npm run analyze
```

### Pipeline Durumu

Pipeline'ların durumunu GitHub Actions sekmesinden takip edebilirsiniz:
- ✅ Yeşil: Tüm kontroller başarılı
- ⚠️ Sarı: Uyarılar var ama build başarılı
- ❌ Kırmızı: Kritik hatalar var

## 📈 Raporlar

### Oluşturulan Artifactler

Her pipeline çalıştırmasında şu raporlar oluşturulur:

1. **eslint-report.json**: ESLint bulguları
2. **bundle-analysis**: Bundle boyut analizi
3. **security-reports**: Güvenlik tarama sonuçları
4. **complexity-report**: Kod karmaşıklığı metrikleri
5. **playwright-report**: E2E test sonuçları

Raporlara Actions > Workflow Run > Artifacts'den erişebilirsiniz.

## 🔄 Sürekli İyileştirme

### Haftalık Görevler
- 📊 Performance metrics'i gözden geçir
- 🔐 Security scan sonuçlarını kontrol et
- 📦 Dependency güncellemelerini değerlendir

### Aylık Görevler
- 📈 Code quality trend'lerini analiz et
- 🎯 Quality gate threshold'larını ayarla
- 📚 Dokümantasyonu güncelle

## 🚨 Sorun Giderme

### Pipeline Başarısız Olursa

1. **ESLint Hataları**: 
   ```bash
   npm run lint -- --fix
   ```

2. **TypeScript Hataları**:
   ```bash
   npx tsc --noEmit
   ```

3. **Test Hataları**:
   ```bash
   npm run test -- --reporter=verbose
   ```

4. **Build Hataları**:
   ```bash
   npm run build -- --mode development
   ```

## 📚 Ek Kaynaklar

- [SonarCloud Docs](https://docs.sonarcloud.io/)
- [CodeClimate Docs](https://docs.codeclimate.com/)
- [Lighthouse CI Docs](https://github.com/GoogleChrome/lighthouse-ci)
- [GitHub Actions Docs](https://docs.github.com/en/actions)
- [Codecov Docs](https://docs.codecov.com/)

## 🎯 Best Practices

1. **Her commit öncesi**:
   - Lint kontrolü yapın
   - Testleri çalıştırın
   - Type check yapın

2. **PR oluşturmadan önce**:
   - Tüm testlerin geçtiğinden emin olun
   - Coverage'ın düşmediğini kontrol edin
   - Bundle size'ın artmadığını doğrulayın

3. **Merge öncesi**:
   - Tüm quality checks'in geçtiğini onaylayın
   - Security scan'leri gözden geçirin
   - Performance regression olmadığını kontrol edin

## 📞 Destek

Pipeline ile ilgili sorunlar için:
- GitHub Issues açın
- Team lead'e bildirin
- CI/CD dokümantasyonuna bakın
