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
            (() => {
                let selectionData = {
                    current: {},
                    next: {},
                };
                const dispatch = $('#dispatch');
                const checkBox = $('input[type=checkbox]');
                let startedSelection = false,
                    selectionStart = null;
                const clearSelectionStart = () => {
                    if (startedSelection) {
                        startedSelection = false;
                        selectionStart.prop('checked', false);
                        selectionStart = null;
                    }
                };
                dispatch.select2({
                    placeholder: 'Select',
                    ajax: {
                        url: '/user/selection',
                        data: (params) => {
                            return {
                                search: params.term,
                                page: params.page || 1,
                                take: 15,
                                type: 'dispatch',
                            };
                        },
                    },
                    allowClear: true,
                })
                    .on('select2:select', () => {
                        clearSelectionStart();
                    })
                    .on('select2:unselect', () => {
                        clearSelectionStart();
                    });
                const getCheckboxDayName = (checkbox) => {
                    return checkbox.attr('id').split('_')[0];
                };
                const getCheckboxDayPosition = (checkbox) => {
                    return Number(checkbox.attr('id').split('_')[1]);
                };
                const getTableType = (checkbox) => {
                    console.log(checkbox.attr('id').split('_'));
                    return checkbox.attr('id').split('_')[2];
                }
                checkBox.click((e) => {
                    const currentCheckbox = $(e.currentTarget);
                    const tableType = getTableType(currentCheckbox);
                    let error = '';
                    if (dispatch.val() === null && currentCheckbox.is(':checked')) {
                        error = 'A dispatcher must be selected to continue';
                    }
                    if (error !== '' && (selectionStart && tableType !== getTableType(selectionStart))) {
                        error = 'Select a range within the same table to continue';
                    }
                    if (error !== '') {
                        throwErrorMsg(error);
                        e.preventDefault();
                        return false;
                    }
                    if (!startedSelection) {
                        // Case for checkbox uncheck
                        if (!currentCheckbox.is(':checked')) {
                            startedSelection = false;
                            currentCheckbox.prev().text('');
                            const nameArray = currentCheckbox.attr('id').split('_');
                            delete selectionData[tableType][`${nameArray[0]}_${nameArray[1]}`];
                        } else {
                            // Init the range selection process
                            startedSelection = true;
                            selectionStart = currentCheckbox;
                            currentCheckbox.prop('checked', true).prev().text(dispatch.find('option:selected').text());
                            currentCheckbox.prev().addClass('box-shadow-1');
                        }
                    } else {
                        const startName = getCheckboxDayName(selectionStart);
                        const endName = getCheckboxDayName(currentCheckbox);
                        // Validation to only choose a range within the same day at the start of the selection
                        if (startName !== endName) {
                            throwErrorMsg('Select the second time on the same day column');
                            return false;
                        }
                        const currentPos = getCheckboxDayPosition(currentCheckbox);
                        const startPos = getCheckboxDayPosition(selectionStart);
                        let start, end;
                        // Check if the direction of the selection
                        if (currentPos > startPos) {
                            start = startPos;
                            end = currentPos;
                        } else {
                            start = currentPos;
                            end = startPos;
                        }
                        // Check all the checkboxes within the range
                        for (let i = start; i <= end; i++) {
                            const loop = $(`#${startName}_${i}_${tableType}`),
                                label = loop.prev();
                            loop.prop('checked', true).val(dispatch.val());
                            label.text(dispatch.find('option:selected').text());
                            selectionData[tableType][`${startName}_${i}`] = {
                                user: dispatch.val(),
                                day: startName,
                                hour: i,
                            };
                        }
                        selectionStart.prev().removeClass('box-shadow-1');
                        // Reset flags to start the selection process again
                        startedSelection = false;
                        selectionStart = null;
                    }
                });
                const currentWeek = $('#current-week-schedule');
                const nextWeek = $('#next-week-schedule');
                $('#week').select2()
                    .on('select2:select', (e) => {
                        switch (e.params.data.id) {
                            default:
                            case 'current':
                                currentWeek.removeClass('d-none');
                                nextWeek.addClass('d-none');
                                break;
                            case 'next':
                                currentWeek.addClass('d-none');
                                nextWeek.removeClass('d-none');
                                break;
                        }
                    });
                const getDayName = (day_number) => {
                    switch (day_number) {
                        default:
                        case 0:
                            return 'mon';
                        case 1:
                            return 'tue';
                        case 2:
                            return 'wed';
                        case 3:
                            return 'thu';
                        case 4:
                            return 'fri';
                        case 5:
                            return 'sat';
                        case 6:
                            return 'sun';
                    }
                };
                schedule.forEach(item => {
                    const dayName = getDayName(item.day);
                    const toCheck = $(`#${dayName}_${item.time_number}_${item.status}`);
                    selectionData[item.status][`${dayName}_${item.time_number}`] = {
                        user: item.user_id,
                        day: dayName,
                        hour: item.time_number,
                    };
                    toCheck.prop('checked', true).val(item.user_id)
                        .prev().text(item.user.name);
                });
                const form = $('#scheduleForm');
                form.submit((e) => {
                    e.preventDefault();
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: selectionData,
                        success: (res) => {
                            if (!res.success) {
                                throwErrorMsg();
                            } else {
                                window.location = '/user/index';
                            }
                        },
                        error: () => {
                            throwErrorMsg();
                        }
                    })
                });
            })();
        </script>
    @endsection

    <div class="card">
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
                {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</x-app-layout>
