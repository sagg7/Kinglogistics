const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix/*sass('resources/sass/app.scss', 'public/css')
    .postCss('resources/css/fontawesome.css', 'public/css')*/
    /*.postCss('resources/css/app.css', 'public/css/guest', [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
    ])
    .js('resources/js/app.js', 'public/js')
    .js('resources/js/common/master.js', 'public/js/common/master.min.js')*/
    // Modules
    /*.scripts(['resources/js/modules/aggrid/dynamicFront.js'], 'public/js/modules/aggrid/dynamicFront.min.js')
    .scripts(['resources/js/modules/aggrid/backRequest.js'], 'public/js/modules/aggrid/backRequest.min.js')
    .scripts(['resources/js/modules/aggrid/simpleTable.js'], 'public/js/modules/aggrid/simpleTable.min.js')*/
    .scripts(['resources/js/sections/rentals/common.js'], 'public/js/sections/rentals/common.min.js')
    .scripts(['resources/js/sections/subdomains/carriers/trailers/common.js'], 'public/js/sections/subdomains/carriers/trailers/common.min.js')
    .scripts(['resources/js/sections/subdomains/carriers/drivers/common.js'], 'public/js/sections/subdomains/carriers/drivers/common.min.js');
