<div>
    <h3>Paperwork</h3>

    <table class="table" id="file-templates">
        <colgroup>
            <col style="width: 3%;">
            <col style="width: 48.5%;">
            <col style="width: 48.5%;">
        </colgroup>
        <thead>
        <tr>
            <th>Status</th>
            <th>Name</th>
            <th>PDF</th>
        </tr>
        </thead>
        <tbody>
        @foreach($filesTemplates as $i => $file)
            <tr data-file="{{ $file->id }}">
                <td><i class="feather @isset($paperworkTemplates[$file->id]){{ 'icon-check-circle text-success' }}@elseif($file->required){{ 'icon-x-circle text-danger' }}@else{{ 'icon-alert-circle text-warning' }}@endisset"></i></td>
                <td><a href="/paperwork/showTemplate/{{ $file->id }}/{{ $related_id }}" target="_blank">{{ $file->name }}</a></td>
                <td>@isset($paperworkTemplates[$file->id])<a href="/paperwork/pdf/{{ $file->id }}/{{ $related_id }}" target="_blank">Show PDF</a>@endisset</td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
