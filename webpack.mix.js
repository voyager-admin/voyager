const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.sass('resources/assets/sass/voyager.scss', 'resources/assets/dist/css', {
    implementation: require('sass')
})
.options({
    processCssUrls: false,
    postCss: [ tailwindcss('tailwind.config.js') ],
})
.js('resources/assets/js/voyager.js', 'resources/assets/dist/js')
.copy('node_modules/inter-ui/Inter (web)', 'resources/assets/dist/fonts/inter-ui');
