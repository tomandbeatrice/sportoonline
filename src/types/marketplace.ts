export interface Task {
  id: number
  title: string
  completed: boolean
  time: string
  icon: string
  color: string
  due_date?: string
  is_completed?: boolean
}

export interface Campaign {
  id: number
  title: string
  name?: string // API might return name
  description: string
  endDate?: string
  end_date?: string // API might return end_date
  colorFrom?: string
  colorTo?: string
  link?: string
  color?: string
  icon?: string
  status?: string
  date?: string
}

export interface Order {
  id: number
  status: 'pending' | 'confirmed' | 'processing' | 'shipped' | 'delivered'
  createdAt: string
  product?: { title: string; image: string }
  itemCount: number
  deliveryProgress?: number
}

export interface Banner {
  id: number
  image: string
  title: string
  link: string
  position?: string
}

export interface Bundle {
  id: number
  main: { title: string; image: string; price: number }
  extra: { title: string; image: string; price: number }
  totalPrice: number
  originalPrice: number
  discount: number
  description?: string
}
