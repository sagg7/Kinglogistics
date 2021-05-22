<x-pdf-layout>
    <h1 class="text-center mb-1">Violations Report Form</h1>
    <h3 class="mb-4"><strong style="font-weight: bold;">Reported by:</strong> {{ $incident['user']['name'] }}</h3>
    <div>
        <table class="table table-primary w-33 mb-4">
            <thead>
            <tr>
                <th>Driver Name</th>
                <th>Truck #</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ $incident["driver"]["name"] }}</td>
                <td>{{ $incident["truck"]["number"] }}</td>
                <td>{{ $incident["date"] }}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="table table-primary w-33 mb-4">
            <thead>
            <tr>
                <th>Company Name</th>
                <th>Trailer #</th>
                <th>Location</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ $incident["carrier"]["name"] }}</td>
                <td>{{ $incident["trailer"]["number"] }}</td>
                <td>{{ $incident["location"] }}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="table table-primary mb-4">
            <thead>
            <tr>
                <th>Incident Type</th>
                <th>Sanction</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="w-50">{{ ucfirst($incident["sanction"]) }}</td>
                <td class="w-50">{{ $incident["incident_type"]["name"] . ($incident["sanction"] === "fine" ? (" - $" . number_format($incident["incident_type"]["fine"], 2)) : '') }}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="table table-primary mb-4">
            <thead>
            <tr>
                <th>Description</th>
                <th>Driver excuse</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="w-50">{{ $incident["description"] }}</td>
                <td class="w-50">{{ $incident["excuse"] }}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="table table-primary text-center w-50">
            <thead>
            </thead>
            <tbody>
            <tr>
                <td><img src="{{ $incident["safety_signature"] }}" alt="Safety Signature"></td>
                <td><img src="{{ $incident["driver_signature"] }}" alt="Driver Signature"></td>
            </tr>
            <tr>
                <td class="w-50">Safety Signature</td>
                <td class="w-50">
                    <div>Driver Signature</div>
                    @if($incident["refuse_sign"])
                        <div><span style="font-size: 20px;">☑</span>️Refuse to sign</div>
                    @endif
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</x-pdf-layout>
