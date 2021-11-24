<x-app-layout>
    <div class="card pills-layout">
        <div class="card-content">
            <div class="card-header align-items-center">
                <div class="text-center col">
                    <i class="far fa-check-circle success font-large-5 mb-2"></i>
                    <h1>Congratulations!</h1>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <h3>{!! $text !!}</h3>
                    <a class="btn btn-primary mt-2" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-power-off"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
