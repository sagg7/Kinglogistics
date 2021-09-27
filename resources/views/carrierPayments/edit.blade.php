<x-app-layout>
    <x-slot name="crumb_section">Payments</x-slot>
    <x-slot name="crumb_subsection">Edit - {{ $carrierPayment->carrier->name }}</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script src="{{ asset('js/modules/aggrid/common.min.js?1.0.0') }}"></script>
        <script>
            var _aggrid;
            const bonuses = @json($carrierPayment->bonuses);
            const expenses = @json($carrierPayment->expenses);
        </script>
        <script src="{{ asset('js/sections/carrierPayments/editPayment.min.js?1.0.2') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
    @endsection

    @section('modals')
        @include('common.modals.typeModal', ['id' => 'bonusTypeModal', 'name' => 'Bonus Type', 'route' => 'bonusType.store', 'selectId' => 'bonus_type', 'deleteTypeModalId' => 'deleteBonusTypeModal'])
        @include('common.modals.deleteTypeModal', ['id' => 'deleteBonusTypeModal', 'name' => 'Bonus Type', 'route' => 'bonusType.delete', 'selectId' => 'bonus_type'])
    @endsection


    <div class="card">
        <div class="card-body">
            <div class="card-content">
                <div class="row">
                    <div class="form-group col-md-4">
                        {!! Form::label('type', ucfirst(__('type')), ['class' => 'col-form-label']) !!}
                        {!! Form::select('type', ['bonus' => 'Bonus', 'expense' => 'Expense'], null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group col-md-4">
                        {!! Form::label('bonus_type', ucfirst(__('bonus type')), ['class' => 'col-form-label']) !!}
                        <div class="input-group" id="bonus-type-container">
                            {!! Form::select('bonus_type', $bonusTypes, null, ['class' => 'form-control']) !!}
                            <div class="input-group-append">
                                <button class="btn btn-success pl-1 pr-1" type="button" data-toggle="modal" data-target="#bonusTypeModal"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="d-none" id="expense-type-container">
                            {!! Form::label('expense_type', ucfirst(__('expense type')), ['class' => 'col-form-label']) !!}
                            {!! Form::select('expense_type', $expenseTypes, null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <fieldset>
                            {!! Form::label('date', ucfirst(__('date')), ['class' => 'col-form-label']) !!}
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-calendar-alt"></i></span>
                                </div>
                                {!! Form::text("date", null, ['class' => 'form-control pickadate-months-year']) !!}
                            </div>
                        </fieldset>
                    </div>
                    <div class="form-group col-md-4">
                        {!! Form::label('description', ucfirst(__('description')), ['class' => 'col-form-label']) !!}
                        {!! Form::text('description', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group col-md-4">
                        <fieldset>
                            {!! Form::label('amount', ucfirst(__('amount')), ['class' => 'col-form-label']) !!}
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-dollar-sign"></i></span>
                                </div>
                                {!! Form::text('amount', null, ['class' => 'form-control']) !!}
                                <div class="input-group-append">
                                    <button class="btn btn-success pl-1 pr-1" type="button" id="addElement"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <div id="paymentData" class="aggrid ag-auto-height total-row ag-theme-material w-100"></div>
                </div>
                <div class="row">
                    <div class="col-sm-6 offset-sm-6">
                        <div class="row form-group">
                            <div class="col-6">
                                <strong>{!! Form::label('subtotal', 'Subtotal', ['class' => 'col-form-label']) !!}</strong>
                            </div>
                            <div class="col-6">
                                {!! Form::text('subtotal', $carrierPayment->gross_amount, ['class' => 'form-control', 'readonly']) !!}
                            </div>
                            <div class="col-6">
                                <strong>{!! Form::label('reductions', 'Reductions', ['class' => 'col-form-label']) !!}</strong>
                            </div>
                            <div class="col-6">
                                {!! Form::text('reductions', $carrierPayment->reductions, ['class' => 'form-control', 'readonly']) !!}
                            </div>
                            <div class="col-6">
                                <strong>{!! Form::label('total', 'Total', ['class' => 'col-form-label']) !!}</strong>
                            </div>
                            <div class="col-6">
                                {!! Form::text('total', $carrierPayment->total, ['class' => 'form-control', 'readonly']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::open(['route' => ['carrierPayment.update', $carrierPayment->id], 'method' => 'post', 'class' => 'form form-vertical', 'id' => 'updatePayment']) !!}
            {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
            {!! Form::close() !!}
        </div>
    </div>
</x-app-layout>
