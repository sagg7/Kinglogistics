(() => {
    const typeSel = $('#type'),
        truckSel = $('#truck_id');
    typeSel.select2({
        placeholder: 'Select',
    });

    truckSel.select2({
        ajax: {
            url: '/truck/selection',
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

    $('#deleteExpenseTypeModal').on('show.bs.modal', (e) => {
        let options = $('#type').html(),
            select = $('#delete_type');
        deleteHandler(select,options);
    });
})();
