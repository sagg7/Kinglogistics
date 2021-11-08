<?php

namespace App\Traits\Paperwork;

use App\Models\Driver;
use App\Models\Paperwork;
use App\Models\PaperworkFile;
use App\Models\PaperworkTemplate;

trait PaperworkFilesFunctions
{
    /**
     * @param string $type
     * @return array
     */
    private function getPaperworkByType(string $type, $id = null): array
    {
        return [
            'filesUploads' => Paperwork::whereNull('template')
                ->where('type', $type)
                ->where(function ($q) use ($type, $id) {
                    $q->whereHas('shipper', function ($q) use ($type, $id) {
                        switch ($type) {
                            case 'driver':
                                $driver = Driver::with([
                                    'truck' => function ($q) {
                                        $q->with([
                                            'trailer' => function ($q) {
                                                $q->with(['shippers' => function ($q) {
                                                    $q->select('id');
                                                }])
                                                    ->select(['id']);
                                            }
                                        ])
                                            ->select(['id','driver_id','trailer_id']);
                                    },
                                ])
                                    ->find($id);
                                $shippers = $driver->truck->trailer->shippers_ids ?? [];
                                $q->whereIn('id', $shippers);
                                break;
                            default:
                                break;
                        }
                    })
                        ->orWhereDoesntHave('shipper');
                })
                ->orderBy('required', 'DESC')
                ->get(),
            'filesTemplates' => Paperwork::whereNotNull('template')
                ->where('type', $type)
                ->orderBy('required', 'DESC')
                ->get(['id', 'name', 'required']),
        ];
    }

    private function getFilesPaperwork(object $paperworkArray, int $related_id)
    {
        $ids = [];
        foreach ($paperworkArray as $item)
            $ids[] = $item->id;
        return PaperworkFile::whereIn('paperwork_id', $ids)
            ->where('related_id', $related_id)
            ->get()
            ->keyBy('paperwork_id')
            ->toArray();
    }

    private function getTemplatesPaperwork(object $paperworkArray, int $related_id)
    {
        $ids = [];
        foreach ($paperworkArray as $item)
            $ids[] = $item->id;
        return PaperworkTemplate::whereIn('paperwork_id', $ids)
            ->where('related_id', $related_id)
            ->get(['id', 'paperwork_id'])
            ->keyBy('paperwork_id')
            ->toArray();
    }
}
