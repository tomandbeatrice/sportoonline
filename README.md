# 🏆 sportoonline - Multi-Platform Marketplace

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10+-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white" alt="Vue 3">
  <img src="https://img.shields.io/badge/TypeScript-5.9-3178C6?style=for-the-badge&logo=typescript&logoColor=white" alt="TypeScript">
  <img src="https://img.shields.io/badge/Vite-7.1-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
</p>

<p align="center">
  <a href="https://sonarcloud.io/dashboard?id=sportoonline"><img src="https://sonarcloud.io/api/project_badges/measure?project=sportoonline&metric=alert_status" alt="Quality Gate Status"></a>
  <a href="https://sonarcloud.io/dashboard?id=sportoonline"><img src="https://sonarcloud.io/api/project_badges/measure?project=sportoonline&metric=coverage" alt="Coverage"></a>
  <a href="https://sonarcloud.io/dashboard?id=sportoonline"><img src="https://sonarcloud.io/api/project_badges/measure?project=sportoonline&metric=bugs" alt="Bugs"></a>
  <a href="https://sonarcloud.io/dashboard?id=sportoonline"><img src="https://sonarcloud.io/api/project_badges/measure?project=sportoonline&metric=vulnerabilities" alt="Vulnerabilities"></a>
  <a href="https://sonarcloud.io/dashboard?id=sportoonline"><img src="https://sonarcloud.io/api/project_badges/measure?project=sportoonline&metric=code_smells" alt="Code Smells"></a>
  <a href="https://sonarcloud.io/dashboard?id=sportoonline"><img src="https://sonarcloud.io/api/project_badges/measure?project=sportoonline&metric=duplicated_lines_density" alt="Duplicated Lines (%)"></a>
</p>

<p align="center">
  <strong>A comprehensive e-commerce, service marketplace, and ride-sharing platform with AI-powered search and multi-language support</strong>
</p>

---

## 🌟 Features

### 🛒 E-Commerce Platform
- **Seller Management** - Product listings, inventory, campaigns
- **Buyer Experience** - Shopping cart, favorites, order tracking
- **Admin Tools** - Order management, seller approvals, analytics
- **Multi-Vendor Support** - Independent seller dashboards

### 🔧 Service Marketplace
- **Service Provider Applications** - Professional service listings
- **Admin Approval Workflow** - Approve, reject, suspend, activate providers
- **Email Notifications** - Automated status updates
- **Service Booking System** - Real-time availability management

### 🚗 Ride-Sharing Platform
- **Ride Listings** - P2P ride sharing with detailed itineraries
- **Passenger Management** - Booking, cancellations, refunds
- **Admin Moderation** - Flag inappropriate rides, safety controls
- **Real-time Notifications** - SMS, email, push notifications

### 🤖 AI-Powered Features
- **🎤 Voice Search** - Multi-language speech recognition (TR/EN/AR/DE)
- **📷 Visual Search** - Google Cloud Vision API integration
- **🌍 Multi-Language** - 4 languages with RTL support for Arabic
- **🔍 Smart Search** - Semantic search with filters

### 📊 Admin Dashboard
- **Analytics & Reports** - Sales metrics, user statistics
- **Financial Management** - Commission tracking, payouts
- **User Management** - Customers, sellers, service providers
- **System Settings** - Email config, themes, notifications

---

## 🚀 Tech Stack

### Backend
- **Laravel 10+** - PHP framework
- **MySQL/PostgreSQL** - Database
- **Laravel Sanctum** - API authentication
- **Laravel Mail** - Email system
- **Queue Workers** - Background jobs

### Frontend
- **Vue 3.5** - Progressive JavaScript framework
- **TypeScript 5.9** - Type-safe development
- **Vite 7.1** - Build tool & dev server
- **Pinia 3.0** - State management
- **vue-i18n 9.14** - Internationalization
- **DaisyUI 5.0** - UI components
- **Tailwind CSS 4.1** - Utility-first CSS

### APIs & Services
- **Google Cloud Vision** - Visual search
- **Web Speech API** - Voice recognition
- **WebSocket** - Real-time communications

---

## 📦 Installation

### Prerequisites
- PHP 8.1+
- Composer 2.5+
- Node.js 18+
- MySQL 8.0+ / PostgreSQL 14+

### Quick Start

```bash
# Clone repository
git clone https://github.com/yourusername/sportoonline.git
cd sportoonline

# Backend setup
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

# Frontend setup
npm install
npm run dev

# Start Laravel server
php artisan serve
```

**Access:** http://localhost:3000

---

## 🌍 Multi-Language Support

