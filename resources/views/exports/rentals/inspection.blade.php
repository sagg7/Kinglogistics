<x-pdf-layout>
    @section("head")
        <style>
            body {
                line-height: 1em;
                /*pointer-events: none;*/
                width: 750px;
            }

            .info_table {
                text-align: left;
                border: 1px solid;
                width: 100%;
            }

            .top_row td {
                padding-top: .8em;
            }

            .bottom_row td {
                padding-bottom: .8em;
            }

            .main_table table tbody > tr > td {
                padding-right: .8em;
                padding-left: .8em;
            }
        </style>
    @endsection
    @section("scripts")
        <script src="{{ asset('js/sections/exports/rentals/inspection.min.js?1.0.0') }}" defer></script>
    @endsection
    <x-slot name="title">Rental Inspection</x-slot>
    <x-slot name="advanced"></x-slot>
    <div class="row m-0 main_table mb-5">
        <div class="col-6">
            <table style="width: 100%;">
                <tbody>
                <tr>
                    <td>
                        <img src="{{ $companyLogo }}" alt="company logo" style="width: 100%;max-width: 200px;max-height: 200px;">
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-6">
            <table class="info_table" style="width: 100%;">
                <tbody>
                <tr class="top_row">
                    <td><strong>Date:</strong></td>
                    <td>{{ $rental->date->format('m/d/Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Carrier:</strong></td>
                    <td>{{ $rental->carrier->name }}</td>
                </tr>
                <tr>
                    <td><strong>Driver:</strong></td>
                    <td>{{ $rental->driver->name ?? null }}</td>
                </tr>
                <tr>
                    <td><strong>Trailer:</strong></td>
                    <td>{{ $rental->trailer->number }}</td>
                </tr>
                <tr>
                    <td><strong>Period:</strong></td>
                    <td>{{ ucfirst($rental->period) }}</td>
                </tr>
                <tr>
                    <td><strong>Cost:</strong></td>
                    <td>{{ $rental->cost }}</td>
                </tr>
                <tr class="bottom_row">
                    <td><strong>Deposit:</strong></td>
                    <td>{{ $rental->deposit }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row m-0 masonry_grid">
        @foreach($categories as $i => $category)
            @php
                $options = json_decode($category['options']);
            @endphp
            @switch($options->type)
                @case('options')
                <div class="col-6">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>{{ $category['name'] }}</th>
                            <th>Good</th>
                            <th>Damaged</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($category['rental_items'] as $item)
                            <tr>
                                <td>{{ $item["name"] }}</td>
                                @if($item["pivot"]["option_value"] === "1")
                                    <td></td>
                                    <td class="text-center @if($item["pivot"]["value_changed"] ?? false){{ 'text-danger' }}@endif">✓</td>
                                @else
                                    <td class="text-center @if($item["pivot"]["value_changed"] ?? false){{ 'text-danger' }}@endif">✓</td>
                                    <td></td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @break
                @case('inputs')
                <div class="col-6">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>{{ $category['name'] }}</th>
                            @foreach($options->options as $item)
                                <th>{{ $item }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($category['rental_items'] as $item)
                            <tr>
                                <td>{{ $item["name"] }}</td>
                                @foreach(json_decode($item["pivot"]["option_value"]) as $i => $value)
                                    <td class="@if($item["pivot"]["value_changed"][$i] ?? false){{ 'text-danger' }}@endif">{{ $value }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @break
                @case('base64')
                <div class="col-6">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>{{ $category['name'] }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($category['rental_items'] as $item)
                            <tr>
                                <td class="text-center">
                                    <img src="{{ $item["pivot"]["option_value"] }}" alt="base64" class="img-fluid">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @break
                @case('coords')
                <div class="col-12">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>{{ $category['name'] }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($category['rental_items'] as $item)
                            <tr>
                                <td class="text-center">
                                    <canvas width="700" height="416" class="condition-canvas"></canvas>
                                    <input type="hidden" name="condition-data" id="conditionData" value="{{ $item["pivot"]["option_value"] }}" original="{{ $item["pivot"]["option_value"] }}" disabled>
                                    <input type="hidden" value="{{ $item["pivot"]["coords_template"]["img_src"] }}" id="conditionBackground">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @break
                @case('text-area')
                <div class="col-12">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>{{ $category['name'] }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @isset($category['rental_items'])
                            @foreach($category['rental_items'] as $item)
                                <tr>
                                <!--<td>{{ $item["name"] }}</td>-->
                                    <td>{{ $item["pivot"]["option_value"] }}</td>
                                </tr>
                            @endforeach
                        @endisset
                        </tbody>
                    </table>
                </div>
                @break
                @default
                <div class="col-6">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th colspan="100">{{ $category['name'] }}</th>
                        </tr>
                        </thead>
                        @break
                    </table>
                </div>
            @endswitch
        @endforeach
    </div>
    <div class="row m-0" style="page-break-before: always;">
        <div class="col-12">
            {!! nl2br(e($rental->broker->config->rental_inspection_check_out_annex ?? $rental->broker->config->rental_inspection_check_in_annex ?? null))  !!}
        </div>
    </div>
</x-pdf-layout>
