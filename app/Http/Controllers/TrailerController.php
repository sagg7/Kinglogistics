<?php

namespace App\Http\Controllers;

use App\Enums\TrailerEnum;
use App\Exports\TemplateExport;
use App\Models\ChassisType;
use App\Models\Trailer;
use App\Models\TrailerType;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Paperwork\PaperworkFilesFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\CRUD\crudMessage;

class TrailerController extends Controller
{
    use GetSelectionData, GetSimpleSearchData, PaperworkFilesFunctions, crudMessage;

    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'trailer_type_id' => ['required', 'exists:trailer_types,id'],
            'chassis_type_id' => ['required', 'exists:chassis_types,id'],
            'shipper_id' => ['nullable', 'exists:shippers,id'],
            'number' => ['required', 'string', 'max:255', "unique:trailers,number,$id,id"],
            'plate' => ['nullable', 'string', 'max:255'],
            'vin' => ['nullable', 'string', 'max:255'],
            'shippers' => ['nullable', 'array', 'exists:shippers,id'],
            //'status' => ['required'],
        ],
        [],
        [
            'trailer_type_id' => 'trailer type',
            'chassis_type_id' => 'chassis type',
        ]);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
                'trailer_types' => [null => ''] + TrailerType::whereHas('broker', function ($q) {
                        $q->where('id', session('broker'));
                    })
                        ->pluck('name', 'id')->toArray(),
                'chassis_types' => [null => ''] + ChassisType::pluck('name', 'id')->toArray(),
                'statuses' => [null => ''] + [TrailerEnum::AVAILABLE => 'Available', TrailerEnum::RENTED => 'Rented', TrailerEnum::OUT_OF_SERVICE => 'Ouf of service'],
            ] + $this->getPaperworkByType("trailer");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('trailers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('trailers.create', $params);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Trailer
     */
    private function storeUpdate(Request $request, $id = null): Trailer
    {
        return DB::transaction(function ($q) use ($request, $id) {
            if ($id)
                $trailer = Trailer::whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                })
                    ->findOrFail($id);
            else {
                $trailer = new Trailer();
                $trailer->broker_id = session('broker');
                $trailer->status = TrailerEnum::AVAILABLE;
                // TODO: CASE FOR CARRIER OWNED TRAILER
                /*if (false && auth()->guard('carrier')->check())
                    $trailer->carrier_id = auth()->user()->id;*/
            }

            $trailer->trailer_type_id = $request->trailer_type_id;
            $trailer->chassis_type_id = $request->chassis_type_id;
            $trailer->number = $request->number;
            $trailer->plate = $request->plate;
            $trailer->vin = $request->vin;
            $trailer->inactive = $request->inactive ?? null;;
            $trailer->save();

            $trailer->shippers()->sync($request->shippers);

            return $trailer;
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

        return redirect()->route('trailer.index');
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
        $trailer = Trailer::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->with('shippers:id,name')
            ->find($id);
        $createEdit = $this->createEditParams();
        $paperworkUploads = $this->getFilesPaperwork($createEdit['filesUploads'], $trailer->id);
        $paperworkTemplates = $this->getTemplatesPaperwork($createEdit['filesTemplates'], $trailer->id);
        $params = compact('trailer', 'paperworkUploads', 'paperworkTemplates') + $createEdit;
        return view('trailers.edit', $params);
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
        $this->validator($request->all(), $id)->validate();

        $this->storeUpdate($request, $id);

        return redirect()->route('trailer.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trailer = Trailer::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);

        if ($trailer) {
            $message = '';
            if ($trailer->rentals()->first())
                $message .= "•" . $this->generateCrudMessage(4, 'Trailer') . "<br>";
            if ($message)
                return ['success' => false, 'msg' => $message];
            else
                return ['success' => $trailer->delete()];
        } else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = Trailer::select([
            'id',
            'number as text',
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->where("number", "LIKE", "%$request->search%")
            /*->whereHas("truck", function ($q) use ($request) {
                if ($request->driver)
                    $q->whereHas("driver", function ($s) use ($request) {
                        $s->where("driver_id", $request->driver);
                    });
            })*/
            ->where(function ($q) use ($request) {
                if ($request->rental)
                    $q->where('status', TrailerEnum::AVAILABLE);
            })
            ->whereNull("inactive");

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        switch ($item) {
            case 'trailer_type':
                $array = [
                    'relation' => $item,
                    'column' => 'name',
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
     * @return array
     */
    public function search(Request $request, $type = null)
    {
        $query = Trailer::select([
            "trailers.id",
            "trailers.number",
            "trailers.plate",
            "trailers.vin",
            "trailers.status",
            "trailers.trailer_type_id",
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->where(function ($q) use ($request, $type) {
                if ($request->driver)
                    $q->whereHas('truck', function ($q) use ($request) {
                        $q->whereHas('driver', function ($q) use ($request) {
                            $q->where('id', $request->driver);
                        });
                    });
                if ($request->shipper)
                    $q->whereHas('shippers', function ($q) use ($request) {
                        $q->where('id', $request->shipper);
                    });
                if ($request->trip)
                    $q->whereHas('truck', function ($q) use ($request) {
                        $q->whereHas('driver', function ($q) use ($request) {
                            $q->whereHas('active_load', function ($q) use ($request) {
                                $q->where('trip_id', $request->trip_id);
                            });
                        });
                    });
                if ($type)
                    $q->where('status', $type);
            })
            ->with(['trailer_type:id,name']);

        if ($request->graph) {
            $all = clone $query;
            $all = $all->where('status', "!=", 'returned')->count();
            $available = clone $query;
            $available = $available->where('status', TrailerEnum::AVAILABLE)->count();
            $rented = clone $query;
            $rented = $rented->where('status', TrailerEnum::RENTED)->count();
            return compact('all', 'available', 'rented');
        }

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }

    public function downloadXLS($type)
    {
        $trailers = Trailer::with([
            'trailer_type:id,name',
            'chassis_type:id,name',
            'rentals' => function ($q) {
                $q->where('status', '!=', 'finished')
                ->with(['carrier:id,name','driver:id,name']);
            }
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->where('status', $type)
            ->get();

        if (count($trailers) === 0)
            return redirect()->back()->withErrors('There are no rentals to generate the document');

        $data = [];
        foreach ($trailers as $trailer) {
            $data[] = [
                'number' => $trailer->number,
                'carrier' => isset($trailer->rentals[0]) ? (isset($trailer->rentals[0]->carrier)) ? $trailer->rentals[0]->carrier->name : null : null,
                'driver' => (isset($trailer->rentals[0]) && isset($trailer->rentals[0]->driver)) ? $trailer->rentals[0]->driver->name : null,
                'trailer_type' => $trailer->trailer_type->name,
                'chassis_type' => $trailer->chassis_type->name,
                'plate' => $trailer->plate,
                'vin' => $trailer->vin,
                //'status' => $trailer->status,
            ];
        }
        return (new TemplateExport([
            "data" => $data,
            "headers" => ["Number", "Carrier", "Driver", "Trailer Type", "Chassis Type", "Plate", "Vin"/*, "Status"*/],
        ]))->download("Trailers - " . Carbon::now()->format('m-d-Y') . ".xlsx");
    }
}
