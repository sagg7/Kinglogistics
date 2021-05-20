(() => {
    $('#trailer_type_id').select2({
        placeholder: 'Select',
    });
    $('#status').select2({
        placeholder: 'Select',
    });
    $('#deleteTrailerType').on('show.bs.modal', (e) => {
        let options = $('#trailer_type_id').html(),
            select = $('#delete_type');
        deleteHandler(select,options);
    });
})();
