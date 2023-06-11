/** @type {import('tailwindcss').Config} */
const daisyui = require('daisyui');

module.exports = {
  content: [
    './index.html',
    './src/App.vue',
    './src/**/*.{js,ts,vue,tsx}',
  ],

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
