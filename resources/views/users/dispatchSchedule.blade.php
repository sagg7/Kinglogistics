<x-app-layout>
    <x-slot name="crumb_section">Staff</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section("head")
        <style>
            .table {
                table-layout: fixed;
            }
            .table tr td {
                width: 12.5%;
            }
            .table tr td:not(:nth-of-type(1)) {
                padding: 0;
            }
            .table tr td:nth-of-type(1) {
                padding-top: 0;
                padding-bottom: 0;
            }
            .table label {
                padding: 1rem;
                width: 100%;
                cursor: pointer;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .table input[type=checkbox] {
                display: none;
            }
        </style>
    @endsection

    @section('scripts')
        <script>
            const schedule = @json($schedule);
        </script>
        <script src="{{ asset('js/sections/users/dispatchSchedule.min.js') }}"></script>
    @endsection

    <div class="card">
        @if(auth()->user()->can(['create-dispatch-schedule', 'update-dispatch-schedule']))
        <div class="card-header">
            <div class="col-6">
                {!! Form::label('dispatch', ucfirst(__('dispatcher')), ['class' => 'col-form-label']) !!}
                {!! Form::select('dispatch', [], null, ['class' => 'form-control select2']) !!}
            </div>
            <div class="col-6">
                {!! Form::label('week', ucfirst(__('week')), ['class' => 'col-form-label']) !!}
                {!! Form::select('week', ['current' => 'Current week', 'next' => 'Next Week'], null, ['class' => 'form-control select2']) !!}
            </div>
        </div>
        <hr>
        @endif
        <div class="card-body">
            <div class="card-content">
                {!! Form::open(['route' => ['user.storeDispatchSchedule'], 'method' => 'post', 'class' => 'form form-vertical', 'id' => 'scheduleForm']) !!}
                <div class="col-12" id="current-week-schedule">
                    <div class="table-responsive mb-2" style="max-height: calc(100vh - 410px);">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wed</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                                <th>Sun</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($range as $item => $readable)
                                <tr>
                                    <td>{{ $readable }}</td>
                                    <td><label for="mon_{{ $item }}_current">&nbsp;</label><input type="checkbox" id="mon_{{ $item }}_current" name="days[mon][{{ $item }}]"></td>
                                    <td><label for="tue_{{ $item }}_current">&nbsp;</label><input type="checkbox" id="tue_{{ $item }}_current" name="days[tue][{{ $item }}]"></td>
                                    <td><label for="wed_{{ $item }}_current">&nbsp;</label><input type="checkbox" id="wed_{{ $item }}_current" name="days[wed][{{ $item }}]"></td>
                                    <td><label for="thu_{{ $item }}_current">&nbsp;</label><input type="checkbox" id="thu_{{ $item }}_current" name="days[thu][{{ $item }}]"></td>
                                    <td><label for="fri_{{ $item }}_current">&nbsp;</label><input type="checkbox" id="fri_{{ $item }}_current" name="days[fri][{{ $item }}]"></td>
                                    <td><label for="sat_{{ $item }}_current">&nbsp;</label><input type="checkbox" id="sat_{{ $item }}_current" name="days[sat][{{ $item }}]"></td>
                                    <td><label for="sun_{{ $item }}_current">&nbsp;</label><input type="checkbox" id="sun_{{ $item }}_current" name="days[sun][{{ $item }}]"></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 d-none" id="next-week-schedule">
                    <div class="table-responsive mb-2" style="max-height: calc(100vh - 410px);">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wed</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                                <th>Sun</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($range as $item => $readable)
                                <tr>
                                    <td>{{ $readable }}</td>
                                    <td><label for="mon_{{ $item }}_next">&nbsp;</label><input type="checkbox" id="mon_{{ $item }}_next" name="days_next[mon][{{ $item }}]"></td>
                                    <td><label for="tue_{{ $item }}_next">&nbsp;</label><input type="checkbox" id="tue_{{ $item }}_next" name="days_next[tue][{{ $item }}]"></td>
                                    <td><label for="wed_{{ $item }}_next">&nbsp;</label><input type="checkbox" id="wed_{{ $item }}_next" name="days_next[wed][{{ $item }}]"></td>
                                    <td><label for="thu_{{ $item }}_next">&nbsp;</label><input type="checkbox" id="thu_{{ $item }}_next" name="days_next[thu][{{ $item }}]"></td>
                                    <td><label for="fri_{{ $item }}_next">&nbsp;</label><input type="checkbox" id="fri_{{ $item }}_next" name="days_next[fri][{{ $item }}]"></td>
                                    <td><label for="sat_{{ $item }}_next">&nbsp;</label><input type="checkbox" id="sat_{{ $item }}_next" name="days_next[sat][{{ $item }}]"></td>
                                    <td><label for="sun_{{ $item }}_next">&nbsp;</label><input type="checkbox" id="sun_{{ $item }}_next" name="days_next[sun][{{ $item }}]"></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(auth()->user()->can(['create-dispatch-schedule', 'update-dispatch-schedule']))
                {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
                @endif
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</x-app-layout>