| Language | Code | Direction | Status |
|----------|------|-----------|--------|
| Türkçe   | `tr` | LTR       | ✅ Complete |
| English  | `en` | LTR       | ✅ Complete |
| العربية  | `ar` | RTL       | ✅ Complete |
| Deutsch  | `de` | LTR       | ✅ Complete |

**Translation Files:** `src/i18n/locales/`

### Usage
```typescript
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

// Change language
locale.value = 'ar' // Arabic with RTL

// Use translation
{{ t('nav.home') }}
```

---

## 🎤 Voice Search

Built with **Web Speech API** for natural language search.

### Supported Languages
- Turkish (tr-TR)
- English (en-US)
- Arabic (ar-SA)
- German (de-DE)

### Requirements
- HTTPS (required for microphone access)
- Chrome/Edge browser (best support)

**Component:** `src/components/VoiceSearch.vue`

---

## 📷 Visual Search

Powered by **Google Cloud Vision API** for image-based product search.

### Features
- Image upload (drag & drop)
- Camera capture (mobile)
- Object detection
- Label recognition

### Setup
```env
GOOGLE_CLOUD_KEY_FILE=/path/to/service-account-key.json
GOOGLE_CLOUD_PROJECT_ID=your-project-id
```

**API Endpoint:** `POST /api/search/visual`

---

## 📧 Email System

### Notification Templates
- ✅ Service Provider Approved
- ✅ Service Provider Rejected
- ✅ Service Provider Suspended
- ✅ Service Provider Activated

**Location:** `resources/views/emails/`

### Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=noreply@sportoonline.com
```

---

## 🔧 Admin Workflows

### Service Provider Approval

```php
// Approve provider
POST /api/admin/service-providers/{id}/approve

// Reject provider
POST /api/admin/service-providers/{id}/reject
{
  "rejection_reason": "Incomplete documentation"
}

// Suspend provider
POST /api/admin/service-providers/{id}/suspend
{
  "suspension_reason": "Policy violation",
  "suspension_until": "2025-12-31"
}

// Activate provider
POST /api/admin/service-providers/{id}/activate
```

### Ride Moderation

```php
// Approve ride
POST /api/admin/rides/{id}/approve

// Flag ride
POST /api/admin/rides/{id}/flag
{
  "flag_reason": "Inappropriate description",
  "suspend": true
}

// Cancel ride (with refunds)
POST /api/admin/rides/{id}/cancel
{
  "cancel_reason": "Safety concerns"
}
```

---

## 📁 Project Structure

```
sportoonline/
├── app/
│   ├── Http/Controllers/
│   │   ├── ServiceMarketplaceController.php
│   │   ├── RideShareController.php
│   │   └── VisualSearchController.php
│   ├── Mail/
│   │   ├── ServiceProviderApproved.php
│   │   └── ...
│   └── Models/
│       ├── ServiceProvider.php
│       └── RideAd.php
├── resources/
│   └── views/
│       └── emails/
├── src/
│   ├── components/
│   │   ├── VoiceSearch.vue
│   │   ├── VisualSearch.vue
│   │   └── LanguageSwitcher.vue
│   ├── i18n/
│   │   └── locales/
│   ├── views/
│   │   ├── admin/
│   │   ├── seller/
│   │   ├── buyer/
│   │   └── marketplace/
│   └── services/
│       └── api.ts
└── routes/
    ├── api.php
    └── web.php
```

---

## 🧪 Testing

Comprehensive test results available in `TEST_RESULTS.md`.

```bash
# Run backend tests
php artisan test

# Run frontend tests
npm run test

# E2E tests
npm run test:e2e
```

**Test Coverage:**
- ✅ Multi-language switching
- ✅ Voice search functionality
- ✅ Visual search upload
- ✅ Admin approval workflows
- ✅ Email notifications
- ✅ RTL layout (Arabic)

---

## 📚 Documentation

- **[CI/CD Pipeline](docs/CI_CD_DOCUMENTATION.md)** - Automated testing & deployment
- **[GitHub Secrets Setup](docs/GITHUB_SECRETS_SETUP.md)** - Token configuration guide
- **[SonarQube Setup](docs/SONARQUBE_SETUP.md)** - Code quality analysis
- **[Code Improvements](docs/SONARQUBE_IMPROVEMENTS.md)** - Quality improvement guide
- **[Deployment Guide](DEPLOYMENT_GUIDE.md)** - Production setup
- **[Test Results](TEST_RESULTS.md)** - Comprehensive testing
- **[Code Cleanup](CODE_CLEANUP.md)** - Code quality recommendations

---

## 🔐 Security

- Laravel Sanctum authentication
- CSRF protection
- XSS prevention
- SQL injection protection
- Rate limiting
- HTTPS required (production)

---

## 🤝 Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
