/**
 * Validation utilities
 */

export const validators = {
  /**
   * Check if value is required (not empty)
   */
  required: (value: any): boolean => {
    if (value === null || value === undefined) return false
    if (typeof value === 'string') return value.trim().length > 0
    if (Array.isArray(value)) return value.length > 0
    return true
  },

  /**
   * Validate email format
   */
  email: (value: string): boolean => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailRegex.test(value)
  },

  /**
   * Validate Turkish phone number
   */
  phone: (value: string): boolean => {
    const phoneRegex = /^0[0-9]{3}\s?[0-9]{3}\s?[0-9]{2}\s?[0-9]{2}$/
    const cleaned = value.replace(/\s/g, '')
    return phoneRegex.test(cleaned) || /^0[0-9]{10}$/.test(cleaned)
  },

  /**
   * Validate URL
   */
  url: (value: string): boolean => {
    try {
      new URL(value)
      return true
    } catch {
      return false
    }
  },

  /**
   * Validate minimum length
   */
  minLength: (value: string, min: number): boolean => {
    return value.length >= min
  },

  /**
   * Validate maximum length
   */
  maxLength: (value: string, max: number): boolean => {
    return value.length <= max
  },

  /**
   * Validate minimum value
   */
  min: (value: number, min: number): boolean => {
    return value >= min
  },

  /**
   * Validate maximum value
   */
  max: (value: number, max: number): boolean => {
    return value <= max
  },

  /**
   * Validate pattern (regex)
   */
  pattern: (value: string, pattern: RegExp): boolean => {
    return pattern.test(value)
  },

  /**
   * Validate Turkish ID number (TC Kimlik No)
   */
  tcId: (value: string): boolean => {
    if (value.length !== 11) return false
    if (!/^[0-9]{11}$/.test(value)) return false
    if (value[0] === '0') return false

    const digits = value.split('').map(Number)
    
    // Calculate 10th digit
    const odd = digits[0] + digits[2] + digits[4] + digits[6] + digits[8]
    const even = digits[1] + digits[3] + digits[5] + digits[7]
    const digit10 = ((odd * 7) - even) % 10
    
    if (digit10 !== digits[9]) return false
    
    // Calculate 11th digit
    const sum = digits.slice(0, 10).reduce((a, b) => a + b, 0)
    const digit11 = sum % 10
    
    return digit11 === digits[10]
  },

  /**
   * Validate credit card number (Luhn algorithm)
   */
  creditCard: (value: string): boolean => {
    const cleaned = value.replace(/\s/g, '')
    if (!/^[0-9]{13,19}$/.test(cleaned)) return false

    let sum = 0
    let isEven = false

    for (let i = cleaned.length - 1; i >= 0; i--) {
      let digit = parseInt(cleaned[i], 10)

      if (isEven) {
        digit *= 2
        if (digit > 9) digit -= 9
      }

      sum += digit
      isEven = !isEven
    }

    return sum % 10 === 0
  },

  /**
   * Validate IBAN
   */
  iban: (value: string): boolean => {
    const cleaned = value.replace(/\s/g, '').toUpperCase()
    if (!/^TR[0-9]{2}[0-9]{5}[0-9A-Z]{17}$/.test(cleaned)) return false

    // Move first 4 characters to end
    const rearranged = cleaned.slice(4) + cleaned.slice(0, 4)
    
    // Replace letters with numbers (A=10, B=11, etc.)
    const numericString = rearranged
      .split('')
      .map(char => {
        const code = char.charCodeAt(0)
        return code >= 65 && code <= 90 ? code - 55 : char
      })
      .join('')

    // Calculate mod 97
    let remainder = numericString
    while (remainder.length > 2) {
      const block = remainder.slice(0, 9)
      remainder = (parseInt(block, 10) % 97) + remainder.slice(block.length)
    }

    return parseInt(remainder, 10) % 97 === 1
  },

  /**
   * Validate strong password
   */
  strongPassword: (value: string): boolean => {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number, 1 special char
    const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/
    return strongRegex.test(value)
  },

  /**
   * Validate postal code (Turkey)
   */
  postalCode: (value: string): boolean => {
    return /^[0-9]{5}$/.test(value)
  },

  /**
   * Validate age (minimum)
   */
  minAge: (birthDate: string | Date, minAge: number): boolean => {
    const birth = typeof birthDate === 'string' ? new Date(birthDate) : birthDate
    const today = new Date()
    const age = today.getFullYear() - birth.getFullYear()
    const monthDiff = today.getMonth() - birth.getMonth()
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
      return age - 1 >= minAge
    }
    
    return age >= minAge
  },

  /**
   * Validate file size
   */
  maxFileSize: (file: File, maxSizeMB: number): boolean => {
    const maxSizeBytes = maxSizeMB * 1024 * 1024
    return file.size <= maxSizeBytes
  },

  /**
   * Validate file type
   */
  fileType: (file: File, allowedTypes: string[]): boolean => {
    return allowedTypes.some(type => {
      if (type.includes('*')) {
        const [mainType] = type.split('/')
        return file.type.startsWith(mainType)
      }
      return file.type === type
    })
  }
}

/**
 * Create custom validator
 */
export function createValidator(
  fn: (value: any, ...args: any[]) => boolean,
  message: string
) {
  return {
    validate: fn,
    message
  }
}
