/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.php",
    "./node_modules/flowbite/**/*.js",
  ],
  theme: {
    extend: {},
  },
  plugins: [
      require('@tailwindcss/forms'),
      require('flowbite/plugin'),
  ],
  darkMode: 'media',
}

