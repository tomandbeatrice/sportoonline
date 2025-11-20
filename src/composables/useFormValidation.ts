import { ref, Ref } from 'vue'

interface ValidationRule {
  test: (value: any) => boolean
  message: string
}

interface FieldValidation {
  value: Ref<any>
  rules: ValidationRule[]
  error: Ref<string>
  touched: Ref<boolean>
}

export function useFormValidation() {
  const fields = new Map<string, FieldValidation>()

  const registerField = (
    name: string,
    value: Ref<any>,
    rules: ValidationRule[]
  ) => {
    fields.set(name, {
      value,
      rules,
      error: ref(''),
      touched: ref(false)
    })
  }

  const validateField = (name: string): boolean => {
    const field = fields.get(name)
    if (!field) return true

    field.touched.value = true
    field.error.value = ''

    for (const rule of field.rules) {
      if (!rule.test(field.value.value)) {
        field.error.value = rule.message
        return false
      }
    }
    return true
  }

  const validateAll = (): boolean => {
    let isValid = true
    for (const [name] of fields) {
      if (!validateField(name)) {
        isValid = false
      }
    }
    return isValid
  }

  const resetField = (name: string) => {
    const field = fields.get(name)
    if (field) {
      field.error.value = ''
      field.touched.value = false
    }
  }

  const resetAll = () => {
    for (const [name] of fields) {
      resetField(name)
    }
  }

  const getFieldError = (name: string): string => {
    return fields.get(name)?.error.value || ''
  }

  const isFieldTouched = (name: string): boolean => {
    return fields.get(name)?.touched.value || false
  }

  return {
    registerField,
    validateField,
    validateAll,
    resetField,
    resetAll,
    getFieldError,
    isFieldTouched
  }
}

// Common validation rules
export const validationRules = {
  required: (message = 'Bu alan zorunludur'): ValidationRule => ({
    test: (value) => {
      if (typeof value === 'string') return value.trim().length > 0
      if (Array.isArray(value)) return value.length > 0
      return value !== null && value !== undefined
    },
    message
  }),

  email: (message = 'Geçerli bir email adresi giriniz'): ValidationRule => ({
    test: (value) => {
      if (!value) return true
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)
    },
    message
  }),

  minLength: (min: number, message?: string): ValidationRule => ({
    test: (value) => {
      if (!value) return true
      return String(value).length >= min
    },
    message: message || `En az ${min} karakter olmalıdır`
  }),

  maxLength: (max: number, message?: string): ValidationRule => ({
    test: (value) => {
      if (!value) return true
      return String(value).length <= max
    },
    message: message || `En fazla ${max} karakter olmalıdır`
  }),

  min: (min: number, message?: string): ValidationRule => ({
    test: (value) => {
      if (!value) return true
      return Number(value) >= min
    },
    message: message || `En az ${min} olmalıdır`
  }),

  max: (max: number, message?: string): ValidationRule => ({
    test: (value) => {
      if (!value) return true
      return Number(value) <= max
    },
    message: message || `En fazla ${max} olmalıdır`
  }),

  pattern: (regex: RegExp, message = 'Geçersiz format'): ValidationRule => ({
    test: (value) => {
      if (!value) return true
      return regex.test(String(value))
    },
    message
  }),

  phone: (message = 'Geçerli bir telefon numarası giriniz'): ValidationRule => ({
    test: (value) => {
      if (!value) return true
      return /^[\d\s\-\+\(\)]{10,}$/.test(value)
    },
    message
  }),

  url: (message = 'Geçerli bir URL giriniz'): ValidationRule => ({
    test: (value) => {
      if (!value) return true
      try {
        new URL(value)
        return true
      } catch {
        return false
      }
    },
    message
  })
}
