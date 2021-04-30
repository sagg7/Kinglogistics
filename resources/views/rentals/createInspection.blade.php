@extends('layouts.app')
@section('content')
    @include('layouts.headers.cards')
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
                                <?php $catJSON = json_decode($category->inspection_category_options); ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#inspectionAccordion" href="#collapse{{ $category->id }}" aria-expanded="true" aria-controls="collapse{{ $category->id }}">
                                                {{ $category->inspection_category_name }}
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapse{{ $category->id }}" class="panel-collapse collapse" role="tabpanel">
                                        <div class="panel-body" style="position: relative;">
                                            @if($catJSON->type == 'options')
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
                                                                {{ $item->inspection_item_name }}
                                                            </td>
                                                            @foreach($catJSON->options as $index => $option)
                                                                <td>
                                                                    @if(isset($inspection_items[$item->id]))
                                                                        @if($inspection_items[$item->id] == $index)
                                                                            <label class="k-checkbox k-checkbox-circle k-checkbox-normalize">
                                                                                <input class="original" type="radio" name="option_{{ $item->id }}" value="{{ $index }}" checked @if(isset($is_deliver)){{ 'disabled' }}@endif>
                                                                                <div><span><i class="fas fa-check"></i></span></div>
                                                                            </label>
                                                                        @else
                                                                            <label class="k-checkbox k-checkbox-circle k-checkbox-normalize">
                                                                                <input type="radio" name="option_{{ $item->id }}" value="{{ $index }}" @if(isset($is_deliver)){{ 'disabled' }}@endif>
                                                                                <div><span><i class="fas fa-check"></i></span></div>
                                                                            </label>
                                                                        @endif
                                                                    @else
                                                                        <label class="k-checkbox k-checkbox-circle k-checkbox-normalize">
                                                                            <input class="original" type="radio" name="option_{{ $item->id }}" value="{{ $index }}"
                                                                            @if($index === json_decode($category->inspection_category_options)->default){{ 'checked' }}@endif @if(isset($is_deliver)){{ 'disabled' }}@endif>
                                                                            <div><span><i class="fas fa-check"></i></span></div>
                                                                        </label>
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @endif

                                            @if($catJSON->type == 'inputs')
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
                                                                {{ $item->inspection_item_name }}
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

                                            @if($catJSON->type == 'coords')
                                                <div class="row">
                                                    <div class="col-sm-4 col-md-6">
                                                        <div class="condition-background-select">
                                                            <select name="condition-background" id="conditionBackground" tabindex="-1" aria-hidden="true" @if(isset($is_deliver)){{ 'disabled' }}@endif>
                                                                <option value="5" data-img="{{ asset('assets')}}/img/trailers/sandbox.jpg">Sandox</option>
                                                                <option value="6" data-img="{{ asset('assets')}}/img/trailers/sandbox.jpg">Pick-Up</option>
                                                                <option value="7" data-img="{{ asset('assets')}}/img/trailers/sandbox.jpg">Hatchback</option>
                                                                <option value="8" data-img="{{ asset('assets')}}/img/trailers/sandbox.jpg">Camioneta</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    @if(!isset($is_deliver))
                                                        <div class="col-sm-8 col-md-6">
                                                            <div class="condition-buttons">
                                                                <button type="button" data-type="impact">Golpes</button>
                                                                <button type="button" data-type="broken">Roto o Estrellado</button>
                                                                <button type="button" data-type="scratch">Rayones</button>
                                                                <button type="button" data-type="eraser" class="eraser"><span class="fas fa-eraser"></span></button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <canvas width="760" height="416" class="condition-canvas"></canvas>
                                                <input type="text" hidden name="condition-data" id="conditionData" value="@if(isset($inspection_items[38])){{ $inspection_items[38] }}@endif"
                                                       original="@if(isset($inspection_items[38])){{ $inspection_items[38] }}@endif" @if(isset($is_deliver)){{ 'disabled' }}@endif>
                                            @endif

                                            @if($catJSON->type == 'base64')
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

                                            @if($catJSON->type == 'text-area')
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
                                            <div class="card bg-secondary shadow border-0">
                                                <div class="card-header bg-transparent pb-5">
                                                    <div class="text-muted text-center mt-2 mb-3"><small>Add photos</small></div>
                                                    <div class="btn-wrapper text-center">
                                                        {!!Form::open(['route' => 'rental.uploadPhoto',
                                                                'method'=>'POST','id'=>'hiddenForm','class' =>'hidden','files'=>true,'role'=>'form'])!!}
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

    </div>
    @include('rentals.modals.slider')
    @include('layouts.footers.auth')
    @push('css')
        <style>
            #triangleMarker svg {
                margin: 0 auto;
            }
        </style>
        <link href="{{ asset('assets') }}/css/upload-photo.css" rel="stylesheet">
        <link href="{{ asset('assets') }}/css/select2.min.css" rel="stylesheet">
        <link href="{{ asset('assets') }}/css/owl.theme.default.min.css" rel="stylesheet">
        <link href="{{ asset('assets') }}/css/owl.carousel.min.css" rel="stylesheet">
        <link href="{{ asset('assets') }}/css/checkbox.css" rel="stylesheet">

    @endpush
    @push('js')
        <script src="{{ asset('assets') }}/js/select2.min.js"></script>
        <script src="{{ asset('assets') }}/js/owl.carousel.min.js"></script>
        <script src="{{ asset('assets') }}/js/components/common/imageResizer.js?1.0.1"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
        @include('public.imageUploaderJs')
        <script>
            $("#inspectionBtn").click(function (e)
            {
                $("#loading").removeClass("hidden");
                e.preventDefault();

                var formAction = $('form[name=inspectionForm]').attr('action');
                var formData = new FormData(document.getElementById('inspectionForm'));
                let error = '';
                formData.forEach(function (i,o){
                    if (i == "" && o != "deleteId" && o != "commentInspection"  && o != "signature-8"  && o != "signature-7" ){
                        error = 'Make sure to fill in all the fields and set the signatures'
                    }
                })

                if (error != ''){
                    $.alert({
                        animation: 'scale',
                        closeAnimation: 'scale',
                        animateFromElement: false,
                        columnClass: 'col-md-6 col-md-offset-3 span6 offset3',
                        type: 'red',
                        title: "error",
                        content: error,
                        backgroundDismiss: false,
                        buttons: {
                            confirm: {
                                text: 'OK',
                                btnClass: 'btn-blue',
                            }
                        }
                    });
                    $("#loading").addClass("hidden");

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
                        $("#loading").addClass("hidden");
                        $.alert({
                            animation: 'scale',
                            closeAnimation: 'scale',
                            animateFromElement: false,
                            columnClass: 'col-md-6 col-md-offset-3 span6 offset3',
                            type: 'blue',
                            title: "Success!",
                            content: `Inspection created correctly`,
                            backgroundDismiss: false,
                            buttons: {
                                confirm: {
                                    text: 'OK',
                                    btnClass: 'btn-blue',
                                    action: () => {
                                        window.location = '{{url('rentals')}}';
                                    }
                                }
                            }
                        });
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
                                break;
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

                    let options = {
                            geometry: {
                                startAngle: 180,
                                endAngle: 0
                            },
                            scale: {
                                startValue: 0,
                                endValue: 1,
                                tickInterval: .125,
                                label: {
                                    customizeText: function (arg) {
                                        return arg.valueText * 8 + "/8";
                                    }
                                }
                            }
                        },
                        gasChart = $('#gasChart'),
                        triangle = $('#triangleMarker');
                    triangle.dxCircularGauge($.extend(true, {}, options, {
                        value: gasChart.val(),
                        // subvalues: [2, 8],
                        subvalueIndicator: {
                            type: "triangleMarker",
                            color: "#8FBC8F"
                        }
                    }));
                    gasChart.change(function () {
                        triangle.dxCircularGauge($.extend(true, {}, options, {
                            value: $(this).val(),
                            // subvalues: [2, 8],
                            subvalueIndicator: {
                                type: "triangleMarker",
                                color: "#8FBC8F"
                            }
                        }));
                    });

                });
                $(".panel-heading").click(function (){
                    $(".panel-collapse.collapse").collapse('hide');
                });
            })();

        </script>
    @endpush
@endsection
