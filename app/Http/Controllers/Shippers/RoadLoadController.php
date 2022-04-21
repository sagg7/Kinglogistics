<?php


namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Controller;
use App\Models\Load;
use App\Models\RoadLoad;
use App\Models\LoadType;
use App\Models\LoadTrailerType;
use App\Models\LoadMode;
use App\Models\State;
use App\Models\City;
use App\Rules\EmailArray;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use function React\Promise\Stream\first;

class RoadLoadController extends Controller
{
    use GetSelectionData, GetSimpleSearchData;
    // load_trailer_types

    private function validator(array $data)
    {
        return Validator::make($data, [
            'statesOrigin' => ['required'],
            'citiesOrigin' => ['required'],
            'origin_early_pick_up_date' => ['nullable', 'date'],
            'origin_late_pick_up_date' => ['nullable', 'date'],
            'stateDestination' => ['required', 'numeric'],
            'cityDestination' => ['required', 'numeric'],
            'destination_early_pick_up_date' => ['nullable', 'date'],
            'destination_late_pick_up_date' => ['nullable', 'date'],
            'trailer_type_id' => ['required', 'numeric'],
            'mode_id' => ['nullable', 'numeric'],
            'shipper_rate' => ['nullable', 'numeric'],
            'rate' => ['nullable', 'numeric'],
            'weight' => ['nullable', 'numeric'],
            'tons' => ['nullable', 'numeric'],
            'width' => ['nullable', 'numeric'],
            'height' => ['nullable', 'numeric'],
            'length' => ['nullable', 'numeric'],
            'pieces' => ['nullable', 'numeric'],
            'pallets' => ['nullable', 'numeric'],
            'load_type_id' => ['required', 'numeric'],
            'mileage' => ['nullable', 'numeric'],
            'silo_number' => ['nullable', 'numeric'],
            'customer_po' => ['nullable', 'numeric'],
            'control_number' => ['required',  'string', 'max:255'],
            'pay_rate' => ['nullable', 'numeric'],
            'load_size' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:255']
        ]);
    }


    public function storeUpdate(Request $request)
    {
        // if ($id )
        //     $load = Load::whereHas('broker', function ($q) {
        //         $q->where('id', session('broker'));
        //     })
        //         ->findOrFail($id);
        // else {

        // if (auth()->guard('shipper')->check())
        // $q->where('shipper_id', auth()->user()->id);
        // dd($request->citiesOrigin);

        $coordsOrigin = City::with('state:id,name')->findOrFail($request->citiesOrigin)->first();
        $coordsDestination = City::with('state:id,name')->findOrFail($request->cityDestination)->first();
        $roadLoad = new RoadLoad();
        $load = new Load();
        $load->broker_id = auth()->user()->broker_id;
        $load->shipper_rate = $request->shipper_rate;
        $load->rate = $request->rate;
        $load->weight = $request->weight;
        $load->tons = $request->tons;
        $load->origin = $coordsOrigin->state->name . ', ' . $coordsOrigin->name;
        $load->origin_coords = $coordsOrigin->latitude . ', -' . $coordsOrigin->longitude;
        $load->destination = $coordsOrigin->state->name . ', ' . $coordsOrigin->name;
        $load->destination_coords = $coordsDestination->latitude . ', -' . $coordsDestination->longitude;
        $load->mileage = $request->mileage;
        $load->silo_number = $request->silo_number;
        $load->load_type_id = $request->load_type_id;
        $load->type = 'road';
        $load->customer_po = $request->customer_po;
        $load->control_number = $request->control_number;
        $load->notes = $request->notes;
        $load->shipper_id = auth()->user()->id;
        $load->date = Carbon::now()->format('Y-m-d');
        $load->creator_id = auth()->user()->id;
        $load->save();

        $roadLoad->load_id = $load->id;
        $roadLoad->origin_city_id = $request->citiesOrigin;
        $roadLoad->destination_city_id = $request->cityDestination;
        $roadLoad->origin_early_pick_up_date = Carbon::parse($request->origin_early_pick_up_date);
        $roadLoad->origin_late_pick_up_date = Carbon::parse($request->origin_late_pick_up_date);
        $roadLoad->destination_early_pick_up_date = Carbon::parse($request->destination_early_pick_up_date);
        $roadLoad->destination_late_pick_up_date = Carbon::parse($request->destination_late_pick_up_date);
        $roadLoad->trailer_type_id = $request->trailer_type_id;
        $roadLoad->mode_id = $request->mode_id;
        $roadLoad->width = $request->width;
        $roadLoad->height = $request->height;
        $roadLoad->length = $request->length;
        $roadLoad->cube = $request->width * $request->height * $roadLoad->length;
        $roadLoad->pay_rate = $request->pay_rate;
        $roadLoad->load_size = $request->load_size;
        $roadLoad->save();

    }


    public function store(Request $request)
    {
        $data = $request->all();
        $this->validator($data)->validate();
        $this->storeUpdate($request);
        if ($request->ajax()) {
            return ['success' => true];
        }
        return $request;
    }

    public function selectionLoadType(Request $request)
    {
        $query = LoadType::select([
            'id',
            'name as text',
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', auth()->user()->broker_id); // check why session('broker') is null
            });

        return $this->selectionData($query, $request->take, $request->page);
    }

    public function selectionTrailerType(Request $request)
    {
        $query = LoadTrailerType::select([
            'id',
            'name as text',
        ]);

        return $this->selectionData($query, $request->take, $request->page);
    }

    public function selectionLoadMode(Request $request)
    {
        $query = LoadMode::select([
            'id',
            'name as text',
        ]);

        return $this->selectionData($query, $request->take, $request->page);
    }

    public function selectionStates(Request $request)
    {
        $query = State::select([
            'id',
            'name as text',
        ]);

        return $this->selectionData($query, $request->take, $request->page);
    }
    public function selectionCity(Request $request)
    {
        if ($request->statesOrigin) {
            $state = $request->statesOrigin;
        } else {

            $state = $request->stateDestination;
        }
        $query = City::select([
            'id',
            'name as text',
        ])->where('state_id', $state);

        return $this->selectionData($query, $request->take, $request->page);
    }
    public function selectionSalers(Request $request)
    {
        if ($request->statesOrigin) {
            $state = $request->statesOrigin;
        } else {

            $state = $request->stateDestination;
        }
        $query = City::select([
            'id',
            'name as text',
        ])->where('state_id', $state);

        return $this->selectionData($query, $request->take, $request->page);
    }
}
