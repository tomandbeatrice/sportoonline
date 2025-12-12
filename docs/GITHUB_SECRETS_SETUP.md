# GitHub Secrets Kurulum Rehberi

Bu döküman, CI/CD pipeline'ları için gerekli GitHub Secrets'ların nasıl ekleneceğini açıklar.

## 📋 Gerekli Token'lar

Aşağıdaki token'lar repository'ye eklenmelidir:

### 1. SONAR_TOKEN (SonarCloud)

**Amaç:** SonarCloud kod analizi için authentication

**Nasıl Alınır:**
1. [SonarCloud](https://sonarcloud.io) hesabına giriş yapın
2. My Account > Security > Generate Token
3. Token adı: `SportoOnline-GitHub-Actions`
4. Scope: Analyze projects
5. Token'ı kopyalayın (bir daha göstermez!)

**Nasıl Eklenir:**
```bash
# GitHub Repository'de:
Settings > Secrets and variables > Actions > New repository secret

Name: SONAR_TOKEN
Secret: <your_sonarcloud_token>
```

---

### 2. CC_TEST_REPORTER_ID (CodeClimate)

**Amaç:** CodeClimate kod kalitesi raporlaması

**Nasıl Alınır:**
1. [CodeClimate](https://codeclimate.com) hesabı oluşturun
2. Repository'yi bağlayın (Add a repository)
3. Repo Settings > Test Coverage
4. Test Reporter ID'yi kopyalayın

**Nasıl Eklenir:**
```bash
# GitHub Repository'de:
Settings > Secrets and variables > Actions > New repository secret

Name: CC_TEST_REPORTER_ID
Secret: <your_codeclimate_reporter_id>
```

---

### 3. SNYK_TOKEN (Snyk)

**Amaç:** Güvenlik açığı taraması

**Nasıl Alınır:**
1. [Snyk](https://snyk.io) hesabı oluşturun
2. Account Settings > General
3. Auth Token bölümünden token'ı kopyalayın
4. Veya CLI ile: `snyk auth` komutu

**Nasıl Eklenir:**
```bash
# GitHub Repository'de:
Settings > Secrets and variables > Actions > New repository secret

Name: SNYK_TOKEN
Secret: <your_snyk_auth_token>
```

---

### 4. CODECOV_TOKEN (Codecov)

**Amaç:** Test coverage tracking ve raporlama

**Nasıl Alınır:**
1. [Codecov](https://codecov.io) hesabı oluşturun (GitHub ile)
2. Repository'yi ekleyin
3. Settings > General
4. Repository Upload Token'ı kopyalayın

**Nasıl Eklenir:**
```bash
# GitHub Repository'de:
Settings > Secrets and variables > Actions > New repository secret

Name: CODECOV_TOKEN
Secret: <your_codecov_upload_token>
```

---

### 5. PERCY_TOKEN (Percy)

**Amaç:** Visual regression testing

**Nasıl Alınır:**
1. [Percy](https://percy.io) hesabı oluşturun
2. Create new project
3. Project settings > Tokens
4. PERCY_TOKEN'ı kopyalayın

**Nasıl Eklenir:**
```bash
# GitHub Repository'de:
Settings > Secrets and variables > Actions > New repository secret

Name: PERCY_TOKEN
Secret: <your_percy_project_token>
```

---

### 6. LHCI_GITHUB_APP_TOKEN (Lighthouse CI)

**Amaç:** Performance monitoring ve Lighthouse CI entegrasyonu

**Nasıl Alınır:**

**Opsiyon 1: GitHub App (Önerilen)**
1. [Lighthouse CI GitHub App](https://github.com/apps/lighthouse-ci) yükleyin
2. Repository'ye erişim verin
3. Token otomatik oluşturulur

**Opsiyon 2: Personal Access Token**
1. GitHub > Settings > Developer settings
2. Personal access tokens > Tokens (classic)
3. Generate new token (classic)
4. Permissions:
   - `repo` - Full control
   - `workflow` - Update workflows
5. Token'ı kopyalayın

**Nasıl Eklenir:**
```bash
# GitHub Repository'de:
Settings > Secrets and variables > Actions > New repository secret

Name: LHCI_GITHUB_APP_TOKEN
Secret: <your_github_token_or_app_token>
```

---

## 🔧 Toplu Kurulum Script'i

Tüm token'ları bir defada eklemek için GitHub CLI kullanabilirsiniz:

```bash
# GitHub CLI kurulumu (Windows)
winget install GitHub.cli

# veya (macOS)
brew install gh

# GitHub'a login
gh auth login

# Token'ları ekle
gh secret set SONAR_TOKEN -b "your_token_here"
gh secret set CC_TEST_REPORTER_ID -b "your_token_here"
gh secret set SNYK_TOKEN -b "your_token_here"
gh secret set CODECOV_TOKEN -b "your_token_here"
gh secret set PERCY_TOKEN -b "your_token_here"
gh secret set LHCI_GITHUB_APP_TOKEN -b "your_token_here"

# Tüm secret'ları listele
gh secret list
```

---

## ✅ Kurulum Doğrulama

Tüm secret'lar eklendikten sonra:

1. **GitHub Repository > Settings > Secrets and variables > Actions**
2. Şu secret'ların listede olduğunu kontrol edin:
   - ✅ SONAR_TOKEN
   - ✅ CC_TEST_REPORTER_ID
   - ✅ SNYK_TOKEN
   - ✅ CODECOV_TOKEN
   - ✅ PERCY_TOKEN
   - ✅ LHCI_GITHUB_APP_TOKEN

3. **Test için bir commit yapın:**
```bash
git commit --allow-empty -m "test: trigger CI/CD pipeline"
git push
```

4. **Actions sekmesinde workflow'ları kontrol edin**
   - Code Quality ✅
   - Performance Monitoring ✅
   - Security Scan ✅
   - Testing ✅

---

## 🔐 Güvenlik Best Practices

### Token Güvenliği
- ✅ Token'ları asla kod içine yazmayın
- ✅ Token'ları commit etmeyin
- ✅ `.env` dosyalarını `.gitignore`'a ekleyin
- ✅ Token'ları düzenli olarak rotate edin
- ✅ Minimum gerekli permission'ları verin

### Token Rotation (Dönem Dönem Yenileme)
```bash
# Her 3-6 ayda bir token'ları yenileyin
# Eski token'ı revoke edin
# Yeni token'ı GitHub'a ekleyin
gh secret set SONAR_TOKEN -b "new_token_here"
```

### Org-Level Secrets
Birden fazla repository kullanıyorsanız:
```bash
# Organization seviyesinde secret ekle
# Settings > Organization > Secrets > New organization secret
```

---

## 🐛 Sorun Giderme

### "Secret not found" Hatası
```bash
# Secret'ın doğru yazıldığından emin olun
# Workflow dosyasında: ${{ secrets.SONAR_TOKEN }}
# GitHub'da: SONAR_TOKEN (büyük/küçük harf duyarlı)
```

### "Unauthorized" Hatası
```bash
# Token'ın geçerli olduğunu kontrol edin
# Token'ın doğru permission'lara sahip olduğunu doğrulayın
# Token'ın expire olmadığını kontrol edin
```

### Token Test Etme
```bash
# SonarCloud
curl -u <SONAR_TOKEN>: https://sonarcloud.io/api/authentication/validate

# Codecov
curl --header "Authorization: token <CODECOV_TOKEN>" https://codecov.io/api/

# Snyk
snyk test --org=<your-org> --token=<SNYK_TOKEN>
```

---

## 📚 Ek Kaynaklar

- [GitHub Secrets Docs](https://docs.github.com/en/actions/security-guides/encrypted-secrets)
- [SonarCloud Tokens](https://docs.sonarcloud.io/advanced-setup/ci-based-analysis/github-actions/)
- [CodeClimate Test Reporter](https://docs.codeclimate.com/docs/configuring-test-coverage)
- [Snyk GitHub Actions](https://docs.snyk.io/integrations/ci-cd-integrations/github-actions-integration)
- [Codecov GitHub Actions](https://docs.codecov.com/docs/quick-start)
- [Percy CI/CD](https://docs.percy.io/docs/github-actions)
- [Lighthouse CI](https://github.com/GoogleChrome/lighthouse-ci/blob/main/docs/getting-started.md)

---

## 📞 Yardım

Sorun yaşarsanız:
1. Token'ların expire tarihi kontrolü
2. Permission'ları kontrol et
3. GitHub Actions logs'ları incele
4. Workflow dosyalarını kontrol et
5. Team lead'e danış

---

## ✨ Hızlı Başlangıç Checklist

- [ ] SonarCloud hesabı oluştur
- [ ] CodeClimate hesabı oluştur
- [ ] Snyk hesabı oluştur
- [ ] Codecov hesabı oluştur
- [ ] Percy hesabı oluştur (opsiyonel)
- [ ] Tüm token'ları al
- [ ] GitHub Secrets'a ekle
- [ ] Test commit yap
- [ ] Pipeline'ları kontrol et
- [ ] Badge'leri README'ye ekle
