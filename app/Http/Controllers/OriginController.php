<?php

namespace App\Http\Controllers;

use App\Models\Origin;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OriginController extends Controller
{
    use GetSelectionData, GetSimpleSearchData;

    private function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'coords' => ['required', 'string', 'max:255'],
        ], [
            'coords.required' => 'The origin map location is required',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        return view('trips.origins.index');
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Origin
     */
    private function storeUpdate(Request $request, int $id = null): Origin
    {
        if ($id) {
            $origin = Origin::where('id', session('broker'))
                ->findOrFail($id);
        } else {
            $origin = new Origin();
            $origin->broker_id = session('broker');
        }

        $origin->name = $request->name;
        $origin->coords = $request->coords;
        $origin->save();

        return $origin;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('trips.origins.create');
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

        return redirect()->route('origin.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $origin = Origin::where('broker_id', session('broker'))
            ->findOrFail($id);
        $params = compact('origin');
        return view('trips.origins.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request, $id);

        return redirect()->route('origin.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return array
     */
    public function destroy(int $id): array
    {
        $origin = Origin::where('id', session('broker'))
            ->findOrFail($id);

        return ['success' => $origin->delete()];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = Origin::select([
            'id',
            'name AS text',
            'coords',
        ])
            ->where("name", "LIKE", "%$request->search%")
            ->where(function ($q) {
                if (auth()->guard('web')->check()) {
                    $q->where('broker_id', session('broker'));
                } else if (auth()->guard('shipper')->check()) {
                    $q->where('broker_id', auth()->user()->broker_id);
                }
            });

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function search(Request $request): array
    {
        $customSearch = [];

        $query = Origin::select([
            'id',
            'name',
            'coords',
        ])
            ->where('broker_id', session('broker'))
            ->with([
                'trips' => function ($q) {
                    $q->select('id', 'origin_id')
                        ->withCount('loads')
                        ->withCount([
                            'loads AS loads_tons_sum' => function ($q) {
                                $q->select(DB::raw('SUM(tons) tons_sum'));
                                    //->where('tons', 'regexp', '^[0-9]+$');
                            }
                        ]);
                }
            ]);

        return $this->multiTabSearchData($query, $request);
    }
}
