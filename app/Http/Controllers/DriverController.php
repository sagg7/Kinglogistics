<?php

namespace App\Http\Controllers;

use App\Enums\DriverEnum;
use App\Enums\LoadStatusEnum;
use App\Exceptions\DriverHasUnfinishedLoadsException;
use App\Exports\TemplateExport;
use App\Mail\SendNotificationTemplate;
use App\Models\AvailableDriver;
use App\Models\BotAnswers;
use App\Models\Driver;
use App\Models\Shift;
use App\Models\Zone;
use App\Traits\Driver\DriverParams;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Paperwork\PaperworkFilesFunctions;
use App\Traits\Turn\DriverTurn;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
            ['zones' => [null => 'Select'] + Zone::whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                })
                    ->pluck('name', 'id')->toArray()] +
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
                    ->where(function ($q) {
                        if (auth()->guard('web')->check()) {
                            $q->whereHas('broker', function ($q) {
                                $q->where('id', session('broker'));
                            });
                        }
                    })
                    ->findOrFail($id);
                if (auth()->guard('web')->check())
                    $driver->carrier_id = $carrier_id;
            } else {
                $driver = new Driver();
                $driver->broker_id = session('broker') ?? auth()->user()->broker_id;
                $driver->carrier_id = $carrier_id;
            }

            $driver->name = $request->name;
            $driver->email = $request->email;
            $driver->truck_id = $request->truck_id;
            if ($request->password)
                $driver->password = Hash::make($request->password);

            if ($id && $request->inactive)
                $this->endShift($id);

            if ($request->turn_id)
                $driver->turn_id = $request->turn_id;
            if ($request->zone_id)
                $driver->zone_id = $request->zone_id;
            $driver->phone = $request->phone;
            $driver->address = $request->address;
            $driver->language = $request->language;
            $driver->inactive = $request->inactive ?? null;
            $driver->inactive_observations = $request->inactive_observations;
            $driver->save();

        
                $driver->shippers()->sync($request->shippers);

            if (!$id) {
                $host = explode(".", $request->getHost());
                $host = $host[1] . "." . $host[2];
                $subject = "Hello $driver->name Please complete your paperwork";
                $title = "Complete your paperwork to continue the process";
                $content = "Login to the paperwork completion process by this link";
                $params = [
                    "subject" => $subject,
                    "title" => $title,
                    "content" => $content,
                    "route" => "https://" . env('ROUTE_DRIVERS') . ".$host/tokenLogin?token=" . crc32($driver->id.$driver->password),
                ];
                Mail::to($driver->email)->send(new SendNotificationTemplate($params));
            }

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
            ->where(function ($q) {
                if (auth()->guard('web')->check()) {
                    $q->whereHas('broker', function ($q) {
                        $q->where('id', session('broker'));
                    });
                }
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
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
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
           //->where(function ($q) use ($request) {//temporal
           //    if ($request->rental)
           //        $q->whereHas("truck", function ($s) {
           //            $s->whereDoesntHave("trailer");
           //        });
           //})
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
        $driver = Driver::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->where(function ($q) {
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
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
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
    private function filterByType($query, $type, Request $request)
    {
        switch ($type)
        {
            case 'dispatch':
                $query->where(function ($q) {
                    $q->where('status', DriverEnum::ACTIVE)
                        ->orwhere('status', DriverEnum::PENDING)
                        ->orwhere('status', DriverEnum::READY)
                        ->orWhere('status', DriverEnum::INACTIVE);
                });
                break;
            case 'morning':
                $query->where(function ($q) {
                    $q->where('turn_id', 1);
                });
                break;
            case 'night':
                $query->where(function ($q) {
                    $q->where('turn_id', 2);
                });
                break;
            case 'active':
            case 'awaiting':
            case 'loaded':
                $query->where(function ($q) {
                    $q->where('status', DriverEnum::ACTIVE)
                        ->orwhere('status', DriverEnum::PENDING)
                        ->orwhere('status', DriverEnum::READY);
                });
                if ($type === 'awaiting') {
                    $query->whereDoesntHave('active_load');
                    if (!$request->dispatch) {
                        $query->with('latestLoad', function ($q) {
                            $q->with('loadStatus:load_id,finished_timestamp');
                        });
                    }
                }
                if ($type === 'loaded') {
                    $query->whereHas('active_load');
                }
                break;
            case 'inactive':
                $query->where('status', DriverEnum::INACTIVE);
                break;
            case 'down':
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
     * @param string|array|null $type
     * @return array
     */
    public function search(Request $request, $type = null): array
    {
        if (!$type) {
            $type = $request->type ?? null;
        }
        $query = Driver::select([
            "drivers.id",
            "drivers.name",
            "drivers.zone_id",
            "drivers.carrier_id",
            "drivers.turn_id",
            "drivers.status",
            "drivers.inactive",
            "drivers.inactive_observations",
            "drivers.phone",
            "drivers.broker_id",
            "drivers.truck_id",
        ])
            ->where(function ($q) {
                if (auth()->guard('web')->check()) {
                    $q->whereHas('broker', function ($q) {
                        $q->where('id', session('broker'));
                    });
                }
            })
            ->where(function ($q) use ($request, $type) {
                if (auth()->guard('shipper')->check()) {
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
                }
                if ($request->driver) {
                    $q->where('id', $request->driver);
                }
                if ($request->shipper) {
                    $q->whereHas('shippers', function ($q) use ($request) {
                        $q->where('id', $request->shipper);
                    });
                }
                        //->orWhereHas('truck', function ($q) use ($request) {
                        //    $q->whereHas('trailer', function ($q) use ($request) {
                        //        $q->whereHas('shippers', function ($q) use ($request) {
                        //            $q->where('id', $request->shipper);
                        //        });
                        //    });
                        //});
                if ($request->trip) {
                    $q->whereHas('active_load', function ($q) use ($request) {
                        $q->where('trip_id', $request->trip_id);
                    });
                }
                if ($type !== 'down') {
                    $q->whereNull('inactive');
                }
            });


        if ($request->graph) {
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
                if ($item->turn_id === 1) {
                    $morning[$item->status]++;
                } else {
                    $night[$item->status]++;
                }
            }
            return compact('morning', 'night');
        }

        $customSearch = [];
        if ($request->dispatch) {
            if ($request->count) {
                $morningQuery = (clone $query)->where('turn_id', 1)->wherehas('truck')->whereNull('inactive');
                $nightQuery = (clone $query)->where('turn_id', 2)->wherehas('truck')->whereNull('inactive');
                return [
                    "morning" => [
                        "active" => $this->filterByType((clone $morningQuery), 'active', $request)->count(),
                        "inactive" => $this->filterByType((clone $morningQuery), 'inactive', $request)->count(),
                        "awaiting" => $this->filterByType((clone $morningQuery), 'awaiting', $request)->count(),
                        "loaded" => $this->filterByType((clone $morningQuery), 'loaded', $request)->count(),
                    ],
                    "night" => [
                        "active" => $this->filterByType((clone $nightQuery), 'active', $request)->count(),
                        "inactive" => $this->filterByType((clone $nightQuery), 'inactive', $request)->count(),
                        "awaiting" => $this->filterByType((clone $nightQuery), 'awaiting', $request)->count(),
                        "loaded" => $this->filterByType((clone $nightQuery), 'loaded', $request)->count(),
                    ],
                ];
            }
            $array = $request->searchable;
            foreach ($array as $idx => $item) {
                switch ($item) {
                    case 'trailer':
                    case 'carrier_phone':
                        unset($array[$idx]);
                        break;
                    default:
                        break;
                }
            }
            $request->searchable = $array;
            $query->wherehas('truck')
                ->with([
                'carrier:id,name,phone',
                'truck' => function ($q) {
                    $q->with(['trailer:id,number'])
                        ->select('id', 'trailer_id', 'number');
                },
            ]);
            if ($request->search) {
                $customSearch = function ($q) use ($request) {
                    $search = $request->search;
                    $q->orWhere(function ($q) use ($search) {
                        $q->whereHas('carrier', function ($q) use ($search) {
                            $q->where('phone', 'LIKE', "%$search%");
                        })
                            ->orWhereHas('truck', function ($q) use ($search) {
                                $q->whereHas('trailer', function ($q) use ($search) {
                                    $q->where('number', 'LIKE', "%$search%");
                                });
                            });
                    });
                };
            }
        } else {
            $query->with([
                'truck:id,number',
                'zone:id,name',
                'carrier:id,name',
                'botAnswer',
                'latestLoad' => function ($q) {
                    $q->where('status', '!=', 'finished')
                        ->select('status', 'driver_id');
                },
                'shift:id,driver_id',
            ]);
        }

        if (is_array($type)) {
            foreach ($type as $item) {
                $query = $this->filterByType($query, $item, $request);
            }
        } else {
            $query = $this->filterByType($query, $type, $request);
        }

        $result = $this->multiTabSearchData($query, $request, 'getRelationArray', 'where', $customSearch);
        if ($request->startRow == 0 && !$request->dispatch) {
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

        $driver = Driver::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->find($id);

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

        BotAnswers::where('driver_id', $id)->delete();

        $driver->status = 'inactive';
        $driver->save();


        return ['success' => $driver];

    }

    public function setActive($id){

        $driver = Driver::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);
        $driver->status = 'active';
        $driver->save();

        BotAnswers::where('driver_id', $id)->delete();

        return ['success' => $driver];

    }

    public function downloadExcel(){
        $query = Driver::select([
            "drivers.id",
            "drivers.name",
            "drivers.zone_id",
            "drivers.carrier_id",
            "drivers.turn_id",
            "drivers.status",
            "drivers.inactive",
            "drivers.inactive_observations",
            "drivers.phone",
            "drivers.broker_id",
            "drivers.truck_id",
        ])
            ->where(function ($q) {
                if (auth()->guard('web')->check()) {
                    $q->whereHas('broker', function ($q) {
                        $q->where('id', session('broker'));
                    });
                }
            })->orderBy("inactive", 'asc')
            ->orderBy("name", 'asc')->get();

        if (count($query) === 0)
            return redirect()->back()->withErrors('There are no Users to generate the document');

        $data = [];
        foreach ($query as $driver) {
            $data[] = [
                'name' => $driver->name,
                'truck' => $driver->truck ? $driver->truck->number : null,
                'carrier' => $driver->carrier ? $driver->carrier->name : null,
                'status' => $driver->status,
                'inactive' => ($driver->inactive) ? "Yes" : "No",
                'observations' => $driver->inactive_observations
            ];
        }

        return (new TemplateExport([
            "data" => $data,
            "headers" => ["Name", "Truck", "Carrier", "Status", "Inactive", "Observations"],
        ]))->download("Drivers - " . Carbon::now()->format('m-d-Y') . ".xlsx");
    }
}
