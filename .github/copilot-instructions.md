# GitHub Copilot Instructions for sportoonline

## Project Overview

sportoonline is a comprehensive multi-platform marketplace built with Laravel 11 and Vue 3, featuring:
- **E-Commerce Platform** - Multi-vendor marketplace with seller/buyer dashboards
- **Service Marketplace** - Professional service listings with admin approval workflow
- **Ride-Sharing Platform** - P2P ride sharing with real-time notifications
- **AI-Powered Features** - Voice search and visual search (Google Cloud Vision)
- **Multi-Language Support** - Currently Turkish, with plans for English, Arabic (RTL), and German

## Technology Stack

### Backend
- **Laravel 11** (PHP 8.2+)
- **MySQL/PostgreSQL** database
- **Laravel Sanctum** for API authentication
- **Queue Workers** for background jobs
- **Pest/PHPUnit** for testing

### Frontend
- **Vue 3.5** with Composition API
- **TypeScript 5.9** (strict mode)
- **Vite 7.1** for build tooling
- **Pinia 3.0** for state management
- **vue-i18n 11.2** for internationalization
- **DaisyUI 5.0 + Tailwind CSS 4.1** for styling
- **Vitest** for testing

## Architecture & Project Structure

### Modular Architecture
The project uses a modular architecture where each module has its own:
- Controllers
- Services (business logic layer)
- Models
- Routes
- Requests (validation)

```
app/Modules/
├── Ecommerce/
├── Campaign/
├── Seller/
└── Wallet/
```

### Frontend Structure
```
src/
├── components/     # Reusable Vue components
├── views/          # Page-level components (admin/, seller/, buyer/, marketplace/)
├── locales/        # Translation files (currently tr.json, planned: en, ar, de)
├── i18n/           # i18n configuration
├── services/       # API client and business logic
└── stores/         # Pinia state management
```

## Coding Standards & Conventions

### PHP/Laravel
- Follow **PSR-12** coding standards
- Use **Service classes** for business logic (never put business logic in controllers)
- Use **Form Request classes** for validation
- Use **Eloquent ORM** for database operations
- Use **PHPStan level 5** for static analysis
- Always type-hint parameters and return types
- Use **Laravel's built-in features** (Mail, Queue, Events, etc.) instead of custom implementations

### JavaScript/TypeScript
- Use **TypeScript** for all new code
- Follow **Vue 3 Composition API** style (script setup)
- Use **double quotes** for strings (enforced by ESLint)
- Always end statements with **semicolons**
- Use **Pinia** for state management (no Vuex)
- Use **vue-i18n** for all user-facing text (never hardcode text)
- Follow **Vue 3 style guide** recommendations

### Code Style
- Prefer **explicit over implicit** code
- Write **self-documenting code** with clear variable/function names
- Add comments only for complex business logic or non-obvious decisions
- Keep functions small and focused (single responsibility)

## Build, Test & Lint Commands

### Backend
```bash
# Install dependencies
composer install

# Run tests
php artisan test
./vendor/bin/pest

# Static analysis
./vendor/bin/phpstan analyse

# Code formatting
./vendor/bin/pint
```

### Frontend
```bash
# Install dependencies
npm install

# Development server
npm run dev

# Build for production
npm run build

# Run tests
npm run test
npm run test:watch
npm run test:coverage

# Lint and format
npm run lint
```

## Key Patterns & Best Practices

### Multi-Language Support
- **ALWAYS** use `{{ t('key.path') }}` for user-facing text
- Currently implemented: Turkish (tr) in `src/locales/tr.json`
- Planned languages: English (en), Arabic (ar - RTL), German (de)
- When adding new languages:
  - Create corresponding JSON file in `src/locales/`
  - Update `src/i18n/index.ts` configuration
  - Consider RTL layout for Arabic

### API Communication
- Use the centralized API client in `src/services/api.ts`
- All API endpoints should be in `routes/api.php`
- Use Laravel Sanctum for authentication
- Return JSON responses from controllers

### State Management
- Use **Pinia stores** for global state
- Keep store logic minimal, delegate to services
- Use composables for shared reactive logic

### Email Notifications
- Use Laravel Mail and Mailable classes
- Templates located in `resources/views/emails/`
- Send emails via queues for better performance

### Security
- **NEVER** commit secrets or API keys
- Use `.env` for configuration
- Validate all user input (use Form Requests)
- Use Laravel's CSRF protection
- Sanitize output to prevent XSS
- Use parameterized queries (Eloquent does this automatically)
- Rate limit sensitive endpoints

### Database
- Use **migrations** for schema changes
- Use **seeders** for sample data
- Use **factories** for testing data
- Follow naming conventions: `snake_case` for tables and columns

### Module Development
- Each module should have a **ServiceProvider**
- Register routes in module's `Routes/` directory
- Keep modules independent and loosely coupled
- Use dependency injection for services

## Testing Guidelines

### Backend Testing
- Write **unit tests** for services
- Write **feature tests** for API endpoints
- Use **factories** for test data
- Mock external services (Google Cloud Vision, payment gateways)
- Aim for high coverage on critical business logic

### Frontend Testing
- Write **component tests** using Vitest + Vue Test Utils
- Test user interactions and state changes
- Mock API calls
- Test multi-language functionality
- Test RTL layout for Arabic

## Common Tasks & Examples

### Adding a New Feature
1. Create/update database migration
2. Create/update model with relationships
3. Create service class for business logic
4. Create controller with thin logic (delegate to service)
5. Create form request for validation
6. Add routes
7. Create Vue components/views
8. Add translations (currently Turkish required, other languages planned)
9. Write tests
10. Update documentation

### Working with Internationalization
```typescript
// In Vue component
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();

// Use in template
{{ t('nav.home') }}

// Change language (when other languages are implemented)
locale.value = 'en'; // Will switch to English
```

### Creating a Service
```php
namespace App\Modules\Ecommerce\Services;

class ProductService
{
    public function createProduct(array $data): Product
    {
        // Business logic here
        return Product::create($data);
    }
}
```

### API Controller Pattern
```php
public function store(StoreProductRequest $request)
{
    $product = $this->productService->createProduct($request->validated());
    
    return response()->json([
        'success' => true,
        'data' => $product
    ], 201);
}
```

## Known Issues & Limitations

- Voice search requires HTTPS (browser security requirement)
- Google Cloud Vision API requires valid credentials
- Some legacy code may not follow current conventions (refactor when touching)
- Test coverage is incomplete in some areas

## Additional Resources

- **README.md** - General project overview
- **MODULE_ARCHITECTURE.md** - Detailed module structure
- **docs/** - Feature-specific documentation
- **ROADMAP.md** - Future development plans

## When Working on Issues

1. **Understand the scope** - Read the issue description thoroughly
2. **Make minimal changes** - Only modify what's necessary
3. **Follow existing patterns** - Match the style of surrounding code
4. **Test your changes** - Run relevant tests before committing
5. **Update translations** - If adding user-facing text, add to Turkish (tr.json), other languages to be added later
6. **Document significant changes** - Update relevant documentation
7. **Security first** - Consider security implications of all changes

## Questions or Clarifications?

If you encounter ambiguity or need clarification:
- Check existing code for similar patterns
- Review documentation in `docs/` folder
- Look at recent commits for context
- Ask for clarification in the issue/PR comments
