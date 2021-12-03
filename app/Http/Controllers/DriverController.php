<?php

namespace App\Http\Controllers;

use App\Enums\LoadStatusEnum;
use App\Exceptions\DriverHasUnfinishedLoadsException;
use App\Models\AvailableDriver;
use App\Models\Driver;
use App\Models\Shift;
use App\Models\Zone;
use App\Traits\Driver\DriverParams;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Paperwork\PaperworkFilesFunctions;
use App\Traits\Turn\DriverTurn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DriverController extends Controller
{
    use GetSelectionData, GetSimpleSearchData, DriverTurn, DriverParams, PaperworkFilesFunctions;

    /**
     * @param null $id
     * @return array
     */
    private function createEditParams($id = null): array
    {
        return $this->getTurnsArray() + $this->getPaperworkByType('driver', $id) +
            ['zones' => [null => 'Select'] + Zone::pluck('name', 'id')->toArray()] +
            ['language' => ['spanish' => 'Spanish', 'english' => 'English']];
    }

    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'turn_id' => ['sometimes', 'numeric'],
            'zone_id' => ['sometimes', 'exists:zones,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['string', 'email', 'max:255', "unique:drivers,email,$id,id"],
            'password' => [$id ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'inactive_observations' => ['nullable', 'string', 'max:512'],
            'shippers' => ['nullable', 'array', 'exists:shippers,id'],
        ]);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Driver
     */
    private function storeUpdate(Request $request, $id = null): Driver
    {
        return DB::transaction(function ($q) use ($request, $id) {
            $carrier_id = auth()->guard('carrier')->check() ? auth()->user()->id : $request->carrier_id;
            if ($id) {
                $driver = Driver::where(function ($q) {
                    if (auth()->guard('carrier')->check())
                        $q->where('carrier_id', auth()->user()->id);
                })
                    ->findOrFail($id);
                if (auth()->guard('web')->check())
                    $driver->carrier_id = $carrier_id;
            } else {
                $driver = new Driver();
                $driver->carrier_id = $carrier_id;
            }

            $driver->name = $request->name;
            $driver->email = $request->email;
            if ($request->password)
                $driver->password = Hash::make($request->password);

            $driver->turn_id = $request->turn_id;
            $driver->zone_id = $request->zone_id;
            $driver->phone = $request->phone;
            $driver->address = $request->address;
            $driver->language = $request->language;
            $driver->inactive = $request->inactive ?? null;
            $driver->inactive_observations = $request->inactive_observations;
            $driver->save();

            $driver->shippers()->sync($request->shippers);

            return $driver;
        });
    }

    public function index()
    {
        return view('drivers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $params = $this->createEditParams();

        return view('drivers.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request);

        return redirect()->route('driver.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(int $id)
    {
        $driver = Driver::where(function ($q) {
            if (auth()->guard('carrier')->check())
                $q->where('carrier_id', auth()->user()->id);
        })
            ->with(['carrier', 'shippers'])
            ->with(['zone:id,name'])
            ->findOrFail($id);
        $createEdit = $this->createEditParams($id);
        $paperworkUploads = $this->getFilesPaperwork($createEdit['filesUploads'], $driver->id);
        $paperworkTemplates = $this->getTemplatesPaperwork($createEdit['filesTemplates'], $driver->id);
        $params = compact('driver', 'paperworkUploads', 'paperworkTemplates') + $createEdit;
        return view('drivers.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @param bool $profile
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function update(Request $request, int $id, bool $profile = false): RedirectResponse
    {
        $this->validator($request->all(), $id)->errors();

        $this->storeUpdate($request, $id);

        if ($profile)
            return redirect()->route('driver.profile');
        else
            return redirect()->route('driver.index');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = Driver::select([
            'id',
            'name as text',
        ])
            ->where("name", "LIKE", "%$request->search%")
            ->where(function ($q) use ($request) {
                if ($request->carrier)
                    $q->where("carrier_id", $request->carrier);
                if ($request->zone)
                    $q->where("zone_id", $request->zone);
                if ($request->turn)
                    $q->where("turn_id", $request->turn);
                if ($request->noTruck)
                    $q->whereDoesntHave('truck');
                if ($request->shipper)
                    $q->whereHas('shippers', function ($q) use ($request) {
                        $q->where('id', $request->shipper);
                    })
                        ->orWhereHas('truck', function ($q) use ($request) {
                            $q->whereHas('trailer', function ($q) use ($request) {
                                $q->whereHas('shippers', function ($q) use ($request) {
                                    $q->where('id', $request->shipper);
                                });
                            });
                        });
            })
            ->whereHas("carrier", function ($q) {
                $q->whereNull("inactive");
            })
            ->where(function ($q) use ($request) {
                if ($request->rental)
                    $q->whereHas("truck", function ($s) {
                        $s->whereDoesntHave("trailer");
                    });
            })
            ->with('truck.trailer:id,number');

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return array
     */
    public function destroy(int $id): array
    {
        $driver = Driver::where(function ($q) {
            if (auth()->guard('carrier')->check())
                $q->where('carrier_id', auth()->user()->id);
        })
            ->findOrFail($id);

        if ($driver)
            return ['success' => $driver->delete()];
        else
            return ['success' => false];
    }

    /**
     * @param int $id
     * @return array|false[]
     */
    public function restore(int $id): array
    {
        $driver = Driver::where(function ($q) {
            if (auth()->guard('carrier')->check())
                $q->where('carrier_id', auth()->user()->id);
        })
            ->withTrashed()
            ->where('id', $id);

        if ($driver)
            return ['success' => $driver->restore()];
        else
            return ['success' => false];
    }

    /**
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        switch ($item) {
            case 'shift':
            case 'zone':
            case 'carrier':
                $array = [
                    'relation' => $item,
                    'column' => 'name',
                ];
                break;
            case 'truck':
                $array = [
                    'relation' => $item,
                    'column' => 'number',
                ];
                break;
            case 'latest_load':
                $array = [
                    'relation' => 'latestLoad',
                    'result_relation' => $item,
                    'column' => 'status',
                ];
                break;
            default:
                $array = null;
                break;
        }

        return $array;
    }

    /**
     * @param $query
     * @param $type
     * @return mixed
     */
    private function filterByType($query, $type)
    {
        switch ($type)
        {
            case 'morning':
                $query->where(function ($q) {
                    /*$q->whereHas('shift')
                        ->orWhereHas('turn', function ($q) {
                            $this->filterByActiveTurn($q);
                        });*/
                    $q->where('turn_id', 1);
                });
                break;
            case 'night':
                $query->where(function ($q) {
                    /*$q->whereDoesntHave('shift')
                        ->orWhereHas('turn', function ($q) {
                            $this->filterByInactiveTurn($q);
                        });*/
                    $q->where('turn_id', 2);
                });
                break;
            case 'awaiting':
                $query->whereHas('availableDriver');
                break;
            case 'inactive':
                $query->where('inactive', 1);
                break;
            case 'deleted':
                $query->onlyTrashed();
                break;
        }

        return $query;
    }

    /**
     * @param Request $request
     * @param string|null $type
     * @return array
     */
    public function search(Request $request, string $type = null): array
    {
        $query = Driver::select([
            "drivers.id",
            "drivers.name",
            "drivers.zone_id",
            "drivers.carrier_id",
            "drivers.turn_id",
            "drivers.status",
            "drivers.inactive",
            "drivers.inactive_observations",
        ])
            ->where(function ($q) use ($request, $type) {
                if (auth()->guard('shipper')->check())
                    $q->whereHas('shippers', function ($q) use ($request) {
                        $q->where('id', auth()->user()->id);
                    })
                        ->orWhereHas('truck', function ($q) {
                            $q->whereHas('trailer', function ($q) {
                                $q->whereHas('shippers', function ($q) {
                                    $q->where('id', auth()->user()->id);
                                });
                            });
                        });
                if ($request->driver)
                    $q->where('id', $request->driver);
                if ($request->shipper)
                    $q->whereHas('shippers', function ($q) use ($request) {
                        $q->where('id', $request->shipper);
                    })
                        ->orWhereHas('truck', function ($q) use ($request) {
                            $q->whereHas('trailer', function ($q) use ($request) {
                                $q->whereHas('shippers', function ($q) use ($request) {
                                    $q->where('id', $request->shipper);
                                });
                            });
                        });
                if ($request->trip)
                    $q->whereHas('active_load', function ($q) use ($request) {
                        $q->where('trip_id', $request->trip_id);
                });
                if ($type !== 'inactive')
                    $q->whereNull('inactive');
            });

        if ($request->graph) {
            $query->where(function ($q) use ($request) {
            });
            $all = $query->get();
            $morning = [
                'active' => 0,
                'inactive' => 0,
                'ready' => 0,
                'pending' => 0,
                'error' => 0,
            ];
            $night = [
                'active' => 0,
                'inactive' => 0,
                'ready' => 0,
                'pending' => 0,
                'error' => 0,
            ];
            foreach ($all as $item) {
                if ($item->turn_id == 1) {
                    $morning[$item->status]++;
                } else {
                    $night[$item->status]++;
                }
            }

            return compact('morning', 'night');
        }

        $query->with([
            'truck:driver_id,number',
            'zone:id,name',
            'carrier:id,name',
            'latestLoad' => function ($q) {
                $q->where('status', '!=', 'finished')
                    ->select('status', 'driver_id');
            },
            'shift:id,driver_id',
        ]);

        $query = $this->filterByType($query, $type);

        $result = $this->multiTabSearchData($query, $request, 'getRelationArray', 'where');
        if ($request->startRow == 0) {
            $result = $this->multiTabSearchData($query, $request, 'getRelationArray', 'where', true);
            $result["count"] = [
                "active" => (clone $query)->where('status', 'active')->count(),
                "inactive" => (clone $query)->where('status', 'inactive')->count(),
                "ready" => (clone $query)->where('status', 'ready')->count(),
                "pending" => (clone $query)->where('status', 'pending')->count(),
                "error" => (clone $query)->where('status', 'error')->count(),
            ];
            unset($result['query']);
        }
        return $result;
    }

    public function endShift($id){

        $driver = Driver::find($id);

        $unfinishedLoads = $driver->loads->filter(function ($load) {
            return !in_array($load->status, [LoadStatusEnum::FINISHED, LoadStatusEnum::UNALLOCATED]);
        });

        if (count($unfinishedLoads) > 0) {
            throw new DriverHasUnfinishedLoadsException;
        }

        if (!empty($driver->availableDriver)) {
            AvailableDriver::destroy($driver->availableDriver->id);
            return response(['status' => 'ok'], 200);

        }

        if (!empty($driver->shift)) {
            Shift::destroy($driver->shift->id);
        }
        $driver->status = 'inactive';
        $driver->save();

        return ['success' => $driver->restore()];

    }
}
