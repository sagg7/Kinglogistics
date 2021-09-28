(() => {
    const typeSel = $('#equipment_type'),
        statusSel = $('#status');
    typeSel.select2({
        placeholder: 'Select',
    });
    statusSel.select2();
    console.log(typeSel, statusSel);
})();
