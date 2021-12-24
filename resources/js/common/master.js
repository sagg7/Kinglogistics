(() => {
    //moment.locale('es');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.fn.modal.Constructor.prototype._enforceFocus = function() {};

    $('.modern-nav-toggle').click(() => {
        let body = $('body');
        if (body.hasClass('menu-expanded'))
            document.cookie = "menusize=menu-collapsed";
        else
            document.cookie = "menusize=menu-expenaded";
    });
})();
