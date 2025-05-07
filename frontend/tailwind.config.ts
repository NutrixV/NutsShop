import type { Config } from 'tailwindcss'

export default <Config>{
  content: [
    './components/**/*.{js,vue,ts}',
    './layouts/**/*.vue',
    './pages/**/*.vue',
    './plugins/**/*.{js,ts}',
    './app.vue',
    './error.vue'
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#D97706', // amber-600
          light: '#F59E0B', // amber-500
          dark: '#B45309', // amber-700
        },
        secondary: {
          DEFAULT: '#84cc16', // lime-500
          light: '#a3e635', // lime-400
          dark: '#65a30d', // lime-600
        }
      }
    }
  },
  plugins: []
} 