<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Paperwork\PaperworkFilesFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TruckController extends Controller
{
    use GetSelectionData, GetSimpleSearchData, PaperworkFilesFunctions;

    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        $validations = [
            'trailer_id' => ['nullable', 'exists:trailers,id'],
            'driver_id' => ['nullable', 'exists:drivers,id'],
            'seller_id' => ['nullable', 'exists:users,id'],
            'number' => ['required', 'string', 'max:255'],
            'plate' => ['nullable', 'string', 'max:255'],
            'vin' => ['nullable', 'string', 'max:255'],
            'make' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'numeric', 'max:' . (date('Y') + 1)],
            'diesel_card' => ['nullable', 'string', 'max:255'],
        ];

        if (auth()->guard('web')->check()) {
            $validations['carrier_id'] = ['required', 'exists:carriers,id'];
            $validations['seller_id'] = ['nullable', 'exists:users,id'];
        }
        return Validator::make($data, $validations);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        $data = [];
        /*if (auth()->guard('web')->check())
            $data = [
                //'carriers' => Carrier::pluck('name', 'id')->toArray(),
                'sellers' => [null => 'Select'] + User::where(function ($q) {
                    $q->whereHas('roles', function ($r) {
                        $r->where('slug', 'seller');
                    });
                })
                    ->pluck('name', 'id')->toArray(),
            ];*/
        return $data + $this->getPaperworkByType('truck');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('trucks.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('trucks.create', $params);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Truck
     */
    private function storeUpdate(Request $request, $id = null): Truck
    {
        return DB::transaction(function () use ($request, $id) {
            if ($id)
                $truck = Truck::findOrFail($id);
            else {
                $truck = new Truck();
            }

            $trailer = Trailer::whereHas('truck')
                ->with('truck')
                ->find($request->trailer_id);
            if ($trailer) {
                $trailer->truck->trailer_id = null;
                $trailer->truck->save();
            }
            if (auth()->guard('carrier')->check())
                $truck->carrier_id = auth()->user()->id;
            else
                $truck->carrier_id = $request->carrier_id;
            $truck->trailer_id = $request->trailer_id;
            $truck->driver_id = $request->driver_id;
            $truck->number = $request->number;
            $truck->plate = $request->plate;
            $truck->vin = $request->vin;
            $truck->make = $request->make;
            $truck->model = $request->model;
            $truck->year = $request->year;
            if ($request->diesel_card)
                $truck->diesel_card = $request->diesel_card;
            $truck->inactive = $request->inactive ?? null;
            if ($request->seller_id)
                $truck->seller_id = $request->seller_id;
            $truck->save();

            return $truck;
        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request);

        return redirect()->route('truck.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $truck = Truck::with([
            'driver:id,name',
            'trailer:id,number',
            'seller:id,name',
            'carrier:id,name',
        ])->find($id);
        $createEdit = $this->createEditParams();
        $paperworkUploads = $this->getFilesPaperwork($createEdit['filesUploads'], $truck->id);
        $paperworkTemplates = $this->getTemplatesPaperwork($createEdit['filesTemplates'], $truck->id);
        $params = compact('truck', 'paperworkUploads', 'paperworkTemplates') + $createEdit;
        return view('trucks.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request, $id);

        return redirect()->route('truck.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $truck = Truck::findOrFail($id);

        if ($truck) {
            $message = '';
            if ($truck->driver)
                $message .= "•" . $this->generateCrudMessage(4, 'Truck', ['constraint' => 'driver']) . "<br>";
            if ($message)
                return ['success' => false, 'msg' => $message];
            else
                return ['success' => $truck->delete()];
        } else
            return ['success' => false];
    }


    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = Truck::select([
            'id',
            'number as text',
        ])
            ->where("number", "LIKE", "%$request->search%")
            ->where(function ($q) use ($request) {
                if (auth()->guard('web')->check() && $request->carrier)
                    $q->where("carrier_id", $request->carrier);
                else if (auth()->guard('carrier')->check())
                    $q->where("carrier_id", auth()->user()->id);
            })
            ->whereNull("inactive");

        if ($request->type === "drivers") {
            $query->whereDoesntHave('driver');
        }

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        switch ($item) {
            case 'driver':
                $array = [
                    'relation' => $item,
                    'column' => 'name',
                ];
                break;
            case 'trailer':
                $array = [
                    'relation' => $item,
                    'column' => 'number',
                ];
                break;
            default:
                $array = null;
                break;
        }

        return $array;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Truck::select([
            "trucks.id",
            "trucks.number",
            "trucks.plate",
            "trucks.vin",
            "trucks.trailer_id",
            "trucks.driver_id",
        ])
            ->where(function ($q) use ($request) {
                if (auth()->guard('carrier')->check())
                    $q->where("carrier_id", auth()->user()->id);
                if ($request->trip || $request->shipper || $request->driver)
                    $q->whereHas('driver', function ($q) use ($request) {
                        if ($request->driver)
                            $q->where('id', $request->driver);
                        if ($request->trip)
                            $q->whereHas('active_load', function ($q) use ($request) {
                                $q->where('trip_id', $request->trip);
                            });
                        if ($request->shipper)
                            $q->whereHas('shippers', function ($q) use ($request) {
                                $q->where('id', $request->shipper);
                            });
                    });
            })
            ->with([
                'driver' => function ($q) use ($request) {
                    $q->with([
                        'active_rental:driver_id,deposit,deposit_is_paid',
                        'shippers' => function ($q) use ($request) {
                            if ($request->shipper) {
                                $q->where('id', $request->shipper);
                            }
                            $q->select('id', 'name');
                        },
                    ])
                        ->select('id', 'name');
                },
                'trailer:id,number',
            ]);

        if ($request->graph) {
            $query = $query->whereNull("inactive")->get();
            $shippers = [["shipper" => "Unassigned", "count" => 0]];
            $sortShipper = function ($shipper) use (&$shippers) {
                $key = array_search($shipper, array_column($shippers, 'shipper'));
                if ($key !== false) {
                    $shippers[$key]['count']++;
                } else {
                    $shippers[] = [
                        'shipper' => $shipper,
                        'count' => 1,
                    ];
                }
            };
            foreach ($query as $item) {
                if (!$item->driver || count($item->driver->shippers) === 0) {
                    $sortShipper('Unassigned');
                } else {
                    foreach ($item->driver->shippers as $shipper) {
                        $sortShipper($shipper->name);
                    }
                }
            }
            if ($shippers[0]["count"] === 0)  {
                unset($shippers[0]);
                $shippers = array_values($shippers);
            }
            return $shippers;
        }

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }
}
