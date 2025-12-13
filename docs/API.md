# API Documentation

## Base URL
```
Development: http://localhost:8000/api
Production: https://yourdomain.com/api
```

## Authentication

All authenticated endpoints require a Bearer token in the Authorization header.

```bash
Authorization: Bearer {your-token}
```

## Authentication Endpoints

### Register User
**POST** `/register`

Create a new user account.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePass123!",
  "password_confirmation": "SecurePass123!",
  "accept_terms": true
}
```

**Response:** `201 Created`
```json
{
  "token": "1|abcdef...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "buyer"
  }
}
```

**Validation Rules:**
- `name`: required, string, min:3, max:255
- `email`: required, email, unique
- `password`: required, min:8, must contain lowercase, uppercase, number, and special character
- `password_confirmation`: required, must match password
- `accept_terms`: required, must be true

---

### Login
**POST** `/login`

Authenticate a user and receive an access token.

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "SecurePass123!"
}
```

**Response:** `200 OK`
```json
{
  "token": "1|abcdef...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "buyer"
  }
}
```

**Error Response:** `401 Unauthorized`
```json
{
  "message": "Email veya şifre hatalı",
  "remaining_attempts": 4
}
```

**Rate Limiting:**
- Maximum 5 failed attempts per IP
- Lock duration: 15 minutes

---

### Logout
**POST** `/logout` 🔒

Invalidate the current access token.

**Response:** `200 OK`
```json
{
  "message": "Çıkış yapıldı"
}
```

---

### Get Current User
**GET** `/me` 🔒

Retrieve the authenticated user's information.

**Response:** `200 OK`
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "buyer",
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

---

### Forgot Password
**POST** `/forgot-password`

Request a password reset link.

**Request Body:**
```json
{
  "email": "john@example.com"
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Şifre sıfırlama bağlantısı e-posta adresinize gönderildi."
}
```

---

### Reset Password
**POST** `/reset-password`

Reset password using the token from email.

**Request Body:**
```json
{
  "token": "abc123...",
  "password": "NewSecurePass123!",
  "password_confirmation": "NewSecurePass123!"
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Şifreniz başarıyla güncellendi."
}
```

---

### Change Password
**POST** `/change-password` 🔒

Change the current user's password.

**Request Body:**
```json
{
  "current_password": "OldPassword123!",
  "password": "NewPassword123!",
  "password_confirmation": "NewPassword123!"
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Şifreniz başarıyla değiştirildi."
}
```

---

## Products

### List Products
**GET** `/products`

Get a paginated list of products.

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 20)
- `category_id`: Filter by category
- `search`: Search term
- `min_price`: Minimum price
- `max_price`: Maximum price
- `sort`: Sort by (price_asc, price_desc, newest, popular)

**Response:** `200 OK`
```json
{
  "data": [
    {
      "id": 1,
      "name": "Product Name",
      "price": 99.99,
      "stock": 10,
      "category_id": 1,
      "vendor_id": 1,
      "images": ["url1", "url2"]
    }
  ],
  "current_page": 1,
  "last_page": 5,
  "per_page": 20,
  "total": 100
}
```

---

### Get Product
**GET** `/products/{id}`

Get detailed information about a specific product.

**Response:** `200 OK`
```json
{
  "id": 1,
  "name": "Product Name",
  "description": "Product description...",
  "price": 99.99,
  "stock": 10,
  "category": {
    "id": 1,
    "name": "Category Name"
  },
  "vendor": {
    "id": 1,
    "store_name": "Store Name"
  }
}
```

---

## Cart

### Get Cart
**GET** `/cart` 🔒

Get the current user's shopping cart.

**Response:** `200 OK`
```json
{
  "items": [
    {
      "id": 1,
      "product_id": 1,
      "quantity": 2,
      "product": {
        "id": 1,
        "name": "Product Name",
        "price": 99.99
      }
    }
  ],
  "total": 199.98
}
```

