const select2Lang = {
        language: {
            noResults: () => {
                return "No hay resultado"
            },
            searching: () => {
                return "Buscando.."
            },
            removeAllItems: () => {
                return "Eliminar todos los elementos"
            },
        },
        placeholder: 'Buscar...',
    },
    throwErrorMsg = (error, config = {}) => {
        let optns = {
            title: config.title ? config.title :"Error!",
            html: error ? error : "There was an error processing your request",
            type: config.type ? config.type :"error",
            confirmButtonClass: 'btn btn-primary',
            buttonsStyling: false,
            timer: 5000,
        };
        _.merge(optns, config);
        if (config.redirect){
            Swal.fire(optns).then((result) => {
                window.location = config.redirect;
            });
        } else {
            Swal.fire(optns);
        }
    },
    confirmMsg = (obj = {}) => {
        let optns = {
            title: '¿Confirmar?',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#7367F0',
            cancelButtonColor: '#EA5455',
            allowOutsideClick: () => !Swal.isLoading()
        };
        if (obj.config)
            _.merge(optns, obj.config);
        Swal.fire(optns)
            .then((result) => {
                if (result.value && obj.onConfirm)
                    obj.onConfirm();
                else if (obj.onCancel)
                    obj.onCancel();
            });
    },
    UrlExists = (url, callback) => {
        $.ajax({
            url:      url,
            dataType: 'text',
            type:     'GET',
            complete:  function(xhr){
                if(typeof callback === 'function')
                    callback.apply(this, [xhr.status]);
            }
        });
    },
    deleteHandler = (select, options) => {
        select.html(options);
        select.find('option:selected').prop('selected', false);
        select.find('option:first').remove();
        select.prepend('<option selected disabled></option>');
    },
    removeAjaxLoaders = () => {
        $('.ajax-loader').parent().prop('disabled',false).html('Submit');
    };
$('.submit-ajax').click((e) => {
    const btn = $(e.currentTarget);
    btn.html(`<span class="spinner-border spinner-border-sm ajax-loader" role="status" aria-hidden="true"></span>`);
    setTimeout(() => {
        btn.prop('disabled', true);
    }, 3)
});
$('input[data-email=multi]').focusout((e) => {
    const validateEmail = (email) => {
        const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    const input = $(e.currentTarget);
    const val = input.val();
    if (!input.val())
        return;
    const arr = val.split(',');
    let string = '',
        errors = '';
    arr.forEach((email, i) => {
        const cleanEmail = email.trim();
        if (validateEmail(cleanEmail)) {
            if (i !== 0)
                string += ',';
            string += cleanEmail;
        }
        else
            errors += `• ${email}<br>`;
    });
    if (errors !== '')
        throwErrorMsg(`The following email addresses were not valid:<br><br>${errors}`);
    input.val(string);
});
