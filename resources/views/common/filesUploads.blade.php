<div>
    <h3>File uploads</h3>

    <table class="table" id="file-uploads">
        <colgroup>
            <col style="width: 15%;">
            <col style="width: 28.333%;">
            <col style="width: 28.333%;">
            <col style="width: 28.333%;">
        </colgroup>
        <thead>
        <tr>
            <th>Name</th>
            <th>Upload</th>
            <th>File</th>
            <th>Expiration date</th>
        </tr>
        </thead>
        <tbody>
        @foreach($filesUploads as $i => $file)
            <tr data-file="{{ $file->id }}">
                <td>{{ $file->name }}</td>
                <td>
                    <div class="file-group">
                        <label for="file-{{ $file->id }}"
                               class="btn form-control @if($file->required){{ 'btn-warning' }}@else{{ 'btn-primary' }}@endif btn-block">
                            <i class="fas fa-file"></i> <span class="file-name">Upload File</span>
                            <input type="file" name="file[{{ $file->id }}]" id="file-{{ $file->id }}" hidden>
                        </label>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-danger remove-file d-none"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </td>
                <td class="file-link">
                    @if(isset($paperworkUploads[$file->id]))
                        <a href="/{{ $paperworkUploads[$file->id]['url'] }}" target="_blank">{{ $paperworkUploads[$file->id]['file_name'] }}</a>
                    @endif
                </td>
                <td class="position-relative">
                    {!! Form::text("expiration_date[$file->id]", $paperworkUploads[$file->id]['expiration_date'] ?? null, ['class' => 'form-control pickadate-months-year']) !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {!! Form::hidden('related_id', $related_id) !!}
    {!! Form::hidden('type', $type) !!}

    {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}

</div>
