<script>
    let requestHeaders = {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'rentalId': {{$rental->id}}
    }
    let maxPhotos = 20;
    $(document).ready(() => {
        let uploader = new ImageUploader({
            inputElement: document.getElementById('newImage'),
            uploadUrl: '/rental/uploadPhoto',
            maxWidth: 1024,
            quality: 0.90,
            requestHeaders: requestHeaders,
            maxFiles: Number(maxPhotos),
            onComplete: function (event) {
                if(event.errors && event.errors.length > 0) {
                    let errStr = '';
                    event.errors.forEach((error, index) => {
                        errStr += error+'<br>';
                    });
                    throwErrorMsg('Error accepting the image!')
                }
                listUploadedImages();
            },
            debug: false
        });
        $(document).on('click', '[data-uploader="#newImage"]', function () {
            console.log("aaaaa");
            let uploader = $(this),
                container = uploader.parent(),
                tgt = uploader.data('uploader'),
                tgtInp = $(tgt),
                autoSubmit = uploader.data('autosubmit'),
                clear = uploader.data('clear'),
                uploadingmessage = uploader.data('uploadingmessage');
            tgtInp.change(function () {

                if (this.files && this.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function (e) {
                        let html = '<img src="' + e.target.result + '" alt="Logotipo">';
                        if (typeof uploadingmessage != "undefined")
                            html += "<div class='change-image uploading-msg' data-uploader='" + tgt + "'>Uploading</div>";
                        else
                            html += "<div class='change-image' data-uploader='" + tgt + "'>" +
                                `<img src='${MyAssets('img/upper-bar/edit-account.png')}'>&nbsp;Change</div>`;
                        container.html(html);
                        if (typeof autoSubmit != "undefined") {
                            if (typeof clear != "undefined") $(clear).val('');
                            $(autoSubmit).click();
                        }
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
            tgtInp.click();
        });
    });

    bindDeletePhoto = (buttonId,carouselId) => {
        $('span.deleteImage').click(function (e) {
            e.stopPropagation();
            let toDelete = $(this).data('imageid') ,
                img = $(this).parent(),
                container = $(this).parent();
            $.confirm({
                animation: 'scale',
                closeAnimation: 'scale',
                animateFromElement: false,
                columnClass: 'col-md-6 col-md-offset-3 span6 offset3',
                title: 'Fotograf??as  de la orden',
                content: '??Est??s seguro de eliminar la fotograf??a seleccionada?',
                backgroundDismiss: false,
                buttons: {
                    confirm: {
                        text: 'CONTINUAR',
                        btnClass: 'btn-blue',
                        action: function () {
                            container.append("<div class='change-image uploading-msg'>Eliminando</div>");
                            $.post('/rental/deletePhoto', {deleteId : toDelete}).success((data) => {
                                img.remove();
                                let photoCount = $(`${buttonId} .circle_files`),
                                    carousel = $(carouselId);
                                photoCount.text($('.photo-preview').not('.upload').length.toString() + photoCount.text().substring(1));
                                carousel.trigger('destroy.owl.carousel');
                                $('#slider-'+toDelete).remove();
                                initOwlCarousel(carousel);
                            });
                        }
                    },
                    cancel: {
                        text: 'CANCELAR'
                    }
                }
            });
        });
    };

    unbindDeletePhoto = () => {
        $('span.deleteImage').unbind('click');
    };

    initOwlCarousel = (carousel) => {
        carousel.owlCarousel({
            items: 1,
            lazyLoad: true,
            loop: true,
            margin: 10,
            nav: true,
            navText: ["<i class='fa fa-angle-left' aria-hidden='true'></i>", "<i class='fa fa-angle-right' aria-hidden='true'></i>"]
        });
    };

    listUploadedImages = () => {
        unbindDeletePhoto();
        let imageUploaded = ImageUploader.uploads;
        imageUploaded.forEach((photo, index) => {
            console.log(photo);
            let picDiv = `<div class="photo-preview" data-toggle="modal" data-target="#swiperModal" id="photo${photo.id}"
        style="background-image: url(${photo.url})">
        <span class="deleteImage" data-imageid="${photo.id}">
        <span class="fas fa-times"></span></span></div>`;
            $('#imagesCollapse .photos-wrapper').prepend(picDiv);
            let carousel = $('#picturesCarousel');
            carousel.trigger('destroy.owl.carousel');
            carousel.prepend(`<div id="slider-${photo.id}"><img class="owl-lazy" data-src="${photo.url}" alt="" title="$photo${photo.id}"></div>`);
            initOwlCarousel(carousel);
        });
        imageUploaded.slice().reverse().forEach((photo, index) => {
            let upPicCount = $('.photo-preview', '#imagesCollapse').not('.upload').length
            $("#photo" + photo.id).click(function () {
                $('#picturesValuacionCarousel').trigger('to.owl.carousel', upPicCount)
            });
        });
        $('#uploadImage').html(`<div data-uploader="#newImage" id="imageUploader" class="center plus-wrapper" data-uploadingmessage=""><div class="fas fa-camera fa-5x"></div></div>`);
        let photoCount = $('#getPhotos .circle_files');
        photoCount.text($('.photo-preview', '#imagesCollapse').not('.upload').length.toString() + ' / ' + maxPhotos);
        ImageUploader.uploads = [];
        bindDeletePhoto('#getPhotos', '#picturesCarousel');
    };

    $('#getPhotos').one('click', () => {
        let picInput = JSON.parse($('#picturesInput').val());
        let picture = {};
        let sliderPhotos = [];
        picInput.forEach((pic) => {
            picture[pic.id] =
                `<div class="photo-preview" data-toggle="modal" data-target="#swiperModal" id="photo${pic.id}"
                 style="background-image: url(${pic.url})" title="${pic.created_at}">
                <span class="deleteOrderImage" data-imageid="${pic.id}">
                    <span class="fas fa-times"></span>
                </span>
            </div>`;
            $('.photos-wrapper', '#imagesCollapse').prepend(picture[pic.id]);
            sliderPhotos.push({
                id: pic.id,
                picture_slider: pic.url,
                picture_path: pic.url,
            });
        });
        sliderPhotos.slice().reverse().forEach((photo, index) => {
            $("#photo" + photo.id).click(function () {
                $('#picturesCarousel').trigger('to.owl.carousel', index)
            });
        });
        initOwlCarousel($('#picturesCarousel'));
        bindDeletePhoto('#getPhotos', '#picturesCarousel');
    });

</script>
