export default {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        primary: '#3b82f6',
        secondary: '#8b5cf6',
        success: '#22c55e',
        danger: '#ef4444',
        warning: '#f59e0b',
        info: '#06b6d4',
        brand: '#4F46E5',
        vertical: {
          food: '#F97316',
          hotel: '#06B6D4',
          ride: '#10B981',
          service: '#8B5CF6',
          career: '#EC4899',
        },
      },
    },
  },
  plugins: [],
}