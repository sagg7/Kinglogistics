<x-app-layout>
    <x-slot name="crumb_section">Daily Pay</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script>
        </script>
    @endsection

    {!! Form::open(['route' => 'dailyPay.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('subdomains.carriers.accounting.dailyPay.common.form')
    {!! Form::close() !!}
</x-app-layout>
