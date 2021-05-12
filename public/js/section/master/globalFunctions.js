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
            title: "Error!",
            html: error ? error : "Ocurrió un error al procesar la solicitud",
            type: "error",
            confirmButtonClass: 'btn btn-primary',
            buttonsStyling: false,
            timer: 5000,
        };
        _.merge(optns, config);
        Swal.fire(optns);
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
    };
