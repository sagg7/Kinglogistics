(() => {
    $('#incident_type_id').select2({
        placeholder: 'Select',
    });
    $('#driver_id').select2({
        placeholder: 'Select',
        ajax: {
            url: '/driver/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        }
    });
    $('#sanction').select2({
        placeholder: 'Select',
    });
    $('#deleteIncidentType').on('show.bs.modal', (e) => {
        let options = $('#incident_type_id').html(),
            select = $('#delete_type');
        deleteHandler(select,options);
    });
})();
