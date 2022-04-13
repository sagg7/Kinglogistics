<x-guest-layout>
    @section('scripts')
        <script>
            (() => {
                const form = $('form');
                form.submit((e) => {
                    e.preventDefault();
                    const formData = new FormData(form[0]);
                    formData.append('timezone', moment.tz.guess());
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: (res) => {
                            if (res.success) {
                                window.location = res.route;
                            }
                        },
                        error: (res) => {
                            let errors = `<ul class="text-left">`;
                            Object.values(res.responseJSON.errors).forEach((error) => {
                                errors += `<li>${error}</li>`;
                            });
                            errors += `</ul>`;
                            throwErrorMsg(errors);
                        }
                    });
                });
            })();
        </script>
    @endsection
    <form method="POST" action="{{ route('login') }}">
    @csrf

        <h1 class="main-title mb-1">WELCOME</h1>
        <h5 class="mt-0 mb-5">Please fill in the following fields:</h5>

        <!-- Email Address -->
        <div class="form-group">
            {!! Form::email('email', null, ['class' => 'form-control' . ($errors->first('type') ? ' is-invalid' : ''), 'placeholder' => 'Email']) !!}
            <span class="input-focus-after"></span>
        </div>

        <!-- Password -->
        <div class="form-group">
            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password']) !!}
            <span class="input-focus-after"></span>
        </div>

        <div class="row mt-4 mb-4">
            <div class="col">
                <!-- Remember Me -->
                <label for="remember_me" class="inline-flex items-center">
                    {!! Form::checkbox('remember', null, false, ['class' => 'form-check-input', 'id' => 'remember']) !!}
                    {!! Form::label('remember', 'Remember me',['class' => 'form-check-label']) !!}
                </label>
            </div>
            <div class="col text-end">
                @if(Route::has('password.request'))
                    <a class="link-secondary" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
        </div>

        {!! Form::submit('LOG IN', ['class' => 'btn btn-primary ps-5 pe-5 pt-3 pb-3']) !!}
    </form>
</x-guest-layout>
