/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./public/index.php",
    "./resources/**/*.php",
    "./node_modules/flowbite/**/*.js",
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
    require('flowbite/plugin'),
  ],
  darkMode: 'media',
}

