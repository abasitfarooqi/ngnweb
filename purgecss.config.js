// purgecss.config.js
module.exports = {
    content: [
      './resources/views/**/*.blade.php',
      './resources/js/**/*.vue',
      './resources/js/**/*.js',
    ],
    css: ['./public/css/app.css'], // or your big CSS file path
    safelist: [
      /^bg-/,
      /^text-/,
      /^btn-/,
      /^alert-/,
    ],
    output: './purge-report', // temporary folder for testing
  }
  