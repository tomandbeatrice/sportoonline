import { reactive } from 'vue'

interface ValidationRule {
  rule: 'required' | 'minLength' | 'email' | 'phone' | 'pattern' | 'custom'
  value?: any
  message: string
  validator?: (value: any) => boolean
}

export function useFormValidation() {
  const errors = reactive<Record<string, string>>({})

  const validate = (data: any, rules: Record<string, ValidationRule[]>) => {
    // Clear previous errors
    Object.keys(errors).forEach(key => delete errors[key])
    
    let isValid = true

    for (const [field, fieldRules] of Object.entries(rules)) {
      for (const ruleConfig of fieldRules) {
        const value = data[field]
        let passed = true

        switch (ruleConfig.rule) {
          case 'required':
            passed = value !== null && value !== undefined && value !== ''
            if (typeof value === 'string') passed = value.trim() !== ''
            break
            
          case 'minLength':
            passed = value && value.length >= ruleConfig.value
            break
            
          case 'email':
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
            passed = emailRegex.test(value)
            break
            
          case 'phone':
            // Basic phone validation: allows numbers, spaces, +, -, (, )
            const phoneRegex = /^[0-9+\-\s()]*$/
            passed = phoneRegex.test(value) && value.replace(/\D/g, '').length >= 10
            break
            
          case 'pattern':
            if (ruleConfig.value instanceof RegExp) {
              passed = ruleConfig.value.test(value)
            }
            break
            
          case 'custom':
            if (ruleConfig.validator) {
              passed = ruleConfig.validator(value)
            }
            break
        }

        if (!passed) {
          errors[field] = ruleConfig.message
          isValid = false
          break // Stop at first error for this field
        }
      }
    }
    
    return isValid
  }

  const clearErrors = () => {
    Object.keys(errors).forEach(key => delete errors[key])
  }

  return {
    errors,
    validate,
    clearErrors
  }
}
