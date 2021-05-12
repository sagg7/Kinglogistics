<?php

namespace App\Traits\Storage;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait FileUpload
{
    private function uploadProfileImg($file, string $path, bool $delete = true)
    {
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
        $img->save($storage_path . 'profile.jpg', 100);
        $md5 = md5($img->__toString());
        // Store file on local storage
        $filepath = "$path/$md5.jpg";
        Storage::put($filepath, (string)file_get_contents($storage_path . 'profile.jpg'));
        // Delete temporary files
        Storage::delete("$path/temp.jpg");
        Storage::delete("$path/profile.jpg");

        return "images/$filepath";
    }

    private function deleteDirectory(string $path)
    {
        return Storage::deleteDirectory($path);
    }
}
