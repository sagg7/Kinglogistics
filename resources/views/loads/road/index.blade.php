<x-app-layout>
    <x-slot name="crumb_section">Load</x-slot>
    <x-slot name="crumb_subsection">Search</x-slot>

    @section('head')
        <style>
            .ag-theme-material .ag-header-cell, .ag-theme-material .ag-header-group-cell,
            .ag-theme-material .ag-cell {
                padding-left: 5px;
                padding-right: 5px;
            }

            /*span.ag-header-icon.ag-header-cell-menu-button {
                display: none;
            }*/
            #dataTable, .aggrid .ag-header-cell-text {
                font-size: .9rem !important;
            }
        </style>
    @endsection

    @section('modals')
        @include("loads.road.modals.loadDetails")
        @include("loads.road.modals.postLoadModal")
    @endsection
    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script src="{{ asset('js/sections/loads/road/postLoad.min.js') }}"></script>
        <script src="{{ asset('js/sections/loads/road/searchBoard.min.js?1.0.0') }}"></script>
    @endsection

    @if(auth()->guard('web')->check() || auth()->guard('shipper')->check())
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#postLoadModal" id="postLoadButton">Post load</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row form-group">
                <div class="col-8">
                    {!! Form::label('origin_city', 'Origin city, State(s) or Zipcode') !!}
                    {!! Form::select('origin_city', [], null, ['class' => 'form-control', 'multiple']) !!}
                </div>
                <div class="col-4">
                    {!! Form::label('origin_radius', 'Radius') !!}
                    {!! Form::select('origin_radius', $radius, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row form-group">
                <div class="col-8">
                    {!! Form::label('destination_city', 'Destination city, State(s) or Zipcode') !!}
                    {!! Form::select('destination_city', [], null, ['class' => 'form-control', 'multiple']) !!}
                </div>
                <div class="col-4">
                    {!! Form::label('destination_radius', 'Radius') !!}
                    {!! Form::select('destination_radius', $radius, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row form-group">
                <div class="col">
                    {!! Form::label('trailer_type', 'Trailer type') !!}
                    {!! Form::select('trailer_type', $trailer_types, null, ['class' => 'form-control', 'multiple']) !!}
                </div>
                <div class="col">
                    {!! Form::label('load_size', 'Load size') !!}
                    {!! Form::select('load_size', [null => 'All'] + $load_sizes, null, ['class' => 'form-control']) !!}
                </div>
                <div class="col">
                    {!! Form::label('ship_date', 'Ship date') !!}
                    {!! Form::text('ship_date', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col">
                    {!! Form::label('weight', 'Weight') !!}
                    {!! Form::select('weight', $weight, null, ['class' => 'form-control']) !!}
                </div>
                <div class="col">
                    {!! Form::label('length', 'Length') !!}
                    {!! Form::select('length', $length, null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="dataTable" class="aggrid ag-auto-height total-row ag-theme-material w-100"></div>
        </div>
    </div>
</x-app-layout>
