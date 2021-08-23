<?php

namespace App\Traits\Storage;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait FileUpload
{
    /**
     * @param $file
     * @param string $path
     * @param int $quality
     * @param string|null $options
     * @param bool $local
     * @param bool $delete
     * @return string
     */
    private function uploadImage($file, string $path, int $quality = 100, string $options = null, bool $local = false, bool $delete = true, string $extension = 'png'): string
    {
        $originalPath = $path;
        $path = ($local ? "public" : "temp") . "/$path";
        if ($delete)
            Storage::deleteDirectory($path);
        Storage::makeDirectory($path);
        $storage_path = storage_path("app/$path/");
        $img = Image::make($file)->encode($extension, $quality);
        $md5 = md5($img->__toString());
        $img->save($storage_path . "$md5.$extension");
        // Store file on local storage
        $filepath = "$originalPath/$md5.$extension";

        if ($local)
            return "storage/$filepath";
        else {
            Storage::disk('s3')
                ->put($filepath, (string)file_get_contents($storage_path . "$md5.$extension"), $options);
            Storage::deleteDirectory($path);
            return $filepath;
        }
    }

    /**
     * @param $file
     * @param string $path
     * @param bool $local
     * @return string
     */
    private function uploadFile($file, string $path, bool $local = false): string
    {
        $originalPath = $path;
        $path = ($local ? "public" : "temp") . "/$path";
        $name = $file->getClientOriginalName();
        Storage::putFileAS($path, $file, $name);
        $filepath = "$originalPath/$name";
        $storage_path = storage_path("app/$path/");

        if ($local)
            return "storage/$filepath";
        else {
            Storage::disk('s3')->put($filepath, (string)file_get_contents($storage_path . $name));
            Storage::deleteDirectory($path);
            return $filepath;
        }
    }

    private function deleteFile(string $path)
    {
        return Storage::disk('s3')->delete($path);
    }

    private function deleteDirectory(string $path)
    {
        return Storage::deleteDirectory("public/$path");
    }
}
