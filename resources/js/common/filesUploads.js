(() => {
    $('input[type=file]').change((e) => {
        const target = e.currentTarget,
            inp = $(target),
            label = inp.closest('label').find('.file-name'),
            group = inp.closest('.file-group'),
            rmvBtn = group.find('.remove-file'),
            file = target.files[0];
        if (file) {
            label.text(file.name);
            group.addClass('input-group');
            rmvBtn.removeClass('d-none');
        } else {
            label.text('Upload File');
            group.removeClass('input-group');
            rmvBtn.addClass('d-none');
        }
    });
    $('.remove-file').click((e) => {
        const btn = $(e.currentTarget),
            group = btn.closest('.file-group'),
            inp = group.find('input[type=file]');
        inp.val('').trigger('change');
    });

    const table = $('#file-uploads'),
        tbody = table.find('tbody');
    $('#pane-paperwork form').submit((e) => {
        e.preventDefault();
        const form = $(e.currentTarget),
            url = form.attr('action');
        let formData = new FormData(form[0]);
        const btn = $(e.originalEvent.submitter),
            btnText = btn.text();
        btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`);
        btn.prop('disabled', true);
        $.ajax({
            url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: (res) => {
                if (res.success) {
                    res.data.forEach((item) => {
                        const tr = tbody.find(`tr[data-file="${item.paperwork_id}"]`),
                            fLink = tr.find('.file-link');
                        tr.find('input[type=file]').val('').trigger('change');
                        fLink.html(`<a href="${item.url}" target="_blank">${item.file_name}</a>`);
                    });
                } else
                    throwErrorMsg();
            },
            error: () => {
                throwErrorMsg();
            }
        }).always(() => {
            btn.text(btnText).prop('disabled', false);
        });
    });
})();
