(() => {
    const toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
        ['blockquote', 'code-block'],
        ['link', 'image', 'video'],

        [{ 'header': 1 }, { 'header': 2 }],               // custom button values
        //[{ 'list': 'ordered'}, { 'list': 'bullet' }],
        //[{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
        //[{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
        [{ 'direction': 'rtl' }],                         // text direction

        //[{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

        //[{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
        //[{ 'font': [] }],
        [{ 'align': [] }],

        ['link', 'image']

            ['clean']                                         // remove formatting button
    ];
    const quill = new Quill('#message_quill', {
        modules: { toolbar: toolbarOptions },
        theme: 'snow'
    });

    if (typeof content != "undefined")
        quill.setContents(content);

    $('form').submit((e) => {
        e.preventDefault();
        const form = $(e.currentTarget),
            message = JSON.stringify(quill.getContents());
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: {
                title: $('#title').val(),
                message,
            },
            success: (res) => {
                if (res.success)
                    window.location = '/notification/index';
                else
                    throwErrorMsg();
            },
            error: (res) => {
                console.log(res);
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
