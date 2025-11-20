/**
 * Mock Authentication Service
 * Provides fake authentication for frontend development when backend is not available
 */

// Mock users database
const MOCK_USERS = [
  {
    id: 1,
    name: 'Admin User',
    email: 'admin@sportoonline.com',
    password: 'admin123',
    role: 'admin',
    avatar: 'https://ui-avatars.com/api/?name=Admin+User'
  },
  {
    id: 2,
    name: 'Satıcı Demo',
    email: 'seller@sportoonline.com',
    password: 'seller123',
    role: 'seller',
    avatar: 'https://ui-avatars.com/api/?name=Satici+Demo'
  },
  {
    id: 3,
    name: 'Alıcı Demo',
    email: 'buyer@sportoonline.com',
    password: 'buyer123',
    role: 'buyer',
    avatar: 'https://ui-avatars.com/api/?name=Alici+Demo'
  }
]

// Generate fake token
function generateToken(userId: number): string {
  return `mock_token_${userId}_${Date.now()}`
}

// Delay to simulate network request
function delay(ms: number = 500): Promise<void> {
  return new Promise(resolve => setTimeout(resolve, ms))
}

/**
 * Mock login
 */
export async function mockLogin(email: string, password: string) {
  await delay()

  const user = MOCK_USERS.find(u => u.email === email && u.password === password)

  if (!user) {
    throw new Error('Geçersiz email veya şifre')
  }

  const { password: _, ...userWithoutPassword } = user
  const token = generateToken(user.id)

  return {
    token,
    user: userWithoutPassword
  }
}

/**
 * Mock register
 */
export async function mockRegister(name: string, email: string, password: string) {
  await delay()

  // Check if email already exists
  if (MOCK_USERS.find(u => u.email === email)) {
    throw new Error('Bu email adresi zaten kayıtlı')
  }

  const newUser = {
    id: MOCK_USERS.length + 1,
    name,
    email,
    password,
    role: 'buyer' as const,
    avatar: `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}`
  }

  MOCK_USERS.push(newUser)

  const { password: _, ...userWithoutPassword } = newUser
  const token = generateToken(newUser.id)

  return {
    token,
    user: userWithoutPassword
  }
}

/**
 * Mock me (get current user)
 */
export async function mockMe(token: string) {
  await delay(200)

  if (!token || !token.startsWith('mock_token_')) {
    throw new Error('Unauthorized')
  }

  // Extract user ID from token
  const userId = parseInt(token.split('_')[2])
  const user = MOCK_USERS.find(u => u.id === userId)

  if (!user) {
    throw new Error('User not found')
  }

  const { password: _, ...userWithoutPassword } = user
  return userWithoutPassword
}

/**
 * Mock logout
 */
export async function mockLogout() {
  await delay(200)
  return { message: 'Logged out successfully' }
}

/**
 * Check if using mock auth
 */
export function isMockAuthEnabled(): boolean {
  return localStorage.getItem('USE_MOCK_AUTH') === 'true'
}

/**
 * Enable mock auth
 */
export function enableMockAuth(): void {
  localStorage.setItem('USE_MOCK_AUTH', 'true')
  console.log('🎭 Mock Auth ENABLED')
  console.log('Available test accounts:')
  console.log('  Admin:  admin@sportoonline.com / admin123')
  console.log('  Seller: seller@sportoonline.com / seller123')
  console.log('  Buyer:  buyer@sportoonline.com / buyer123')
}

/**
 * Disable mock auth
 */
export function disableMockAuth(): void {
  localStorage.removeItem('USE_MOCK_AUTH')
  console.log('🔌 Mock Auth DISABLED - Using real API')
}

// Auto-enable mock auth if backend is not available
export function autoDetectMockAuth(): void {
  // Check if we should use mock auth
  const useMock = localStorage.getItem('USE_MOCK_AUTH')
  
  if (useMock === null) {
    // First time - enable by default for development
    if (import.meta.env.DEV) {
      enableMockAuth()
    }
  }
}

export default {
  mockLogin,
  mockRegister,
  mockMe,
  mockLogout,
  isMockAuthEnabled,
  enableMockAuth,
  disableMockAuth,
  autoDetectMockAuth,
  MOCK_USERS
}
