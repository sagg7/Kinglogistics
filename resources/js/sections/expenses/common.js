(() => {
    const typeSel = $('#type');
    typeSel.select2({
        placeholder: 'Select',
    });

    $('#deleteExpenseTypeModal').on('show.bs.modal', (e) => {
        let options = $('#type').html(),
            select = $('#delete_type');
        deleteHandler(select,options);
    });
})();
