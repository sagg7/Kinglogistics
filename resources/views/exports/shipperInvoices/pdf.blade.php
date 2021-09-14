<x-pdf-layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <div>
        <br>
        <h6>Date Invoiced: {{ $shipperInvoice->date->format('m/d/Y') }}</h6>
    </div>
    <div class="text-center">
        <h3><strong>{{ $broker->name }}</strong></h3>
        <h3>{{ $broker->address }}</h3>
        <h3>WEEK ENDING: {{ $shipperInvoice->date->endOfWeek()->format('m/d/Y') }}</h3>
        <h3>INVOICE #: {{ $shipperInvoice->id }}</h3>
    </div>
    <div>
        <table class="table table-striped mb-4">
            <thead>
            <tr>
                <th style="width: 12%;">LOAD DATE</th>
                <th style="width: 14%;">DRIVER</th>
                <th style="width: 14%;">WELL NAME</th>
                <th style="width: 12%;">Sand Ticket&nbsp;#</th>
                <th style="width: 12%;">Sandbox Control</th>
                <th style="width: 12%;">BOL</th>
                <th style="width: 12%;">MILES</th>
                <th style="width: 12%;">RATE</th>
            </tr>
            </thead>
            <tbody>
            @foreach($shipperInvoice->loads as $load)
            <tr>
                <td>{{ $load->date->format('m/d/Y') }}</td>
                <td>{{ $load->driver->name }}</td>
                <td>{{ $load->destination }}</td>
                <td>{{ $load->sand_ticket }}</td>
                <td>{{ $load->control }}</td>
                <td>{{ $load->bol }}</td>
                <td>{{ $load->mileage }}</td>
                <td>${{ number_format($load->shipper_rate, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td><strong>${{ number_format($shipperInvoice->total, 2) }}</strong></td>
            </tr>
            </tbody>
        </table>
    </div>
</x-pdf-layout>
