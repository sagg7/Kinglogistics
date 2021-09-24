<x-app-layout>
    <x-slot name="crumb_section">Job Opportunity</x-slot>

    <div class="card">
        <div class="card-body">
            {!! $opportunity->html !!}
        </div>
    </div>
</x-app-layout>
