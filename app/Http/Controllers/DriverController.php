<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Turn\DriverTurn;
use Illuminate\Http\Request;
use function Clue\StreamFilter\fun;

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

    private function filterByType($query, $type)
    {
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
                $query->where(function ($q) {
                    $q->whereDoesntHave('shift')
                        ->orWhereHas('turn', function ($q) {
                            $this->filterByInactiveTurn($q);
                        });
                });
                break;
            case 'awaiting':
                $query->whereHas('availableDriver');
                break;
        }

        return $query;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function search(Request $request, $type = null)
    {
        $query = Driver::select([
            "drivers.id",
            "drivers.name",
            "drivers.zone_id",
            "drivers.carrier_id",
            "drivers.turn_id",
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
            });

        if ($request->graph) {
            $all = $query->get();
            $onShift = 0;
            $outOfShift = 0;
            foreach ($all as $item) {
                if ($item->shift)
                    $onShift++;
                else
                    $outOfShift++;
            }
            $active = $this->filterByType($query, 'active')->count();

            return compact('onShift', 'outOfShift', 'active');
        } else {
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
        }

        $query = $this->filterByType($query, $type);

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }
}
