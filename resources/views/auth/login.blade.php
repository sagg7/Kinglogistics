@extends('layouts.app', ['class' => 'bg-default'])

@section('content')
    @include('layouts.headers.guest')

    <div class="container mt--8 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-header bg-transparent">
                        <div class="text-muted text-center mt-2 mb-3"><small>{{ __('Sign') }}</small></div>
                    </div>
                    <div class="card-body px-lg-5 py-lg-5">
                        <div class="col-md-12">
                            <div class="alert alert-danger" id="errorBox" style="display: none; font-size: 14px;"></div>
                        </div>
                        <form role="form" method="POST" action="{{ route('validate') }}" id="login-form">
                            @csrf

                            <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }} mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" type="email" name="email" value="{{ old('email') }}" required autofocus>
                                </div>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ __('Password') }}" type="password" required>
                                </div>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="custom-control custom-control-alternative custom-checkbox">
                                <input class="custom-control-input" name="remember" id="customCheckLogin" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="customCheckLogin">
                                    <span class="text-muted">{{ __('Remember me') }}</span>
                                </label>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary my-4" id="submit">{{ __('Sign in') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-light">
                                <small>{{ __('Forgot password?') }}</small>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('js')
    <script>
        let loginCounter = Number('{{ session('login_attempts') ?? 0 }}');
            //isCaptchaLoaded = false;

        function verifyCallback() {
            $('button[type=submit]').prop('disabled', false);
        }

        function expiredCallback() {
            $('button[type=submit]').prop('disabled', true);
        }

        /*function resetCaptcha() {
            grecaptcha.reset();
        }

        function loadCaptcha() {
            if (loginCounter >= 5) {
                $('button[type=submit]').prop('disabled', true);
                let recaptcha = grecaptcha.render('login-captcha', {
                    'sitekey': '6Ld8LboUAAAAAHQJ8ItAUZrVzobAlHw4PJK67m9B',
                    'data-type': 'image',
                    'callback': verifyCallback,
                    'expired-callback': expiredCallback,
                });
                isCaptchaLoaded = true;
            }
        }*/

        $(document).ready(function () {
            // $('#login-btn').attr('disabled', 'disabled');
            $('#login-form').submit(function (e) {
                //terms = $('#check_privacy').val();
                e.preventDefault();
                let submit = $("#submit");
                submit.html("<i class=\"fa fa-spinner fa-spin\"></i> Loading").attr("disabled");

                loginCounter++;
                let form = $(this),
                    formAction = form.attr('action'),
                    formData = new FormData(form[0]),
                    errorBox = $('#errorBox');
                errorBox.fadeOut('fast').html('');
                /*if (terms == 0) {
                    errorBox.fadeIn('fast').html('Debes aceptar los términos y condiciones y las políticas de privacidad para continuar');
                    setTimeout(removeButtonLoader, 250);
                } else {*/
                $.ajax({
                        type: 'POST',
                        url: formAction,
                        data: formData,
                        processData: false,
                        contentType: false,
                        complete: () => {
                            setTimeout(removeButtonLoader, 250);
                        },
                        success: () => {
                            window.location = '/';
                        },
                        error: (res) => {
                            submit.html("{{ __('Sign in') }}").removeAttr("disabled");

                            if (loginCounter >= 5 && !isCaptchaLoaded)
                                loadCaptcha();
                            if (loginCounter >= 5 && isCaptchaLoaded)
                                resetCaptcha();
                            if (!res.responseJSON)
                                errorBox.append('<li>An error occurred, try to login again, if the problem persists, please contact support.</li>');
                            else {
                                let json = res.responseJSON,
                                    errors = '';
                                if (json.errors !== undefined)
                                    $.each(json.errors, function (index, value) {
                                        errors += `<li>${value}</li>`;
                                    });
                                else
                                    errors += '<li>An error occurred while trying to perform the operation</li>';
                                errorBox.append(errors);

                                if (json.errorCode === 'TMNT')
                                    $.alert({
                                        animation: 'scale',
                                        closeAnimation: 'scale',
                                        animateFromElement: false,
                                        columnClass: 'col-md-6 col-md-offset-3 span6 offset3',
                                        type: 'red',
                                        title: json.msg,
                                        content: `In order to continue with the login, this view needs to be reloaded`,
                                        backgroundDismiss: false,
                                        buttons: {
                                            confirm: {
                                                text: 'RELOAD',
                                                btnClass: 'btn-blue',
                                                action: () => {
                                                    location.reload();
                                                }
                                            }
                                        }
                                    });
                            }
                            errorBox.fadeIn('fast');
                        }
                    });
                //}
            });
        });
    </script>
    @endpush
@endsection
