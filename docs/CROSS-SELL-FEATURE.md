# Cross-Promotion (Cross-Selling) Feature

## Overview

This feature implements a "Smart Suggestion" flow that recommends Transfer/Ride services to users after they complete a Hotel booking, creating a unified user experience across different verticals (Food, Hotel, Ride).

## How It Works

### Flow
1. **Trigger**: User completes a Hotel Booking successfully
2. **Suggestion**: System displays a Transfer/Ride suggestion card on the payment success page
3. **Action**: User can easily add a transfer to their transaction or view more options

### Components

#### Frontend (Vue.js)

**CrossSellCard.vue** (`src/components/marketplace/CrossSellCard.vue`)
- Displays transfer options with pricing
- Shows special discount for hotel guests (15% off)
- Provides three transfer tiers: Economic, Comfort, and VIP
- Allows users to add transfer, view options, or dismiss

**PaymentSuccess.vue** (`src/views/PaymentSuccess.vue`)
- Detects hotel bookings via `type` query parameter
- Conditionally shows CrossSellCard for hotel reservations
- Handles transfer addition and navigation to rides page
- Remembers dismissed suggestions in localStorage

#### Backend (Laravel)

**Ride Module** (`app/Modules/Ride/`)
- `RideServiceProvider.php`: Registers the module
- `Services/TransferService.php`: Provides transfer suggestions and booking logic
- `Controllers/TransferController.php`: Handles API requests
- `Routes/ride.php`: Defines transfer-related endpoints

### API Endpoints

```
GET /api/v1/transfers/suggestions?booking_id={id}&location={location}
GET /api/v1/transfers/available?from={from}&to={to}&date={date}
POST /api/v1/transfers/book
```

### Usage Example

To trigger the cross-sell flow, redirect to the payment success page with the appropriate query parameters:

```javascript
router.push({
  path: '/payment/success',
  query: {
    order_id: 'HTL-123456',
    type: 'hotel',
    location: 'Istanbul',
    total: 2450.00
  }
})
```

## Transfer Options

The system offers three transfer tiers:

1. **Ekonomik Transfer** (₺150)
   - Shared vehicle service
   - ~45 minutes
   - 15% discount for hotel guests

2. **Konforlu Transfer** (₺300)
   - Private vehicle service
   - ~30 minutes
   - 15% discount for hotel guests

3. **VIP Transfer** (₺500)
   - Luxury vehicle service
   - ~25 minutes
   - 15% discount for hotel guests

## Features

- ✅ Automatic detection of hotel bookings
- ✅ Beautiful, responsive UI with gradient background
- ✅ Three transfer options with different price points
- ✅ 15% discount badge for hotel guests
- ✅ Dismissible card (remembers user preference)
- ✅ Smooth animations and transitions
- ✅ Mobile-responsive design
- ✅ Backend API with mock data (ready for database integration)

## Configuration

The module is registered in `config/app.php`:

```php
'providers' => [
    // ...
    App\Modules\Ride\RideServiceProvider::class,
],
```

## Screenshots

![Cross-Sell Demo](https://github.com/user-attachments/assets/491c8b16-a67e-4569-afff-55d5cabef0b8)

The screenshot shows:
- Payment success confirmation for a hotel booking
- Cross-sell card with transfer options
- Three transfer tiers with pricing and estimated duration
- Action buttons for adding transfer or viewing more options
- Special discount badge

## Future Enhancements

- [ ] Integration with real transfer service providers
- [ ] Dynamic pricing based on distance and time
- [ ] User location detection for better suggestions
- [ ] Integration with hotel check-in/check-out times
- [ ] Multi-language support for international travelers
- [ ] Analytics tracking for conversion rates
- [ ] A/B testing for different cross-sell strategies

## Testing

To test the feature:

1. Build the frontend: `npm run build`
2. Navigate to the payment success page with hotel booking parameters:
   ```
   /payment/success?order_id=HTL-123&type=hotel&location=Istanbul&total=2450
   ```
3. The cross-sell card should appear below the success message
4. Click "Transfer Ekle" to add a transfer
5. Click "Seçenekleri Gör" to view more options
6. Click "Şimdi Değil" to dismiss

## Notes

- The backend currently uses mock data for transfer options
- To integrate with a real transfer service, modify `TransferService::getTransferSuggestions()`
- The dismissal state is stored in browser localStorage
- Cross-sell suggestions can be disabled by setting `showCrossSell` to `false` in PaymentSuccess.vue
