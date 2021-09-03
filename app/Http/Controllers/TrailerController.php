<?php

namespace App\Http\Controllers;

use App\Models\ChassisType;
use App\Models\Trailer;
use App\Models\TrailerType;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Paperwork\PaperworkFilesFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrailerController extends Controller
{
    use GetSelectionData, GetSimpleSearchData, PaperworkFilesFunctions;

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
            'number' => ['required', 'string', 'max:255'],
            'plate' => ['nullable', 'string', 'max:255'],
            'vin' => ['nullable', 'string', 'max:255'],
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
                'trailer_types' => [null => ''] + TrailerType::pluck('name', 'id')->toArray(),
                'chassis_types' => [null => ''] + ChassisType::pluck('name', 'id')->toArray(),
                'statuses' => [null => ''] + ['available' => 'Available', 'rented' => 'Rented', 'oos' => 'Ouf of service'],
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
                $trailer = Trailer::findOrFail($id);
            else {
                $trailer = new Trailer();
                $trailer->status = 'available';
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
        $trailer = Trailer::with('shippers:id,name')
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
        $this->validator($request->all())->validate();

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
        $trailer = Trailer::findOrFail($id);

        if ($trailer) {
            $message = '';
            if ($trailer->rentals()->first())
                $message .= "•" . $this->generateCrudMessage(4, 'Trailer', ['constraint' => 'rentals']) . "<br>";
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
            ->where("number", "LIKE", "%$request->search%")
            /*->whereHas("truck", function ($q) use ($request) {
                if ($request->driver)
                    $q->whereHas("driver", function ($s) use ($request) {
                        $s->where("driver_id", $request->driver);
                    });
            })*/
            ->where(function ($q) use ($request) {
                if ($request->rental)
                    $q->where('status', 'available');
            })
            ->whereNull("inactive");

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Trailer::select([
            "trailers.id",
            "trailers.number",
            "trailers.plate",
            "trailers.vin",
            "trailers.status",
            "trailers.trailer_type_id",
        ])
            ->with(['trailer_type:id,name']);

        return $this->simpleSearchData($query, $request);
    }
}
