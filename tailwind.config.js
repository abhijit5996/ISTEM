/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/css/**/*.css',
        './storage/framework/views/*.php',
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
