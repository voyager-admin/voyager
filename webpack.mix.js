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
Mix.listen('configReady', function(config) {
    const rules = config.module.rules;
    const svgRegexPart = '|^((?!font).)*\\.svg';

    for (let rule of rules) {
        if (rule.test) {
            if (('' + rule.test).indexOf(svgRegexPart) > -1) {
                rule.test = new RegExp(('' + rule.test).replace(svgRegexPart, ''));
            }
        }
    }
});

mix.webpackConfig({
    module: {
        rules: [{
            test: /\.svg$/,
            use: [{
                loader: 'html-loader',
                options: {
                    minimize: true
                }
            }]
        }]
    }
});

mix.sass('resources/assets/sass/voyager.scss', 'resources/assets/dist/css', {
    implementation: require('sass')
})
.options({
    processCssUrls: false,
    postCss: [ tailwindcss('tailwind.config.js') ],
})
.js('resources/assets/js/voyager.js', 'resources/assets/dist/js')
.copy('node_modules/inter-ui/Inter (web)', 'resources/assets/dist/fonts/inter-ui');
