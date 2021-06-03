<?php

namespace App\Traits\Storage;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait FileUpload
{
    private function uploadImage($file, string $path, bool $delete = true)
    {
        $path = "public/$path";
        if ($delete)
            Storage::deleteDirectory($path);
        Storage::makeDirectory($path);
        $storage_path = storage_path("app/$path/");
        // Temporary high quality image to encode in JPG format
        $temp = Image::make($file)->encode('jpg', 100);
        $temp->save($storage_path . 'temp.jpg');
        // Get temporal image and resize image (with aspect ratio) and canvas
        $img = Image::make($storage_path . 'temp.jpg');
        $img->fit(500, 500, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->resizeCanvas(500, 500, 'center', false, [255, 255, 255, 0]);
        $img->save($storage_path . 'img.jpg', 100);
        $md5 = md5($img->__toString());
        // Store file on local storage
        $filepath = "$path/$md5.jpg";
        Storage::put($filepath, (string)file_get_contents($storage_path . 'img.jpg'));
        // Delete temporary files
        Storage::delete("$path/temp.jpg");
        Storage::delete("$path/img.jpg");

        return "images/$filepath";
    }

    private function uploadSignature($file, string $path, bool $delete = true)
    {
        $originalPath = $path;
        $path = "public/$path";
        if ($delete)
            Storage::deleteDirectory($path);
        Storage::makeDirectory($path);
        $storage_path = storage_path("app/$path/");
        $img = Image::make($file)->encode('png', 100);
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
        return Storage::deleteDirectory($path);
    }
}
