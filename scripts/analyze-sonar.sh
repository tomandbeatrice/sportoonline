#!/bin/bash

# SonarQube Analiz Script'i
# Bu script proje için SonarQube analizi yapar

set -e

echo "🔍 SonarQube Analizi Başlatılıyor..."

# Renk kodları
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Analiz tipi kontrolü
ANALYSIS_TYPE=${1:-cloud}

# SonarQube Scanner kontrolü
if ! command -v sonar-scanner &> /dev/null; then
    echo -e "${RED}❌ SonarQube Scanner bulunamadı!${NC}"
    echo "Kurulum için: https://docs.sonarqube.org/latest/analysis/scan/sonarscanner/"
    exit 1
fi

echo -e "${GREEN}✅ SonarQube Scanner bulundu${NC}"

# Coverage oluştur
echo "📊 Test coverage oluşturuluyor..."
npm run test:coverage || {
    echo -e "${YELLOW}⚠️  Coverage oluşturulamadı, devam ediliyor...${NC}"
}

# PHP Coverage (eğer varsa)
if [ -f "phpunit.xml" ]; then
    echo "🐘 PHP test coverage oluşturuluyor..."
    php artisan test --coverage-clover coverage/clover.xml || {
        echo -e "${YELLOW}⚠️  PHP coverage oluşturulamadı, devam ediliyor...${NC}"
    }
fi

# Analiz tipine göre çalıştır
if [ "$ANALYSIS_TYPE" = "local" ]; then
    echo "🏠 Local SonarQube analizi yapılıyor..."
    
    # Local SonarQube URL kontrolü
    SONAR_HOST=${SONAR_HOST_URL:-http://localhost:9000}
    
    sonar-scanner \
        -Dsonar.host.url="$SONAR_HOST" \
        -Dsonar.login="${SONAR_TOKEN:-admin}" \
        ${SONAR_PROJECT_KEY:+-Dsonar.projectKey="$SONAR_PROJECT_KEY"}
        
elif [ "$ANALYSIS_TYPE" = "cloud" ]; then
    echo "☁️  SonarCloud analizi yapılıyor..."
    
    # Token kontrolü
    if [ -z "$SONAR_TOKEN" ]; then
        echo -e "${RED}❌ SONAR_TOKEN environment variable bulunamadı!${NC}"
        echo "Export edin: export SONAR_TOKEN=your_token"
        exit 1
    fi
    
    sonar-scanner \
        -Dsonar.host.url=https://sonarcloud.io \
        -Dsonar.login="$SONAR_TOKEN"
        
else
    echo -e "${RED}❌ Geçersiz analiz tipi: $ANALYSIS_TYPE${NC}"
    echo "Kullanım: ./analyze-sonar.sh [cloud|local]"
    exit 1
fi

echo -e "${GREEN}✅ Analiz tamamlandı!${NC}"
echo ""
echo "📊 Sonuçları görüntülemek için:"
if [ "$ANALYSIS_TYPE" = "cloud" ]; then
    echo "   https://sonarcloud.io/dashboard?id=sportoonline"
else
    echo "   $SONAR_HOST/dashboard?id=sportoonline"
fi
