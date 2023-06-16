/** @type {import('tailwindcss').Config} */
const daisyui = require('daisyui');
const path = require('path');
const colors = require('tailwindcss/colors');

module.exports = {
  content: [
    './index.html',
    './src/App.vue',
    './src/**/*.{js,ts,vue,tsx}',
    './node_modules/litepie-datepicker/**/*.js',
  ],
  // purge: [
  //   path.resolve(__dirname, './node_modules/litepie-datepicker/**/*.js'),
  // ],
  darkMode: 'classDarkMode',
  theme: {
    extend: {
      colors: {
        // Change with you want it
        'litepie-primary': colors.sky, // color system for light mode
        'litepie-secondary': colors.coolGray, // color system for dark mode
      },
    },
  },
  variants: {
    extend: {
      cursor: ['disabled'],
      textOpacity: ['disabled'],
      textColor: ['disabled'],
    },
  },
  plugins: [daisyui],
  daisyui: {
    themes: [
      {
        mytheme: {
          primary: '#1e3a8a',
          secondary: '#f59e0b',
          accent: '#1dcdbc',
          neutral: '#2b3440',
          info: '#3abff8',
          success: '#36d399',
          warning: '#fbbd23',
          error: '#f87272',
          'primary-content': '#FFFFFF',
          'base-content': '#020617',
        },
      },
      'light',
    ],
    base: true,
  },
};
