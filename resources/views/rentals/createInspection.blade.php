<x-app-layout>
    <x-slot name="crumb_section">Inspection</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>
    <div class="container-fluid mt--6" bis_skin_checked="1">
        <div class="row justify-content-center" bis_skin_checked="1">
            <div class=" col " bis_skin_checked="1">
                <div class="card" id="vehicleInspection" bis_skin_checked="1">
                    <div class="card-header bg-transparent" bis_skin_checked="1">
                        <h3 class="mb-0">Trailer Inspection - {{$title}}</h3>
                    </div>
                    <input type="hidden" name="" value="{{json_encode($pictures)}}" id='picturesInput'>
                    {!! Form::open(['route' => $action, 'method' => 'POST', 'role' => 'form','id'=>'inspectionForm','name'=>'inspectionForm']) !!}
                    <div class="card-body" bis_skin_checked="1">
                        <div class="panel-group" id="inspectionAccordion" role="blist" aria-multiselectable="true">
                             <input type="hidden" name="rental_id" value="{{$rental->id}}">
                            @foreach($inspection_categories as $categoryIndex => $category)
                                <?php $catJSON = json_decode($category->options);?>
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#inspectionAccordion" href="#collapse{{ $category->id }}" aria-expanded="true" aria-controls="collapse{{ $category->id }}">
                                                {{ $category->name }}
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapse{{ $category->id }}" class="panel-collapse collapse" role="tabpanel">
                                        <div class="panel-body" style="position: relative;">
                                            @if($catJSON->type === 'options')
                                                <table class="table table-striped table-os-bottom table-hover">
                                                    <thead>
                                                    <tr>
                                                        <td></td>
                                                        @foreach($catJSON->options as $option)
                                                            {!! '<td>'.$option.'</td>' !!}
                                                        @endforeach
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($category->items as $optionIndex => $item)
                                                        <tr>
                                                            <td>
                                                                {{ $item->name }}
                                                            </td>
                                                            @foreach($catJSON->options as $index => $option)
                                                                <td>
                                                                    @if(isset($inspection_items[$item->id]))
                                                                        @if($inspection_items[$item->id] == $index)
                                                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                                                <input class="original" type="radio" name="option_{{ $item->id }}" value="{{ $index }}" checked @if(isset($is_deliver)){{ 'disabled' }}@endif>
                                                                                <span class="vs-checkbox">
                                                                                    <span class="vs-checkbox--check">
                                                                                        <i class="vs-icon feather icon-check"></i>
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                        @else
                                                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                                                <input type="radio" name="option_{{ $item->id }}" value="{{ $index }}" @if(isset($is_deliver)){{ 'disabled' }}@endif>
                                                                                <span class="vs-checkbox">
                                                                                    <span class="vs-checkbox--check">
                                                                                        <i class="vs-icon feather icon-check"></i>
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                        @endif
                                                                    @else
                                                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                                                            <input class="original" type="radio" name="option_{{ $item->id }}" value="{{ $index }}"
                                                                            @if(json_decode($category->options)->options[$index] === json_decode($category->options)->default){{ 'checked' }}@endif @if(isset($is_deliver)){{ 'disabled' }}@endif>
                                                                            <span class="vs-checkbox">
                                                                                <span class="vs-checkbox--check">
                                                                                    <i class="vs-icon feather icon-check"></i>
                                                                                </span>
                                                                            </span>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @endif

                                            @if($catJSON->type === 'inputs')
                                                <table class="table table-striped table-os-bottom table-hover">
                                                    <thead>
                                                    <tr>
                                                        <td></td>
                                                        @foreach($catJSON->options as $option)
                                                            {!! '<td>'.$option.'</td>' !!}
                                                        @endforeach
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($category->items as $optionIndex => $item)
                                                        <tr>
                                                            <td>
                                                                {{ $item->name }}
                                                            </td>
                                                            @foreach($catJSON->options as $index => $option)
                                                                <td>
                                                                    @if(isset($inspection_items[$item->id]))
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <input type="text" name="option_{{$item->id}}[]" placeholder="eg. {{$catJSON->default[$index]}}" value="{{json_decode($inspection_items[$item->id])[$index]}}" class="form-control form-control-alternative" />
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <input type="text" name="option_{{$item->id}}[]" placeholder="eg. {{$catJSON->default[$index]}}" class="form-control form-control-alternative" />
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @endif

                                            @if($catJSON->type === 'coords')
                                                <div class="row">
                                                    <div class="col-sm-4 col-md-6">
                                                        <div class="condition-background-select">
                                                            <select name="condition-background" id="conditionBackground" tabindex="-1" aria-hidden="true" @if(isset($is_deliver)){{ 'disabled' }}@endif>
                                                                @foreach($coordsTemplates as $id => $template)
                                                                    <option value="{{ $id }}" data-img="{{ $template["img_src"] }}">{{ $template["text"] }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    @if(!isset($is_deliver))
                                                        <div class="col-sm-8 col-md-6">
                                                            <div class="condition-buttons">
                                                                <button type="button" data-type="impact">Hits</button>
                                                                <button type="button" data-type="broken">Broken</button>
                                                                <button type="button" data-type="scratch">Scratches</button>
                                                                <button type="button" data-type="eraser" class="eraser"><span class="fas fa-eraser"></span></button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <canvas width="760" height="416" class="condition-canvas"></canvas>
                                                <input type="text" hidden name="condition-data" id="conditionData" value="@if(isset($inspection_items[38])){{ $inspection_items[38] }}@endif"
                                                       original="@if(isset($inspection_items[38])){{ $inspection_items[38] }}@endif" @if(isset($is_deliver)){{ 'disabled' }}@endif>
                                            @endif

                                            @if($catJSON->type === 'base64')
                                                <div class="signature-wrapper-{{ $category->id }}">
                                                    @if($category->id == 7 && isset($inspection_items[40]) || $category->id == 8 && isset($inspection_items[41]))
                                                        <input type="hidden" name="modified" value="false" @if(isset($is_deliver)){{ 'disabled' }}@endif>
                                                        <img class="center-block" src="@if($category->id == 7){{ $inspection_items[40] }}@elseif($category->id == 8){{ $inspection_items[41] }}@endif" alt="signature">
                                                    @endif
                                                    <div class="signature-pad @if($category->id == 7 && isset($inspection_items[40]) || $category->id == 8 && isset($inspection_items[41])){{ 'hidden' }}@endif">
                                                        <button type="button" class="clear @if(isset($is_deliver)){{ 'hidden' }}@endif" id="clearSignatureButton-{{ $category->id }}">Clean</button>
                                                        <canvas id="signatureCanvas-{{ $category->id }}" class="signature-canvas @if(isset($is_deliver)){{ 'hidden' }}@endif" width="400" height="200" style="touch-action: none;"></canvas>
                                                    </div>
                                                </div>
                                                <input type="text" hidden name="signature-{{ $category->id }}" id="signatureInput-{{ $category->id }}" @if(isset($is_deliver)){{ 'disabled' }}@endif>
                                            @endif

                                            @if($catJSON->type === 'text-area')
                                                <div class="k-form-group">
                                                    <textarea class="form-control form-control-alternative" name="commentInspection" id="commentInspection" rows="3" placeholder="Write a comment here ...">{{ $inspection_items[39] ?? null }}</textarea>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="panel panel-default" id="getPhotos">
                                <div class="panel-heading" role="tab">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#inspectionAccordion" href="#collapsePhotos" aria-expanded="true" aria-controls="collapsePhotos">
                                            Add photos
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapsePhotos" class="panel-collapse collapse" role="tabpanel">
                                    <div class="panel-body" style="position: relative;">
                                        <div class="card shadow border-0">
                                            <div class="card-header bg-transparent pb-5">
                                                <div class="btn-wrapper text-center">
                                                    {!!Form::open(['route' => 'rental.uploadPhoto', 'method'=>'POST','id'=>'hiddenForm','class' =>'hidden','files'=>true,'role'=>'form'])!!}
                                                    <input type="text" id="deleteId" class="hidden" name="deleteId">
                                                    <input name="newImage" type="file" id="newImage" class="hidden" accept="image/*" multiple>
                                                    <button id="submitOrderImages" class="hidden"></button>
                                                    {!!Form::close()!!}
                                                    <div class="os-sphere-photos" id="imagesCollapse">
                                                        <div class="photos-wrapper">
                                                            <div class="photo-preview upload" id="uploadImage">
                                                                <div data-uploader='#newImage' id="imageUploader"
                                                                     class="center plus-wrapper" data-uploadingmessage>
                                                                    <div class="fas fa-camera fa-5x"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default" id="annex">
                                <div class="panel-heading" role="tab">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#inspectionAccordion" href="#collapseAnnex" aria-expanded="true" aria-controls="collapseAnnex">
                                            @if($type === "deliver"){{ 'Rental check out pdf annex' }}@else{{ 'Rental check in pdf annex' }}@endif
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseAnnex" class="panel-collapse collapse" role="tabpanel">
                                    <div class="panel-body text-left">
                                        <div class="form-group">
                                            {!! nl2br(e($rental->broker->config->rental_inspection_check_out_annex ?? $rental->broker->config->rental_inspection_check_in_annex ?? null))  !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-heading" role="tab" style="background-color:#36A66C !important; margin-top:5px">
                                <h4 class="panel-title">
                                    <a role="button" href="#vehicleInspection" aria-expanded="true" id="inspectionBtn">Save</a>
                                </h4>
                            </div>
                        </div>

                    </div>
                    {!! Form::close()!!}
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Content -->
    @include('rentals.modals.slider')
    @section('head')
        <style>
            #triangleMarker svg {
                margin: 0 auto;
            }
        </style>
        <link rel="stylesheet" href="{{ asset("app-assets/css/upload-photo.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/css/owl.theme.default.min.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/css/owl.carousel.min.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/css/inspection.css") }}">
    @endsection
    @section('scripts')
        <script src="{{ asset('app-assets/js/scripts/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('app-assets/js/scripts/imageResizer.js?1.0.1') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
        @include('public.imageUploaderJs')
        <script>
            $("#inspectionBtn").click(function (e) {
                let submit = $("#inspectionBtn");
                submit.html("<i class=\"fa fa-spinner fa-spin\"></i> Loading").addClass("disabled");
                e.preventDefault();

                const formAction = $('form[name=inspectionForm]').attr('action');
                const formData = new FormData(document.getElementById('inspectionForm'));
                let error = '';
                formData.forEach(function (i,o){
                    if (i == "" && o != "deleteId" && o != "commentInspection"  && o != "signature-8"  && o != "signature-7" ){
                        error = 'Make sure to fill in all the fields and set the signatures'
                    }
                })

                if (error != ''){
                    throwErrorMsg(error);
                    submit.html("Save").removeClass("disabled");
                } else {
                    $.ajax({
                        type        : 'POST',//    Define the type of HTTP verb we want to use (POST for our form).
                        url         : formAction,//   The url where we want to POST.
                        data        : formData,//  Our data object
                        processData: false,
                        contentType: false,
                        dataType    : 'json'// What type of data do we expect back from the server
                        //,encode    : true
                    }).done(function(data) {
                        submit.html("Save").removeClass("disabled");
                        throwErrorMsg("Inspection created Correctly", {"title": "Success!", "type": "success", "redirect": "{{url('rental/index')}}"})
                    });
                }
            });
            (() => {
                let conditionCtx,
                    selectedConditionOption,
                    conditionJSON,
                    carConditionImg,
                    signaturePad7,
                    signaturePad8,
                    condBckgnd = $('#conditionBackground'),
                    condCanvas = $('.condition-canvas');

                let resizeSignatureCanvas = (signatureCanvas) => {
                        let maxWidth = $('#inspectionAccordion').width(),
                            canvas7 = $('#signatureCanvas-7')[0],
                            canvas8 = $('#signatureCanvas-8')[0];
                        if (maxWidth < 720) {
                            if (canvas7.width != maxWidth) {
                                let currentImage = signaturePad7.toDataURL();
                                canvas7.width = maxWidth;
                                canvas7.height = maxWidth * 9 / 16;
                                signaturePad7.clear();
                                signaturePad7.fromDataURL(currentImage);
                            }
                        } else /*if (canvas7.width != 720)*/ {
                            let currentImage = signaturePad7.toDataURL();
                            canvas7.width = 720;
                            canvas7.height = 405;
                            signaturePad7.clear();
                            signaturePad7.fromDataURL(currentImage);
                        }
                        if (maxWidth < 720) {
                            if (canvas8.width != maxWidth) {
                                let currentImage = signaturePad8.toDataURL();
                                canvas8.width = maxWidth;
                                canvas8.height = maxWidth * 9 / 16;
                                signaturePad8.clear();
                                signaturePad8.fromDataURL(currentImage);
                            }
                        } else /*if (canvas8.width != 720)*/ {
                            let currentImage = signaturePad8.toDataURL();
                            canvas8.width = 720;
                            canvas8.height = 405;
                            signaturePad8.clear();
                            signaturePad8.fromDataURL(currentImage);
                        }
                    },

                    initSignatureCanvas = () => {
                        let canvas7 = $('#signatureCanvas-7')[0],
                            canvas8 = $('#signatureCanvas-8')[0],
                            data7,
                            data8;

                        signaturePad7 = new SignaturePad(canvas7, {
                            onEnd: function () {
                                $('#signatureInput-7').val(signaturePad7.toDataURL()).change();
                            }
                        });

                        signaturePad8 = new SignaturePad(canvas8, {
                            onEnd: function () {
                                $('#signatureInput-8').val(signaturePad8.toDataURL()).change();
                            }
                        });

                        data7 = $('#signatureInput-7').val();
                        data8 = $('#signatureInput-8').val();

                        signaturePad7.fromDataURL(data7);
                        signaturePad8.fromDataURL(data8);
                    },
                    populateConditionCanvas = () => {
                        conditionJSON.forEach(function (element) {
                            drawConditionFigure(element.type, element.pos, true);
                        }, this);
                    },
                    initConditionCanvas = () => {
                        carConditionImg = new Image();

                        conditionCtx = condCanvas[0].getContext('2d');

                        conditionJSON = $('#conditionData').val();

                        if (conditionJSON === "") {
                            conditionJSON = [];
                        } else {
                            conditionJSON = JSON.parse(conditionJSON);
                        }

                        carConditionImg.addEventListener('load', function () {
                            conditionCtx.drawImage(carConditionImg, 0, 0, 760, 416);
                            populateConditionCanvas();
                        }, false);

                        carConditionImg.src = condBckgnd.find('option:selected').data('img');
                    },
                    redrawConditionCanvas = () => {
                        let ctx = conditionCtx;

                        ctx.save();

                        ctx.fillStyle = "#FFF";
                        ctx.fillRect(0, 0, 760, 416);
                        conditionCtx.drawImage(carConditionImg, 0, 0, 760, 416);

                        populateConditionCanvas();

                        ctx.restore();
                    },
                    eraseConditionFigure = (coords) => {
                        conditionJSON = conditionJSON.filter(function (element) {
                            // If the distance between the points is greater than 10, dont erase that point. (using sqmagnitude)
                            return Math.pow(coords.x - element.pos.x, 2) + Math.pow(coords.y - element.pos.y, 2) > 144;
                        });

                        redrawConditionCanvas();
                    },
                    changeBackgroundImage = (newBg) => {
                        conditionCtx.save();

                        conditionCtx.fillStyle = "#FFF";
                        conditionCtx.fillRect(0, 0, 760, 416);

                        carConditionImg.src = newBg;

                        conditionCtx.restore();
                    },
                    drawConditionFigure = (figure, coords, skipSave) => {
                        let ctx = conditionCtx;
                        ctx.save();
                        ctx.beginPath();
                        ctx.fillStyle = "#C80000";
                        ctx.strokeStyle = "#C80000";

                        switch (figure) {
                            case "impact":
                                ctx.arc(coords.x, coords.y, 8, 0, 2 * Math.PI);
                                ctx.stroke();
                                ctx.beginPath();
                                ctx.arc(coords.x, coords.y, 8, 0, 2 * Math.PI);
                                ctx.fill();
                                break;
                            case "broken":
                                ctx.lineWidth = 2;
                                ctx.beginPath();
                                ctx.moveTo(coords.x - 7, coords.y - 7);
                                ctx.lineTo(coords.x + 7, coords.y + 7);
                                ctx.moveTo(coords.x - 7, coords.y + 7);
                                ctx.lineTo(coords.x + 7, coords.y - 7);
                                ctx.stroke();
                                break;
                            case "scratch":
                                ctx.lineWidth = 2;
                                ctx.beginPath();
                                ctx.moveTo(coords.x - 8, coords.y + 3);
                                ctx.lineTo(coords.x - 2, coords.y - 2);
                                ctx.lineTo(coords.x + 2, coords.y + 2);
                                ctx.lineTo(coords.x + 8, coords.y - 3);
                                ctx.stroke();
                                break;
                            default:
                                return false;
                        }

                        if (typeof (skipSave) == "undefined" || !skipSave) {
                            conditionJSON.push({
                                type: figure,
                                pos: coords
                            });
                        }

                        ctx.restore();
                    },
                    getMousePos = (canvas, evt) => {
                        let rect = canvas.getBoundingClientRect(),
                            baseCords = {
                                x: evt.clientX - rect.left,
                                y: evt.clientY - rect.top
                            };

                        baseCords.x = baseCords.x * 760 / rect.width;
                        baseCords.y = baseCords.y * 416 / rect.height;

                        return baseCords;
                    },
                    validateMaxLength = (figure, coords) => {
                        let temp = [...conditionJSON];
                        temp.push({
                            type: figure,
                            pos: coords,
                        });
                        let json = JSON.stringify(temp),
                            val = json.length <= 5000;
                        if (!val) {
                            alertWindow({
                                title: 'INFO',
                                content: 'Se ha llegado al número máximo de elementos disponibles para dibujar sobre el áre de "Condiciones de carrocería"',
                            });
                        }
                        return val;
                    };

                $(function () {
                    initConditionCanvas();
                    initSignatureCanvas();

                    window.addEventListener("resize", resizeSignatureCanvas);
                });


                condCanvas.click(function (e) {
                    if (selectedConditionOption === "eraser") {
                        eraseConditionFigure(getMousePos($(this)[0], e));
                    } else {
                        if (!validateMaxLength(selectedConditionOption, getMousePos($(this)[0], e)))
                            return false;
                        else
                            drawConditionFigure(selectedConditionOption, getMousePos($(this)[0], e));
                    }

                    $('#conditionData').val(JSON.stringify(conditionJSON)).change();
                });

                $('.condition-buttons button').click(function () {
                    selectedConditionOption = $(this).data('type');

                    $('.condition-buttons button').removeClass('selected');

                    $(this).addClass('selected');
                });

                condBckgnd.change(function () {
                    changeBackgroundImage($(this).find('option:selected').data('img'));
                });

                $('#clearSignatureButton-7').click(function () {
                    signaturePad7.clear();
                    $('#signatureInput-7').val("").change();
                });

                $('#clearSignatureButton-8').click(function () {
                    signaturePad8.clear();
                    $('#signatureInput-8').val("").change();
                });

                $('#vehicleInspection').on('shown.bs.modal', function (e) {
                    resizeSignatureCanvas();
                });

                $(document).ready(() => {
                    @if(isset($inspection_items[39]) && isset($vehicle_type->car_type))
                    condBckgnd.find('option').each(function () {
                        if (Number($(this).val()) === parseInt('{{ $vehicle_type->car_type }}')) {
                            $(this).prop('selected', true).trigger('change');
                        }
                    });
                    @endif
                    condBckgnd.select2();
                });
                $(".panel-heading").click(function (){
                    $(".panel-collapse.collapse").collapse('hide');
                });
            })();

        </script>
    @endsection
</x-app-layout>

