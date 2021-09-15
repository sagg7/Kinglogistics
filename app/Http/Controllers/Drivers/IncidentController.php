<?php

namespace App\Http\Controllers\Drivers;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Storage\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncidentController extends Controller
{
    use GetSimpleSearchData, FileUpload;

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'sanctions' => [null => '', 'warning' => 'Warning', 'fine' => 'Fine', 'termination' => 'Termination'],
        ];
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('subdomains.drivers.incidents.index');
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(int $id)
    {
        $incident = Incident::where('driver_id', auth()->user()->id)
            ->with([
                'incident_type:id,name',
                'driver:id,name',
                'carrier:id,name',
                'truck:id,number',
                'trailer:id,number',
            ])
            ->findOrFail($id);
        $createEdit = $this->createEditParams();
        $incident->sanction_name = $createEdit['sanctions'][$incident->sanction];
        $params = compact('incident');
        return view('subdomains.drivers.incidents.edit', $params);
    }

    /**
     * @param Request $request
     * @param int $id
     */
    public function update(Request $request, int $id)
    {
        DB::transaction(function () use ($request, $id) {
            $incident = Incident::where('driver_id', auth()->user()->id)
                ->findOrFail($id);
            $incident->driver_signature = $this->uploadImage($request->driver_signature, "safety/incident/$incident->id/driver");
            $incident->save();
        });

        return redirect()->route('incident.index');
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
            "incidents.user_id",
            "incidents.sanction",
        ])
            ->with([
                'incident_type:id,name',
                'carrier:id,name',
                'driver:id,name',
                'user:id,name',
            ])
            ->where('driver_id', auth()->user()->id)
            ->whereNull('refuse_sign')
            ->whereNull('driver_signature');

        $relationships = [];
        if ($request->searchable) {
            $searchable = [];
            foreach ($request->searchable as $item) {
                switch ($item) {
                    case 'carrier':
                    case 'driver':
                    case 'user':
                    case 'incident_type':
                        $relationships[] = [
                            'relation' => $item,
                            'column' => 'name',
                        ];
                        break;
                    default:
                        $searchable[count($searchable) + 1] = $item;
                        break;
                }
            }
            $request->searchable = $searchable;
        }

        return $this->multiTabSearchData($query, $request, $relationships);
    }
}
