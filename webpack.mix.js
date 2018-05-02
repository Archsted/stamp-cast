let mix = require('laravel-mix');

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

mix.js('resources/assets/js/app.js', 'public/js')
    .js('resources/assets/js/top.js', 'public/js')
    .js('resources/assets/js/listener.js', 'public/js')
    .js('resources/assets/js/broadcaster.js', 'public/js')
    .js('resources/assets/js/broadcaster_beta.js', 'public/js')
    .js('resources/assets/js/imprint.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .sass('resources/assets/sass/top.scss', 'public/css');

if (mix.inProduction()) {
    mix.version();
}
