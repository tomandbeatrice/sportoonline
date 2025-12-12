# GitHub Secrets Kurulum Script'i
# Bu script GitHub CLI kullanarak tüm gerekli secrets'ları ekler

Write-Host "🔐 GitHub Secrets Kurulum Script'i" -ForegroundColor Cyan
Write-Host ""

# GitHub CLI kontrolü
$gh = Get-Command gh -ErrorAction SilentlyContinue
if (-not $gh) {
    Write-Host "❌ GitHub CLI (gh) bulunamadı!" -ForegroundColor Red
    Write-Host "Kurulum için: winget install GitHub.cli" -ForegroundColor Yellow
    Write-Host "veya: https://cli.github.com/" -ForegroundColor Yellow
    exit 1
}

Write-Host "✅ GitHub CLI bulundu" -ForegroundColor Green
Write-Host ""

# GitHub'a login kontrolü
$authStatus = gh auth status 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ GitHub'a login olmanız gerekiyor!" -ForegroundColor Red
    Write-Host "Login için: gh auth login" -ForegroundColor Yellow
    exit 1
}

Write-Host "✅ GitHub authentication OK" -ForegroundColor Green
Write-Host ""

# Token'ları kullanıcıdan al
Write-Host "📝 Token'ları girin (boş bırakırsanız skip edilir):" -ForegroundColor Cyan
Write-Host ""

# SONAR_TOKEN
Write-Host "1️⃣  SONAR_TOKEN (SonarCloud)" -ForegroundColor Yellow
Write-Host "   Nasıl alınır: https://sonarcloud.io > My Account > Security > Generate Token" -ForegroundColor Gray
$SONAR_TOKEN = Read-Host "   Token"

# CC_TEST_REPORTER_ID
Write-Host ""
Write-Host "2️⃣  CC_TEST_REPORTER_ID (CodeClimate)" -ForegroundColor Yellow
Write-Host "   Nasıl alınır: https://codeclimate.com > Repo Settings > Test Coverage" -ForegroundColor Gray
$CC_TEST_REPORTER_ID = Read-Host "   Reporter ID"

# SNYK_TOKEN
Write-Host ""
Write-Host "3️⃣  SNYK_TOKEN (Snyk)" -ForegroundColor Yellow
Write-Host "   Nasıl alınır: https://snyk.io > Account Settings > Auth Token" -ForegroundColor Gray
$SNYK_TOKEN = Read-Host "   Token"

# CODECOV_TOKEN
Write-Host ""
Write-Host "4️⃣  CODECOV_TOKEN (Codecov)" -ForegroundColor Yellow
Write-Host "   Nasıl alınır: https://codecov.io > Settings > Repository Upload Token" -ForegroundColor Gray
$CODECOV_TOKEN = Read-Host "   Token"

# PERCY_TOKEN
Write-Host ""
Write-Host "5️⃣  PERCY_TOKEN (Percy - Opsiyonel)" -ForegroundColor Yellow
Write-Host "   Nasıl alınır: https://percy.io > Project Settings > Tokens" -ForegroundColor Gray
$PERCY_TOKEN = Read-Host "   Token"

# LHCI_GITHUB_APP_TOKEN
Write-Host ""
Write-Host "6️⃣  LHCI_GITHUB_APP_TOKEN (Lighthouse CI)" -ForegroundColor Yellow
Write-Host "   Nasıl alınır: GitHub > Settings > Developer settings > Personal tokens" -ForegroundColor Gray
$LHCI_GITHUB_APP_TOKEN = Read-Host "   Token"

Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Cyan
Write-Host ""

# Secret'ları ekle
$secretsAdded = 0
$secretsSkipped = 0

function Add-GitHubSecret {
    param(
        [string]$Name,
        [string]$Value
    )
    
    if ([string]::IsNullOrWhiteSpace($Value)) {
        Write-Host "  ⏭️  $Name - Atlandı (boş)" -ForegroundColor Gray
        $script:secretsSkipped++
        return
    }
    
    try {
        $Value | gh secret set $Name
        if ($LASTEXITCODE -eq 0) {
            Write-Host "  ✅ $Name - Eklendi" -ForegroundColor Green
            $script:secretsAdded++
        } else {
            Write-Host "  ❌ $Name - Hata!" -ForegroundColor Red
        }
    } catch {
        Write-Host "  ❌ $Name - Hata: $_" -ForegroundColor Red
    }
}

Write-Host "🚀 Secrets ekleniyor..." -ForegroundColor Cyan
Write-Host ""

Add-GitHubSecret -Name "SONAR_TOKEN" -Value $SONAR_TOKEN
Add-GitHubSecret -Name "CC_TEST_REPORTER_ID" -Value $CC_TEST_REPORTER_ID
Add-GitHubSecret -Name "SNYK_TOKEN" -Value $SNYK_TOKEN
Add-GitHubSecret -Name "CODECOV_TOKEN" -Value $CODECOV_TOKEN
Add-GitHubSecret -Name "PERCY_TOKEN" -Value $PERCY_TOKEN
Add-GitHubSecret -Name "LHCI_GITHUB_APP_TOKEN" -Value $LHCI_GITHUB_APP_TOKEN

Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Cyan
Write-Host ""
Write-Host "📊 Özet:" -ForegroundColor Cyan
Write-Host "  ✅ Eklenen: $secretsAdded" -ForegroundColor Green
Write-Host "  ⏭️  Atlanan: $secretsSkipped" -ForegroundColor Gray
Write-Host ""

if ($secretsAdded -gt 0) {
    Write-Host "✅ Secret'lar başarıyla eklendi!" -ForegroundColor Green
    Write-Host ""
    Write-Host "🔍 Kontrol için:" -ForegroundColor Cyan
    Write-Host "  gh secret list" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "📚 Veya GitHub'da:" -ForegroundColor Cyan
    Write-Host "  Settings > Secrets and variables > Actions" -ForegroundColor Yellow
    Write-Host ""
    
    # Secret listesini göster
    Write-Host "📋 Mevcut secrets:" -ForegroundColor Cyan
    gh secret list
} else {
    Write-Host "⚠️  Hiçbir secret eklenmedi!" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "🎯 Sonraki adımlar:" -ForegroundColor Cyan
Write-Host "  1. Test için commit yapın: git commit --allow-empty -m 'test: CI/CD'" -ForegroundColor White
Write-Host "  2. Push edin: git push" -ForegroundColor White
Write-Host "  3. Actions sekmesini kontrol edin" -ForegroundColor White
Write-Host ""
