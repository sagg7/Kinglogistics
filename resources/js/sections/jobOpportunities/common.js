(() => {
    const toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
        ['blockquote', 'code-block'],
        ['link', 'image', 'video'],
        [{ 'header': 1 }, { 'header': 2 }],               // custom button values
        [{ 'direction': 'rtl' }],                         // text direction
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [{ 'align': [] }],
        ['link', 'image'],
        ['clean'],                                         // remove formatting button
    ];
    const quill = new Quill('#message_quill', {
        modules: { toolbar: toolbarOptions },
        theme: 'snow'
    });

    if (typeof content != "undefined")
        quill.setContents(content);

    const carrierSel = $('[name="carriers[]"]');
    carrierSel.select2({
        ajax: {
            url: '/carrier/selection',
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

    $('form').submit((e) => {
        e.preventDefault();
        const form = $(e.currentTarget),
            message = JSON.stringify(quill.getContents());
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: {
                title: $('#title').val(),
                carriers: carrierSel.val(),
                message,
            },
            success: (res) => {
                if (res.success)
                    window.location = '/jobOpportunity/index';
                else
                    throwErrorMsg();
            },
            error: (res) => {
                let errors = `<ul class="text-left">`;
                Object.values(res.responseJSON.errors).forEach((error) => {
                    errors += `<li>${error}</li>`;
                });
                errors += `</ul>`;
                throwErrorMsg(errors, {timer: false});
            },
        });
    });
})();
