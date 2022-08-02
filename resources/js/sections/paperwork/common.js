(() => {
    const adv = $('#advancedTemplate'),
        advInp = adv.find(':input'),
        simp = $('#simpleTemplate'),
        simpInp = simp.find(':input'),
        mode = $('#mode'),
        type = $('#type'),
        shipper = $('#shipper_id'),
        share = $('#shareCheck'),
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
                    share.removeClass('d-none');
                    break;
                default:
                    share.addClass('d-none');
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

    const imgsInp = $('#imagesInput');
    const imgsList = $('#imagesList');
    const copyToClipboard = (id) => {
        const element = document.querySelector(`#${id}`);
        element.addEventListener("click", () => {
            window.getSelection().selectAllChildren(element);
            document.execCommand("copy");
        });
    }
    imgsList.find('li').each((i, element) => {
        copyToClipboard(element.firstElementChild.getAttribute('id'));
    });
    imgsInp.change((e) => {
        let number = 0;
        const savedImgs = imgsList.find('code:not(.new_img)').parent();
        if (savedImgs.length > 0) {
            const lastEntry = savedImgs[savedImgs.length - 1].firstElementChild.getAttribute('id');
            number = lastEntry.split('_')[1];
        }
        const newImgs = imgsList.find('.new_img').parent();
        newImgs.remove();
        const input = e.currentTarget,
            files = input.files;
        Array.from(files).forEach((file, i) => {
            number++;
            const id = `img_${number}`;
            imgsList.append(`<li><code id="${id}" class="new_img">{{"image":"${number}"}}</code></li>`);
            copyToClipboard(id);
        });
    });
    $('.deleteImage').click((e) => {
        const button = $(e.currentTarget),
            listElement = button.closest('li'),
            id = button.data('imageid');
        confirmMsg({
            config: {title: 'Confirm deleting the image file?'},
            onConfirm: () => {
                $.ajax({
                    url: '/paperwork/deleteImage',
                    type: 'POST',
                    data: {
                        id,
                    },
                    success: (res) => {
                        if (res.success)
                            listElement.remove();
                    },
                    error: () => {
                        throwErrorMsg();
                    }
                });
            }
        });
    });
})();
