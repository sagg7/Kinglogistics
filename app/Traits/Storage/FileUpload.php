<?php

namespace App\Traits\Storage;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait FileUpload
{
    private function uploadImage($file, string $path, $quality = 100, bool $delete = true)
    {
        $originalPath = $path;
        $path = "public/$path";
        if ($delete)
            Storage::deleteDirectory($path);
        Storage::makeDirectory($path);
        $storage_path = storage_path("app/$path/");
        $img = Image::make($file)->encode('png', $quality);
        $md5 = md5($img->__toString());
        $img->save($storage_path . "$md5.png");
        // Store file on local storage
        $filepath = "$originalPath/$md5.png";

        return "storage/$filepath";
    }

    /**
     * @param $file
     * @param string $path
     * @return string
     */
    public function uploadFile($file, string $path): string
    {
        $originalPath = $path;
        $path = "public/$path";
        $name = $file->getClientOriginalName();
        Storage::putFileAS($path, $file, $name);
        return "storage/$originalPath/$name";
    }


    private function deleteDirectory(string $path)
    {
        return Storage::deleteDirectory("public/$path");
    }
}
