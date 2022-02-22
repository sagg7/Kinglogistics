<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DestinationController extends Controller
{
    use GetSelectionData, GetSimpleSearchData;

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'statuses' => ['stage' => 'Stage', 'loads' => 'Loads'],
        ];
    }

    private function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'coords' => ['required', 'string', 'max:255'],
            'status' => ['required'],
            'status_current' => ['numeric'],
            'status_total' => ['numeric'],
        ], [
            'coords.required' => 'The destination map location is required',
        ]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        return view('trips.destinations.index');
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Destination
     */
    private function storeUpdate(Request $request, int $id = null): Destination
    {
        if ($id) {
            $destination = Destination::where('id', session('broker'))
                ->findOrFail($id);
        } else {
            $destination = new Destination();
            $destination->broker_id = session('broker');
        }

        $destination->name = $request->name;
        $destination->coords = $request->coords;
        $destination->status = $request->status;
        $destination->status_current = $request->status_current;
        $destination->status_total = $request->status_total;
        $destination->save();

        return $destination;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $params = $this->createEditParams();
        return view('trips.destinations.create', $params);
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

        return redirect()->route('destination.index');
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
        $destination = Destination::where('id', session('broker'))
            ->findOrFail($id);
        $params = compact('destination') + $this->createEditParams();
        return view('trips.destinations.edit', $params);
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

        return redirect()->route('destination.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return array
     */
    public function destroy(int $id): array
    {
        $destination = Destination::where('id', session('broker'))
            ->findOrFail($id);

        return ['success' => $destination->delete()];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = Destination::select([
            'id',
            'name AS text',
        ])
            ->where("name", "LIKE", "%$request->search%")
            ->where('broker_id', session('broker'));

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        return null;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function search(Request $request): array
    {
        $customSearch = [];

        $query = Destination::select([
            'id',
            'name',
            'coords',
            'status',
            'status_current',
            'status_total',
        ])
            ->where('broker_id', session('broker'))
            ->with([
                'trips' => function ($q) {
                    $q->select('trips.id', 'destination_id')
                        ->withCount('loads')
                        ->withCount([
                            'loads AS loads_tons_sum' => function ($q) {
                                $q->select(DB::raw('SUM(tons) tons_sum'));
                                    //->where('tons', 'regexp', '^[0-9]+$');
                            }
                        ]);
                }
            ]);

        return $this->multiTabSearchData($query, $request, 'getRelationArray', 'where', $customSearch);
    }
}
