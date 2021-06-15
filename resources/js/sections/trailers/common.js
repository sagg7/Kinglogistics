(() => {
    $('#trailer_type_id').select2({
        placeholder: 'Select',
    });
    $('#status').select2({
        placeholder: 'Select',
    });
    $('[id="shippers[]"]').select2({
        placeholder: 'Select',
        ajax: {
            url: '/shipper/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        }
    });
    $('#deleteTrailerType').on('show.bs.modal', (e) => {
        let options = $('#trailer_type_id').html(),
            select = $('#delete_type');
        deleteHandler(select,options);
    });
})();
