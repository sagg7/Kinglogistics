/*=========================================================================================
    File Name: picker-date-time.js
    Description: Pick a date/time Picker, Date Range Picker JS
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/
// Picker Translations
const initPickadateEs = (e, properties = {}) => {
    e.pickadate(_.merge({
        // Strings and translations
        formatSubmit: 'yyyy/mm/dd',
        monthsFull: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        weekdaysFull: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        weekdaysShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
        // Buttons
        today: 'hoy',
        clear: 'limpiar',
        close: 'cerrar',
        // Accessibility labels
        labelMonthNext: 'Mes siguiente',
        labelMonthPrev: 'Mes anterior',
        labelMonthSelect: 'Seleccionar mes',
        labelYearSelect: 'Seleccionar año',
    }, properties));

    return e;
}, initPickadate = (e, properties = {}) => {
    e.pickadate(_.merge({
        // Strings and translations
        formatSubmit: 'yyyy/mm/dd',
    }, properties));

    return e;
}, initPickatime = (e, properties = {}) => {
    e.pickatime(_.merge({
        // Escape any “rule” characters with an exclamation mark (!).
        //format: 'T!ime selected: h:i a',
        //formatLabel: 'HH:i a',
        formatSubmit: 'HH:i',
        min: new Date(2015, 3, 20, 6),
        max: new Date(2015, 7, 14, 20),
        // Buttons
        clear: 'limpiar',
        //interval: 10,
    }, properties));

    return e;
};
(function (window, document, $) {
    'use strict';

    /*******    Pick-a-date Picker  *****/
        // Basic date
    let pick = $('.pickadate');
    if (pick.length > 0)
        pick.pickadate({
            formatSubmit: 'yyyy/mm/dd',
        });

    // Format Date Picker
    $('.format-picker').pickadate({
        formatSubmit: 'yyyy/mm/dd',
        format: 'mmmm, d, yyyy'
    });

    // Date limits
    $('.pickadate-limits').pickadate({
        min: [2019, 3, 20],
        max: [2019, 5, 28]
    });

    // Disabled Dates & Weeks

    $('.pickadate-disable').pickadate({
        disable: [
            1,
            [2019, 3, 6],
            [2019, 3, 20]
        ]
    });

    // Picker Translations
    $('.pickadate-translations').pickadate({
        formatSubmit: 'dd/mm/yyyy',
        monthsFull: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
        monthsShort: ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec'],
        weekdaysShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
        today: 'aujourd\'hui',
        clear: 'clair',
        close: 'Fermer'
    });
    let pickers = initPickadateEs($('.pickadate-es'));
    $.each(pickers, (index, item) => {
        let inp = $(item),
            picker = inp.pickadate('picker');
        if (inp.val() !== '')
            picker.set('select', moment(inp.val()).format('YYYY-MM-DD'), { format: 'yyyy-mm-dd' });
    });

    // Month Select Picker
    $('.pickadate-months').pickadate({
        selectYears: false,
        selectMonths: true
    });

    // Month and Year Select Picker
    initPickadateEs($('.pickadate-months-year'), {
        selectYears: 250,
        selectMonths: true,
    });
    //$('.pickadate-months-year').pickadate();

    // Short String Date Picker
    $('.pickadate-short-string').pickadate({
        weekdaysShort: ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'],
        showMonthsShort: true
    });

    // Change first weekday
    $('.pickadate-firstday').pickadate({
        firstDay: 1
    });


    /*******    Pick-a-time Picker  *****/
    // Basic time
    pick = $('.pickatime');
    if (pick.length > 0)
        pick.pickatime();

    // Format options
    pick = $('.pickatime-format');
    if (pick.length > 0)
        pick.pickatime({
            // Escape any “rule” characters with an exclamation mark (!).
            format: 'T!ime selected: h:i a',
            formatLabel: 'HH:i a',
            formatSubmit: 'HH:i',
            hiddenPrefix: 'prefix__',
            hiddenSuffix: '__suffix'
        });


    // Format options
    pick = $('.pickatime-formatlabel');
    if (pick.length > 0)
        pick.pickatime({
            formatLabel: function (time) {
                var hours = (time.pick - this.get('now').pick) / 60,
                    label = hours < 0 ? ' !hours to now' : hours > 0 ? ' !hours from now' : 'now';
                return 'h:i a <sm!all>' + (hours ? Math.abs(hours) : '') + label + '</sm!all>';
            }
        });

    // Min - Max Time to select
    pick = $('.pickatime-min-max');
    if (pick.length > 0)
        pick.pickatime({

            // Using Javascript
            min: new Date(2015, 3, 20, 7),
            max: new Date(2015, 7, 14, 18, 30)

            // Using Array
            // min: [7,30],
            // max: [14,0]
        });

    // Intervals
    pick = $('.pickatime-intervals');
    if (pick.length > 0)
        pick.pickatime({
            interval: 150
        });

    // Disable Time
    pick = $('.pickatime-disable');
    if (pick.length > 0)
        pick.pickatime({
            disable: [
                // Disable Using Integers
                3, 5, 7, 13, 17, 21

                /* Using Array */
                // [0,30],
                // [2,0],
                // [8,30],
                // [9,0]
            ]
        });


    // Close on a user action
    pick = $('.pickatime-close-action');
    if (pick.length > 0)
        pick.pickatime({
            closeOnSelect: false,
            closeOnClear: false
        });


})(window, document, jQuery);
