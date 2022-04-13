
<div class="modal fade" id="selectTruckModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Selector Of Truck</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                
                {!! Form::open(['route' => ['truck.store', 'carrierFlag'], 'method' => 'post', 'class' => 'form form-vertical', 'id'=> 'truckForm']) !!}
                @include('trucks.common.form')
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
{{-- C:\Users\King Logistic Oil\Documents\King\app-kinglogistics\resources\views\carriers\common\modals\selectTruckModal.blade.php --}}