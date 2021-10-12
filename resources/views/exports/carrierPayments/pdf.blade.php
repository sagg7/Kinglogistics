<x-pdf-layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <div class="text-center">
        <h3><strong>{{ strtoupper($carrierPayment->carrier->name) }}</strong></h3>
        <h3>{{ $title }}</h3>
    </div>
    <div>
        <table class="table mb-4">
            <thead>
            <tr>
                <th style="width: 2%;">#</th>
                <th style="width: 16%;">Carrier</th>
                <th style="width: 7%;">Truck&nbsp;#</th>
                <th style="width: 8%;">Load Date</th>
                <th style="width: 13%;">Driver</th>
                <th style="width: 12%;">Destination</th>
                <th style="width: 9%;">Sand&nbsp;Ticket&nbsp;#</th>
                <th style="width: 9%;">Control</th>
                <th style="width: 8%;">BOL</th>
                <th style="width: 8%;">Miles</th>
                <th style="width: 8%;">Rate</th>
            </tr>
            </thead>
            <tbody>
            @foreach($carrierPayment->loads as $key => $load)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $carrierPayment->carrier->name }}</td>
                <td>{{ $load->driver->truck->number ?? null }}</td>
                <td>{{ $load->date->format('m/d/Y') }}</td>
                <td>{{ $load->driver->name }}</td>
                <td>{{ $load->trip->name}}</td>
                <td>{{ $load->customer_reference }}</td>
                <td>{{ $load->control_number }}</td>
                <td>{{ $load->bol }}</td>
                <td>{{ $load->mileage }}</td>
                <td style="text-align: left;">${{ number_format($load->rate, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td colspan="8"></td>
                <td style="text-align: right; padding-right: 10px;">Subtotal</td>
                <td style="text-align: left;">${{ number_format($carrierPayment->gross_amount, 2) }}</td>
            </tr>
            @foreach($bonuses as $bonus)
            <tr>
                <td colspan="6"></td>
                <td colspan="3" style="text-align: right; padding-right: 10px;">{{ $bonus["name"] }}</td>
                <td style="text-align: left;">${{ number_format($bonus["amount"], 2) }}</td>
            </tr>
            @endforeach
            @foreach($expenses as $expense)
            <tr>
                <td colspan="6"></td>
                <td colspan="3" style="text-align: right; padding-right: 10px;">{{ $expense["name"] }}</td>
                <td style="text-align: left;">$({{ number_format($expense["amount"], 2) }})</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="6"></td>
                <td colspan="3" style="text-align: right; padding-right: 10px;"><strong>Total</strong></td>
                <td style="text-align: left;"><strong>${{ number_format($carrierPayment->total, 2) }}</strong></td>
            </tr>
            </tbody>
        </table>
    </div>
</x-pdf-layout>
