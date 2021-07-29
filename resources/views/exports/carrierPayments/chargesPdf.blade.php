<x-pdf-layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <div class="text-center">
        <h3>{{ strtoupper($carrierPayment->carrier->name) }}</h3>
        <h3>{{ $title }}</h3>
    </div>
    <div>
        <table class="table mb-4">
            <thead>
            <tr>
                <th style="width: 11%;">Description</th>
                <th style="width: 11%;">Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach($carrierPayment->expenses as $expense)
                <tr>
                    <td>{{ $expense->description }}</td>
                    <td>${{ number_format($expense->amount, 2) }}</td>
                </tr>
            @endforeach
            <tr style="border-top: 1px solid;">
                <td><strong>Total</strong></td>
                <td>${{ number_format(-$carrierPayment->total, 2) }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</x-pdf-layout>
