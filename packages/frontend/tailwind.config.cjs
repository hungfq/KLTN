/** @type {import('tailwindcss').Config} */
const daisyui = require('daisyui');

module.exports = {
  content: [
    './index.html',
    './src/App.vue',
    './src/**/*.{js,ts,vue,tsx}',
  ],
  theme: {
    extend: {},
  },
  plugins: [daisyui],
  daisyui: {
    darkTheme: 'light',
  },
};
