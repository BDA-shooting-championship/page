/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    './src/pages/**/*.{js,ts,jsx,tsx,mdx}',
    './src/components/**/*.{js,ts,jsx,tsx,mdx}',
    './src/app/**/*.{js,ts,jsx,tsx,mdx}',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#fbf7f4',
          100: '#f5eee7',
          200: '#ebd9cb',
          300: '#ddbda8',
          400: '#cfa082',
          500: '#c17f4e', // Primary Copper / Bronze Accent
          600: '#b06c3d',
          700: '#935632',
          800: '#77462c',
          900: '#623b26',
        },
        navy: {
          800: '#1e293b',
          900: '#1a2332',
          950: '#0f1419',
        },
      },
      fontFamily: {
        heading: ['var(--font-heading)', 'Oswald', 'sans-serif'],
        sans: ['var(--font-sans)', 'Inter', 'sans-serif'],
      },
    },
  },
  plugins: [],
};
