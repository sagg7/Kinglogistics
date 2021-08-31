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

mix//.sass('resources/sass/app.scss', 'public/css')
    /*.postCss('resources/css/fontawesome.css', 'public/css')*/
    /*.postCss('resources/css/app.css', 'public/css/guest', [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
    ])
    .js('resources/js/common/master.js', 'public/js/common/master.min.js')*/
    //.js('resources/js/bootstrap.js', 'public/js/')
    //.sass('resources/css/bootstrap.scss', 'public/css/')
    // Modules
    /*.scripts(['resources/js/modules/aggrid/dynamicFront.js'], 'public/js/modules/aggrid/dynamicFront.min.js')*/
    .scripts(['resources/js/modules/aggrid/backRequest.js'], 'public/js/modules/aggrid/backRequest.min.js')
    /*.scripts(['resources/js/modules/aggrid/simpleTable.js'], 'public/js/modules/aggrid/simpleTable.min.js')*/
    //.js('resources/js/app.js', 'public/js')
    // Common
    //.js('resources/js/common/typesModal.js', 'public/js/common/typesModal.min.js')
    //.js('resources/js/modules/laravel-echo/echo.js', 'public/js/modules/laravel-echo/echo.js')
    //.js('resources/js/modules/laravel-echo/echo.js', 'public/js/modules/laravel-echo/echo.js')
    //.js('resources/js/common/filesUploads.js', 'public/js/common/filesUploads.min.js?1.0.0')
    //.js('resources/js/common/initSignature.js', 'public/js/common/initSignature.min.js?1.0.0')
    //.js('resources/js/broadcasting-test.js', 'public/js/broadcasting-test.js')
    // Sections
    //.scripts(['resources/js/sections/safetyMessages/common.js'], 'public/js/sections/safetyMessages/common.min.js')
    //.scripts(['resources/js/sections/rentals/common.js'], 'public/js/sections/rentals/common.min.js')
    //.scripts(['resources/js/sections/trailers/common.js'], 'public/js/sections/trailers/common.min.js')
    //.scripts(['resources/js/sections/incidents/common.js'], 'public/js/sections/incidents/common.min.js')
    .scripts(['resources/js/sections/loads/common.js'], 'public/js/sections/loads/common.min.js')
    //.scripts(['resources/js/sections/paperwork/common.js'], 'public/js/sections/paperwork/common.min.js')
    //.scripts(['resources/js/sections/expenses/common.js'], 'public/js/sections/expenses/common.min.js')
    // Subdomains
    //.scripts(['resources/js/sections/subdomains/carriers/drivers/common.js'], 'public/js/sections/subdomains/carriers/drivers/common.min.js')
    //.scripts(['resources/js/sections/subdomains/carriers/trucks/common.js'], 'public/js/sections/subdomains/carriers/trucks/common.min.js')
    //.scripts(['resources/js/sections/subdomains/carriers/expenses/common.js'], 'public/js/sections/subdomains/carriers/expenses/common.min.js')
    //.scripts(['resources/js/sections/subdomains/carriers/reports/historical.js'], 'public/js/sections/subdomains/carriers/reports/historical.min.js')
    // App Assets
    //.scripts(['public/app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js'], 'public/app-assets/js/scripts/pickers/dateTime/pick-a-datetime.min.js')
    //.scripts(['public/app-assets/js/scripts/pages/app-chat.js'], 'public/app-assets/js/scripts/pages/app-chat.min.js')
    ;
