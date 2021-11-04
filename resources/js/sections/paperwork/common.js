(() => {
    const adv = $('#advancedTemplate'),
        advInp = adv.find('textarea'),
        simp = $('#simpleTemplate'),
        simpInp = simp.find('input'),
        mode = $('#mode'),
        type = $('#type'),
        shipper = $('#shipper_id'),
        toggleMode = (id) => {
            switch (id) {
                case "0":
                    simpInp.prop('disabled', false);
                    advInp.prop('disabled', true);
                    simp.removeClass('d-none');
                    adv.addClass('d-none');
                    break;
                case "1":
                    simpInp.prop('disabled', true);
                    advInp.prop('disabled', false);
                    simp.addClass('d-none');
                    adv.removeClass('d-none');
                    break;
            }
        },
        toggleShipper = () => {
            const val = type.val();
            switch (val) {
                case 'driver':
                    shipper.prop('disabled', false);
                    break;
                default:
                    shipper.prop('disabled', true);
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
    }).on('select2:select', (e) => {
        toggleShipper();
    });
    simpInp.change((e) => {
        const input = e.currentTarget,
            files = input.files,
            btn = $(input).prev();
        if (files.length > 0)
            btn.text(files[0].name);
        else
            btn.text('Upload file');
    });
    shipper.select2({
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
    })
    toggleMode(mode.val());
    toggleShipper();
})();
