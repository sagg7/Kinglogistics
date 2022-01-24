<?php

namespace App\Traits\Ranking;

use App\Enums\RentalStatusEnums;
use App\Models\Carrier;
use App\Models\CarrierRanking;
use Carbon\Carbon;

trait RankingTrait
{
    private function calculateRanking()
    {
        $now = Carbon::now();
        $week = (clone $now)->subWeek();
        $weekStart = (clone $week)->startOfWeek();
        $weekEnd = (clone $week)->endOfWeek();
        $rentalsFilter = function ($q) {
            $q->where('status', RentalStatusEnums::DELIVERED);
        };
        $loadsFilter = function ($q) use ($weekStart, $weekEnd) {
            $q->join('load_statuses', 'load_statuses.load_id', '=', 'loads.id')
                ->whereDate('finished_timestamp', '>=', $weekStart)
                ->whereDate('finished_timestamp', '<=', $weekEnd);
        };
        $carriers = Carrier::select([
                'id',
                'name',
                'broker_id',
            ])
            ->whereHas('rentals', function ($q) use ($rentalsFilter) {
                $rentalsFilter($q);
            })
            ->withCount([
                'rentals' => function ($q) use ($rentalsFilter) {
                    $rentalsFilter($q);
                },
            ])
            ->with([
                'drivers' => function ($q) use ($loadsFilter) {
                    $q->select('id', 'carrier_id')
                        ->whereHas('loads', function ($q) use ($loadsFilter) {
                            $loadsFilter($q);
                        })
                        ->withCount([
                            'loads as loads_count' => function ($q) use ($loadsFilter) {
                                $loadsFilter($q);
                            }
                        ]);
                }
            ])
            ->orderBy('broker_id')
            ->get();
        $temp = [];
        foreach ($carriers as $carrier) {
            $loadSum = 0;
            foreach ($carrier->drivers as $driver) {
                $loadSum += $driver->loads_count;
            }
            $loadsPerRental = $loadSum / $carrier->rentals_count;
            $temp[$carrier->broker_id][] = [
                'carrier_id' => $carrier->id,
                'value' => $loadsPerRental,
            ];
        }
        $ranking = [];
        foreach ($temp as $data) {
            usort($data, function ($a, $b) {
                return $b['value'] <=> $a['value'];
            });
            foreach ($data as $pos => $item) {
                $ranking[] = [
                    'carrier_id' => $item["carrier_id"],
                    'ranking' => $pos + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        CarrierRanking::truncate();
        CarrierRanking::insert($ranking);
    }
}
