<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Turn\DriverTurn;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    use GetSelectionData, GetSimpleSearchData, DriverTurn;

    public function index()
    {
        return view('drivers.index');
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, $type)
    {
        $query = Driver::select([
            "drivers.id",
            "drivers.name",
            "drivers.zone_id",
            "drivers.carrier_id",
        ])
            ->whereNull('inactive')
            ->where(function ($q) {
                if (auth()->guard('shipper')->check())
                    $q->whereHas('truck', function ($q) {
                        $q->whereHas('trailer', function ($q) {
                            $q->whereHas('shippers', function ($q) {
                                $q->where('id', auth()->user()->id);
                            });
                        });
                    });
            })
            ->with([
                'truck:driver_id,number',
                'zone:id,name',
                'carrier:id,name',
                'latestLoad' => function ($q) {
                    $q->where('status', '!=', 'finished')
                        ->select('status', 'driver_id');
                },
                'shift:id,driver_id',
            ]);

        switch ($type)
        {
            case 'active':
                $query->where(function ($q) {
                    $q->whereHas('shift')
                        ->orWhereHas('turn', function ($q) {
                            $this->filterByActiveTurn($q);
                        });
                });
                break;
            case 'inactive':
                $query->whereDoesntHave('shift');
                break;
            case 'awaiting':
                $query->whereHas('availableDriver');
                break;
        }

        $relationships = [];
        if ($request->searchable) {
            $searchable = [];
            foreach ($request->searchable as $item) {
                switch ($item) {
                    case 'zone':
                    case 'carrier':
                        $relationships[] = [
                            'relation' => $item,
                            'column' => 'name',
                        ];
                        break;
                    case 'truck':
                        $relationships[] = [
                            'relation' => $item,
                            'column' => 'number',
                        ];
                        break;
                    case 'latest_load':
                        $relationships[] = [
                            'relation' => 'latestLoad',
                            'column' => 'status',
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
