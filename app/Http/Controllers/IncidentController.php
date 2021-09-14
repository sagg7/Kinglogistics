<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentType;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Storage\FileUpload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class IncidentController extends Controller
{
    use GetSimpleSearchData, FileUpload;

    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'incident_type_id' => ['required', 'exists:incident_types,id'],
            'carrier_id' => ['required', 'exists:carriers,id'],
            'driver_id' => ['required', 'exists:drivers,id'],
            'truck_id' => ['required', 'exists:trucks,id'],
            'trailer_id' => ['required', 'exists:trailers,id'],
            'sanction' => ['required'],
            'date_submit' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1024'],
            'excuse' => ['required', 'string', 'max:1024'],
        ]);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'incident_types' => [null => ''] + IncidentType::select(DB::raw("IF(fine IS NOT NULL, CONCAT(name, ' - ', CONCAT('$', FORMAT(fine, 2))), name) as text"), 'id')
                    ->pluck('text', 'id')
                    ->toArray(),
            'sanctions' => [null => '', 'warning' => 'Warning', 'fine' => 'Fine', 'termination' => 'Termination'],
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->guard('shipper')->check())
            return view('subdomains.shippers.incidents.index');
        else
            return view('incidents.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('incidents.create', $params);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Incident
     */
    private function storeUpdate(Request $request, $id = null): Incident
    {
        if ($id)
            $incident = Incident::findOrFail($id);
        else {
            $incident = new Incident();
            $incident->user_id = auth()->user()->id;
        }

        $incident->incident_type_id = $request->incident_type_id;
        $incident->carrier_id = $request->carrier_id;
        $incident->driver_id = $request->driver_id;
        $incident->truck_id = $request->truck_id;
        $incident->trailer_id = $request->trailer_id;
        $incident->sanction = $request->sanction;
        $incident->date = Carbon::parse($request->date_submit);
        $incident->location = $request->location;
        $incident->description = trim($request->description);
        $incident->excuse = trim($request->excuse);
        $incident->refuse_sign = $request->refuse_sign ?? null;
        $incident->save();

        if ($request->sanction === "termination") {
            $incident->driver->inactive = 1;
            $incident->driver->save();
        }

        if (!$id) {
            $incident->safety_signature = $this->uploadImage($request->safety_signature, "safety/incident/$incident->id/safety");
            $incident->driver_signature = $this->uploadImage($request->driver_signature, "safety/incident/$incident->id/driver");
            $incident->save();
        }

        return $incident;
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

        return redirect()->route('incident.index');
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
        $incident = Incident::findOrFail($id);
        $params = compact('incident') + $this->createEditParams();
        return view('incidents.edit', $params);
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

        return redirect()->route('incident.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $incident = Incident::findOrFail($id);

        if ($incident)
            return ['success' => $incident->delete()];
        else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Incident::select([
            "incidents.id",
            "incidents.date",
            "incidents.incident_type_id",
            "incidents.carrier_id",
            "incidents.driver_id",
            "incidents.user_id",
            "incidents.sanction",
        ])
            ->where(function ($q) {
                if (auth()->guard('shipper')->check()) {
                    $q->whereHas("incident_type", function ($q) {
                        $q->whereNotNull('visible');
                    })
                        ->whereHas('trailer', function ($q) {
                            $q->whereHas('shippers', function ($q) {
                                $q->where('id', auth()->user()->id);
                            });
                        });
                }
            })
            ->with([
                'incident_type:id,name',
                'carrier:id,name',
                'driver:id,name',
                'user:id,name',
            ]);

        if ($request->searchable) {
            $searchable = [];
            $statement = "whereHas";
            foreach ($request->searchable as $item) {
                switch ($item) {
                    case 'carrier':
                    case 'driver':
                    case 'user':
                    case 'incident_type':
                        $query->$statement($item, function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%$request->search%");
                        });
                        $statement = "orWhereHas";
                        break;
                    default:
                        $searchable[count($searchable) + 1] = $item;
                        break;
                }
            }
            $request->searchable = $searchable;
        }

        return $this->simpleSearchData($query, $request, 'orWhere');
    }

    public function downloadPDF($id)
    {
        $incident = Incident::with([
            "carrier",
            "driver",
            "truck",
            "trailer",
            "user",
            "incident_type",
        ])
            ->where(function ($q) {
                if (auth()->guard('shipper')->check()) {
                    $q->whereHas("incident_type", function ($q) {
                        $q->whereNotNull('visible');
                    })
                        ->whereHas('trailer', function ($q) {
                            $q->whereHas('shippers', function ($q) {
                                $q->where('id', auth()->user()->id);
                            });
                        });
                }
            })
            ->findOrFail($id)
            ->toArray();

        $mpdf = new Mpdf();
        $mpdf->SetHTMLHeader('<div style="text-align: left; font-weight: bold;"><img style="width: 160px;" src=' . asset('images/logo.png') . ' alt="Logo"></div>');
        $title = "Violation Report Form";
        $html = view('exports.incidents.pdf', compact('title', 'incident'));
        $mpdf->AddPage('', // L - landscape, P - portrait
            '', '', '', '',
            5, // margin_left
            5, // margin right
            22, // margin top
            22, // margin bottom
            3, // margin header
            0); // margin footer
        $mpdf->WriteHTML($html);
        return $mpdf->Output();
    }
}
