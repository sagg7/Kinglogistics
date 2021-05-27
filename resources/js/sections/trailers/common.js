(() => {
    $('#trailer_type_id').select2({
        placeholder: 'Select',
    });
    $('#status').select2({
        placeholder: 'Select',
    });
    $('#shipper_id').select2({
        ajax: {
            url: '/shipper/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        },
        placeholder: 'Select',
        allowClear: true,
    });
    $('#deleteTrailerType').on('show.bs.modal', (e) => {
        let options = $('#trailer_type_id').html(),
            select = $('#delete_type');
        deleteHandler(select,options);
    });
})();
