(() => {
    const template = $('#template'),
        tempCont = template.parent(),
        mode = $('#mode'),
        type = $('#type'),
        toggleMode = (id) => {
            switch (id) {
                case "0":
                    template.prop('disabled', true);
                    tempCont.addClass('d-none');
                    break;
                case "1":
                    template.prop('disabled', false);
                    tempCont.removeClass('d-none');
                    break;
            }
        };
    mode.select2({
        placeholder: 'Select',
    })
        .on('select2:select', (e) => {
            toggleMode(e.params.data.id);
        });
    type.select2({
        placeholder: 'Select',
    });
    toggleMode(mode.val());
})();
