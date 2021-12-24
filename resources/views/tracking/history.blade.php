<x-app-layout>
    <x-slot name="crumb_section">Tracking</x-slot>
    <x-slot name="crumb_subsection">History</x-slot>

    @section('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API") }}"></script>
        <script>
            const company = @json($company);
        </script>
        <script src="{{ asset('js/sections/tracking/history.min.js?1.0.3') }}"></script>
    @endsection

    <div class="card">
        <div class="card-body">
            <div class="card-content">
                <div class="row">
                    <div class="col-sm-6">
                        <fieldset class="form-group">
                            <label for="dateRange">Select Load Dates</label>
                            <input type="text" id="dateRange" class="form-control">
                        </fieldset>
                    </div>
                    <div class="col-sm-6">
                        <fieldset class="form-group">
                            <label for="driver">Driver</label>
                            {!! Form::select('driver', [], null, ['class' => 'form-control']) !!}
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="card-content">
                <div id="map" style="width: 100%; height: calc(100vh - 416px);"></div>
            </div>
        </div>
    </div>
</x-app-layout>
