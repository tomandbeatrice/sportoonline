/**
 * Hotel Booking Types
 */
export interface HotelBooking {
  id: string | number
  hotelName: string
  destinationCoordinates?: {
    lat: number
    lng: number
  }
  destinationAddress?: string
  checkInDate: string
  checkOutDate?: string
  numberOfGuests: number
  roomType?: string
  totalPrice?: number
  status?: 'pending' | 'confirmed' | 'cancelled'
}

/**
 * Hotel Booking Completed Event
 * Triggered when a hotel booking is successfully completed
 */
export interface HotelBookingCompletedEvent {
  type: 'HotelBookingCompleted'
  booking: HotelBooking
  timestamp: string
}

/**
 * Transfer/Ride Service Types
 */
export interface TransferOption {
  id: string | number
  type: 'airport_transfer' | 'station_transfer' | 'standard_ride'
  vehicleType: string
  capacity: number
  price: number
  estimatedDistance?: string
  estimatedDuration?: string
}

export interface TransferSearchParams {
  destination?: {
    lat: number
    lng: number
  } | string
  date?: string
  passengers?: number
  serviceType?: 'airport_transfer' | 'station_transfer' | 'standard_ride'
}

/**
 * Cross-Promotion Recommendation
 */
export interface CrossPromotionRecommendation {
  id: string
  sourceService: 'hotel' | 'food' | 'ride' | 'service'
  targetService: 'hotel' | 'food' | 'ride' | 'service'
  priority: number
  message: string
  dismissed?: boolean
  dismissedAt?: string
}
