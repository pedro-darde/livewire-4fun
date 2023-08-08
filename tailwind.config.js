/** @type {import('tailwindcss').Config} */
export default {
    presets: [
        require('./vendor/wireui/wireui/tailwind.config.js')
    ],
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
      './vendor/wireui/wireui/resources/**/*.blade.php',
      './vendor/wireui/wireui/ts/**/*.ts',
      './vendor/wireui/wireui/src/View/**/*.php'
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

