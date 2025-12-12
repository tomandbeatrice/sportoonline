#!/bin/bash

# GitHub Secrets Kurulum Script'i (Bash/Linux/macOS)

echo "🔐 GitHub Secrets Kurulum Script'i"
echo ""

# GitHub CLI kontrolü
if ! command -v gh &> /dev/null; then
    echo "❌ GitHub CLI (gh) bulunamadı!"
    echo "Kurulum için:"
    echo "  macOS: brew install gh"
    echo "  Linux: https://github.com/cli/cli/blob/trunk/docs/install_linux.md"
    exit 1
fi

echo "✅ GitHub CLI bulundu"
echo ""

# GitHub'a login kontrolü
if ! gh auth status &> /dev/null; then
    echo "❌ GitHub'a login olmanız gerekiyor!"
    echo "Login için: gh auth login"
    exit 1
fi

echo "✅ GitHub authentication OK"
echo ""

# Token'ları kullanıcıdan al
echo "📝 Token'ları girin (boş bırakırsanız skip edilir):"
echo ""

# SONAR_TOKEN
echo "1️⃣  SONAR_TOKEN (SonarCloud)"
echo "   Nasıl alınır: https://sonarcloud.io > My Account > Security > Generate Token"
read -p "   Token: " SONAR_TOKEN

# CC_TEST_REPORTER_ID
echo ""
echo "2️⃣  CC_TEST_REPORTER_ID (CodeClimate)"
echo "   Nasıl alınır: https://codeclimate.com > Repo Settings > Test Coverage"
read -p "   Reporter ID: " CC_TEST_REPORTER_ID

# SNYK_TOKEN
echo ""
echo "3️⃣  SNYK_TOKEN (Snyk)"
echo "   Nasıl alınır: https://snyk.io > Account Settings > Auth Token"
read -p "   Token: " SNYK_TOKEN

# CODECOV_TOKEN
echo ""
echo "4️⃣  CODECOV_TOKEN (Codecov)"
echo "   Nasıl alınır: https://codecov.io > Settings > Repository Upload Token"
read -p "   Token: " CODECOV_TOKEN

# PERCY_TOKEN
echo ""
echo "5️⃣  PERCY_TOKEN (Percy - Opsiyonel)"
echo "   Nasıl alınır: https://percy.io > Project Settings > Tokens"
read -p "   Token: " PERCY_TOKEN

# LHCI_GITHUB_APP_TOKEN
echo ""
echo "6️⃣  LHCI_GITHUB_APP_TOKEN (Lighthouse CI)"
echo "   Nasıl alınır: GitHub > Settings > Developer settings > Personal tokens"
read -p "   Token: " LHCI_GITHUB_APP_TOKEN

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Secret'ları ekle
secretsAdded=0
secretsSkipped=0

add_secret() {
    local name=$1
    local value=$2

    if [ -z "$value" ]; then
        echo "  ⏭️  $name - Atlandı (boş)"
        ((secretsSkipped++))
        return
    fi

    if echo "$value" | gh secret set "$name"; then
        echo "  ✅ $name - Eklendi"
        ((secretsAdded++))
    else
        echo "  ❌ $name - Hata!"
    fi
}

echo "🚀 Secrets ekleniyor..."
echo ""

add_secret "SONAR_TOKEN" "$SONAR_TOKEN"
add_secret "CC_TEST_REPORTER_ID" "$CC_TEST_REPORTER_ID"
add_secret "SNYK_TOKEN" "$SNYK_TOKEN"
add_secret "CODECOV_TOKEN" "$CODECOV_TOKEN"
add_secret "PERCY_TOKEN" "$PERCY_TOKEN"
add_secret "LHCI_GITHUB_APP_TOKEN" "$LHCI_GITHUB_APP_TOKEN"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📊 Özet:"
echo "  ✅ Eklenen: $secretsAdded"
echo "  ⏭️  Atlanan: $secretsSkipped"
echo ""

if [ $secretsAdded -gt 0 ]; then
    echo "✅ Secret'lar başarıyla eklendi!"
    echo ""
    echo "🔍 Kontrol için:"
    echo "  gh secret list"
    echo ""
    echo "📚 Veya GitHub'da:"
    echo "  Settings > Secrets and variables > Actions"
    echo ""

    # Secret listesini göster
    echo "📋 Mevcut secrets:"
    gh secret list
else
    echo "⚠️  Hiçbir secret eklenmedi!"
fi

echo ""
echo "🎯 Sonraki adımlar:"
echo "  1. Test için commit yapın: git commit --allow-empty -m 'test: CI/CD'"
echo "  2. Push edin: git push"
echo "  3. Actions sekmesini kontrol edin"
echo ""
