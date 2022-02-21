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
    ])*/
    //.js('resources/js/common/master.js', 'public/js/common/master.min.js')
    //.js('resources/js/bootstrap.js', 'public/js/')
    //.sass('resources/css/bootstrap.scss', 'public/css/')
    // Modules
    //.scripts(['resources/js/modules/aggrid/dynamicFront.js'], 'public/js/modules/aggrid/dynamicFront.min.js')
    //.scripts(['resources/js/modules/aggrid/backRequest.js'], 'public/js/modules/aggrid/backRequest.min.js')
    //.scripts(['resources/js/modules/aggrid/simpleTable.js'], 'public/js/modules/aggrid/simpleTable.min.js')
    //.scripts(['resources/js/modules/aggrid/common.js'], 'public/js/modules/aggrid/common.min.js')
    //.js('resources/js/app.js', 'public/js')
    // Common
    //.js('resources/js/common/typesModal.js', 'public/js/common/typesModal.min.js')
    //.js('resources/js/modules/laravel-echo/echo.js', 'public/js/modules/laravel-echo/echo.js')
    //.js('resources/js/common/filesUploads.js', 'public/js/common/filesUploads.min.js?1.0.1')
    //.js('resources/js/common/initSignature.js', 'public/js/common/initSignature.min.js?1.0.0')
    //.js('resources/js/broadcasting-test.js', 'public/js/broadcasting-test.js')
    // Sections
    //.scripts(['resources/js/sections/safetyMessages/common.js'], 'public/js/sections/safetyMessages/common.min.js')
    //.scripts(['resources/js/sections/rentals/common.js'], 'public/js/sections/rentals/common.min.js')
    //.scripts(['resources/js/sections/trailers/common.js'], 'public/js/sections/trailers/common.min.js')
    //.scripts(['resources/js/sections/incidents/common.js'], 'public/js/sections/incidents/common.min.js')
    //.scripts(['resources/js/sections/loads/common.js'], 'public/js/sections/loads/common.min.js')
    //.scripts(['resources/js/sections/loads/dispatch/driverStatus.js'], 'public/js/sections/loads/dispatch/driverStatus.min.js')
    //.scripts(['resources/js/sections/loads/common.js'], 'public/js/sections/loads/common.min.js')
    //.scripts(['resources/js/sections/loads/dispatch/driverStatus.js'], 'public/js/sections/loads/dispatch/driverStatus.min.js')
    //.scripts(['resources/js/sections/loads/dispatch/loadSummary.js'], 'public/js/sections/loads/dispatch/loadSummary.min.js')
    .scripts(['resources/js/sections/loads/dispatch/customerStatus.js'], 'public/js/sections/loads/dispatch/customerStatus.min.js')
    //.scripts(['resources/js/sections/paperwork/common.js'], 'public/js/sections/paperwork/common.min.js')
    //.scripts(['resources/js/sections/expenses/common.js'], 'public/js/sections/expenses/common.min.js')
    //.scripts(['resources/js/sections/incomes/common.js'], 'public/js/sections/incomes/common.min.js')
    //.scripts(['resources/js/sections/charges/common.js'], 'public/js/sections/charges/common.min.js')
    //.scripts(['resources/js/sections/bonuses/common.js'], 'public/js/sections/bonuses/common.min.js')
    //.scripts(['resources/js/sections/loans/common.js'], 'public/js/sections/loans/common.min.js')
    //.scripts(['resources/js/sections/tracking/common.js'], 'public/js/sections/tracking/common.min.js')
    //.scripts(['resources/js/sections/tracking/history.js'], 'public/js/sections/tracking/history.min.js')
    //.scripts(['resources/js/sections/dashboard/common.js'], 'public/js/sections/dashboard/common.min.js')
    //.scripts(['resources/js/sections/dashboard/loadSummary.js'], 'public/js/sections/dashboard/loadSummary.min.js')
    //.scripts(['resources/js/sections/trips/common.js'], 'public/js/sections/trips/common.min.js')
    //.scripts(['resources/js/sections/trips/location.js'], 'public/js/sections/trips/location.min.js')
    //.scripts(['resources/js/sections/carrierPayments/editPayment.js'], 'public/js/sections/carrierPayments/editPayment.min.js')
    //.scripts(['resources/js/sections/drivers/common.js'], 'public/js/sections/drivers/common.min.js')
    //.scripts(['resources/js/sections/trucks/common.js'], 'public/js/sections/trucks/common.min.js')
    //.scripts(['resources/js/sections/reports/dailyLoads.js'], 'public/js/sections/reports/dailyLoads.min.js')
    //.scripts(['resources/js/sections/reports/activeTime.js'], 'public/js/sections/reports/activeTime.min.js')
    //.scripts(['resources/js/sections/users/dispatchSchedule.js'], 'public/js/sections/users/dispatchSchedule.min.js')
    //.scripts(['resources/js/sections/carriers/show.js'], 'public/js/sections/carriers/show.min.js')
    // Subdomains
    //.scripts(['resources/js/sections/subdomains/carriers/expenses/common.js'], 'public/js/sections/subdomains/carriers/expenses/common.min.js')
    //.scripts(['resources/js/sections/subdomains/carriers/reports/historical.js'], 'public/js/sections/subdomains/carriers/reports/historical.min.js')
    //.scripts(['resources/js/sections/subdomains/carriers/profile/equipment/common.js'], 'public/js/sections/subdomains/carriers/profile/equipment/common.min.js')
    // App Assets
    //.scripts(['public/app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js'], 'public/app-assets/js/scripts/pickers/dateTime/pick-a-datetime.min.js')
    //.scripts(['public/app-assets/js/scripts/pages/app-chat.js'], 'public/app-assets/js/scripts/pages/app-chat.min.js')
    //.scripts(['resources/js/sections/chat/bottomChat.js'], 'public/js/sections/chat/bottomChat.min.js')
    ;
