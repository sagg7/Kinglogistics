<?php

namespace App\Http\Controllers;

use App\Traits\Storage\FileUpload;
use App\Traits\Storage\S3Functions;
use Illuminate\Http\Request;

class S3StorageController extends Controller
{
    use S3Functions, FileUpload;

    /**
     * @param Request $request
     */
    public function temporaryUrl(Request $request)
    {
        return redirect()->to($this->getTemporaryFile($request->url));
    }

    public function getTemporaryUrl(Request $request)
    {
        return $this->getTemporaryFile($request->url);
    }

    public function deleteFileFromUrl(Request $request)
    {
        return $this->deleteFile($request->url);
    }
}
