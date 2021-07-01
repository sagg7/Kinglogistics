<x-app-layout>
    <x-slot name="crumb_section">Diesel Charges</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/charges/common.min.js') }}"></script>
    @endsection

    {!! Form::open(['route' => 'charge.storeDiesel', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    <div class="card">
        <div class="card-body">
            <div class="card-content">
                <div class="row">
                    <table class="table">
                        <colgroup>
                            <col style="width: 50%;">
                            <col style="width: 50%;">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>Carrier</th>
                            <th>Diesel Price</th>
                        </tr>
                        </thead>
                        <tbdoy>
                            @foreach($carriers as $id => $carrier)
                                <tr>
                                    <td>{{ $carrier }}</td>
                                    <td>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-dollar-sign"></i></span>
                                            </div>
                                            {!! Form::text("diesel[$id]",null,['class' => 'form-control']) !!}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbdoy>
                    </table>
                </div>
            </div>
            {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
        </div>
    </div>
    {!! Form::close() !!}
</x-app-layout>
