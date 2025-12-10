export interface GeoLocation {
  lat: number
  lng: number
}

export interface Business {
  id: number
  name: string
  category: 'restaurant' | 'hotel' | 'store' | 'gym' | 'other'
  rating: number
  reviewCount: number
  distance: number // in km
  address: string
  image: string
  isOpen: boolean
  location: GeoLocation
  tags: string[]
  deliveryTime?: string // for restaurants
  priceRange?: '₺' | '₺₺' | '₺₺₺'
}

export interface LocationState {
  userLocation: GeoLocation | null
  businesses: Business[]
  loading: boolean
  error: string | null
  permissionGranted: boolean
}
