<?php

namespace App\Traits\Storage;

use Illuminate\Support\Facades\Storage;

trait S3Functions
{
    private function getTemporaryFile($value)
    {
        $disk = Storage::disk('s3');
        return $disk->getAwsTemporaryUrl($disk->getDriver()->getAdapter(), $value, now()->addMinutes(5), []);
    }
}
