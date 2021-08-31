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
    };
