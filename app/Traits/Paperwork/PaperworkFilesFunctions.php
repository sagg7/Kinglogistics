<?php

namespace App\Traits\Paperwork;

use App\Models\Paperwork;
use App\Models\PaperworkFile;
use App\Models\PaperworkTemplate;

trait PaperworkFilesFunctions
{
    /**
     * @param string $type
     * @return array
     */
    private function getPaperworkByType(string $type): array
    {
        return [
            'filesUploads' => Paperwork::whereNull('template')
                ->where('type', $type)
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
