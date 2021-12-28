<?php

namespace App\Http\Controllers;

use App\Models\DispatchSchedule;
use App\Models\Role;
use App\Models\User;
use App\Traits\EloquentQueryBuilder\agFilter;
use App\Traits\EloquentQueryBuilder\EloquentFiltering;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use agFilter, EloquentFiltering, GetSelectionData, GetSimpleSearchData;
    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['string', 'email', 'max:255', "unique:users,email,$id,id"],
            'password' => [$id ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'role' => ['sometimes', 'exists:roles,id']
        ]);
    }

    private function createEditParams()
    {
        return [
            'roles' => [null => 'Select'] + Role::orderBy('name')->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('users.create', $params);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return User
     */
    private function storeUpdate(Request $request, $id = null): User
    {
        if ($id)
            $user = User::findOrFail($id);
        else
            $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->turn_start = date("H:i:s", strtotime($request->turn_start));
        $user->turn_end = date("H:i:s", strtotime($request->turn_end));
        if ($request->password)
            $user->password = Hash::make($request->password);
        $user->save();

        if ($request->role)
            $user->roles()->sync($request->role);

        return $user;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request);

        return redirect()->route('user.index');
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
    public function edit(int $id)
    {
        $user = User::findOrFail($id);
        $user->role = $user->getRoleId();
        $params = compact('user') + $this->createEditParams();
        return view('users.edit', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = User::findOrFail(auth()->user()->id);
        $params = compact('user');
        return view('users.profile', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $this->validator($request->all(), $id)->validate();

        $this->storeUpdate($request, $id);

        if (!$request->role)
            return redirect()->route('user.profile');
        else
            return redirect()->route('user.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return array
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user) {
            return ['success' => $user->delete()];
        } else
            return ['success' => false];
    }


    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = User::select([
            'id',
            'name as text',
        ])
            ->where("name", "LIKE", "%$request->search%")
            ->where(function ($q) use ($request) {
                if ($request->type)
                    $q->whereHas('roles', function ($r) use ($request) {
                        $r->where('slug', $request->type);
                    });
            })
            ->with('roles');

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        switch ($item) {
            case 'role':
                $array = [
                    'relation' => 'roles',
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = User::select([
            "users.id",
            "users.name",
            "users.email",
            "users.phone",
        ])
            ->with('roles:name');

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function staffOnTurn(Request $request)
    {
        $query = User::select([
            "users.id",
            "users.name",
            "users.email",
            "users.phone",
        ])
            ->with('roles:name');
        $now = Carbon::now();
        $timeString = $now->toTimeString();
        $query->where(function ($q) use ($timeString, $now) {
            $q->whereTime('users.turn_end', '<', DB::raw('TIME(users.turn_start)'));
            if ($now->hour >= 0 && $now->hour <= 12)
                $q->whereTime('turn_end', '>', $timeString);
            else
                $q->whereTime('turn_start', '<=', $timeString);
        })
            ->orWhere(function ($q) use ($timeString) {
                $q->whereTime('turn_end', '>', DB::raw('TIME(turn_start)'))
                    ->whereTime('turn_start', '<=', $timeString)
                    ->whereTime('turn_end', '>', $timeString);
            });

        if ($request->all)
            return $query->get();

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }

    /**
     * @param bool $decode
     * @return array
     */
    private function getHoursRange(bool $decode = false): array
    {
        $range = [];
        $timeRange = range(0, 47*1800, 3600);
        foreach ($timeRange as $time) {
            $time += (3600 * 6);
            if ($decode) {
                $date = date('H:i:s', $time);
            } else {
                $date = date('h:i a', $time);
            }
            $range[] = $date;
        }
        return $range;
    }

    /**
     * @param $day_string
     * @return int
     */
    private function getDayNumber($day_string): int
    {
        switch ($day_string) {
            default:
            case 'mon':
                return 0;
            case 'tue':
                return 1;
            case 'wed':
                return 2;
            case 'thu':
                return 3;
            case 'fri':
                return 4;
            case 'sat':
                return 5;
            case 'sun':
                return 6;
        }
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function dispatchSchedule()
    {
        $range = $this->getHoursRange();
        $schedule = DispatchSchedule::with('user:id,name')
            ->get();
        foreach ($schedule as $i => $item) {
            $schedule[$i]->time_number = array_search(Carbon::parse($item->time)->format('h:i a'), $range);
        }
        $params = compact('range', 'schedule');
        return view('users.dispatchSchedule', $params);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function storeDispatchSchedule(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $range = $this->getHoursRange(true);

            $formatScheduleArray = function ($schedule, $type = 'current') use ($range) {
                $formatted = [];
                $now = Carbon::now();
                foreach ($schedule as $i => $item) {
                    $formatted[] = [
                        'day' => $this->getDayNumber($item['day']),
                        'time' => $range[$item['hour']],
                        'user_id' => $item['user'],
                        'status' => $type,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                return $formatted;
            };

            // Delete past schedule data
            // TODO: CHANGE WHEN MORE THAN ONE DOMAIN TO FILTER BY DOMAIN DATA
            DispatchSchedule::truncate();
            $current = $formatScheduleArray($request->current);
            $next = $formatScheduleArray($request->next, "next");
            DispatchSchedule::insert(array_merge($current, $next));

            return ['success' => true];
        });
    }
}
