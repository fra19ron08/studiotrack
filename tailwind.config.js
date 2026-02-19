import defaultTheme from 'tailwindcss/defaultTheme'

export default {
  darkMode: 'class',
  content: ['./resources/**/*.blade.php', './resources/**/*.js'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        brand: {
          50:  '#F1EEFF',
          100: '#E3DDFF',
          200: '#C9BFFF',
          300: '#AF9FFF',
          400: '#8A74FF',
          500: '#6C4DFF',
          600: '#5B3DF5',  // accent principale (come highlight)
          700: '#4A2FE0',
          800: '#3A25B8',
          900: '#2B1C8A',
        },
      },
    },
  },
  plugins: [],
}
