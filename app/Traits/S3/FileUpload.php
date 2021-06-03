<?php

namespace App\Traits\S3;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait FileUpload
{
    protected $disk;

    /**
     * @param string $model_string
     * @param null $id
     * @return mixed
     */
    private function getModel(string $model_string, $id = null)
    {
        return new $model_string();
    }

    /**
     * @param $path
     * @param string $name
     * @param string $file
     * @return string
     */
    public function uploadS3($path, string $name, string $file): string
    {
        Storage::disk('s3')->putFileAS($path, $file, $name, 'public');
        return 'https://' . env('AWS_BUCKET') . '.s3.amazonaws.com/' . $path . '/';
    }

    /**
     * @param $files
     * @param string $path
     * @param string|null $model_string
     * @param null $id
     * @return array
     */
    public function uploadFiles($files, string $path, string $model_string = null, $id = null): array
    {
        $fileResults = [];
        foreach ($files as $index => $file) {
            $name = Carbon::now()->getTimestamp() . '.' . $file->extension();
            $original = urldecode($file->getClientOriginalName());

            $amazon_path = $this->uploadS3($path, $name, $file);

            $fileResults[$index]['name'] = $name;
            $fileResults[$index]['url'] = $amazon_path;

            if ($model_string)
                $result['file'] = $this->saveDB($name, $original, $path, $model_string, $id);
        }
        return $fileResults;
    }

    /**
     * @param $file
     * @param string $path
     * @param string|null $model_string
     * @param null $id
     * @return array
     */
    public function uploadFile($file, string $path, string $model_string = null, $id = null): array
    {
        $name = Carbon::now()->getTimestamp() . '.' . $file->extension();
        $original = urldecode($file->getClientOriginalName());

        $this->uploadS3($path, $name, $file);

        $result = [
            'original' => $original,
            'name' => $name,
            'url' => 'https://' . env('AWS_BUCKET') . '.s3-' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . $path . '/' . $name,
        ];

        if ($model_string)
            $result['file'] = $this->saveDB($name, $original, $path, $model_string, $id);

        return $result;
    }

    /**
     * @param string $filename
     * @param string $original
     * @param string $path
     * @param string $model_string
     * @param $id
     * @return mixed
     */
    private function saveDB(string $filename, string $original, string $path, string $model_string, $id)
    {
        $file = $this->getModel($model_string, $id);
        $file->original_name = $original;
        $file->name = $filename;
        $file->path = $path;
        $file->save();

        return $file;
    }

    /**
     * @param $file
     * @param string $file_name
     * @param string $path
     * @return bool|int
     */
    public function uploadBase64($file, string $file_name, string $path)
    {
        list(, $file) = explode(';', $file);
        list(, $file) = explode(',', $file);
        $file = base64_decode($file);
        Storage::makeDirectory($path);
        $extension = '.png';
        $path_file = storage_path('app/' . $path . '/');
        Storage::deleteDirectory($path);
        return file_put_contents($path_file . '/' . $file_name . $extension, $file);
    }

    /**
     * @param $file
     * @param $id
     * @param string $type
     * @param string $amazonPath
     * @return string
     */
    public function uploadProfileImage($file, $id, string $type, string $amazonPath): string
    {
        $path = "temp/$type/$id";
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
        // Store file on Amazon
        Storage::disk('s3')->put("$amazonPath/profile.jpg", (string)file_get_contents($storage_path . 'profile.jpg'), 'public');
        // Get file url
        $url = 'https://' . env('AWS_BUCKET') . '.s3.amazonaws.com/' . $amazonPath . '/profile.jpg';
        // Delete temporary files
        Storage::delete($path . '/temp.jpg');
        Storage::delete($path . '/profile.jpg');
        Storage::deleteDirectory($path);

        return $url;
    }

    /**
     * @param string $path
     * @param string|null $model_string
     * @param null $id
     * @return bool
     * @throws \Exception
     */
    public function deleteFile(string $path, string $model_string = null, $id = null): bool
    {
        $path = explode('?',array_values(array_filter(explode('https://' . env('AWS_BUCKET') . '.s3.amazonaws.com/', $path)))[0])[0];
        $response = Storage::disk('s3')->delete($path);
        if ($response && $model_string) {
            $file = $this->getModel($model_string)->find($id);
            $file->delete();
        }
        return $response;
    }

    /**
     * @param string $path
     * @return array
     */
    private function getFiles(string $path): array
    {
        $storage = Storage::disk('s3')->allFiles($path);

        $files = [];
        foreach ($storage as $filename) {
            $explode = explode('/', $filename);
            $length = count($explode) - 1;
            $version = Storage::disk('s3')->lastModified($filename);
            $files[] = [
                'filename' => $filename,
                'url' => 'https://' . env('AWS_BUCKET') . '.s3.amazonaws.com/' . $filename . "?" . $version,
                'name' => $explode[$length],
            ];
        }
        return $files;
    }

    /**
     * @param object $file
     * @return string
     */
    public function getAmazonUrl(object $file): string
    {
        return 'https://' . env('AWS_BUCKET') . '.s3.amazonaws.com/' . $file->path . '/' . $file->name;
    }
}
