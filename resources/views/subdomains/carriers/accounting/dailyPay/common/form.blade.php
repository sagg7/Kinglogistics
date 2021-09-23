<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="table-responsive">
                @error('loads')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ ucfirst($message) }}</strong>
                </span>
                @enderror
                <table class="table table-hover">
                    <colgroup>
                        <col style="width: 16%;">
                        <col style="width: 20%;">
                        <col style="width: 20%;">
                        <col style="width: 20%;">
                        <col style="width: 20%;">
                        <col style="width: 4%;">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Driver</th>
                        <th>Control #</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($loads as $load)
                        <tr>
                            <td>{{ $load["date"] }}</td>
                            <td>{{ $load["driver"]["name"] }}</td>
                            <td>{{ $load["control_number"] }}</td>
                            <td>{{ $load["origin"] }}</td>
                            <td>{{ $load["destination"] }}</td>
                            <td>
                                <fieldset>
                                    <div class="vs-checkbox-con vs-checkbox-success">
                                        <input type="checkbox" value="{{ $load["id"] }}" name="loads[]" @if(isset($load['checked'])){{ 'checked' }}@endif>
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                    </div>
                                </fieldset>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div> <!-- end card -->
