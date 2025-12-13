# Technical Debt & Known Issues

This document tracks technical debt items and known issues that should be addressed in future iterations.

## 🔴 High Priority

### Duplicate Toast Notification Libraries
**Issue:** Both `vue-toastification` and `vue3-toastify` are installed and used across the codebase.

**Impact:**
- Increased bundle size (~50KB)
- Inconsistent toast notification UX
- Maintenance overhead

**Current Usage:**
- `vue3-toastify`: Used in main.ts and newer components
- `vue-toastification`: Used in stores (cartStore, orderStore) and some composables

**Recommendation:**
- Standardize on `vue3-toastify` (already in main.ts)
- Refactor all `vue-toastification` imports to use `vue3-toastify`
- Remove `vue-toastification` from package.json

**Affected Files:**
```
src/stores/orderStore.ts
src/stores/cartStore.ts
src/composables/useNotifications.ts
src/components/TransferRecommendation.vue
src/components/cart/CartItemEnhanced.vue
src/views/seller/SellerReviewsAndQuestions.vue
src/views/seller/SellerReportsEnhanced.vue
src/views/seller/SellerProductsEnhanced.vue
src/views/seller/SellerCampaignEnhanced.vue
```

**Estimated Effort:** 2-4 hours

---

## 🟡 Medium Priority

### Dependency Injection in Controllers
**Issue:** Controllers use global facades (`\Mail`, `\Log`, `\Cache`) instead of dependency injection.

**Example:**
```php
// Current (tightly coupled)
\Mail::to($email)->queue($mailable);

// Better (testable)
public function __construct(
    private Mailer $mailer
) {}

$this->mailer->to($email)->queue($mailable);
```

**Benefits:**
- Better testability
- SOLID principles compliance
- Easier mocking in tests

**Affected Controllers:**
- `SellerReturnController.php`
- `AuthController.php` (uses `\Cache`)

**Estimated Effort:** 4-6 hours

---

### Input Sanitization Middleware
**Issue:** No global input sanitization middleware exists.

**Risk:**
- Potential XSS vulnerabilities
- SQL injection (mitigated by Eloquent)
- Script injection in user-generated content

**Recommendation:**
- Create `app/Http/Middleware/SanitizeInput.php`
- Add to global middleware stack
- Sanitize all text inputs before validation
- Use `htmlspecialchars()` for output escaping

**Example:**
```php
class SanitizeInput
{
    public function handle($request, Closure $next)
    {
        $input = $request->all();
        
        array_walk_recursive($input, function (&$input) {
            if (is_string($input)) {
                $input = strip_tags($input);
            }
        });
        
        $request->merge($input);
        return $next($request);
    }
}
```

**Estimated Effort:** 2-3 hours

---

## 🟢 Low Priority

### Frontend Type Coverage
**Issue:** Some components still use `any` types or lack proper type definitions.

**Recommendation:**
- Run `npx type-coverage` to identify gaps
- Add strict TypeScript checks to CI
- Gradually improve type coverage to >95%

**Estimated Effort:** 8-12 hours

---

### Unused Dependencies Cleanup
**Issue:** Some packages may not be actively used:
- `storybook` (if not generating stories)
- `shadcn-ui` (if not fully integrated)
- Duplicate UI libraries

**Recommendation:**
- Audit package.json
- Remove unused dependencies
- Document all dependencies in README

**Estimated Effort:** 2-3 hours

---

### Environment Variable Validation
**Issue:** No runtime validation of required environment variables.

**Recommendation:**
- Create `app/Services/ConfigValidator.php`
- Validate required env vars on boot
- Fail fast with clear error messages

**Example:**
```php
class ConfigValidator
{
    private const REQUIRED_ENV = [
        'APP_KEY',
        'DB_CONNECTION',
        'MAIL_MAILER',
        // ...
    ];
    
    public static function validate(): void
    {
        foreach (self::REQUIRED_ENV as $var) {
            if (empty(env($var))) {
                throw new RuntimeException("Missing required env: $var");
            }
        }
    }
}
```

**Estimated Effort:** 1-2 hours

---

## 📝 Documentation Gaps

### API Documentation
**Status:** ✅ Completed (docs/API.md)

### Development Setup Guide
**Missing:**
- Step-by-step local development setup
- Docker compose setup instructions
- Troubleshooting common issues

**Estimated Effort:** 3-4 hours

---

## 🔒 Security Considerations

### Rate Limiting Granularity
**Current:** IP-based rate limiting in AuthController
**Enhancement:** User-based rate limiting for authenticated endpoints

### Password Reset Token Security
**Current:** Tokens stored in database with hash
**Enhancement:** Add token expiration cleanup job

### CORS Configuration
**Status:** Exists in `config/cors.php`
**Recommendation:** Review allowed origins for production

---

## 🧪 Test Coverage Gaps

### Backend Coverage
**Current:** Authentication, basic flows
**Missing:**
- Email notification tests
- Payment gateway tests
- Return/refund flow tests
- Service marketplace tests

### Frontend Coverage
**Current:** None for composables
**Missing:**
- useAuth tests
- Store tests (Pinia)
- Component tests (Vue Test Utils)
- E2E tests (Playwright)

**Target:** 70% code coverage

---

## 📊 Performance Optimizations

### Database Query Optimization
- Add indexes for frequently queried columns
- Implement query result caching
- Use eager loading to prevent N+1 queries

### Asset Optimization
- Implement image lazy loading
- Use WebP format for images
- Add CDN for static assets

---

## 🔄 Refactoring Opportunities

### Large Component Splitting
**Components >300 lines:**
- `src/views/user/Addresses.vue` (308 lines)
- Extract address form into separate component
- Extract address list into separate component

### Extract Business Logic to Services
- Move complex logic from controllers to service classes
- Improve testability and reusability

---

## 📅 Maintenance Schedule

### Monthly
- Review and update dependencies
- Security audit (npm audit, composer audit)
- Performance monitoring review

### Quarterly
- Refactor technical debt items
- Update documentation
- Review and optimize bundle size

### Annually
- Framework version upgrades
- Major dependency updates
- Architecture review

---

## 📞 Questions & Discussion

For questions about any technical debt item, please:
1. Open a GitHub issue with label `technical-debt`
2. Reference this document
3. Provide context and proposed solution

---

**Last Updated:** 2024-12-13  
**Next Review:** 2025-01-13
