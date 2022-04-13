<?php

namespace App\Http\Controllers;

use App\Enums\CarrierEnum;
use App\Enums\RoleSlugs;
use App\Mail\SendNotificationTemplate;
use App\Models\Carrier;
use App\Models\CarrierEquipment;
use App\Models\Load;
use App\Models\User;
use App\Rules\EmailArray;
use App\Traits\CRUD\crudMessage;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Paperwork\PaperworkFilesFunctions;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CarrierController extends Controller
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['string', 'email', 'max:255', "unique:carriers,email,$id,id"],
            'password' => [$id ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable','string','max:255'],
            'address' => ['nullable','string','max:255'],
            'city' => ['nullable','string','max:255'],
            'state' => ['nullable','string','max:255'],
            'zip_code' => ['nullable','string','max:255'],
            'owner' => ['nullable','string','max:255'],
            'invoice_email' => ['nullable', new EmailArray, 'max:255'],
        ]);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return $this->getPaperworkByType("carrier");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        return view('carriers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('carriers.create', $params);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Carrier
     */
    private function storeUpdate(Request $request, $id = null): Carrier
    {
        if ($id)
            $carrier = Carrier::whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
                ->findOrFail($id);
        else {
            $carrier = new Carrier();
            $carrier->broker_id = session('broker');
            if (auth()->user()->hasRole('seller'))
                $carrier->seller_id = auth()->user()->id;
        }

        $carrier->name = $request->name;
        $carrier->email = $request->email;
        $carrier->phone = $request->phone;
        $carrier->address = $request->address;
        $carrier->city = $request->city;
        $carrier->state = $request->state;
        $carrier->zip_code = $request->zip_code;
        $carrier->owner = $request->owner;
        $carrier->inactive = $request->inactive ?? null;
        $carrier->invoice_email = $request->invoice_email;
        if ($request->password)
            $carrier->password = Hash::make($request->password);
        $carrier->save();

        return $carrier;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse|\Illuminate\Http\Response
     * @throws ValidationException
     */
    public function store(Request $request)
    {   
        $this->validator($request->all())->validate();

        return DB::transaction(function () use ($request) {
            $carrier = $this->storeUpdate($request);
            $request->merge(['id' => $carrier->id]);
            $request->merge(['status' => 'prospect']);
            $this->setStatus($request);
            return ['success' => true, 'data' => $carrier];
        });

    }

    public function getCarrierData(int $id)
    {
        $carrier = Carrier::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);
        $createEdit = $this->createEditParams();
        $paperworkUploads = $this->getFilesPaperwork($createEdit['filesUploads'], $carrier->id);
        $paperworkTemplates = $this->getTemplatesPaperwork($createEdit['filesTemplates'], $carrier->id);
        return compact('carrier', 'paperworkUploads', 'paperworkTemplates') + $createEdit;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $carrier = Carrier::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);
        return view('carriers.show', compact('carrier'));
    }

    public function summaryData(int $id = null)
    {
        $id ?: $id = auth()->user()->id;
        $carrier = auth()->guard('web')->check()
            ? Carrier::whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
                ->findOrFail($id)
            : auth()->user();

        $loadsBase = Load::whereHas('driver', function ($q) use ($id) {
            $q->whereHas('carrier', function ($q) use ($id) {
                $q->where('carrier_id', $id);
            });
        });
        $now = Carbon::now();
        $startOfYear = (clone $now)->startOfYear();
        $aYearAgo = (clone $now)->subtract('year', 1);
        $startOfWeek = (clone $now)->startOfWeek();
        $startPastWeek = (clone $now)->subtract('week', 1)->startOfWeek();
        $endPastWeek = (clone $startPastWeek)->endOfWeek();
        $startPastMonth = (clone $now)->subtract('month', 1)->startOfMonth();
        $endPastMonth = (clone $startPastMonth)->endOfMonth();

        // Current Year Query Base
        $currentYear = (clone $loadsBase)->whereDate('date', '>=', $startOfYear)
            ->whereDate('date', '<=', $now);
        // Current Week Query Base
        $currentWeek = (clone $loadsBase)->whereDate('date', '>=', $startOfWeek)
            ->whereDate('date', '<=', $now);
        // Past Week Query Base
        $pastWeek = (clone $loadsBase)->whereDate('date', '>=', $startPastWeek)
            ->whereDate('date', '<=', $endPastWeek);
        // Past Month Query Base
        $pastMonth = (clone $loadsBase)->whereDate('date', '>=', $startPastMonth)
            ->whereDate('date', '<=', $endPastMonth);

        $carrier->load([
            'ranking',
            'rentals' => function ($q) {
                $q->select([
                    'rentals.id',
                    'rentals.carrier_id',
                    'rentals.trailer_id',
                    'rentals.cost',
                ])
                    ->whereNull('finished_at')
                    ->with('trailer:id,number,status');
            },
            'drivers' => function ($q) use ($startOfWeek, $now) {
                $q->select([
                    'drivers.id',
                    'drivers.carrier_id',
                    'drivers.name',
                ])
                    ->with('turn')
                    ->withCount([
                        'loads as loads_count' => function ($q) use ($startOfWeek, $now) {
                            $q->whereDate('date', '>=', $startOfWeek)
                                ->whereDate('date', '<=', $now);
                        },
                    ]);
            },
        ])
            ->loadCount([
                'incidents' => function ($q) use ($aYearAgo) {
                    $q->whereDate('date', '>=', $aYearAgo);
                }
            ]);

        // Card #1
        $incomeYear = $currentYear->sum('rate');
        // Card #2
        $incomePastMonth = $pastMonth->sum('rate');
        // Card #3
        $incomePastWeek = $pastWeek->sum('rate');
        // Card #4
        $incomeWeek = $currentWeek->sum('rate');
        // Card #5
        $ranking = $carrier->ranking->ranking ?? "N/A";
        // Card #6
        // TODO: ALERTS
        // Card #7
        $status = ucfirst($carrier->status);
        // Card #8 Loads Last Week
        $totalLoadsPastWeek = $pastWeek->count();
        // Card #9 Loads This Week
        $totalLoadsWeek = $currentWeek->count();
        // Card #10 Active Rentals
        $rentals = $carrier->rentals;
        // Card #10 Active Drivers
        $drivers = $carrier->drivers;
        // Card #11 Number of Incidents
        $incidents = $carrier->incidents_count;

        return compact('incomeYear', 'incomePastMonth', 'incomePastWeek', 'incomeWeek', 'status',
            'totalLoadsPastWeek', 'totalLoadsWeek', 'rentals', 'drivers', 'incidents', 'ranking');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $params = $this->getCarrierData($id);
        return view('carriers.edit', $params);
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
        $this->validator($request->all(), $id)->validate();

        $this->storeUpdate($request, $id);

        if ($profile)
            return redirect()->route('carrier.profile');
        else
            return redirect()->route('carrier.index');
    }

    /**
     * @param Request $request
     * @return array|\never
     */
    public function setStatus(Request $request)
    {
        $carrier = Carrier::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($request->id);
        switch ($request->status) {
            case "prospect":
                $status = CarrierEnum::PROSPECT;
                $host = explode(".", $request->getHost());
                $host = $host[1] . "." . $host[2];
                $subject = "Please complete your paperwork";
                $title = "Complete your paperwork to continue the process";
                $content = "Login to the paperwork completion process by this link";
                foreach ($carrier->drivers as $driver) {
                    $params = [
                        "subject" => $subject,
                        "title" => $title,
                        "content" => $content,
                        "route" => "https://" . env('ROUTE_DRIVERS') . ".$host/tokenLogin?token=" . crc32($driver->id.$driver->password),
                    ];
                    Mail::to($driver->email)->send(new SendNotificationTemplate($params));
                }
                $params = [
                    "subject" => $subject,
                    "title" => $title,
                    "content" => $content,
                    "route" => "https://" . env('ROUTE_CARRIERS') . ".$host/tokenLogin?token=" . crc32($carrier->id.$carrier->password),
                ];
                Mail::to($carrier->email)->send(new SendNotificationTemplate($params));
                break;
            case "ready":
                $status = CarrierEnum::READY_TO_WORK;
                $users = User::whereHas('roles', function ($q) {
                    $q->where('slug', RoleSlugs::HUMAN_RESOURCES)
                        ->orWhere('slug', RoleSlugs::OPERATIONS);
                })
                    ->get();
                foreach ($users as $user) {
                    $params = [
                        "subject" => "There's a new carrier ready to work",
                        "title" => 'The carrier "' . $carrier->name . '" has been set as ready to work',
                        "content" => "Continue to the site to see its progress",
                        "route" => route('dashboard'),
                    ];
                    Mail::to($user->email)->send(new SendNotificationTemplate($params));
                }
                break;
            case "active":
                $status = CarrierEnum::ACTIVE;
                $users = User::where(function ($q) use ($carrier) {
                    $q->where(function ($q) use ($carrier) {
                        $q->where('users.id', $carrier->seller_id)
                            ->whereHas('roles', function ($q) {
                                $q->where('slug', RoleSlugs::SELLER);
                            });
                    })
                        ->orWhereHas('roles', function ($q) {
                            $q->where('slug', RoleSlugs::OPERATIONS)
                                ->orWhere('slug', RoleSlugs::ACCOUNTANT);
                        });
                })
                    ->get();
                foreach ($users as $user) {
                    $params = [
                        "subject" => "A carrier carrier is now active",
                        "title" => 'The carrier "' . $carrier->name . '" has been set as active',
                        "content" => "Continue to the site to see more information",
                        "route" => route('dashboard'),
                    ];
                    Mail::to($user->email)->send(new SendNotificationTemplate($params));
                }
                break;
            case "not_working":
                $status = CarrierEnum::NOT_WORKING;
                break;
            case "not_rehirable":
                $status = CarrierEnum::NOT_REHIRABLE;
                $carrier->drivers()->delete();
                $carrier->delete();
                break;
            default:
                return abort(404);
        }
        $carrier->status = $status;

        return ['success' => $carrier->save()];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return array
     */
    public function destroy(int $id): array
    {
        $carrier = Carrier::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);

        if ($carrier) {
            $message = '';
            if ($carrier->rentals()->first()) {
                $message .= "•" . $this->generateCrudMessage(4, 'Carrier', ['constraint' => 'rentals']) . "<br>";
            }
            if ($message) {
                return ['success' => false, 'msg' => $message];
            }
            $carrier->drivers()->delete();
            return ['success' => $carrier->delete()];
        }
        return ['success' => false];
    }

    /**
     * @param int $id
     * @return array|false[]
     */
    public function restore(int $id): array
    {
        $carrier = Carrier::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->withTrashed()
            ->where('id', $id);

        if ($carrier) {
            $carrierFound = (clone $carrier->first());
            $carrierFound->drivers()->withTrashed()->restore();
            if ($carrierFound->status === CarrierEnum::NOT_REHIRABLE) {
                $carrierFound->status = CarrierEnum::ACTIVE;
                $carrierFound->save();
            }
            return ['success' => $carrier->restore()];
        }

        return ['success' => false];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = Carrier::select([
            'id',
            'name as text',
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->where("name", "LIKE", "%$request->search%")
            ->whereNull("inactive");

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param $query
     * @param $type
     * @return mixed
     */
    private function filterByType($query, $type)
    {
        switch ($type) {
            default:
            case 'active':
                $query->where(function ($q) {
                    $q->where('status', '=', CarrierEnum::INTERESTED)
                        ->orWhere('status', '=', CarrierEnum::ACTIVE);
                });
                break;
            case 'ready':
                $query->where('status', '=', CarrierEnum::READY_TO_WORK);
                break;
            case 'prospect':
                $query->where('status', '=', CarrierEnum::PROSPECT);
                break;
            case 'deleted':
                $query->onlyTrashed()
                    ->where('status', '!=', CarrierEnum::NOT_REHIRABLE);
                break;
            case 'notWorking':
                $query->where('status', CarrierEnum::NOT_WORKING);
                break;
            case 'notRehirable':
                $query->onlyTrashed()
                    ->where('status', CarrierEnum::NOT_REHIRABLE);
                break;
        }

        return $query;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function search(Request $request, string $type = null): array
    {
        $query = Carrier::select([
            "carriers.id",
            "carriers.name",
            "carriers.email",
            "carriers.phone",
            "carriers.status",
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            });

        $query = $this->filterByType($query, $type);

        return $this->multiTabSearchData($query, $request);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function searchEquipment(Request $request): array
    {
        $query = CarrierEquipment::select([
            "carrier_equipment.id",
            "carrier_equipment.name",
            "carrier_equipment.status",
            "carrier_equipment.description",
        ]);

        return $this->multiTabSearchData($query, $request);
    }
}
