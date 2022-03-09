(() => {
    const typeSel = $('#type'),
        accountSel = $('#account'),
        dateInp = $('#date'),
        date = initPickadate(dateInp).pickadate('picker');
    date.set('select', dateInp.val(), {format: 'yyyy/mm/dd'});
    typeSel.select2({
        placeholder: 'Select',
    });
    accountSel.select2({
        placeholder: 'Select',
    });

    $('#deleteExpenseTypeModal').on('show.bs.modal', (e) => {
        let options = typeSel.html(),
            select = $('#delete_type');
        deleteHandler(select,options);
    });

    $('#deleteExpenseAccountModal').on('show.bs.modal', (e) => {
        let options = accountSel.html(),
            select = $('#delete_account');
        deleteHandler(select,options);
    });
})();
