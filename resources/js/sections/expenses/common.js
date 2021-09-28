(() => {
    const typeSel = $('#type'),
        dateInp = $('#date'),
        date = initPickadate(dateInp).pickadate('picker');
    date.set('select', dateInp.val(), {format: 'yyyy/mm/dd'});
    typeSel.select2({
        placeholder: 'Select',
    });

    $('#deleteExpenseTypeModal').on('show.bs.modal', (e) => {
        let options = $('#type').html(),
            select = $('#delete_type');
        deleteHandler(select,options);
    });
})();
