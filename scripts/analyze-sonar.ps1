# SonarQube Analiz Script'i - PowerShell
# Bu script proje için SonarQube analizi yapar

param(
    [Parameter(Position=0)]
    [ValidateSet("cloud", "local")]
    [string]$AnalysisType = "cloud"
)

Write-Host "🔍 SonarQube Analizi Başlatılıyor..." -ForegroundColor Cyan

# SonarQube Scanner kontrolü
$sonarScanner = Get-Command sonar-scanner -ErrorAction SilentlyContinue

if (-not $sonarScanner) {
    Write-Host "❌ SonarQube Scanner bulunamadı!" -ForegroundColor Red
    Write-Host "Kurulum için:" -ForegroundColor Yellow
    Write-Host "  choco install sonarqube-scanner" -ForegroundColor Yellow
    Write-Host "  veya https://docs.sonarqube.org/latest/analysis/scan/sonarscanner/" -ForegroundColor Yellow
    exit 1
}

Write-Host "✅ SonarQube Scanner bulundu" -ForegroundColor Green

# Coverage oluştur
Write-Host "📊 Test coverage oluşturuluyor..." -ForegroundColor Cyan
try {
    npm run test:coverage
    Write-Host "✅ Coverage oluşturuldu" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Coverage oluşturulamadı, devam ediliyor..." -ForegroundColor Yellow
}

# PHP Coverage (eğer varsa)
if (Test-Path "phpunit.xml") {
    Write-Host "🐘 PHP test coverage oluşturuluyor..." -ForegroundColor Cyan
    try {
        php artisan test --coverage-clover coverage/clover.xml
        Write-Host "✅ PHP coverage oluşturuldu" -ForegroundColor Green
    } catch {
        Write-Host "⚠️  PHP coverage oluşturulamadı, devam ediliyor..." -ForegroundColor Yellow
    }
}

# Analiz tipine göre çalıştır
if ($AnalysisType -eq "local") {
    Write-Host "🏠 Local SonarQube analizi yapılıyor..." -ForegroundColor Cyan
    
    # Local SonarQube URL
    $sonarHost = if ($env:SONAR_HOST_URL) { $env:SONAR_HOST_URL } else { "http://localhost:9000" }
    $sonarLogin = if ($env:SONAR_TOKEN) { $env:SONAR_TOKEN } else { "admin" }
    
    $arguments = @(
        "-Dsonar.host.url=$sonarHost",
        "-Dsonar.login=$sonarLogin"
    )
    
    if ($env:SONAR_PROJECT_KEY) {
        $arguments += "-Dsonar.projectKey=$($env:SONAR_PROJECT_KEY)"
    }
    
    & sonar-scanner $arguments
    
} elseif ($AnalysisType -eq "cloud") {
    Write-Host "☁️  SonarCloud analizi yapılıyor..." -ForegroundColor Cyan
    
    # Token kontrolü
    if (-not $env:SONAR_TOKEN) {
        Write-Host "❌ SONAR_TOKEN environment variable bulunamadı!" -ForegroundColor Red
        Write-Host "Set edin: `$env:SONAR_TOKEN='your_token'" -ForegroundColor Yellow
        exit 1
    }
    
    $arguments = @(
        "-Dsonar.host.url=https://sonarcloud.io",
        "-Dsonar.login=$($env:SONAR_TOKEN)"
    )
    
    & sonar-scanner $arguments
}

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "✅ Analiz tamamlandı!" -ForegroundColor Green
    Write-Host ""
    Write-Host "📊 Sonuçları görüntülemek için:" -ForegroundColor Cyan
    
    if ($AnalysisType -eq "cloud") {
        Write-Host "   https://sonarcloud.io/dashboard?id=sportoonline" -ForegroundColor Yellow
    } else {
        $sonarHost = if ($env:SONAR_HOST_URL) { $env:SONAR_HOST_URL } else { "http://localhost:9000" }
        Write-Host "   $sonarHost/dashboard?id=sportoonline" -ForegroundColor Yellow
    }
} else {
    Write-Host ""
    Write-Host "❌ Analiz sırasında hata oluştu!" -ForegroundColor Red
    exit 1
}