---

### Add to Cart
**POST** `/cart` 🔒

Add an item to the shopping cart.

**Request Body:**
```json
{
  "product_id": 1,
  "quantity": 2
}
```

**Response:** `201 Created`
```json
{
  "message": "Ürün sepete eklendi",
  "cart": { /* cart object */ }
}
```

---

### Update Cart Item
**PUT** `/cart/{id}` 🔒

Update the quantity of a cart item.

**Request Body:**
```json
{
  "quantity": 3
}
```

**Response:** `200 OK`
```json
{
  "message": "Sepet güncellendi",
  "cart": { /* cart object */ }
}
```

---

### Remove from Cart
**DELETE** `/cart/{id}` 🔒

Remove an item from the cart.

**Response:** `200 OK`
```json
{
  "message": "Ürün sepetten kaldırıldı"
}
```

---

## Orders

### Create Order
**POST** `/orders` 🔒

Create a new order from cart items.

**Request Body:**
```json
{
  "shipping_address_id": 1,
  "billing_address_id": 1,
  "payment_method": "credit_card",
  "notes": "Optional notes"
}
```

**Response:** `201 Created`
```json
{
  "order": {
    "id": 1,
    "total": 199.98,
    "status": "pending",
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

---

### List Orders
**GET** `/orders` 🔒

Get the current user's orders.

**Response:** `200 OK`
```json
{
  "data": [
    {
      "id": 1,
      "total": 199.98,
      "status": "pending",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "items": [/* order items */]
    }
  ]
}
```

---

### Get Order
**GET** `/orders/{id}` 🔒

Get detailed information about a specific order.

**Response:** `200 OK`
```json
{
  "id": 1,
  "total": 199.98,
  "status": "pending",
  "shipping_address": { /* address */ },
  "items": [/* order items */],
  "created_at": "2024-01-01T00:00:00.000000Z"
}
```

---

## Admin Endpoints

All admin endpoints require authentication with `role: admin`.

### Get Statistics
**GET** `/admin/statistics` 🔒 👑

Get dashboard statistics.

**Response:** `200 OK`
```json
{
  "total_users": 1000,
  "total_orders": 500,
  "total_revenue": 50000,
  "pending_orders": 25
}
```

---

## Seller Endpoints

All seller endpoints require authentication with `role: seller`.

### List Seller Products
**GET** `/seller/products` 🔒 🏪

Get products for the authenticated seller.

**Response:** `200 OK`
```json
{
  "data": [/* products */]
}
```

---

## Error Responses

### Validation Error
**422 Unprocessable Entity**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### Unauthorized
**401 Unauthorized**
```json
{
  "message": "Unauthenticated."
}
```

### Forbidden
**403 Forbidden**
```json
{
  "message": "This action is unauthorized."
}
```

### Not Found
**404 Not Found**
```json
{
  "message": "Resource not found."
}
```

### Server Error
**500 Internal Server Error**
```json
{
  "message": "Server error. Please try again later."
}
```

---

## Rate Limiting

API requests are rate-limited to prevent abuse:

- **Public endpoints:** 60 requests per minute
- **Authenticated endpoints:** 120 requests per minute
- **Admin endpoints:** 200 requests per minute

Rate limit headers are included in all responses:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1640000000
```

---

## Icons Legend

- 🔒 Requires authentication
- 👑 Requires admin role
- 🏪 Requires seller role

---

## Testing

You can test the API using:

### cURL
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

### Postman
Import the Postman collection (if available) or create requests manually.

### Frontend Integration
```typescript
import apiClient from '@/services/apiClient'

const response = await apiClient.post('/login', {
  email: 'test@example.com',
  password: 'password'
})
```

---

## Versioning

The API uses URI versioning. Current version: `v1`

```
/api/v1/products
```

Legacy routes (without version prefix) redirect to v1 for backward compatibility.
