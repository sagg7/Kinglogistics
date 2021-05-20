(() => {
    $('.optionHandler').submit((e) => {
        e.preventDefault();
        let form = $(e.currentTarget),
            input = form.find('input[type=text]'),
            url = form.attr('action'),
            select = $(form.data('target-select')),
            hAction = form.data('handler-action'),
            formData = new FormData(form[0]),
            modal = form.closest('.modal');
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: (res) => {
                if (res.success) {
                    if (hAction === 'create')
                        select.append(`<option value="${res.data.id}">${res.data.name}</option>`);
                    else if (hAction === 'delete') {
                        let optVal = form.find('select').val();
                        select.find(`option[value=${optVal}]`).remove();
                    }
                    input.val('');
                    modal.modal('hide');
                } else
                    throwErrorMsg();
            },
            error: (res) => {
                let errors = null;
                if (res.responseJSON.errors) {
                    errors = '';
                    Object.values(res.responseJSON.errors).forEach((error) => {
                        errors += `•${error}<br>`;
                    });
                }
                throwErrorMsg(errors);
            }
        });
    });
})();
